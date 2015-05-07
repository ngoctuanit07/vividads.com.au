<?php

class Aptoplex_EasyUploader_Block_Index extends Mage_Core_Block_Template { 
	
	///fetching the file type by its extension   
	  
	
	public function getFileType($filename=''){
		
		/*finding file extension*/			
			$_file = explode('.',$filename);			
			$_file_ext = $_file[count($_file)-1];			
			$mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'upload/fileicons/';			
			$_known_file_icons=array( 'jpg'=>'jpg_icon.png','jpeg'=>'jpg_icon.png','png'=>'png_icon.png',
									  'tif'=>'tif_icon.png','tiff'=>'tif_icon.png',  
									  'gif'=>'gif_icon.png',										
									 'rar'=>'rar_icon.png','zip'=>'rar_icon.png',
									 'txt'=>'txt_icon.png','docx'=>'docx_icon.png',
									 'doc'=>'docx_icon.png','xlsx'=>'xlxs_icon.png',
									 'xls'=>'xlxs_icon.png','pdf'=>'pdf_icon.png',
									 'ai'=>'ai_icon.png','psd'=>'psd_icon.png',
									 'fla'=>'fla_icon.png', 'eps'=>'eps_icon.png',									 
									 
									); 
				
			foreach($_known_file_icons as $_key=>$_val){
				
				if(strtolower($_file_ext)==strtolower($_key)){
						$_file_type = $_val;				
					}
			}
				if($_file_type){	
						$nfilename = $mediaUrl.$_file_type;
				}else{
						$nfilename = $mediaUrl.'other_icon.png';
					}
				return $nfilename;					
			 
		}
		
	public function getOrderVars(){	 		
			
			    $post_vars  = $this->getRequest()->getParams();			    
			    $order_id = $post_vars['order_id'];		
				//$order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
				$my_order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
				
				if(count($my_order->getData())==0){
					$my_order = Mage::getModel('Quotation/Quotation')->getCollection()
									->addFieldToFilter('increment_id', $order_id);
					$my_order_data = $my_order->getData();	
					$customer = Mage::getModel('customer/customer')->load($my_order_data[0]['customer_id']);
					$email = $customer->getEmail();
					}else{
						$email = $my_order->getCustomerEmail();		
						}
				   
						
				$order_vars = array('order'=>$my_order,'email'=>$email,'order_id'=>$order_id );
				
				return $order_vars;
		}	

}