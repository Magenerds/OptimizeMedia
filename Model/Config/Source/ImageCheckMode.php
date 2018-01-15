<?php
/**
 * @category   Magenerds
 * @package    Magenerds_OptimizeMedia
 * @author     Mahmood Dhia <m.dhia@techdivision.com>
 * @copyright  Copyright (c) 2018 TechDivision GmbH (http://www.techdivision.com)
 * @link       http://www.techdivision.com/
 */

namespace Magenerds\OptimizeMedia\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ImageCheckMode implements ArrayInterface
{
    /**
     * Check for modified time
     */
    const CHECK_MODIFIED_TIME = 1;

    /**
     * Check for md5 hash
     */
    const CHECK_MD5 = 2;

    /**
     * Check for crc32 sum
     */
    const CHECK_CRC32 = 3;

    /**
     * Do not check
     */
    const CHECK_DISABLED = 4;

    /**
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }
        return $ret;
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        $choose = [
            self::CHECK_MODIFIED_TIME => __('Check file modified time'),
            self::CHECK_MD5 => __('Check file MD5 hash'),
            self::CHECK_CRC32 => __('Check file CRC32 checksum'),
            self::CHECK_DISABLED => __('Do not check')
        ];

        return $choose;
    }
}