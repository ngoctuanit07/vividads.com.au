<?php
class AsiaConnect_Gallery_Block_Gallery extends Mage_Core_Block_Template
{
    public static $PHOTOS_OF_CURRENT_PAGE	= 0;
    public static $ALL_PHOTOS_OF_CATEGORY	= 1;
    
	public function _prepareLayout()
    {
    	$toolbar = $this->getLayout()->createBlock('gallery/list_toolbar','gallery_toolbar');
		$toolbar->setTemplate('gallery/list/toolbar.phtml');
		$toolbar->setOrderField('title');
		if(!$this->getRequest()->getParam('limit')){
			$limit = Mage::getStoreConfig('gallery/info/photo_per_page_default_value');
			$_limit = 0;
			foreach($toolbar->getAvailableLimit() as $value){
				if($limit < $value) break;
				$_limit = $value;
			}

			Mage::getSingleton('catalog/session')->setLimitPage($_limit);
			$this->getRequest()->setParam('limit',$_limit);
		}
		$toolbar->setChild('gallery_pager',$this->getLayout()->createBlock('page/html_pager','gallery_pager'));
    	$this->setToolbar($toolbar);
    	
		if(Mage::app()->getRequest()->getModuleName() == 'gallery'):
			$cate = Mage::getModel('gallery/album')->load($this->getRequest()->getParam('id'),'album_id');
			
			if ($this->getLayout()->getBlock('head')) {
				
				$head = $this->getLayout()->getBlock('head');
				$title = $cate->getTitle();		        
				$keyword = $cate->getMetaKeyword();
				$description = $cate->getMetaDescription();
				
				if ($title) {
					$head->setTitle($title);
				}			
				if ($keyword) {
					$head->setKeywords($keyword);
				}
				if ($description) {
					$head->setDescription($description);
				}
					
			}
		endif;
    	
    	return parent::_prepareLayout();
    }
	protected function _getStoreId()
    {
        $store_id = $this->helper("core")->getStoreId(); 
        return $store_id;
    }
	protected function _getStore()
    {
        $store_id = $this->helper("core")->getStoreId(); 
        return Mage::app()->getStore($store_id);
    }
     public function getGallery()     
     { 
        if (!$this->hasData('gallery')) {
            $this->setData('gallery', Mage::registry('gallery'));
        }
        return $this->getData('gallery','asc');
        
    }
	public function getNewAlbums()
	{
		$total = $this->getTotalAlbum();

		$collection_new_album = Mage::getModel('gallery/album')->getCollection()	
			->addOrder('main_table.order','ASC')
			->addOrder('album_id','DECS')
			->setPageSize($total);

		return $collection_new_album;
	}
	public function getFeatureAlbums()
	{
		$total = $this->getTotalAlbum();

		$collection = Mage::getModel('gallery/album')->getCollection()	
			->addOrder('main_table.order','ASC')
			->addOrder('album_id','DECS')
			->addFieldToFilter('featured' , 1)
			->setPageSize($total);
		return $collection;
	}
	public function getPhotos() {
		$collection = Mage::getModel('gallery/gallery')->getCollection()->addFieldToFilter('main_table.status', true);
		if (Mage::registry('current_album')) $collection->addFieldToFilter('album_id' , Mage::registry('current_album')->getId());
		else if ($this->getData('album_id')) {
			$collection->addFieldToFilter('album_id' , $this->getData('album_id'));
			$album = Mage::getModel('gallery/album')->load($this->getData('album_id'));
			if ($album) {
				Mage::register('current_album', $album);
			}
		}
		
		//add store filter
		$album = Mage::getModel('gallery/album')->getCollection()
						->addFieldToFilter('status', true)
						->addOrder('main_table.order','ASC')
						->addOrder('album_id', 'DESC')
						->addStoreFilter($this->_getStoreId())
						->addFieldToSelect('album_id')
						->toArray();
						
	    $collection->addFieldToFilter('album_id', array("in" => $album));

		return $collection;
	}
	
	private function getRate(AsiaConnect_Gallery_Model_Gallery $gallery){
		$collection = Mage::getModel('gallery/review')
			->getCollection()
			->addFieldToFilter('gallery_id', $gallery->getId());
		$sum = 0;
		foreach($collection as $review)
		{
			$sum += (int) $review->getRate();
		}
		if(sizeof($collection)) return round(($sum/sizeof($collection)),0);
		return 0;
	}
	
	public function getPhotosOfAlbum(AsiaConnect_Gallery_Model_Album $album) {
		
		$collection = Mage::getModel('gallery/gallery')->getCollection()->addFieldToFilter('status', true);
		if ($album) $collection->addFieldToFilter('album_id' , $album->getId());
		$collection ->addOrder('main_table.order','ASC')->addOrder('gallery_id','DESC');
		
		return $collection;
	}
	
	public function getReview(AsiaConnect_Gallery_Model_Gallery $gallery ){
		$reviews = Mage::getModel('gallery/review')
					->getCollection()
					->addFieldToFilter('gallery_id',$gallery->getId());
		if(sizeof($reviews)) return $reviews;

		return false;					
	}
	
	public function fDate($format,$datetime)
	{
		return date($format, strtotime($datetime)); 
	}
	
	public function getDescription() {
		if (Mage::registry('current_album')) return Mage::registry('current_album')->getContent();
		return '';
	}

	public function getAlbums() {
		$collection = Mage::getModel('gallery/album')->getCollection()
						->addFieldToFilter('status', true)
						->addFieldToFilter('parent_id', 1)
						->addOrder('main_table.order','ASC')
						->addOrder('album_id', 'DESC')
						->addStoreFilter($this->_getStoreId());
		return $collection;
	}
	
	public function getSubAlbums() {
		if (Mage::registry('current_album')){
			$collection = Mage::getModel('gallery/album')->getCollection()
							->addFieldToFilter('status', true)
							->addFieldToFilter('parent_id',Mage::registry('current_album')->getId())
							->addOrder('main_table.order','ASC')
							->addOrder('album_id', 'DESC')
							->addStoreFilter($this->_getStoreId());
			return $collection;
		}
		return false;
	}
	
	public function getUrlRewrite(Mage_Core_Model_Abstract $obj){
		$rewrite = Mage::getModel('core/url_rewrite') ->load($obj->getUrlRewriteId());
		
		return $rewrite->getRequest_path();
	}
	
	public function getMediaUrl($media){
		return trim(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA),'/').'/'.$media;
	}
	
	
	public function getTotalAlbumValue()
	{
		return $this->getTotalAlbum();
	}
	
	public function getPhotoThumbnailSize(AsiaConnect_Gallery_Model_Album $album)
	{
		if($album->getDefaultConfig())
		{
			$mode = $this->getRequest()->getParam('mode');
			$size = $mode=="detail"||$mode==""?Mage::getStoreConfig('gallery/default_config/photo_thumbnail_size'):Mage::getStoreConfig('gallery/default_config/simple_photo_thumbnail_size');
			$tmp = explode('-',$size);
			if(sizeof($tmp)==2)
				return array('width'=>is_numeric($tmp[0])?$tmp[0]:175,'height'=>is_numeric($tmp[1])?$tmp[1]:131);
			return array('width'=>175,'height'=>131);
		}
		$size = $album->getThumbnailSize();
		$tmp = explode('-',$size);
		if(sizeof($tmp)==2)
			return array('width'=>is_numeric($tmp[0])?$tmp[0]:175,'height'=>is_numeric($tmp[1])?$tmp[1]:131);
		return array('width'=>175,'height'=>131);
	}
	
	public function getAlbumThumbnailSize()
	{
		$size = Mage::getStoreConfig('gallery/info/album_thumbnail_size');
		$tmp = explode('-',$size);
		if(sizeof($tmp)==2)
			return array('width'=>is_numeric($tmp[0])?$tmp[0]:175,'height'=>is_numeric($tmp[1])?$tmp[1]:131);
		return array('width'=>175,'height'=>131);
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
	
	public function showPhotoLink(AsiaConnect_Gallery_Model_Album $album)
	{
		if($album->getDefaultConfig())
		{
			return Mage::getStoreConfig('gallery/default_config/show_photo_link');
		}
		return $album->getShowPhotoLink();
	}
	public function showPhotoTitle(AsiaConnect_Gallery_Model_Album $album)
	{
		if($album->getDefaultConfig())
		{
			return Mage::getStoreConfig('gallery/default_config/show_photo_title');
		}
		return $album->getShowPhotoTitle();
	}
	public function showPhotoDescription(AsiaConnect_Gallery_Model_Album $album)
	{
		if($album->getDefaultConfig())
		{
			return Mage::getStoreConfig('gallery/default_config/show_photo_des');
		}
		return $album->getShowPhotoDescription();
	}
	public function showPhotoUpdateDate(AsiaConnect_Gallery_Model_Album $album)
	{
		if($album->getDefaultConfig())
		{
			return Mage::getStoreConfig('gallery/default_config/show_photo_update_date');
		}
		return $album->getShowPhotoUpdateDate();
	}
	public function showAlbumUpdateDate()
	{
		return Mage::getStoreConfig('gallery/info/show_album_update_date');
	}
	
	public function showTotalPhotos()
	{
		return Mage::getStoreConfig('gallery/info/show_total_photos_of_album');
	}
	
	public function getRootAlbum()
	{
		$root = Mage::getModel('gallery/album')->load(1);
		Mage::register('root_album',$root);
		return $root;
	}
	
	public function getCurrentAlbum()
	{
		if(Mage::registry('current_album') instanceof AsiaConnect_Gallery_Model_Album) return Mage::registry('current_album');
		else return false;
	}
	
	public function getStoreBackgroundColor($mode)
    {
    	$color = $mode=="detail"?explode(',',Mage::getStoreConfig('gallery/info/photo_background_color')):explode(',',Mage::getStoreConfig('gallery/info/simple_photo_background_color'));
    	if(sizeof($color)==3)
    	{
    		foreach($color as $item){
    			if(!is_numeric($item) || $item >255) return array(255, 255, 255);
    		}
    		return array((int)$color[0], (int)$color[1], (int)$color[2]);
    	}
    	return array(255, 255, 255);
    }
	
	public function getDefaultBackgroundColor()
	{
		$color = explode(',',Mage::getStoreConfig('gallery/info/photo_background_color'));
    	if(sizeof($color)==3)
    	{
    		foreach($color as $item){
    			if(!is_numeric($item) || $item >255) return array(255, 255, 255);
    		}
    		return array((int)$color[0], (int)$color[1], (int)$color[2]);
    	}
    	return array(255, 255, 255);
	}
	public function getSlideShowSpeed()
	{
		$speed = Mage::getStoreConfig('gallery/default_config/slide_show_delay');

		if(!is_numeric($speed)) return 2500;
		return $speed;
	}
	
	public function getSlideShowPhotosAs()
	{
		$slideShowPhotosAs = Mage::getStoreConfig('gallery/default_config/slide_show_photos');
		return $slideShowPhotosAs;
	}
	public function isEnableReview()
	{
		return Mage::getStoreConfig('gallery/info/review_enabled');
	}
	
	public function canUploadPhotos()
	{
		$config = Mage::getStoreConfig('gallery/info/upload_photos');
		if($config)
		{
			$currentCustomer = Mage::getSingleton('customer/session')->getCustomer();
			return (($currentCustomer->getId() && $config=='2') || Mage::getStoreConfig('gallery/info/upload_photos')=='1')?true:false;
		}
		return false;
	}
}
