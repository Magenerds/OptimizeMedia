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

namespace Magenerds\OptimizeMedia\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OptimizeImageSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return OptimizeImageInterface[]
     */
    public function getItems();

    /**
     * @param OptimizeImageInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}