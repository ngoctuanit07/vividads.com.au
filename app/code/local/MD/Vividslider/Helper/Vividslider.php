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


class MD_Vividslider_Model_Vividslider extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('vividslider/vividslider');
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
        $collection = Mage::getModel('vividslider/vividslider')->getCollection()
                ->addStoreFilter($store_id)
                ->addCategoryFilter();
        $brandLogos = $collection->getData();
        return $brandLogos;
    }

}