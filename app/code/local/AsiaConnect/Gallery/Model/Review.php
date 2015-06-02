<?php

class AsiaConnect_Gallery_Model_Review extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('gallery/review');
    }
	public static function _getRate(AsiaConnect_Gallery_Model_Gallery $gallery){
		$collection = Mage::getModel('gallery/review')
			->getCollection()
			->addFieldToFilter('gallery_id', $gallery->getId())
			->addFieldToFilter('status',AsiaConnect_Gallery_Model_Review_Status::STATUS_APPROVED);
		$sum = 0;
		foreach($collection as $review)
		{
			$sum += (int) $review->getRate();
		}
		if(sizeof($collection)) return round(($sum/sizeof($collection)),0);
		return 0;
	}
}
