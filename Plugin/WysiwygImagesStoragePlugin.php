<?php
/**
 * @category   Magenerds
 * @package    Magenerds_OptimizeMedia
 * @author     Mahmood Dhia <m.dhia@techdivision.com>
 * @copyright  Copyright (c) 2018 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 */

namespace Magenerds\OptimizeMedia\Plugin;

use Magenerds\OptimizeMedia\Helper\Config as ConfigHelper;
use Magenerds\OptimizeMedia\Helper\OptimizeImage as OptimizeImageHelper;
use Magenerds\OptimizeMedia\Model\ResourceModel\OptimizeImage\Collection as OptimizeImageCollection;
use Magento\Cms\Model\Wysiwyg\Images\Storage as MageStorage;

class WysiwygImagesStoragePlugin
{
    /**
     * Config helper
     *
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * Config helper
     *
     * @var OptimizeImageHelper
     */
    protected $optimizeImageHelper;

    /**
     * Optimize image collection
     *
     * @var OptimizeImageCollection
     */
    protected $optimizeImageCollection;

    /**
     * WysiwygImagesStoragePlugin constructor.
     *
     * @param ConfigHelper $config
     * @param OptimizeImageHelper $optimizeImage
     * @param OptimizeImageCollection $collection
     */
    public function __construct(ConfigHelper $config, OptimizeImageHelper $optimizeImage, OptimizeImageCollection $collection)
    {
        $this->configHelper = $config;
        $this->optimizeImageHelper = $optimizeImage;
        $this->optimizeImageCollection = $collection;
    }

    /**
     * Upload and resize new file
     *
     * @param MageStorage $subject Storage subject
     * @param callable $proceed File info Array
     * @param string $targetPath Target directory
     * @param string $type Type of storage, e.g. image, media etc.
     * @return array File info Array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundUploadFile(MageStorage $subject, callable $proceed, $targetPath, $type = null)
    {
        $result = $proceed($targetPath, $type);

        if ($this->configHelper->isOptimizeWysiwygImagesEnable()) {
            $this->optimizeImageHelper->optimize($result['path'] . DIRECTORY_SEPARATOR . $result['file']);
        }

        return $result;
    }

    /**
     * Delete file (and its thumbnail if exists) from storage
     *
     * @param MageStorage $subject Storage subject
     * @param callable $proceed File info Array
     * @param string $target File path to be deleted
     * @return $this
     */
    public function aroundDeleteFile(MageStorage $subject, callable $proceed, $target)
    {
        $result = $proceed($target);
        $this->optimizeImageHelper->delete($target);

        return $result;
    }
}