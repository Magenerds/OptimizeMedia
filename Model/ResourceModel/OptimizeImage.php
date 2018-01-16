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

namespace Magenerds\OptimizeMedia\Model\ResourceModel;

use Magenerds\OptimizeMedia\Helper\OptimizeImage as OptimizeImageHelper;
use Magenerds\OptimizeMedia\Model\OptimizeImage as OptimizeImageModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class OptimizeImage extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init(OptimizeImageHelper::TABLENAME, OptimizeImageModel::ID);
    }
}
