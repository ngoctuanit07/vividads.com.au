<?php

class Artis_Vendor_Model_Vendor extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendor/vendor');
    }
	
	/*getting vendor assigned items*/
	
	public function getVendorProducts($_order_id=0, $_item_id=0){
		
			$_connection = Mage::getSingleton('core/resource');
			$_read = $_connection->getConnection('core_read');
			$_vendorProductTable = $_read->getTableName('vendor_product');
			
		    //fetching the product assigned
		 	$_pr_sql = $_read->select()
						  ->from($_vendorProductTable,array('*'))
						  ->where('order_id=?',$_order_id)
						  ->where('item_id=?',$_item_id);								
			//var_dump($_pr_sql->__toString());
			try{
			$_result = $_read->fetchOne($_pr_sql);
			}catch(Exception $e){
				print_r($e);
				}
			return $_result;		
		
		}
		
	/*getting vendor assigned items*/
	
	public function getVendorTitle($_order_id=0, $_item_id=0){
		
			$_connection = Mage::getSingleton('core/resource');
			$_read = $_connection->getConnection('core_read');
			$_vendorProductTable = $_read->getTableName('vendor_product');
			
		    //fetching the product assigned
		 	$_pr_sql = $_read->select()
						  ->from($_vendorProductTable,array('vendor_id'))
						  ->where('order_id=?',$_order_id)
						  ->where('item_id=?',$_item_id);								
			// var_dump($_pr_sql->__toString());
			 
			 try{
			 $_vendors = $_read->fetchAll($_pr_sql);			
			 
			if($_vendors){			
			//identifying the user with vendor 
			foreach($_vendors as $_vendor){
				$_vendor = Mage::getModel('admin/user')->load($_vendor['vendor_id']);			
				return $_vendor->getUsername();
					}
					}else{
						return 'Not Assigned';
				}
			}catch(Exception $e){
				print_r($e);
			}
			 	
			
		}
		
	/*  function getProofs*/
	public function getProofs($_order_id = 0, $_item_id=0){
			
			$_connection = Mage::getSingleton('core/resource');
			$_read = $_connection->getConnection('core_read');
			$_proofsProductTable = $_read->getTableName('proofs');
			
		    //fetching the product assigned
		 	$_pr_sql = $_read->select()
						  ->from($_proofsProductTable,array('sum(quantity) as quantity'))
						  ->where('order_id=?',$_order_id)
						  ->where('item_id=?',$_item_id);								
			  // var_dump($_pr_sql->__toString());		  
			
		 try{
			 $_proofs = $_read->fetchAll($_pr_sql);					
			 
			if($_proofs){				
				foreach($_proofs as $_proof){
					
					if($_proof['quantity']==null){
					return 0;	
						}else
					return $_proof['quantity'];				
					}				
			}else{
				return 0;
				}
			}catch(Exception $e){
				print_r($e);
			}
			 	
		}	
		
	/*  function getArtworkFiles*/
	public function getArtworkFiles($_order_id = 0, $_item_id=0, $_user_type='admin'){
			
			$_connection = Mage::getSingleton('core/resource');
			$_read = $_connection->getConnection('core_read');
			$_taskdesignerTable = $_read->getTableName('task_designer');
			
		    //fetching the product assigned
		 	$_pr_sql = $_read->select()
						  ->from($_taskdesignerTable,array('quantity'))
						  ->where('order_quote_id=?',$_order_id)
						  ->where('item_id=?',$_item_id)
						  ->where('proof_type=?','customer')
						  ;								
			 // var_dump($_pr_sql->__toString());		  
			
		 try{
			 $_artworkFiles = $_read->fetchAll($_pr_sql);			
			 	
			if($_artworkFiles){				
					return count($_artworkFiles);			
			}else{
				return 0;
				}
			}catch(Exception $e){
				print_r($e);
			}
			 	
		}
		
		
	/*  function getProofs*/
	public function getProofFiles($_order_id = 0, $_item_id=0, $_user_type='admin'){
			
			$_connection = Mage::getSingleton('core/resource');
			$_read = $_connection->getConnection('core_read');
			$_taskdesignerTable = $_read->getTableName('task_designer');
			
		    //fetching the product assigned
		 	$_pr_sql = $_read->select()
						  ->from($_taskdesignerTable,array('quantity'))
						  ->where('order_quote_id=?',$_order_id)
						  ->where('item_id=?',$_item_id)
						  ->where('proof_type=?','proof')
						  ->where('user_type=?',$_user_type);								
			 // var_dump($_pr_sql->__toString());		  
			
		 try{
			 $_artworkFiles = $_read->fetchAll($_pr_sql);			
			 	
			if($_artworkFiles){				
					return count($_artworkFiles);			
			}else{
				return 0;
				}
			}catch(Exception $e){
				print_r($e);
			}
			 	
		}		
		
	/*  function getProofApproved*/
	public function getProofApprovedFiles($_order_id = 0, $_item_id=0, $_user_type='admin'){
			
			$_connection = Mage::getSingleton('core/resource');
			$_read = $_connection->getConnection('core_read');
			$_proofsTable = $_read->getTableName('proofs');
			
		    //fetching the product assigned
		 	$_pr_sql = $_read->select()
						  ->from($_proofsTable,array('quantity'))
						  ->where('order_id=?',$_order_id)
						  ->where('item_id=?',$_item_id)
						  ->where('status=?','Approved')
						  ->where('proof_type=?','order')
						  ;								
			  //  var_dump($_pr_sql->__toString());
			 	
			
		 try{
			 $_approvedFiles = $_read->fetchAll($_pr_sql);			
			 	
			if($_approvedFiles){				
					//var_dump($_approvedFiles);
					return count($_approvedFiles);			
			}else{
				return 0;
				}
			}catch(Exception $e){
				print_r($e);
			}
			 	
		}	
	
	/*  function getProofApproved*/
	public function getProofRejectedFiles($_order_id = 0, $_item_id=0, $_user_type='admin'){
			
			$_connection = Mage::getSingleton('core/resource');
			$_read = $_connection->getConnection('core_read');
			$_taskdesignerTable = $_read->getTableName('task_designer');
			
		    //fetching the product assigned
		 	$_pr_sql = $_read->select()
						  ->from($_taskdesignerTable,array('sum(approved_status) as approved_status'))
						  ->where('order_quote_id=?',$_order_id)
						  ->where('item_id=?',$_item_id)	
						  ->where('approved_status=?',1)					  
						  ;								
			//var_dump($_pr_sql->__toString());
		try{
			 $_rejectedFiles = $_read->fetchAll($_pr_sql);		
			 	
			if($_rejectedFiles){				
					
					foreach($_rejectedFiles as $_rejectedFile){
							
							if($_rejectedFile['approved_status']==null){
								return 0;
							}else{
								return $_rejectedFile['approved_status'];			
							}
						}
			}else{
				return 0;
				}
			}catch(Exception $e){
				print_r($e);
			}
			 	
		}		
		
	/*get vendorprintingdetail*/
	public function getVendorJobsInfo($vendor_id=0, $sql_array=array(),$condition=null ){
		
	  if( count($sql_array)==0) return 0;
		
		$_connection = Mage::getSingleton('core/resource');
		$_read = $_connection->getConnection('core_read');
		$_vendor_itemTable = $_read->getTableName('vendor_item');
			
		    //fetching the product assigned
		 	$_pr_sql = $_read->select()
						  ->from($_vendor_itemTable,$sql_array)
						  ->where('target_user=?',$vendor_id);	
			if($condition !=null){
				$_pr_sql = $_pr_sql->where($condition);
				}			  							
			   //  var_dump($_pr_sql->__toString());		  
			
		 try{
			 $_vendor_items = $_read->fetchAll($_pr_sql);
			 
				if($_vendor_items){				
					return $_vendor_items;				
				}else{
					return 0;
					}
			}catch(Exception $e){
				print_r($e);
			}
		
		}				
		
}