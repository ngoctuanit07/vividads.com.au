<?php

class ArtworkUploader_Upload_Model_Upload extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('upload/upload');
    }
	
	/*get if quantities approved*/
	
	public function getQantitiesApproved($_order_id=0, $_item_id=0, $_user_type='admin'){
	
		$_connection = Mage::getSingleton('core/resource');
		$_read = $_connection->getConnection('core_read');
		$_taskdesignerTable = $_read->getTableName('task_designer');
			
		//fetching the product assigned
		$_pr_sql = $_read->select()
						  ->from($_taskdesignerTable,array('quantity','file'))
						  ->where('order_quote_id=?',$_order_id)
						  ->where('item_id=?',$_item_id)
						  ->where('proof_type=?','proof')
						  ->where('approved_status=?',2)
						  ->where('user_type=?',$_user_type);								
			 // var_dump($_pr_sql->__toString());	 
		 try{
			 $_quantities = $_read->fetchAll($_pr_sql);			
			 	
			if($_quantities){
				
				return $_quantities;			
			}else{
				return 0;
				}
			}catch(Exception $e){
				print_r($e);
			}
	}
	
	///check if the vendor is assigned or not 
	

	public function getVendorAssigned($_order_id=0, $_item_id=0){
	
		$_connection = Mage::getSingleton('core/resource');
		$_read = $_connection->getConnection('core_read');
		$_vendorProductTable = $_read->getTableName('vendor_product');
			
		//fetching the product assigned
		 	$_vendor_sql = $_read->select()
						  ->from($_vendorProductTable,array('vendor_id'))
						  ->where('order_id=?',$_order_id)
						  ->where('item_id=?',$_item_id);								
			// var_dump($_vendor_sql->__toString());
			 
			 try{
			 $_vendors = $_read->fetchAll($_vendor_sql);			
			 
			if($_vendors){			
			//identifying the user with vendor 
			foreach($_vendors as $_vendor){
				$_vendor = Mage::getModel('admin/user')->load($_vendor['vendor_id']);			
				return true;
					}
					}else{
						return false;
				}
			}catch(Exception $e){
				print_r($e);
			}
	
	}
	
	
	///function getOrderItems()
	public function getOrderItems($order_id=null){
		
		$order_collection = Mage::getModel('sales/order')->load($order_id)->getAllItems();
		$order_items = new Varien_Data_Collection();
		
		foreach($order_collection as $_item){
			
			 $item_type =  $_item->getProduct_type();
			// var_dump($item_type);
				 
				 $product = Mage::getModel('catalog/product')->load($_item->getProduct_id());				  
				 if($item_type =='simple'){
				 if($product->getIs_printable()==165){
				  	 $order_items->addItem($_item);
					 }
				 }
			 
			 if($item_type =='bundle'){
				$product = Mage::getModel('catalog/product')->load($_item->getProduct_id());
				$collection = $product->getTypeInstance(true)
    						 ->getSelectionsCollection(
        							$product->getTypeInstance(true)
                			 ->getOptionsIds($product), $product)
							 ;

					foreach ($collection as $item) {
						# $item->product_id has the product id.						
						
						 $prod = Mage::getModel('catalog/product')->load($item->getProduct_id());						 
						 if($prod->getIs_printable()==165 && $item_type =='simple' ){
						 	
							//$pitems[$item->getSku()]=$item;
							  // $order_items->addItem($item);
						 }
						 
						 
						 
					}
								 
				 }
			 
			
			}
		 //var_dump($order_items);
		return $order_items;
		
		}
		
		
	///function getOrderItems()
	public function getOrderItemsN($order_id=null){
		
		$order_collection = Mage::getModel('sales/order')->load($order_id)->getAllItems();
		$order_items = new Varien_Data_Collection();
		 
		foreach($order_collection as $_item){
			
				 $item_type =  $_item->getProduct_type();			  
				 $product = Mage::getModel('catalog/product')->load($_item->getProduct_id());	
				 
				 $parent_id = $this->getParentProduct($_item->getProduct_id());
				 
				 var_dump($parent_id);
				 if($item_type =='simple'){
				 if($product->getIs_printable()==165){
				  	//$order_items->addItem($_item);
					 }
				 }
							 
			 if($item_type =='bundle'){
				$product = Mage::getModel('catalog/product')->load($_item->getProduct_id());
				$collection = $product->getTypeInstance(true)
    						 ->getSelectionsCollection(
        							$product->getTypeInstance(true)
                			 ->getOptionsIds($product), $product)
							 ;

					foreach ($collection as $item) {
						# $item->product_id has the product id.	
						 $prod = Mage::getModel('catalog/product')->load($item->getProduct_id());
						   if($prod->getIs_printable()==165){						 	
							//$pitems[$item->getSku()]=$item;
							  $order_items->addItem($item);
						 }						 
					}								 
				 }	
			}
			
		 var_dump(count($order_items));
		return $order_items;
		
		}
	
	
	
	////function getParentProduct
		
	public function getParentProduct($_productid=null){
		$product = Mage::getModel('catalog/product')->load($_productid);
		//echo $_productid;
						 
			//if product type is bundle	 
			 
			 if($product->getType_id()=='bundle'){
				 $pids = array();
				 $parent_id=$_productid;
				$product = Mage::getModel('catalog/product')->load($_productid);
				$pcollection = $product->getTypeInstance(true)
    						 ->getSelectionsCollection(
        							$product->getTypeInstance(true)
                			 ->getOptionsIds($product), $product)
							 ;

				foreach($pcollection as $item){
					$pids[][$_productid]= $item->getProduct_id();
					
					}
					
				 }
				 
				foreach($pids as $key=>$val){
					
					if($val[$_productid] == $_productid){
						echo $_productid;
						}
					}
			 
			return $pids;
		}		
	
}