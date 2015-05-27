<?php

class AsiaConnect_Gallery_Model_Gallery extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('gallery/gallery');
    }
	public function getTotalRate(){
		$collection = Mage::getModel('gallery/review')
			->getCollection()
			->addFieldToFilter('gallery_id', $this->getId())
			->addFieldToFilter('status',2);
		return $collection->getSize();
	}
}