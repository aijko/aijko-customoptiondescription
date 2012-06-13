<?php
/**
 * Catalog product option model
 *
 * @category   Aijko
 * @package    Aijko_CustomOptionDescription
 * @author     Gerrit Pechmann <gp@aijko.de>
 * @copyright  Copyright (c) 2012, aijko GmbH (http://www.aijko.de)
 */
class Aijko_CustomOptionDescription_Model_Catalog_Product_Option extends Mage_Catalog_Model_Product_Option
{
    protected function _construct()
    {
        $this->_init('customoptiondescription/product_option');
    }
    
    /**
     * Get Product Option Collection
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return Aijko_CustomOptionDescription_Model_Resource_Eav_Mysql4_Product_Option_Collection
     */
    public function getProductOptionCollection(Mage_Catalog_Model_Product $product)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('product_id', $product->getId())
            ->addTitleToResult($product->getStoreId())
            ->addPriceToResult($product->getStoreId())
            ->addDescriptionToResult($product->getStoreId())
            ->setOrder('sort_order', 'asc')
            ->setOrder('title', 'asc')
            ->addValuesToResult($product->getStoreId());
        
        return $collection;
    }
}