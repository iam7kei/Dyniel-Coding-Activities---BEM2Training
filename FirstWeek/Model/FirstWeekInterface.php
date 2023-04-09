<?php

namespace Dyniel\FirstWeek\Model;

interface FirstWeekInterface
{
    
    /**
     * Get customer ID.
     *
     * @return array|null
     */
    public function getCustomerId();
        
    /**
     * Get Order Collection
     *
     * @return Collection
     */
    public function getOrderIds();

    /**
     * Get Order Item
     * 
     * @param OrderId
     * @return Collection
     */
    public function getOrderItems($orderId);
    
    /**
     * Get Order Item
     *
     * @return Collection
     */
    public function getItems();

    /**
     * Get Filtered Item Data
     *
     * @param array|null
     * @param array\null
     * @return Collection
     */
    public function getFilteredItemData($orderId, $items = []);

    /**
     * Gets cached image URL
     * @param Product
     * @param string
     * @return string
     */
    public function getCachedImageUrl($item, $type, $image_url);
}
