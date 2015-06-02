<?php
class Hm_Testimonial_Block_Testimonial extends Mage_Core_Block_Template
{

    public function getTestimonials()     
    {
    	if (!Mage::app()->isSingleStoreMode()) {
		    $collection = Mage::getModel('testimonial/testimonial')->getCollection()
		    ->addStoreFilter(Mage::app()->getStore()->getId())
		        ->setOrder('created_time', 'desc');
    	}else{
    		$collection = Mage::getModel('testimonial/testimonial')->getCollection()->setOrder('created_time','desc');
    	}

	    $route = Mage::helper('testimonial')->getRoute();
	    Mage::getSingleton('testimonial/status')->addEnabledFilterToCollection($collection);
	    
	    //$collection->setPageSize(Mage::getStoreConfig('hm_testimonial/general/num_testimonial') ? Mage::getStoreConfig('hm_testimonial/general/num_testimonial'):5);
	    
	    foreach ($collection as $item) 
	    {
	        $tempAddress = $item->getWebsite();
	    	if($tempAddress != ''){
			   if(substr($tempAddress,0,4)=='http'){
				  $item->setWebsite($tempAddress);        	
			   }
			   else{
				  $item->setWebsite('http://'.$tempAddress);
			    }
		    }
		 
		    $maxword =  utf8_decode(Mage::getStoreConfig('hm_testimonial/general/maxword'));	
	        $item->setCreatedTime($this->formatTime($item->getCreatedTime(),'d-m-y', true));
	        $item->setUpdateTime($this->formatTime($item->getUpdateTime(),'d-m-y', true));
	        $content = $item->getDescription();
	        $content = $this->closetags($content);
			if($maxword>0){
	        $item->setPostContent(Mage::getModel('testimonial/testimonial')->word_trim($content,$maxword,true));
			}else{
				$item->setPostContent($item->getDescription());
			}
	        
	        
	    }
	    return $collection;
 
        
    }
    
    public function getRecents()     
    {
    	if (!Mage::app()->isSingleStoreMode()) {
		    $collection = Mage::getModel('testimonial/testimonial')->getCollection()
		    ->addStoreFilter(Mage::app()->getStore()->getId())
		        ->setOrder('created_time', 'desc');
    	}else{
    		$collection = Mage::getModel('testimonial/testimonial')->getCollection()->setOrder('created_time','desc');
    	}

	    $route = Mage::helper('testimonial')->getRoute();
	    Mage::getSingleton('testimonial/status')->addEnabledFilterToCollection($collection);
	    
	    //$collection->setPageSize(Mage::getStoreConfig('hm_testimonial/general/num_testimonial') ? Mage::getStoreConfig('hm_testimonial/general/num_testimonial'):5);
      $total = Mage::getStoreConfig('hm_testimonial/general/total'); 
      if (!$total) $total = 5; 
	    $collection->setPageSize($total);
      	    
	    foreach ($collection as $item) 
	    {
	        $tempAddress = $item->getWebsite();
	    	if($tempAddress != ''){
			   if(substr($tempAddress,0,4)=='http'){
				  $item->setWebsite($tempAddress);        	
			   }
			   else{
				  $item->setWebsite('http://'.$tempAddress);
			    }
		    }
		 
		    $maxword =  utf8_decode(Mage::getStoreConfig('hm_testimonial/general/maxword'));	
	        $item->setCreatedTime($this->formatTime($item->getCreatedTime(),'d-m-y', true));
	        $item->setUpdateTime($this->formatTime($item->getUpdateTime(),'d-m-y', true));
	        $content = $item->getDescription();
	        $content = $this->closetags($content);
			if($maxword>0){
	        $item->setPostContent(Mage::getModel('testimonial/testimonial')->word_trim($content,$maxword,true));
			}else{
				$item->setPostContent($item->getDescription());
			}
	        
	        
	    }
	    return $collection;
 
        
    }
    
    public function closetags($html){
        return Mage::helper('testimonial/data')->closetags($html);
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