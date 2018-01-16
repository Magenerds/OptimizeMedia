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

namespace Magenerds\OptimizeMedia\Model\ResourceModel\OptimizeImage;

use Magenerds\OptimizeMedia\Model\OptimizeImage as OptimizeImageModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = OptimizeImageModel::ID;
    protected $_eventPrefix = 'magenerds_optimizemedia_image_collection';
    protected $_eventObject = 'image_collection';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Magenerds\OptimizeMedia\Model\OptimizeImage',
            'Magenerds\OptimizeMedia\Model\ResourceModel\OptimizeImage'
        );
    }
}