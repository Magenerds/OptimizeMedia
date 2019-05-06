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
 * @copyright  Copyright (c) 2019 TechDivision GmbH (https://www.techdivision.com)
 * @link       https://www.techdivision.com/
 */

namespace Magenerds\OptimizeMedia\Model;

use Magenerds\OptimizeMedia\Api\Data\OptimizeImageInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class OptimizeImage extends AbstractExtensibleModel implements OptimizeImageInterface
{
    const ID = 'image_id';
    const PATH = 'image_path';
    const HASHED_PATH = 'image_hashed_path';
    const MODIFY_TIME = 'image_modify_time';
    const MD5 = 'image_md5';
    const CRC32 = 'image_crc32';

    /**
     * @return string
     */
    public function getHashedPath()
    {
        return $this->_getData(self::HASHED_PATH);
    }

    /**
     * @param string $hashedPath
     * @return void
     */
    public function setHashedPath($hashedPath)
    {
        $this->setData(self::HASHED_PATH, $hashedPath);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->_getData(self::PATH);
    }

    /**
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->setData(self::PATH, $path);
    }

    /**
     * @return int
     */
    public function getModifyTime()
    {
        return $this->_getData(self::MODIFY_TIME);
    }

    /**
     * @param int $modifyTime
     * @return void
     */
    public function setModifyTime($modifyTime)
    {
        $this->setData(self::MODIFY_TIME, $modifyTime);
    }

    /**
     * @return string
     */
    public function getMD5()
    {
        return $this->_getData(self::MD5);
    }

    /**
     * @param string $md5
     * @return void
     */
    public function setMD5($md5)
    {
        $this->setData(self::MD5, $md5);
    }

    /**
     * @return string
     */
    public function getCRC32()
    {
        return $this->_getData(self::CRC32);
    }

    /**
     * @param string $crc32
     * @return void
     */
    public function setCRC32($crc32)
    {
        $this->setData(self::CRC32, $crc32);
    }

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\OptimizeImage::class);
    }
}
