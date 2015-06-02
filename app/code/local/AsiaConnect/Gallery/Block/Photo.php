<?php
class AsiaConnect_Gallery_Block_Photo extends Mage_Core_Block_Template
{
	public function _getStoreId()
    {
        $store_id = $this->helper("core")->getStoreId(); 
        return $store_id;
    }
	public function _getStore()
    {
        $store_id = $this->helper("core")->getStoreId(); 
        return Mage::app()->getStore($store_id);
    }

    public function getNextPhoto()
    {
    	return Mage::registry('next_photo');
    }
    
    public function getPreviousPhoto()
    {
    	return Mage::registry('previous_photo');
    }
    public function getCurrentAlbum()
    {
    	return Mage::registry('current_album');
    }
    
    public function getCurrentPhoto()
    {
    	return Mage::registry('current_photo');
    }
    
	public function getReview(AsiaConnect_Gallery_Model_Gallery $gallery ){
		$reviews = Mage::getModel('gallery/review')
					->getCollection()
					->addFieldToFilter('gallery_id',$gallery->getId())
					->addFieldToFilter('status',2/* Approved */);
					
		if(sizeof($reviews)) return $reviews;

		return false;					
	}
	
	public function fDate($format,$datetime)
	{
		return date($format, strtotime($datetime)); 
	}
	
	public function getUrlRewrite(Mage_Core_Model_Abstract $obj){
		$rewrite = Mage::getModel('core/url_rewrite')
			->load($obj->getUrlRewriteId());
			
		return $rewrite->getRequest_path();
	}
	
	public function getMediaUrl($media){
		return trim(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA),'/').'/'.$media;
	}
	
	public function getTotalRate(AsiaConnect_Gallery_Model_Gallery $gallery){
		
		$collection = Mage::getModel('gallery/review')
			->getCollection()
			->addFieldToFilter('gallery_id', $gallery->getId())
			->addFieldToFilter('status',2);
			
		return $collection->getSize();
	}
	
	public function getRate(AsiaConnect_Gallery_Model_Gallery $gallery){
		return AsiaConnect_Gallery_Model_Review::_getRate($gallery);
	}
	public function showPhotoUpdateDate(AsiaConnect_Gallery_Model_Album $album)
	{
		if($album->getDefaultConfig())
		{
			return Mage::getStoreConfig('gallery/default_config/show_photo_update_date');
		}
		return $album->getShowPhotoUpdateDate();
	}
	public function getPhotosOfAlbum(AsiaConnect_Gallery_Model_Album $album) {
		
		$collection = Mage::getModel('gallery/gallery')->getCollection()->addFieldToFilter('status', true);
		if ($album) $collection->addFieldToFilter('album_id' , $album->getId());
		
		$collection->addOrder('main_table.order','ASC')
			       ->addOrder('gallery_id','DESC');
			       
		return $collection;
	}
	public function getCurrentLocation()
	{
		return Mage::registry('location_of_current_photo');
	}
	public function getSlideshowSize(AsiaConnect_Gallery_Model_Album $album)
	{
		$size="";
		if($album->getDefaultConfig())
		{
			$size = Mage::getStoreConfig('gallery/default_config/slide_show_size');
		}else
		{
			$size = $album->getPhotoSlideShowSize();
		}
		
		$tmp = explode('-',$size);
		if(sizeof($tmp)==2)
		
			return array('width'=>is_numeric($tmp[0])?$tmp[0]:600,'height'=>is_numeric($tmp[1])?$tmp[1]:500);
			
		return array('width'=>600,'height'=>500);
	}
	
	public function isEnableReview()
	{
		return Mage::getStoreConfig('gallery/info/review_enabled');
	}
}