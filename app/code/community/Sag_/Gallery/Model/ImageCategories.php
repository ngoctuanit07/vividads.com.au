<?php

class Sag_Gallery_Model_ImageCategories extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('gallery/image_categories');
    }
	
	public function getCollection() {
        return Mage::getResourceModel('gallery/image_categories_collection');
    }
}