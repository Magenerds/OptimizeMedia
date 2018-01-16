<?php
/**
 * @category   Magenerds
 * @package    Magenerds_OptimizeMedia
 * @author     Mahmood Dhia <m.dhia@techdivision.com>
 * @copyright  Copyright (c) 2018 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 */

namespace Magenerds\OptimizeMedia\Helper;

use Braintree\Exception;
use ImageOptimizer\Optimizer;
use ImageOptimizer\OptimizerFactory;
use Magenerds\OptimizeMedia\Helper\Config as ConfigHelper;
use Magenerds\OptimizeMedia\Model\Config\Source\ImageCheckMode;
use Magenerds\OptimizeMedia\Model\OptimizeImageRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Psr\Log\LoggerInterface;


class OptimizeImage extends AbstractHelper
{
    /**
     * Table name
     */
    const TableName = 'magenerds_optimizemedia_image';
    /**
     * Optimizer singleton instance
     *
     * @var Optimizer $instance
     */
    static private $instance = null;
    /**
     * Contains the magento root path
     *
     * @var string
     */
    static private $magentoRootPath = '';

    /**
     * Config helper
     *
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * OptimizeImage repository
     *
     * @var OptimizeImageRepository
     */
    protected $optimizeImageRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * OptimizeImage constructor.
     *
     * @param Context $context
     * @param ConfigHelper $configHelper
     * @param OptimizeImageRepository $optimizeImageRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ConfigHelper $configHelper,
        OptimizeImageRepository $optimizeImageRepository,
        LoggerInterface $logger
    )
    {
        $this->configHelper = $configHelper;
        $this->optimizeImageRepository = $optimizeImageRepository;
        $this->logger = $logger;

        // Initialize ImageOptimizer
        if ($this->configHelper->isModuleEnable() && $this->configHelper->isOptimizeWysiwygImagesEnable() && is_null(self::$instance)) {
            $this->initializeImageOptimizer();
        }

        parent::__construct($context);
    }

    /**
     * Initialize ImageOptimizer
     */
    private function initializeImageOptimizer()
    {
        $options = array();

        //<editor-fold desc="Check is binaries installed over npm">
        $objectManager = ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        self::$magentoRootPath = $directory->getRoot() . DIRECTORY_SEPARATOR;

        $binaryPaths = array(
            'optipng_bin' => self::$magentoRootPath . 'node_modules' . DIRECTORY_SEPARATOR . 'optipng-bin' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'optipng',
            'pngquant_bin' => self::$magentoRootPath . 'node_modules' . DIRECTORY_SEPARATOR . 'pngquant-bin' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'pngquant',
            'gifsicle_bin' => self::$magentoRootPath . 'node_modules' . DIRECTORY_SEPARATOR . 'gifsicle' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'gifsicle',
            'jpegoptim_bin' => self::$magentoRootPath . 'node_modules' . DIRECTORY_SEPARATOR . 'jpegoptim-bin' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'jpegoptim',
            'jpegtran_bin' => self::$magentoRootPath . 'node_modules' . DIRECTORY_SEPARATOR . 'jpegtran-bin' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'jpegtran'
        );

        // check image optimize binary is exist
        foreach ($binaryPaths as $key => $binaryPath) {
            if (is_file($binaryPath)) {
                $options[$key] = $binaryPath;
            }
        }
        //</editor-fold>

        // Initial optimizer
        $optimizerFactory = new OptimizerFactory($options);
        self::$instance = $optimizerFactory->get();

        if ($this->configHelper->isLoggingEnable()) {
            $this->logger->info('ImageOptimizer initialized');
        }
    }

    /**
     * Optimize Image
     *
     * @param string $absolutePath Absolute file path to image
     * @return bool
     */
    public function optimize($absolutePath)
    {
        // check ImageOptimizer is initialized
        if (is_null(self::$instance)) {
            return false;
        }

        // check file exists
        if (!is_file($absolutePath)) {
            if ($this->configHelper->isLoggingEnable()) {
                $this->logger->error('Image in ' . $absolutePath . ' not found');
            }
            return false;
        }

        // get hashed file path
        $imagePathHash = $this->getImageIdFromAbsolutePath($absolutePath);

        // Try to load image information by SearchCriteria
        $optimizeImageRepository = $this->optimizeImageRepository->getByFilePathHash($imagePathHash);

        //<editor-fold desc="Compare with database file information">
        if (!is_null($optimizeImageRepository) && $this->configHelper->getCheckMode() !== ImageCheckMode::CHECK_DISABLED) {

            switch ($this->configHelper->getCheckMode()) {
                case ImageCheckMode::CHECK_MODIFIED_TIME:
                    $fileTime = filemtime($absolutePath);

                    // compare the modify
                    if ($optimizeImageRepository->getModifyTime() === $fileTime) {
                        return true;
                    }
                    break;
                case ImageCheckMode::CHECK_MD5:
                    $fileHash = hash_file('md5', $absolutePath);

                    // compare the MD5 hash
                    if ($optimizeImageRepository->getMD5() === $fileHash) {
                        return true;
                    }
                    break;
                case ImageCheckMode::CHECK_CRC32:
                    $fileSum = hash_file('crc32', $absolutePath);

                    // compare the CRC32 sum
                    if ($optimizeImageRepository->getCRC32() === $fileSum) {
                        return true;
                    }
                    break;
            }
        }
        //</editor-fold>

        try {
            self::$instance->optimize($absolutePath);
        } catch (Exception $exception) {
            if ($this->configHelper->isLoggingEnable()) {
                $this->logger->error($exception->getMessage());
            }
            return false;
        }

        // Dont need to continue
        if ($this->configHelper->getCheckMode() === ImageCheckMode::CHECK_DISABLED) {
            return true;
        }

        //<editor-fold desc="Store file information in the database">
        // if cant find the entry for the file in the database create a new one
        if (is_null($optimizeImageRepository)) {
            $relativeFilePath = str_replace(self::$magentoRootPath, '', $absolutePath);
            $optimizeImageRepository = $this->optimizeImageRepository->create();
            $optimizeImageRepository->setHashedPath($imagePathHash);
            $optimizeImageRepository->setPath($relativeFilePath);
        }

        switch ($this->configHelper->getCheckMode()) {
            case ImageCheckMode::CHECK_MODIFIED_TIME:
                $fileTime = filemtime($absolutePath);
                $optimizeImageRepository->setModifyTime($fileTime);
                break;
            case ImageCheckMode::CHECK_MD5:
                $fileHash = hash_file('md5', $absolutePath);
                $optimizeImageRepository->setMD5($fileHash);
                break;
            case ImageCheckMode::CHECK_CRC32:
                $fileSum = hash_file('crc32', $absolutePath);
                $optimizeImageRepository->setCRC32($fileSum);
                break;
        }

        // insert or update
        $this->optimizeImageRepository->save($optimizeImageRepository);
        //</editor-fold>

        if ($this->configHelper->isLoggingEnable()) {
            $this->logger->info('Image ' . $absolutePath . ' optimize');
        }

        return true;
    }

    /**
     * Get image id from $absolutePath
     *
     * @param string $absolutePath Absolute file path to image
     * @return string
     */
    public function getImageIdFromAbsolutePath($absolutePath)
    {
        $relativeFilePath = str_replace(self::$magentoRootPath, '', $absolutePath);
        $hashedFilePath = md5($relativeFilePath);

        return $hashedFilePath;
    }

    /**
     * Delete a image from database by $imagePath
     *
     * @param $absolutePath string Absolute file path to image
     * @return bool
     */
    public function delete($absolutePath)
    {
        $imageId = $this->getImageIdFromAbsolutePath($absolutePath);

        // Try to load image information by $imageId
        $optimizeImageRepository = $this->optimizeImageRepository->getByFilePathHash($imageId);

        if (!is_null($optimizeImageRepository)) {
            $this->optimizeImageRepository->delete($optimizeImageRepository);
            return true;
        }

        return false;
    }
}