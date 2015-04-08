<?php

/**
 * MD_Vividslider.
 * Company: Vivid Ads Inc Australia
 * Author: AShfaq Ahmed
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.vividads.com.au
 *
 * @category   MD
 * @package    Vividslider
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */


class MD_Vividslider_Model_Mysql4_Vividslider extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the vividslider_id refers to the key field in your database table.
        $this->_init('vividslider/vividslider', 'vividslider_id');
    }
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        $condition = $this->_getWriteAdapter()->quoteInto('vividslider_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('vividslider_store'), $condition);

        if (!$object->getData('stores')) {
            $storeArray = array();
            $storeArray['vividslider_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('vividslider_store'), $storeArray);
        } else {
            foreach ((array) $object->getData('stores') as $store) {
                $storeArray = array();
                $storeArray['vividslider_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->_getWriteAdapter()->insert($this->getTable('vividslider_store'), $storeArray);
            }
        }

        return parent::_afterSave($object);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object) {

        // Cleanup stats on blog delete
        $adapter = $this->_getReadAdapter();
        // 1. Delete testimonial/store
        $adapter->delete($this->getTable('vividslider/vividslider_store'), 'vividslider_id=' . $object->getId());
    }
    
	
	public function getVividsliderStore($id) {
        $arr = $this->getTable('vividslider_store', 'vividslider_id=1');
        return "saflajdfklda";
    }
	
	
	
	
}