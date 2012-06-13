<?php
/**
 * Catalog product options collection
 *
 * @category   Aijko
 * @package    Aijko_CustomOptionDescription
 * @author     Gerrit Pechmann <gp@aijko.de>
 * @copyright  Copyright (c) 2012, aijko GmbH (http://www.aijko.de)
 */
class Aijko_CustomOptionDescription_Model_Resource_Eav_Mysql4_Product_Option_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Collection
{
    public function getOptions($store_id)
    {
        $this->getSelect()
            ->joinLeft(array('default_option_price'=>$this->getTable('catalog/product_option_price')),
                '`default_option_price`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`default_option_price`.store_id=?',0),
                array('default_price'=>'price','default_price_type'=>'price_type'))
            ->joinLeft(array('store_option_price'=>$this->getTable('catalog/product_option_price')),
                '`store_option_price`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`store_option_price`.store_id=?', $store_id),
                array('store_price'=>'price','store_price_type'=>'price_type',
                'price'=>new Zend_Db_Expr('IFNULL(`store_option_price`.price,`default_option_price`.price)'),
                'price_type'=>new Zend_Db_Expr('IFNULL(`store_option_price`.price_type,`default_option_price`.price_type)')))
            ->joinLeft(array('default_option_description'=>$this->getTable('customoptiondescription/product_option_description')),
                '`default_option_description`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`default_option_description`.store_id=?',0),
                array('default_description'=>'description'))
            ->joinLeft(array('store_option_description'=>$this->getTable('customoptiondescription/product_option_description')),
                '`store_option_description`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`store_option_description`.store_id=?', $store_id),
                array('store_description'=>'description',
                'description'=>new Zend_Db_Expr('IFNULL(`store_option_description`.description,`default_option_description`.description)')))
            ->join(array('default_option_title'=>$this->getTable('catalog/product_option_title')),
                '`default_option_title`.option_id=`main_table`.option_id',
                array('default_title'=>'title'))
            ->joinLeft(array('store_option_title'=>$this->getTable('catalog/product_option_title')),
                '`store_option_title`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`store_option_title`.store_id=?', $store_id),
                array('store_title'=>'title',
                'title'=>new Zend_Db_Expr('IFNULL(`store_option_title`.title,`default_option_title`.title)')))
            ->where('`default_option_title`.store_id=?', 0);

        return $this;
    }
    
    public function addDescriptionToResult($store_id)
    {
        $this->getSelect()
            ->joinLeft(array('default_option_description'=>$this->getTable('customoptiondescription/product_option_description')),
                '`default_option_description`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`default_option_description`.store_id=?',0),
                array('default_description'=>'description'))
            ->joinLeft(array('store_option_description'=>$this->getTable('customoptiondescription/product_option_description')),
                '`store_option_description`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`store_option_description`.store_id=?', $store_id),
                array('store_description'=>'description',
                'description'=>new Zend_Db_Expr('IFNULL(`store_option_description`.description,`default_option_description`.description)')));
        return $this;
    }

}