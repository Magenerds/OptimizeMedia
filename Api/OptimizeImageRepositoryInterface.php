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

namespace Magenerds\OptimizeMedia\Api;

use Magenerds\OptimizeMedia\Api\Data\OptimizeImageInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface OptimizeImageRepositoryInterface
{
    /**
     * @param int $id
     * @return \Magenerds\OptimizeMedia\Api\Data\OptimizeImageInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param \Magenerds\OptimizeMedia\Api\Data\OptimizeImageInterface $optimizeImage
     * @return \Magenerds\OptimizeMedia\Api\Data\OptimizeImageInterface
     */
    public function save(OptimizeImageInterface $optimizeImage);

    /**
     * @param \Magenerds\OptimizeMedia\Api\Data\OptimizeImageInterface $optimizeImage
     * @return void
     */
    public function delete(OptimizeImageInterface $optimizeImage);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magenerds\OptimizeMedia\Api\Data\OptimizeImageSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
