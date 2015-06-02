<?php

class Sag_Gallery_Model_Mysql4_Category extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the gallery_id refers to the key field in your database table.
        $this->_init('gallery/category', 'category_id');
    }
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        $condition = $this->_getWriteAdapter()->quoteInto('category_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('category_store'), $condition);

        if (!$object->getData('stores')) {
            $storeArray = array();
            $storeArray['category_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('category_store'), $storeArray);
        } else {
            foreach ((array) $object->getData('stores') as $store) {
                $storeArray = array();
                $storeArray['category_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->_getWriteAdapter()->insert($this->getTable('category_store'), $storeArray);
            }
        }

        return parent::_afterSave($object);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object) {

        // Cleanup stats on blog delete
        $adapter = $this->_getReadAdapter();
        // 1. Delete testimonial/store
        $adapter->delete($this->getTable('gallery/category_store'), 'category_id=' . $object->getId());
    }
    public function getBrandlogoStore($id) {
        $arr = $this->getTable('category_store', 'category_id=1');
        return "saflajdfklda";
    }
}