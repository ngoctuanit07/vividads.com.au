<?php

class AsiaConnect_Gallery_Model_Album extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('gallery/album');
    }
    public function getChildrenAlbum()
    {
    	$collection = Mage::getModel('gallery/album')->getCollection()
    												 ->addFieldToFilter('parent_id',array('eq'=>$this->getId()))
    												 ->addFieldToFilter('status',array('eq'=>1))
    												 ->addOrder('main_table.order','ASC');
    	if($collection->getSize())
    		return $collection;
    	return false;
    }
	
	public function getChildrenAlbumInStore()
    {
    	$collection = Mage::getModel('gallery/album')->getCollection()
    												 ->addFieldToFilter('parent_id',array('eq'=>$this->getId()))
    												 ->addFieldToFilter('status',array('eq'=>1))
    												 ->addOrder('main_table.order','ASC');
		$collection->getSelect()->join(
						array('store'=>$collection->getTable('gallery_store')),
						'store.album_id = main_table.album_id',
						array('store_in_table'=>'store.store_id')
						)
						->where('store.store_id in (?)',array('0',Mage::app()->getStore()->getStoreId()))
						;
    	if($collection->getSize())
    		return $collection;
    	return false;
    }
	
	
    public function getParentAlbum()
    {
    	if($this->getParentId()) return Mage::getModel('gallery/album')->load($this->getParentId());
    	return false;
    }
    
    public function getUrlRewrite()
    {
    	$rewrite = Mage::getModel('core/url_rewrite');
    	$rewrite->load($this->getUrlRewriteId());
    	return trim(Mage::getBaseUrl(),'/')."/".$rewrite->getRequestPath();
    }
}