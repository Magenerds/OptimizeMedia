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

namespace Magenerds\OptimizeMedia\Service;

use Magenerds\OptimizeMedia\Api\Service\OptimizeImageServiceInterface;
use Magenerds\OptimizeMedia\Helper\OptimizeImage as OptimizeImageHelper;

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
    public function __construct(OptimizeImageHelper $optimizeImage)
    {
        $this->optimizeImage = $optimizeImage;
    }

    /**
     * {@inheritdoc}
     */
    public function optimize($absolutePath)
    {
        return $this->optimizeImage->optimize($absolutePath);
    }
}
