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

namespace Magenerds\OptimizeMedia\Model;

use Magenerds\OptimizeMedia\Api\Data\OptimizeImageInterface;
use Magenerds\OptimizeMedia\Api\Data\OptimizeImageSearchResultInterfaceFactory;
use Magenerds\OptimizeMedia\Api\OptimizeImageRepositoryInterface;
use Magenerds\OptimizeMedia\Model\OptimizeImage as OptimizeImageModel;
use Magenerds\OptimizeMedia\Model\ResourceModel\OptimizeImage\Collection;
use Magenerds\OptimizeMedia\Model\ResourceModel\OptimizeImage\CollectionFactory as OptimizeImageCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;


class OptimizeImageRepository implements OptimizeImageRepositoryInterface
{
    /**
     * @var OptimizeImageFactory
     */
    private $optimizeImageFactory;

    /**
     * @var OptimizeImageCollectionFactory
     */
    private $optimizeImageCollectionFactory;

    /**
     * @var OptimizeImageSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * OptimizeImageRepository constructor.
     * @param OptimizeImageFactory $optimizeImageFactory
     * @param OptimizeImageCollectionFactory $optimizeImageCollectionFactory
     * @param OptimizeImageSearchResultInterfaceFactory $optimizeImageSearchResultInterfaceFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OptimizeImageFactory $optimizeImageFactory,
        OptimizeImageCollectionFactory $optimizeImageCollectionFactory,
        OptimizeImageSearchResultInterfaceFactory $optimizeImageSearchResultInterfaceFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->optimizeImageFactory = $optimizeImageFactory;
        $this->optimizeImageCollectionFactory = $optimizeImageCollectionFactory;
        $this->searchResultFactory = $optimizeImageSearchResultInterfaceFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param int $id
     * @return OptimizeImageInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        $optimizeImage = $this->optimizeImageFactory->create();
        $optimizeImage->getResource()->load($optimizeImage, $id);

        if (!$optimizeImage->getId()) {
            $optimizeImage = null;
        }

        return $optimizeImage;
    }

    /**
     * @param string $imagePathHash
     * @return OptimizeImageInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByFilePathHash($imagePathHash)
    {
        // Try to load image information by SearchCriteria
        $this->searchCriteriaBuilder->addFilter(OptimizeImageModel::HASHED_PATH, $imagePathHash);
        $optimizeImageRepositoryList = $this->getList($this->searchCriteriaBuilder->create());

        if ($optimizeImageRepositoryList->getTotalCount() <= 0) {
            return null;
        }

        $t = array_values($optimizeImageRepositoryList->getItems());

        return array_shift($t);
    }

    /**
     * @return OptimizeImageInterface
     */
    public function create()
    {
        return $this->optimizeImageFactory->create();
    }

    /**
     * @param OptimizeImageInterface $optimizeImage
     * @return OptimizeImageInterface
     */
    public function save(OptimizeImageInterface $optimizeImage)
    {
        $optimizeImage->getResource()->save($optimizeImage);
        return $optimizeImage;
    }

    /**
     * @param OptimizeImageInterface $optimizeImage
     * @return OptimizeImageInterface
     */
    public function delete(OptimizeImageInterface $optimizeImage)
    {
        return $optimizeImage->getResource()->delete($optimizeImage);
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return OptimizeImageSearchResult
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->optimizeImageCollectionFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);

        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param OptimizeImageCollectionFactory $collection
     */
    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param OptimizeImageCollectionFactory $collection
     */
    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param OptimizeImageCollectionFactory $collection
     */
    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param OptimizeImageCollectionFactory $collection
     * @return mixed
     */
    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        $searchResults = $this->searchResultFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}