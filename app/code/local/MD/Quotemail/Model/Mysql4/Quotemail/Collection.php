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

class MD_Quotemail_Model_Mysql4_Quotemail_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('quotemail/quotemail');
    }

    public function addEnableFilter($status) {
        $this->getSelect()
                ->where('status = ?', $status);
        return $this;
    }

    public function addStoreFilter($store) {
        if (!Mage::app()->isSingleStoreMode()) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }
            $this->getSelect()->join(
                            array('store_table' => $this->getTable('quotemail_store')), 'main_table.quotemail_id = store_table.quotemail_id', array()
                    )
                    ->where('store_table.store_id in (?)', array(0, $store));

            return $this;
        }
        return $this;
    }
    
    public function addCategoryFilter() {
        if (!Mage::app()->isSingleStoreMode()) {
            $this->getSelect()->join(
                            array('category_table' => $this->getTable('quotemail_category')), 'main_table.category_id = category_table.category_id', array()
                    );
            return $this;
        }
        return $this;
    }

}