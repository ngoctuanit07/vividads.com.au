<?php 
/**
/**
 * Webservice main controller
 *
 * @category   ArtworkUploader
 * @package    Upload
 * @author     Vivid Advertising Australia Development Team <info@vividads.com.au>
 */
//*indexController*/
		
class ArtworkUploader_Upload_ArtworkController extends Mage_Core_Controller_Front_Action
{
	
/*--------------------------------------------------------------------------------*/	
	
	/*fetching orders when sign in from url*/
		
	public function orderListAction(){
		
		/*fetching Order detail from the user*/	
		
		$_post_vars = $this->getRequest()->getParams();		
		$_order_id = $_post_vars['order_id'];
		
		
		/*re-direct to the login URL*/
		
		 
		if(!isset($_order_id) or $_order_id==''){
			
			$_path =  "upload/index" ;
			$this->_redirect($_path); 
			
		}else{
		 
		
		/* load layout*/
		$_layout = $this->loadLayout();
		$_block = $this->getLayout()->createBlock(
			'Mage_Core_Block_Template',
			'uploadArtworkList',
			array('template' => 'upload/sales/order/orderlist.phtml')
			);
		$this->getLayout()->getBlock('head')->setTitle($this->__('Artwork Upload'));	
		$this->getLayout()->getBlock('content')->append($_block) ;
		//
		$_block_data = $this->getLayout()->getBlock('uploadArtworkList');	
		
		
		
		//echo $_order_id;
		/*getting order detail*/
		$_orderObj = Mage::getModel('sales/order')->loadByIncrementId($_order_id);
		
		/*validating, if order id exists*/
		   //print_r(count($_orderObj->getData()));
		
		if(count($_orderObj->getData())!=0 ){
			
			//showing the extracted data at the front panel
			
			$_items_in_order = Mage::getModel('sales/order')->loadByIncrementId($_order_id);			 
			$_items_obj = $_items_in_order->getAllVisibleItems();
			 $_block_data->assign('order',$_orderObj->getData());
			 $_block_data->assign('items',$_items_obj);
			
			}else{
				
			 $_path = "upload/index" ;
			$this->_redirect($_path);  
			}
	
	   try {
            if (empty($_order_id)) {
            Mage::getSingleton('adminhtml/session')->addError("Invalid form data.");
                Mage::throwException($this->__('Invalid form data.'));
            }
	   }catch(Exception $e){
		   echo $e;
		   }

			/*showing block*/
			
			$this->renderLayout();
		
		}	
		
	} ///end of isset data
	
	
	
	/*--------------------------------------------------------------------------------*/		
	
	/*Writing data from chat file*/
	
	public function savechatAction(){
		 
		 /*fetching data via ajax post vars*/
		 $_chat_file = $_POST['file_path'];
		 $_chat_data = $_POST['chat_data'];
		 
		 if(is_file($_chat_file)){
			 
		     	$_chat_file_name = $_chat_file;
				$_file = @fopen($_chat_file,'a');
				$_file = fwrite($_file,$_chat_data);
				$_file = fclose($_file);
				echo 'data updated';
			 }	 
		 
		} ///end of isset data
		
		
/*Reading data from chat file*/
	
	public function readChatAction(){
		 
		 /*fetching data via ajax post vars*/
		 $_chat_file = $_POST['file_path'];
		 $_chat_data = $_POST['chat_data'];
		 
		 if(is_file($_chat_file)){
			 
		     	$_chat_file_name = $_chat_file;
				$_file = @fopen($_chat_file,'r');
				$data = fread($_file,filesize($_chat_file));
				echo $data;
				$_file = fclose($_chat_file);
				
			 }	 
		 
		} ///end of isset data	
		
		
/*Uploading  file*/
	
	public function uploadFileAction(){
		 
		/*taking connection for reading and writing data to tables*/
		$_connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$_connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		 
		 /*fetching data via ajax post vars*/
		$_file_id = $this->getRequest()->getParam('fileid');
		$_user_id = $this->getRequest()->getParam('user_id');
		$_store_id = $this->getRequest()->getParam('store_id');
		$_order_quote_id = $this->getRequest()->getParam('short_order_id') ;
		$_file_type = $this->getRequest()->getParam('file_type') ;
		$_file_size = $this->getRequest()->getParam('file_size') ;
		$_user_type = $this->getRequest()->getParam('user_type');
		$_item_id = $this->getRequest()->getParam('item_id');;
		$_file_name = $this->getRequest()->getParam('filename');
		$_status = 'New';
		$_comments = '';
		$_post_date =NOW();		
		$_proof_type = $this->getRequest()->getParam('proof_type'); 
		
		
		// echo 'file id='.$_file_id.'<br/>';
		 //exit;
		/*SAVING to tables*/
		
		//$_items_in_order = Mage::getModel('sales/order')->loadByIncrementId($_order_id);			 
		//$_items_obj = $_items_in_order->getAllVisibleItems();	
	    
	    /*
		$_temptableDesign = Mage::getSingleton('core/resource')->getTableName('task_designer');
		/*
		$sqlDesign="INSERT INTO  ".$temptableDesign." SET store_id = '".$store_id."', order_quote_id ='".$order_id."', user_id = '".$user_id."',
		user_type = 'customer', item_id = '".$value."', status ='New', comment = '".$comment[$key]."', proof_type = '".$type."', postdate = NOW() ";
		$chkDesign = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlDesign);
		*/
		
		
		$_connectionWrite->beginTransaction();
		
		$_data = array();
		
		$_data['store_id']= $_store_id;
		$_data['order_quote_id']=$_order_quote_id;
		$_data['file']= $_file_name;
		$_data['user_id']=$_user_id;
		$_data['user_type']=$_user_type;
		$_data['item_id'] = $_item_id;
		$_data['status']=$_status;
		$_data['comment'] = $_comments;
		$_data['file_type'] = $_file_type;
		$_data['file_size'] = $_file_size;
		$_data['comment'] = $_comments;
		$_data['proof_type'] = $_proof_type;
		$_data['postdate']= $_post_date;
		
		$_temptableDesign = Mage::getSingleton('core/resource')->getTableName('task_designer');
		
		/*find if entity id is already exists */
		
		$_entity_id_exists = $this->entry_existsAction($_file_id , $_order_quote_id, $_item_id,$_proof_type);
		
		/*if entity id exists then update either insert new entry */
		
		if($_entity_id_exists){
			
			$_connectionWrite->update($_temptableDesign, $_data,'entity_id='.$_file_id);
		
		}else{
			$_connectionWrite->insert($_temptableDesign, $_data);		
		}
		$_connectionWrite->commit();
		
		$_lastInsertId = $_connectionWrite->fetchOne('SELECT last_insert_id()'); 
		
		return $_lastInsertId.'Data has been written successfully';
		
		//exit;
		
		/*
		$temptableComment=Mage::getSingleton('core/resource')->getTableName('task_designer_comment');
		/*
		$sqlComment="INSERT INTO  ".$temptableComment." SET parent_id = '".$lastInsertId."', comment = '".$comment[$key]."' , user_id = '".$user_id."',
		user_type = 'customer', status ='New', postdate = NOW() ";
		$chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
		*/
		/*
		$connectionWrite->beginTransaction();
		$data = array();
		$data['parent_id']= $lastInsertId;
		$data['comment'] = $comment[$key];
		$data['user_id']=$user_id;
		$data['user_type']='customer';
		$data['status']='New';
		$data['postdate']=NOW();
		$connectionWrite->insert($temptableComment, $data);
		$connectionWrite->commit();
		
		$lastInsertId1 = $connectionWrite->fetchOne('SELECT last_insert_id()'); 
			*/
		
	
	
		} ///end of isset data				
		
	 /*function entry_exists
	 @ params: entity id, order id , item it , user id
	 @ output: bolean true/false	 	 
	 */
	 
	 public function entry_existsAction($_entity_id=0, $_order_id=0, $_item_id=0, $_proof_type=''){
		
		/* if entity_id is not present already*/
		 	
		   if($_entity_id==0 || $_order_id==0 || $_item_id==0 ){ return false;	}
		
		/*select and found if entity id is already exists*/
		
			$_resource = Mage::getSingleton('core/resource');
			$_read= $_resource->getConnection('core_read');
			$_designerTable = $_resource->getTableName('task_designer');
				
			$_select = $_read->select()
			   ->from($_designerTable,array('entity_id','status'))
			   ->where('entity_id=?',$_entity_id)
			   ->where('order_quote_id=?',$_order_id)
			   ->where('item_id=?',$_item_id)
			   ->where('proof_type=?',$_proof_type)
			   ->order('entity_id ASC');			
			
			$_entity_ids = $_read->fetchAll($_select);
			
			$_entity_id_exists = count($_entity_ids); 
			 
		/*check if entity id is exists */
			if($_entity_id_exists !=0){
					return true;
				}else{
					return false;
				}
				 
		} //end of function file_exits
		
		
	/*function approvedesignAction
	 @ params: 
	 @ output: bolean true/false updated or not	 	 
	 */
	 
	 public function approvedesignAction(){
		
		/*taking connection for reading and writing data to tables*/
		$_connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$_connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		//getting post vars		 
		
		$_entity_id = $_POST['entity_id'];
		$_approved_status = $_POST['approved_status'];
		$_item_id = $_POST['item_id'];
		$_order_id = $_POST['order_id'];
		$_signature = $_POST['signature'];
		$_quantity = $_POST['quantity'];
		$_proof_type = 'proof';
		
		
		
		$_data = array('approved_status'=>$_approved_status,
					   'signature'=>$_signature,
					   'quantity'=>$_quantity,
					   'approve_date'=>NOW(),	
						);
		// print_r($_data);				
		$_where = ' item_id ='.$_item_id.' 
		          AND order_quote_id ='.$_order_id.' 
				  AND entity_id='.$_entity_id.' 
				  AND proof_type="'.$_proof_type.'"';
										
		
		$_temptableDesign = Mage::getSingleton('core/resource')->getTableName('task_designer');		
		
		$_update = $_connectionWrite->update($_temptableDesign, $_data, $_where);
		
		print_r($_update);
		 
	 } //end of function update


		/*funciton : uploadImages*/
		
		public function uploadImagesAction(){
			
				
       // Mage::log($_FILES);
	   $_file=array();
	   foreach($_FILES as $fileUplaod){
		   foreach($fileUplaod as $file){
			  	 foreach($file as $key=>$fval){
					 $fileId = $key;					 
					 }				 
			   }
		   }
		    
           	$type = 'fileUpload';
			//$target_path = $_SERVER['DOCUMENT_ROOT'].'/tablethrows/media/' ;
			$target_path = $_SERVER['DOCUMENT_ROOT'].'/media/' ;
		 	$file_path = $_POST['client_files'];
			//$file_path = str_replace('\\','/',$target_path);
			 $abs_target = strpos($file_path, 'stores');
			
		 	$target_path = $target_path.substr($file_path,$abs_target).'/';
			
			
			try{
			
			$file_name = $_FILES[$type]['name'][$fileId];
			//$target_path = Mage::getBaseUrl('media').'upload/';
			  
			//echo'<br/>';
			//echo $_FILES[$type]['tmp_name'][$fileId].'<br/>'. $target_path. $file_name;
			 
			 
			 if($_uploaded = move_uploaded_file($_FILES[$type]['tmp_name'][$fileId],$target_path. $file_name)){
				echo $filename.' has been uploaded to '.$target_path. $file_name; 
				 }else{
					 
				echo $filename.' could not be uploaded to '.$target_path. $file_name; 	 
				 }
				 
		}catch(Exception $e){
			
			  print_r($e);	
			}
           
           // echo $filename;
       
    
				//print_r($_FILES);
				
			
			}//end of function imagefilesupload

	
	}
?>