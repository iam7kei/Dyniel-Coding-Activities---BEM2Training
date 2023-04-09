<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dyniel\FirstWeek\Block;

use Dyniel\FirstWeek\Model\FirstWeekInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Framework\App\Cache\Manager;

/**
 * Order item render block
 *
 * @api
 * @since 100.0.2
 */
class FirstWeek extends \Magento\Framework\View\Element\Template
{
    /**
     * Magento string lib
     *
     * @var StringUtils
     */
    protected $string;

    /**
     * @var Customer
     */
    protected $_customer;

    /**
     * @var Customer Session
     */
    protected $customerSession;
    
    /**
     * @var Customer ID
     */
    protected $customerId;
    
    /**
     * @var Product Collection Factory
     */
    protected $productCollectionFactory;

    /**
     * @var Order Collection Factory
     */
    protected $orderCollectionFactory;

    /**
     * @var Cache Manager
     */
    protected $cacheManager;

    /**
     * @var FirstWeek Model
     */
    protected $firstWeek;
    
    /**
     * @param Context $context
     * @param StringUtils $string
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ProductCollectionFactory $productCollectionFactory,
        OrderCollectionFactory $orderCollectionFactory,
        FirstWeekInterface $firstWeek,
        StringUtils $string,
        Manager $cacheManager,
        array $data = []
    ) {
        $this->string = $string;
        $this->customerSession = $customerSession;
        $this->customerId = $customerSession->getId();
        $this->productCollectionFactory = $productCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->firstWeek = $firstWeek;
        $this->cacheManager = $cacheManager;
        parent::__construct($context, $data);
    }

    public function getCacheLifetime()
    {
        return null;
    }


    /**
     * Get All Order Items
     *
     * @return array|null
     */
    public function getAllOrderItems()
    {
        return $this->firstWeek->getItems();
    }

    /**
     * Get customer ID.
     *
     * @return array|null
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}
