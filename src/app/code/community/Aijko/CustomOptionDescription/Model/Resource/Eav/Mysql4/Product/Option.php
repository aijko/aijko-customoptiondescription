<?php
/**
 * Catalog product custom option resource model
 *
 * @category   Aijko
 * @package    Aijko_CustomOptionDescription
 * @author     Gerrit Pechmann <gp@aijko.de>
 * @copyright  Copyright (c) 2012, aijko GmbH (http://www.aijko.de)
 */
class Aijko_CustomOptionDescription_Model_Resource_Eav_Mysql4_Product_Option extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option
{
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $descriptionTable = $this->getTable('customoptiondescription/product_option_description');
        
        if (!$object->getData('scope', 'description')) {
            $statement = $this->_getReadAdapter()->select()
                ->from($descriptionTable)
                ->where('option_id = '.$object->getId().' and store_id = ?', 0);

            if ($this->_getReadAdapter()->fetchOne($statement)) {
                if ($object->getStoreId() == '0') {
                    $this->_getWriteAdapter()->update(
                        $descriptionTable,
                            array('description' => $object->getDescription()),
                            $this->_getWriteAdapter()->quoteInto('option_id='.$object->getId().' AND store_id=?', 0)
                    );
                }
            } else {
                $this->_getWriteAdapter()->insert(
                    $descriptionTable,
                        array(
                            'option_id' => $object->getId(),
                            'store_id' => 0,
                            'description' => $object->getDescription()
                ));
            }
        }

        if ($object->getStoreId() != '0' && !$object->getData('scope', 'description')) {
            $statement = $this->_getReadAdapter()->select()
                ->from($descriptionTable)
                ->where('option_id = '.$object->getId().' and store_id = ?', $object->getStoreId());

            if ($this->_getReadAdapter()->fetchOne($statement)) {
                $this->_getWriteAdapter()->update(
                    $descriptionTable,
                        array('description' => $object->getDescription()),
                        $this->_getWriteAdapter()
                            ->quoteInto('option_id='.$object->getId().' AND store_id=?', $object->getStoreId()));
            } else {
                $this->_getWriteAdapter()->insert(
                    $descriptionTable,
                        array(
                            'option_id' => $object->getId(),
                            'store_id' => $object->getStoreId(),
                            'description' => $object->getDescription()
                ));
            }
        } elseif ($object->getData('scope', 'description')) {
            $this->_getWriteAdapter()->delete(
                $descriptionTable,
                $this->_getWriteAdapter()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
            );
        }

        return parent::_afterSave($object);
    }
    
    /**
     * Duplicate custom options for product
     *
     * @param Mage_Catalog_Model_Product_Option $object
     * @param int $oldProductId
     * @param int $newProductId
     * @return Mage_Catalog_Model_Product_Option
     */
    public function duplicate(Mage_Catalog_Model_Product_Option $object, $oldProductId, $newProductId)
    {
        $write  = $this->_getWriteAdapter();
        $read   = $this->_getReadAdapter();

        $optionsCond = array();
        $optionsData = array();

        // read and prepare original product options
        $select = $read->select()
            ->from($this->getTable('catalog/product_option'))
            ->where('product_id=?', $oldProductId);
        $query = $read->query($select);
        while ($row = $query->fetch()) {
            $optionsData[$row['option_id']] = $row;
            $optionsData[$row['option_id']]['product_id'] = $newProductId;
            unset($optionsData[$row['option_id']]['option_id']);
        }

        // insert options to duplicated product
        foreach ($optionsData as $oId => $data) {
            $write->insert($this->getMainTable(), $data);
            $optionsCond[$oId] = $write->lastInsertId();
        }

        // copy options prefs
        foreach ($optionsCond as $oldOptionId => $newOptionId) {
            // title
            $table = $this->getTable('catalog/product_option_title');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newOptionId . ', `store_id`, `title`'
                . 'FROM `' . $table . '` WHERE `option_id`=' . $oldOptionId;
            $this->_getWriteAdapter()->query($sql);

            // price
            $table = $this->getTable('catalog/product_option_price');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newOptionId . ', `store_id`, `price`, `price_type`'
                . 'FROM `' . $table . '` WHERE `option_id`=' . $oldOptionId;
            $this->_getWriteAdapter()->query($sql);
            
            // description
            $table = $this->getTable('customoptiondescription/product_option_description');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newOptionId . ', `store_id`, `description`'
                . 'FROM `' . $table . '` WHERE `option_id`=' . $oldOptionId;
            $this->_getWriteAdapter()->query($sql);

            $object->getValueInstance()->duplicate($oldOptionId, $newOptionId);
        }

        return $object;
    }
}