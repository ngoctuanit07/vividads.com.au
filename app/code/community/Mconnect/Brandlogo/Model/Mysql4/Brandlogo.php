<?php

class Mconnect_Brandlogo_Model_Mysql4_Brandlogo extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the brandlogo_id refers to the key field in your database table.
        $this->_init('brandlogo/brandlogo', 'brandlogo_id');
    }
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        $condition = $this->_getWriteAdapter()->quoteInto('brandlogo_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('brandlogo_store'), $condition);

        if (!$object->getData('stores')) {
            $storeArray = array();
            $storeArray['brandlogo_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('brandlogo_store'), $storeArray);
        } else {
            foreach ((array) $object->getData('stores') as $store) {
                $storeArray = array();
                $storeArray['brandlogo_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->_getWriteAdapter()->insert($this->getTable('brandlogo_store'), $storeArray);
            }
        }

        return parent::_afterSave($object);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object) {

        // Cleanup stats on blog delete
        $adapter = $this->_getReadAdapter();
        // 1. Delete testimonial/store
        $adapter->delete($this->getTable('brandlogo/brandlogo_store'), 'brandlogo_id=' . $object->getId());
    }
    public function getBrandlogoStore($id) {
        $arr = $this->getTable('brandlogo_store', 'brandlogo_id=1');
        return "saflajdfklda";
    }
}