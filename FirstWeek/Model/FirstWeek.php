<?php

namespace Dyniel\FirstWeek\Model;

use Magento\Customer\Model\Session;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Dyniel\FirstWeek\Model\FirstWeekInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Block\ArgumentInterface as ImageHelper;

class FirstWeek implements FirstWeekInterface
{
    /**
     * @var Customer Session
     */
    protected $customerSession;

    /**
     * @var Product CollectionFactory
     */
    protected $productCollectionFactory;
    
    /**
     * @var Order CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var Order RepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Order ItemRepository
     */
    protected $orderItemRepository;

    /**
     * @var Order IDs
     */
    protected $orderIds;

    /**
     * @var SearchCriteria
     */
    protected $searchCriteriaBuilder;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    public function __construct(
        Session $customerSession,
        ProductCollectionFactory $productCollectionFactory,
        OrderCollectionFactory $orderCollectionFactory,
        OrderItemRepositoryInterface $orderItemRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ImageHelper $imageHelper
    ) {
        $this->customerSession = $customerSession;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->imageHelper = $imageHelper;
    }

    /**
     * Get customer ID.
     *
     * @return array|null
     */
    public function getCustomerId()
    {
        return $this->customerSession->getId();
    }
    
    /**
     * Get Order Collection
     *
     * @return array|null
     */
    public function getOrderIds()
    {
        $orderIds = [];
        $startDate = date('Y-m-d', strtotime('-30 days'));
        $endDate = date('Y-m-d');
        $collection = $this->orderCollectionFactory->create();
        if (empty($orderIds)) {
            $collection->addAttributeToSelect('entity_id');
            $collection->addAttributeToFilter('customer_id', ['eq' => $this->getCustomerId()]);
            $collection->addAttributeToFilter('created_at', ['from' => $startDate, 'to' => $endDate]);
            $collection->getSelect()->distinct(true);
            $collection->getSelect()->order('entity_id', \Magento\Framework\DB\Select::SQL_DESC);
            foreach ($collection as $product) {
                array_push($orderIds, $product->getData());
            }
        }
        return $orderIds;
    }
    
    /**
     * Get Order Item
     *
     * @return Collection
     */
    public function getOrderItems($orderId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('order_id', $orderId)
            ->create();

        $orderItems = $this->orderItemRepository->getList($searchCriteria)->getItems();

        return $orderItems;
    }

    /**
     * Get All Order Items
     *
     * @return Array
     */
    public function getItems()
    {
        $items = [];
        $orderIds = $this->getOrderIds();
        foreach ($orderIds as $orderId) {
            $items = $this->getFilteredItemData($orderId, $items);
        }

        return $items;
    }
    
    /**
     * Get Filtered Order Item Data
     *
     * @return Array
     */
    public function getFilteredItemData($orderId, $items = [])
    {
        $orderedItems = $this->getOrderItems($orderId);
        foreach ($orderedItems as $orderedItem) {
            $item = $orderedItem->getProduct();
            $itemId = $item->getId();
            if (!array_key_exists($itemId, $items)) {
                $items[$itemId] =  [
                    'product_id' => $item->getId(),
                    'name' => $item->getName(),
                    'price' => $item->getPrice(),
                    'url_key' => $item->getUrlKey(),
                    'thumbnail'=> $this->getCachedImageUrl(
                        $item,
                        'product_thumbnail_image',
                        $item->getThumbnailImage()
                    ),
                    'order_ids' => [
                        $orderedItem->getOrderId()
                    ]
                    ];
            } else {
                array_push($items[$itemId]['order_ids'], $orderedItem->getOrderId());
            }
        }
        return $items;
    }

     /**
      * Gets cached image URL of a product
      *
      * @param string
      * @param Product
      * @return string
      */
    public function getCachedImageUrl($item, $type, $image_url)
    {
        return $this->imageHelper->init($item, $type)
        ->setImageFile($image_url)
        ->getUrl();
    }
}
