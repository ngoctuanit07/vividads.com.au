<?php

class Mconnect_Brandlogo_Model_Brandlogo extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('brandlogo/brandlogo');
    }

    public function getAllLogos() {
        $store_id = Mage::app()->getStore()->getId();
        /*
        $store_id = 0;
        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) { // store level
            $store_id = Mage::getModel('core/store')->load($code)->getId();
        } elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) { // website level
            $website_id = Mage::getModel('core/website')->load($code)->getId();
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        } else { // default level
            $store_id = 0;
        }
        echo $store_id;
        */
        $collection = Mage::getModel('brandlogo/brandlogo')->getCollection()
                ->addStoreFilter($store_id)
                ->addCategoryFilter();
        $brandLogos = $collection->getData();
        
		
		
		return $brandLogos;
    }
	
	
	public function getAllStoreLogos() {
        $store_id = Mage::app()->getStore()->getId();
        /*
        $store_id = 0;
        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) { // store level
            $store_id = Mage::getModel('core/store')->load($code)->getId();
        } elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) { // website level
            $website_id = Mage::getModel('core/website')->load($code)->getId();
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        } else { // default level
            $store_id = 0;
        }
        echo $store_id;
        */
        $collection = Mage::getModel('brandlogo/brandlogo')->getCollection()
                ->addStoreFilter($store_id)
                ->addCategoryFilter();
        $brandLogos = $collection->getData();
        
		/*total logo items*/
		
		$media = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		
		$_total_logos = count($brandLogos);
		$_page_limit = Mage::app()->getRequest()->getParam('pLimit');		
		$_current_page = Mage::app()->getRequest()->getParam('cPage');
		
		$_total_pages = ceil($_total_logos/$_page_limit);
		/*page start from */
		$pageend = $_current_page * $_page_limit;
		/*page end here */
		$pagestart=$pageend-$_page_limit;
		
		/*looop through the logo items*/
		for($i=$pagestart; $i<$pageend; $i++){
			$_b_logo = $brandLogos[$i];
			
			$output='';
			$output.='<li class="nav item ';
			
			if($i==1){
			$output.='first';
			}
			if($i%3==0){
				$output.='last';
				}
			$output.=' fadeIn animated" style="min-height: 127px;padding: 25px 5px 0;margin: 0 15px 10px 0;cursor:pointer;">
			<img src="'.$media.'brandlogo'.$_b_logo['filename'].'" title="'.$_b_logo['title'].'" alt="'.$_b_logo['title'].'"/>
				
			</li>';
		echo $output;	
			}
		
		exit;
    }
	
	
	public function _getTotalLogos() {
        $store_id = Mage::app()->getStore()->getId();        
        $collection = Mage::getModel('brandlogo/brandlogo')->getCollection()
                ->addStoreFilter($store_id)
                ->addCategoryFilter();
        $brandLogos = $collection->getData();
        		
		return  count($brandLogos);
    }
	

}