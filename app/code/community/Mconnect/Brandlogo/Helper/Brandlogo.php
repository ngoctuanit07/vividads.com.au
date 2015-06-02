<?php

class Mconnect_Brandlogo_Model_Brandlogo extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('brandlogo/brandlogo');
    }

    public function getAllLogos() {
        $store_id = 0;
        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) { // store level
            $store_id = Mage::getModel('core/store')->load($code)->getId();
        } elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) { // website level
            $website_id = Mage::getModel('core/website')->load($code)->getId();
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        } else { // default level
            $store_id = 0;
        }
        $collection = Mage::getModel('brandlogo/brandlogo')->getCollection()
                ->addStoreFilter($store_id)
                ->addCategoryFilter();
        $brandLogos = $collection->getData();
        return $brandLogos;
    }

}