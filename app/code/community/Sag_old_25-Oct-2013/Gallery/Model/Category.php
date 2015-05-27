<?php

class Sag_Gallery_Model_Category extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('gallery/category');
    }
	
	public function getCollection() {
        return Mage::getResourceModel('gallery/category_collection');
    }
}