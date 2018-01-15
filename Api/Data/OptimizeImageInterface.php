<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 *
 * @category   Magenerds
 * @package    Magenerds_OptimizeMedia
 * @author     Mahmood Dhia <m.dhia@techdivision.com>
 * @copyright  Copyright (c) 2018 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 */

namespace Magenerds\OptimizeMedia\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface OptimizeImageInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     * @return void
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getHashedPath();

    /**
     * @param string $hashedPath
     * @return void
     */
    public function setHashedPath($hashedPath);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     * @return void
     */
    public function setPath($path);

    /**
     * @return int
     */
    public function getModifyTime();

    /**
     * @param int $modifyTime
     * @return void
     */
    public function setModifyTime($modifyTime);

    /**
     * @return string
     */
    public function getMD5();

    /**
     * @param string $md5
     * @return void
     */
    public function setMD5($md5);

    /**
     * @return string
     */
    public function getCRC32();

    /**
     * @param string $crc32
     * @return void
     */
    public function setCRC32($crc32);


}