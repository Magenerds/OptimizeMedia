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

namespace Magenerds\OptimizeImage\Service;

use Magenerds\OptimizeMedia\Helper\OptimizeImage as OptimizeImageHelper;
use Magenerds\OptimizeImage\Api\Service\OptimizeImageServiceInterface;

/**
 * @copyright  Copyright (c) 2018 Magenerds and TechDivision GmbH (http://www.magenerds.com)
 * @link       http://www.magenerds.com/
 * @author     Mahmood Dhia <m.dhia@techdivision.com>
 */
class OptimizeImageService implements OptimizeImageServiceInterface
{
    /**
     * @var OptimizeImageHelper
     */
    private $optimizeImage;

    /**
     * CrawlService constructor.
     * @param OptimizeImageHelper $optimizeImage
     */
    public function __construct(OptimizeImageHelper $optimizeImage){
        $this->optimizeImage = $optimizeImage;
    }

    /**
     * Optimize Image
     *
     * @param string $absolutePath Absolute file path to image
     * @return bool
     */
    public function optimize($absolutePath)
    {
        return $this->optimize($absolutePath);
    }
}