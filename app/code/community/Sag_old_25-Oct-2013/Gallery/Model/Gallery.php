<?php

class Sag_Gallery_Model_Gallery extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('gallery/gallery');
    }
	
	public function getCollection() {
        return Mage::getResourceModel('gallery/gallery_collection');
    }
}