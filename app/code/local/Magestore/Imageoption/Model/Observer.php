<?php
class Magestore_Imageoption_Model_Observer {
	
	public function catalog_product_collection_load_before($observer)
	{
		$collection = $observer->getCollection();
		$collection->addFieldToFilter('entity_id',array('neq'=>0));
	}
	
	public function catalogModelProductDuplicate($observer)
	{
		if(! isset($observer['current_product']) || !isset($observer['new_product']))
			return;
		
		$currProduct = $observer['current_product'];
		$newProduct = $observer['new_product'];
		
		$collection = Mage::getResourceModel('imageoption/imageoption_collection')
					->addFieldToFilter('product_id',$currProduct->getId());
		
		if(! count($collection))
			return;
		
		//create buff folder
		Mage::helper('imageoption')->createImageFolder(0,0,0);
		
		$buff_path = Mage::helper('imageoption')->getImagePath(0,0,0);
		$buff_path_cache = Mage::helper('imageoption')->getImagePathCache(0,0,0);
		
		try{
			// copy images from current folder to buff folder
			foreach($collection as $item)
			{
				$curr_path = Mage::helper('imageoption')->getImagePath($currProduct->getId(),$item->getOptionId(),$item->getOptionTypeId());
				$curr_path_cache = Mage::helper('imageoption')->getImagePathCache($currProduct->getId(),$item->getOptionId(),$item->getOptionTypeId());				
				if(file_exists($curr_path . DS . $item->getImage()))
				{
					copy($curr_path . DS . $item->getImage(), $buff_path . DS . $item->getImage());
				}
				if(file_exists($curr_path_cache . DS . $item->getImage()))
				{
					copy($curr_path_cache . DS . $item->getImage(), $buff_path_cache . DS . $item->getImage());
				}
			}
			
			Mage::getModel('imageoption/imageoption')->duplicate($currProduct->getId(),0);
			
		} catch(Exception $e) {
			Mage::getSingleton('core/session')->addError($e->getMessage());
		}
	}

	public function catalogProductEditAction($observer)
	{
		$this->checkLicense();
		
		if(!isset($observer['product']))
			return;
			
		$product = $observer['product'];
		
			//set has_option product
		$options = Mage::getModel('catalog/product_option')->getCollection()
					->addFieldToFilter('product_id',$product->getId());
		if(count($options))
		{
			$spl_product = Mage::getModel('imageoption/product')->load($product->getId());
			if($spl_product->getHasOptions() ==0)
			{
				$spl_product->setHasOptions(1);
				$spl_product->save();
			}
		}

		
		$collection = Mage::getResourceModel('imageoption/imageoption_collection')
					->addFieldToFilter('product_id',0)
					->addFieldToFilter('is_template',0);

		if(! count($collection))
			return;
		
		// copy images from buff to real folder
		$buff_path = Mage::helper('imageoption')->getImagePath(0,0,0);
		$buff_path_cache = Mage::helper('imageoption')->getImagePathCache(0,0,0);
		
		try{		
			
			$imageoptionResource = Mage::getResourceModel('imageoption/imageoption'); 
			
			foreach($collection as $item)
			{
				//update option_id and option_type_id
				$option_title = $imageoptionResource->getOptionTitle($item->getData('option_id'));
	
				$option_type_title = $imageoptionResource->getOptionTypeTitle($item->getData('option_type_id'));
		
				$IDS = $imageoptionResource->getOptionTypeIdByTitle($product,$option_title,$option_type_title);
				
				$item->setData('product_id',$product->getId());
				$item->setData('option_id',$IDS['option_id']);
				$item->setData('option_type_id',$IDS['option_type_id']);
				$item->save();
				
						//create new image folder
				Mage::helper('imageoption')->createImageFolder($product->getId(),$item->getOptionId(),$item->getOptionTypeId());
				
				$path = Mage::helper('imageoption')->getImagePath($product->getId(),$item->getOptionId(),$item->getOptionTypeId());
				$path_cache = Mage::helper('imageoption')->getImagePathCache($product->getId(),$item->getOptionId(),$item->getOptionTypeId());				
				
				if(file_exists($buff_path . DS . $item->getImage()))
				{
					copy($buff_path . DS . $item->getImage(), $path . DS . $item->getImage());
					unlink($buff_path . DS . $item->getImage());
				}
				
				if(file_exists($buff_path_cache . DS . $item->getImage()))
				{
					copy($buff_path_cache . DS . $item->getImage(), $path_cache . DS . $item->getImage());
					unlink($buff_path_cache . DS . $item->getImage());
				}
				
			}
		} catch(Exception $e) {

		}
	}
	
		//apply template for product
	public function catalogProductPrepareSave($observer)
	{
		if(!isset($observer['product']) || ! isset($observer['request']))
			return;
		
		$product = $observer['product'];
		$data = $observer['request']->getPost();
			
		if(!isset($data['optiontemplate_id']))
			return;
		
		$templateId = $data['optiontemplate_id'];
		
		$productTemplate = Mage::getModel('imageoption/producttemplate')->loadByTempIdPrdId($templateId,$product->getId());
				
		if($productTemplate)
		{		
			$productTemplate->apply();
		}
	}
	
	public function checkLicense()
	{
		$notificationHelper = Mage::helper('magenotification');
		if(!$notificationHelper->checkLicenseKey('Imageoption')){
			$extensionkey_link = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_config/edit',array('section'=>'magenotificationsecure','_secure'=>true));
			$extensionkey_link = '<a href="'.$extensionkey_link.'">'.$notificationHelper->__('extension key management section').'</a>';
			$message = $notificationHelper->__('Invalid License Key of Image Option extension');
			$message .= '! '.$notificationHelper->__('Please go to %s for more information.',$extensionkey_link);			
			Mage::getSingleton('core/session')->addError($message);
		}elseif((int)$notificationHelper->getCookieLicenseType() == Magestore_Magenotification_Model_Keygen::TRIAL_VERSION){
			$message = $notificationHelper->__('You are using a trial version of Image Option extension. It will be expired on %s.',
												$notificationHelper->getCookieData('expired_time'));
			Mage::getSingleton('core/session')->addNotice($message);
		}
	}		
	public function salesOrderPlaceAfter($observer)
	{
		$order = $observer->getEvent()->getOrder();
		$items = $order->getAllItems();
		foreach($items as $item) {
			
			$collections = Mage::getModel('imageoption/imageoption')->getCollection()
					->addFieldToFilter('product_id',$item->getProductId());
			$itemOptions = $item->getProductOptions();
			if (isset($itemOptions['options'])) {
					foreach($itemOptions['options'] as $op){
						$itemOption[] = $op['option_value'];
					}
					$collections = $collections->addFieldToFilter('option_type_id',array('in'=>$itemOption));
			} 
			foreach($collections as $collection) {
				$newQty = $collection['qty'] - ($item->getQtyToInvoice());
				$collection->setQty($newQty);
				try{
					$collection->save();
					} 
					catch(Exception $e) {
						}
			}
			
		}
	}
	public function salesOrderItemCancel($observer)
	{
		$item = $observer->getItem();
		$itemOptions = $item->getProductOptions();
		$collections = Mage::getModel('imageoption/imageoption')->getCollection()
					->addFieldToFilter('product_id',$item->getProductId());
		if (isset($itemOptions['options'])) {
					foreach($itemOptions['options'] as $op){
						$itemOption[] = $op['option_value'];
					}
					$collections = $collections->addFieldToFilter('option_type_id',array('in'=>$itemOption));
			}
		foreach($collections as $collection) {
				$newQty = $collection['qty'] + ($item->getQtyOrdered());
				$collection->setQty($newQty);
				try{
					$collection->save();
					} 
					catch(Exception $e) {
						}
			}
	}
}


?>