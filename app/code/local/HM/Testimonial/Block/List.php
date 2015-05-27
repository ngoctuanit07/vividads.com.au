<?php
class Hm_Testimonial_Block_List extends Mage_Core_Block_Template
{
	protected $_defaultToolbarBlock = 'testimonial/page_pager';
    
    public function _prepareLayout()
    {
        //$route = Mage::helper('news')->getRoute(); 
        $isNewsPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName() == 'testimonial';
        
        // show breadcrumbs
        if ($isNewsPage && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))){
                $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('testimonial')->__('Home'), 'title'=>Mage::helper('testimonial')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));;
                $breadcrumbs->addCrumb('testimonial', array('label'=>'Testimonial', 'title'=>Mage::helper('testimonial')->__('Return to testimonial'), 'link'=>Mage::getUrl('testimonial')));
        }
    }
    
    public function getTestimonials()     
    {
    	if (!Mage::app()->isSingleStoreMode()) {
		    $collection = Mage::getModel('testimonial/testimonial')->getCollection()
		    ->addStoreFilter(Mage::app()->getStore()->getId())
		        ->setOrder('created_time', 'desc');
    	}else{
    		$collection = Mage::getModel('testimonial/testimonial')->getCollection()->setOrder('created_time','desc');
    	}
		
	   // echo count($collection)."<br>";
	    
	    $page = $this->getRequest()->getParam('page');
	    
	    //echo $page;
	    
	    $route = Mage::helper('testimonial')->getRoute();
	    Mage::getSingleton('testimonial/status')->addEnabledFilterToCollection($collection);
	    $currentPage = (int)$this->getRequest()->getParam('page');
	    if(!$currentPage){
	        $currentPage = 1;
	    }
	    $currentLimit = (int)$this->getRequest()->getParam('limit');
	        if(!$currentLimit){
	        $currentLimit = 5;
	    }
	    
	    $collection->setPageSize($currentLimit);
	   // echo count($collection)."<br>";
	    
	    $collection->setCurPage($currentPage);
	   // echo count($collection)."<br>";
	    
	    foreach ($collection as $item) 
	    {
	        //$item->setAddress($this->getUrl($route).'view/details/id/'. $item->getTestimonialId().'/s/'.$item->getPermalink());
	        $tempAddress = $item->getWebsite();
			if($tempAddress != ''){
				if(substr($tempAddress,0,4)=='http'){
					$item->setWebsite($tempAddress);        	
				}
				else{
					$item->setWebsite('http://'.$tempAddress);
				}
			}
		
	        $item->setCreatedTime($this->formatTime($item->getCreatedTime(),'d-m-y', true));
	        $item->setUpdateTime($this->formatTime($item->getUpdateTime(),'d-m-y', true));
	        $content = $item->getDescription();
	        $content = $this->closetags($content);
	        $item->setPostContent($content);
	        
	        
	    }
	    //echo count($collection)."<br>"; exit;
	    return $collection;
 
        
    }
    public function closetags($html){
        return Mage::helper('testimonial/data')->closetags($html);
    }

     public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }
     protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();
		//exit;
        
        // called prepare sortable parameters
        $collection = $this->getTestimonials();
       // echo sizeof($collection);
       //var_dump($toolbar);
        

        // set collection to tollbar and apply sort
        $toolbar->setCollection($collection);
		
        
        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent('testimonial_block_testimonial_collection', array(
            'collection'=>$this->getTestimonials(),
        ));

        $this->getTestimonials()->load();
        return parent::_beforeToHtml();
    }
     public function getToolbarBlock()
    {
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
    	//return "sdfslejfkdj;sdfl;";
        return $this->getChildHtml('toolbar');
    }

    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }
    
    public function getMediaUrl($media){
    	if($media){
    		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$media;
    	}
    }
    
    public function getWidthMedia(){
    	return (Mage::getStoreConfig('hm_testimonial/general/width')) ? (Mage::getStoreConfig('hm_testimonial/general/width')):200;
    }
    
    public function getHeightMedia(){
    	return (Mage::getStoreConfig('hm_testimonial/general/width')) ? (Mage::getStoreConfig('hm_testimonial/general/height')):200;
    }
    
}