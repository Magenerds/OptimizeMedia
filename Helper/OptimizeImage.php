<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;

class OptimizeImage extends AbstractHelper
{
    /**
     * Table name
     */
    const TABLENAME = 'magenerds_optimizemedia_image';
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
     * Contains the magento root path
     *
     * @var string
     */
    private $magentoRootPath = '';
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Optimizer instance
     *
     * @var Optimizer
     */
    private $optimizer = null;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * OptimizeImage constructor.
     *
     * @param Context $context
     * @param ConfigHelper $configHelper
     * @param LoggerInterface $logger
     * @param OptimizerFactory $optimizerFactory
     * @param OptimizeImageRepository $optimizeImageRepository
     */
    public function __construct(
        Context $context,
        ConfigHelper $configHelper,
        DirectoryList $directoryList,
        LoggerInterface $logger,
        OptimizerFactory $optimizerFactory,
        OptimizeImageRepository $optimizeImageRepository
    )
    {
        $this->configHelper = $configHelper;
        $this->directoryList = $directoryList;
        $this->logger = $logger;
        $this->optimizeImageRepository = $optimizeImageRepository;

        // Initialize ImageOptimizer
        if ($this->configHelper->isModuleEnabled() && $this->configHelper->isOptimizeWysiwygImagesEnabled()) {
            $this->initializeImageOptimizer($optimizerFactory);
        }

        parent::__construct($context);
    }

    /**
     * Initialize ImageOptimizer
     */
    private function initializeImageOptimizer(OptimizerFactory $optimizerFactory)
    {
        // Get Magento root directory
        $this->magentoRootPath = $this->directoryList->getRoot() . DIRECTORY_SEPARATOR;

        //<editor-fold desc="Initialize optimizer class">
        /** @var OptimizerFactory $optimizerFactory */
        $this->optimizer = $optimizerFactory->get();

        if ($this->configHelper->isLoggingEnabled()) {
            if (!is_null($this->optimizer)) {
                $this->logger->info('ImageOptimizer initialized');
            } else {
                $this->logger->error('ImageOptimizer cant initialized');
            }
        }
        //</editor-fold>
    }

    /**
     * Optimize Image
     *
     * @param string $absolutePath Absolute file path to image
     * @return bool
     * @throws NoSuchEntityException
     */
    public function optimize($absolutePath)
    {
        // check ImageOptimizer is initialized
        if (is_null($this->optimizer)) {
            return false;
        }

        // check file exists
        if (!is_file($absolutePath)) {
            if ($this->configHelper->isLoggingEnabled()) {
                $this->logger->error('Image in ' . $absolutePath . ' not found');
            }
            return false;
        }

        // get hashed file path
        $imagePathHash = $this->getImageIdFromAbsolutePath($absolutePath);

        // Try to load image information by SearchCriteria
        $optimizeImageRepository = $this->optimizeImageRepository->getByFilePathHash($imagePathHash);


        //<editor-fold desc="Compare with database file information">
        if (!is_null($optimizeImageRepository)) {
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
            $this->optimizer->optimize($absolutePath);
        } catch (Exception $exception) {
            if ($this->configHelper->isLoggingEnabled()) {
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
            $relativeFilePath = str_replace($this->magentoRootPath, '', $absolutePath);
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

        if ($this->configHelper->isLoggingEnabled()) {
            $this->logger->info('Image ' . $absolutePath . ' has been optimized successfully');
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
        $relativeFilePath = str_replace($this->magentoRootPath, '', $absolutePath);
        $hashedFilePath = md5($relativeFilePath);

        return $hashedFilePath;
    }

    /**
     * Delete a image from database by $imagePath
     *
     * @param $absolutePath string Absolute file path to image
     * @return bool
     * @throws NoSuchEntityException
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