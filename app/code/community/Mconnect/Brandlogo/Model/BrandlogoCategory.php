<?php

class Mconnect_Brandlogo_Model_BrandlogoCategory extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('brandlogo/brandlogo_category');
    }
	
	public function getCollection() {
        return Mage::getResourceModel('brandlogo/category_collection');
    }
}