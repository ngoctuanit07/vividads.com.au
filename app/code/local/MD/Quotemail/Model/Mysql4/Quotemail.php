<?php

/**
 * MD_Quotemail.
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
 * @package    Quotemail
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */


class MD_Quotemail_Model_Mysql4_Quotemail extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the quotemail_id refers to the key field in your database table.
        $this->_init('quotemail/quotemail', 'quotemail_id');
    }
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        $condition = $this->_getWriteAdapter()->quoteInto('quotemail_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('quotemail_store'), $condition);

        if (!$object->getData('stores')) {
            $storeArray = array();
            $storeArray['quotemail_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('quotemail_store'), $storeArray);
        } else {
            foreach ((array) $object->getData('stores') as $store) {
                $storeArray = array();
                $storeArray['quotemail_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->_getWriteAdapter()->insert($this->getTable('quotemail_store'), $storeArray);
            }
        }

        return parent::_afterSave($object);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object) {

        // Cleanup stats on blog delete
        $adapter = $this->_getReadAdapter();
        // 1. Delete testimonial/store
        $adapter->delete($this->getTable('quotemail/quotemail_store'), 'quotemail_id=' . $object->getId());
    }
    
	
	public function getQuotemailStore($id) {
        $arr = $this->getTable('quotemail_store', 'quotemail_id=1');
        return "saflajdfklda";
    }
	
	
	
	
}