<?php
/**
 * Copyright (c) 2018 Magenerds and TechDivision GmbH
 * All rights reserved
 *
 * This product includes proprietary software developed at Magenerds, Germany
 * For more information see http://www.magenerds.com/
 *
 * To obtain a valid license for using this software please contact us at
 * info@magenerds.com
 */

namespace Magenerds\OptimizeImage\Api\Service;

/**
 * @copyright  Copyright (c) 2018 Magenerds and TechDivision GmbH (http://www.magenerds.com)
 * @link       http://www.magenerds.com/
 * @author     Mahmood Dhia <m.dhia@techdivision.com>
 */
interface OptimizeImageServiceInterface
{
    /**
     * Optimize Image
     *
     * @param string $absolutePath Absolute file path to image
     * @return bool
     */
    public function optimize($absolutePath);
}