<?php 
/**
/**
 * Webservice main controller
 *
 * @category   ArtworkUploader
 * @package    Upload
 * @author     Ashfaq Ahmed @ Vivid Advertising Australia Development Team <ashfaq@vividads.com.au>
 */
//*indexController*/
		
class ArtworkUploader_Upload_IndexController extends Mage_Core_Controller_Front_Action
		{
 /*following method is act as a default action*/			
  public function indexAction(){			
   
			/*load layout*/
			// $this->loadLayout();			
			//Get current layout state
			 
			$_layout = $this->loadLayout();
			 
			$block = $this->getLayout()->createBlock(
			'Mage_Core_Block_Template',
			'upload',
			array('template' => 'upload/upload.phtml')
			);
			
			$this->getLayout()->getBlock('head')->setTitle($this->__('Artwork Upload'));
			
			$this->getLayout()->getBlock('content')->append($block) ;
			    
			  $this->renderLayout();
			 //  Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());

			
			//  print_r(Mage::getSingleton('core/layout')->getUpdate()->getHandles());
 			 //exit("<br/>Your Layout Path is ".__LINE__." in ".__FILE__);	 	
		}
		
		
 /*upload Artwork for client section */
 
  public function myOrdersAction(){
	  $_layout = $this->loadLayout();
	  $this->renderLayout();
	  
	  }		
	
/*--------------------------------------------------------------------------------*/	
	
	/*fetching orders when sign in from url*/
		
	public function fetchOrderAction(){
		
		/*fetching Order detail from the user*/	
		
		$_post_vars = $this->getRequest()->getPost();		
		
		if(isset($_post_vars) && isset($_post_vars['form_key'])){
			
				$_order_id = $_post_vars['order_id'];		
				$_order_type = $_post_vars['orderType'];			
			}else{
				$_order_id = $_REQUEST['order_id'];		
				$_order_type = $_REQUEST['orderType'];	
				
			}
		
		/*re-direct to the login URL*/
		
		if(!isset($_order_id) or $_order_id==''){
			
			$_path =  "upload/index" ;
			$this->_redirect($_path); 
			
		}else{
		
		
		/* load layout*/
		$_layout = $this->loadLayout();
		$_block = $this->getLayout()->createBlock(
			'Mage_Core_Block_Template',
			'uploadArtworkBlock',
			array('template' => 'upload/order_upload.phtml')
			);
		$this->getLayout()->getBlock('content')->append($_block) ;
		//
		$_block_data = $this->getLayout()->getBlock('uploadArtworkBlock');	
		
		//Zend_debug::dump($_order_id);
		
		//echo $_order_id;
		/*getting order detail*/
		
		if($_order_type=='order_id' || $_order_type=='order'){
		
		    $_orderObj = Mage::getModel('sales/order')->loadByIncrementId($_order_id);
		}
		if($_order_type=='invoice_id'){
		
		    $_orderObj = Mage::getModel('sales/order')->loadByIncrementId($_order_id);
		}
		
		if($_order_type=='quote_id'){		
		   
		   	$_connection = Mage::getSingleton('core/resource')->getConnection('core_read');
			$_sql = "SELECT `quotation_id`  from `quotation` WHERE  `increment_id`= '".$_order_id."'";
			$_quote = $_connection->fetchRow($_sql); //fetchRow($sql), fetchOne($sql),...
			
			$_quote_id = $_quote['quotation_id'];
				
		 //  $_orderObj = Mage::getModel('sales/quote')->loadByIdWithoutStore($_quote_id); 
		    $_orderObj = Mage::getModel('Quotation/Quotation')->load($_quote_id); 
		  
		 // $_items_in_quote = $_orderObj->getItems();
		   
		   	
		}
		
		/*if that is a quote id*/
		
	 if($_order_type=='quote_id'){
			
		
		$_items_obj = $_orderObj->getItems();
		
		$_block_data->assign('order',$_orderObj->getData());
		$_block_data->assign('items',$_items_obj);
		
		
		/*validating, if order id exists*/
		 // print_r(count($_orderObj->getData()));
		}else{
		
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

		}
			/*showing block*/
			
			$this->renderLayout();
		
		}	
		
	} ///end of isset data
	
	
	
	/*--------------------------------------------------------------------------------*/		
	
	/*Writing data from chat file*/
	
	public function savechatAction(){
		 
		 /*Fetching the post variables*/
		 $_entity_id = 	$this->getRequest()->getPost('short_order_id');
		 $_item_id 	 = 	$this->getRequest()->getPost('item_id');
		 $_user_id 	 = 	$this->getRequest()->getPost('user_id');
		 $_file_path = 	$this->getRequest()->getPost('file_path');
		 $_proof_type =  $this->getRequest()->getPost('proof_type');
		 
			 
		 /*update the chat data in the database*/		 
		 $_resource = Mage::getSingleton('core/resource');
		 $_read= $_resource->getConnection('core_read');
		 $_write= $_resource->getConnection('core_write');
		 
		 $_designer_chat_table = $_resource->getTableName('task_designer_chat');			
		 $_select = $_read->select()
			   ->from($_designer_chat_table,array('entity_id','item_id','user_id','client_posted_time','designer_posted_time'))
			   ->where('entity_id=?',$_entity_id)
			   ->where('item_id=?',$_item_id);	
			   		
		 $_chat_status = $_read->fetchAll($_select);
		
		/*update quote status */
		
		if($_proof_type=='quote'){
			
			$tableQuotation = Mage::getSingleton('core/resource')->getTableName('quotation'); 		
			$_connection = $_write->beginTransaction();
        	$_sql_data['message'] = date('d-m-y h:i:s').' GMT New Message';		
							  
			$_where = $_connection->quoteInto('quotation_id=?', $_entity_id);					
			$_update = $_connection->update($tableQuotation, $_sql_data, $_where);		 
			$_write->commit();
			
			}
		
		 
		//print_r($_chat_status);
		/*checking if the chat is being updated by client or designer*/
		 
		 if($_proof_type=='customer'){
			 
			
			 $_designer_posted_status=0;
			 $_client_posted_status=1;
			 $_client_posted_time = NOW();
			 $_designer_posted_time=$_chat_status[0]['designer_posted_time'];
			 
			 
			 }else{
			 $_designer_posted_status=1;
			 $_client_posted_status=0;
			 $_designer_posted_time= NOW();
			 $_client_posted_time=$_chat_status[0]['client_posted_time'];
				 
		  }
		
		//echo 'designer_status = '.$_designer_posted_status;
		//echo '<br/>client status '.$_client_posted_status;
		
		
		 if(count($_chat_status) >0){
			 /*update the chat if exists*/
			 
			$_data = array('designer_posted_status'=>$_designer_posted_status,
						   'client_posted_status'=>$_client_posted_status,
						   'designer_posted_time'=>$_designer_posted_time,
						   'client_posted_time'=>$_client_posted_time,		
							);
			$_where = 'entity_id='.$_entity_id.' AND
					   item_id='.$_item_id.'  ';				
							
			$_data_updatted = $_write->update($_designer_chat_table,$_data,$_where);	
			 
			 }else{
			/*insert new record if dont exists*/
			$_data = array('entity_id'=>$_entity_id,
						   'item_id'=>$_item_id,
						   'user_id'=>$_user_id,
						   'designer_posted_status'=>$_designer_posted_status,
						   'client_posted_status'=>$_client_posted_status,
						   'designer_posted_time'=>$_designer_posted_time,
						   'client_posted_time'=>$_client_posted_time,	
						   'file_path'=>$_file_path,
							);
			$_data_updatted = $_write->insert($_designer_chat_table,$_data);	 
				 
			}
		
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
		
/* send email by template sendTemplateEmail 
	@ function: sendTemplateEmail
	@ params: 
	@ return: emails sent
	# Author: Ashfaq Ahmed	
*/	
    public function sendTemplateEmailAction(){
		///start sending email of chat uplaoded
		
		
		$_data = array();		
		$_admin_enabled = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/enabled');
		$_designer_enabled = Mage::getStoreConfig('artworkuploaderoptions/designeralerts/enabled');
		$_client_enabled = Mage::getStoreConfig('artworkuploaderoptions/clientalerts/enabled');
		
	if($_admin_enabled==1){
		/*getting template ....*/		
		$_data['chat_data'] = $_POST['chat_data'];
		$_data['customer_id'] = $_POST['customer_id'];
		$_data['user_type'] = $_POST['user_type'];
		$_data['message_type'] = $_POST['message_type'];
		$_data['store_id'] = $_POST['store_id'];
		
		$_message_type = $_data['message_type'];
		
	/* Sending emails either by client side or designer side*/	
	/*Client Side */
	
	if($_data['user_type']=='customer'){
		
		switch($_message_type){
			/*if message is sent from the client new message then 
			tempalte will be New Message from Client*/
			
			case 'client_message':
			$_template_id = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/template_new_message_client');		
		/*sending emails from template*/		
		$_email = $this->sendEmailAction($_template_id, $_data);
			break;
			case 'designer_message':
			$_template_id = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/template_new_message_designer');		
		/*sending emails from template*/		
		$_email = $this->sendEmailAction($_template_id, $_data);
			break;
			case 'client_artwork_uploaded':
			$_template_id = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/template_file');		
		/*sending emails from template*/		
		$_email = $this->sendEmailAction($_template_id, $_data);
			break;
			case 'designer_proof_uploaded':
			$_template_id = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/template_proof');		
		/*sending emails from template*/		
		$_email = $this->sendEmailAction($_template_id, $_data);
			break;
			
			case 'client_approved_proof':
			$_template_id = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/template_proof_approved');		
		/*sending emails from template*/		
		$_email = $this->sendEmailAction($_template_id, $_data);
			break;
			case 'client_rejected_proof':
			$_template_id = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/template_proof_rejected');		
		/*sending emails from template*/		
		$_email = $this->sendEmailAction($_template_id, $_data);
			break;
			
			
			} //end of switch statement
				
				
				}  else{				
		
		switch($_message_type){
			/*if message is sent from the client new message then 
			tempalte will be New Message from Client*/
			
			case 'client_message':
			$_template_id = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/template_new_message_client');		
		/*sending emails from template*/		
		$_email = $this->sendEmailAction($_template_id, $_data);
			break;
			case 'designer_message':
			$_template_id = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/template_new_message_designer');		
		/*sending emails from template*/		
		$_email = $this->sendEmailAction($_template_id, $_data);
			break;
			case 'client_artwork_uploaded':
			$_template_id = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/template_file');		
		/*sending emails from template*/		
		$_email = $this->sendEmailAction($_template_id, $_data);
			break;
			case 'designer_proof_uploaded':
			$_template_id = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/template_proof');		
		/*sending emails from template*/		
		$_email = $this->sendEmailAction($_template_id, $_data);
			break;
			
			case 'client_approved_proof':
			$_template_id = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/template_proof_approved');		
		/*sending emails from template*/		
		$_email = $this->sendEmailAction($_template_id, $_data);
			break;
			case 'client_rejected_proof':
			$_template_id = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/template_proof_rejected');		
		/*sending emails from template*/		
		$_email = $this->sendEmailAction($_template_id, $_data);
			break;
			
			
			}//end of switch statement	
			
	 } 
		
 }else{
			return false;
			}///end of if enabled
		/*end of sending emails*/
}  //end of function	
	

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
		
/*Reading chat status from chat file*/
   
	public function getChatStatusAction(){
		 
		 /*Fetching the post variables*/
		 $_entity_id = 	$this->getRequest()->getPost('short_order_id');
		 $_item_id 	 = 	$this->getRequest()->getPost('item_id');
		 $_user_id 	 = 	$this->getRequest()->getPost('user_id');
		 $_file_path = 	$this->getRequest()->getPost('file_path');
		 $_proof_type =  $this->getRequest()->getPost('proof_type');
		 
			 
		 /*update the chat data in the database*/		 
		 $_resource = Mage::getSingleton('core/resource');
		 $_read= $_resource->getConnection('core_read');
		 $_write= $_resource->getConnection('core_write');
		 
		 $_designer_chat_table = $_resource->getTableName('task_designer_chat');			
		
		
		 $_select = $_read->select()
			   ->from($_designer_chat_table,array('entity_id','item_id','user_id','designer_posted_status','client_posted_status','client_posted_time','designer_posted_time'))->where('item_id=?',$_item_id)
			   ;	
		
		 $_where = 'entity_id='.$_entity_id.' AND item_id'.$_item_id;	   		
		 $_chat_status = $_read->fetchAll($_select) ;
		 
		 $_designer_posted = $_chat_status[0]['designer_posted_status'];
		 $_client_posted = $_chat_status[0]['client_posted_status'];		 
		
		// echo '$_designer_posted'.$_designer_posted;
		// exit;
		 $_post_status = 0;
		 
		 if($_designer_posted==1){			 
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
		 }else{
			  echo $_post_status ;
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
		$_order_id = $this->getRequest()->getParam('order_id');
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
		
		
		
		//$target_path = $_SERVER['DOCUMENT_ROOT'].'/tablethrows/media/' ;
			$target_path = $_SERVER['DOCUMENT_ROOT'].'/media/' ;
		 	$file_path = $_POST['client_files'];
			//$file_path = str_replace('\\','/',$target_path);
			$abs_target = strpos($file_path, 'stores');
			
		 	$target_path = $target_path.substr($file_path,$abs_target).'/';
		
			//check if the file exists
			
			if(file_exists($target_path.$_file_name)){
				 
				 $_file_ext = strpos($_file_name,'.');
				 $_file_part_1 =substr($_file_name, 0,$_file_ext); 
				 $_file_part_2 =substr($_file_name, $_file_ext);
				 
				 $_file_name = $_file_part_1.'-1'.$_file_part_2;
				 
			 }
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
		$_data['increment_id']=$_order_id;
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
		
	 
	 
	 /*function sendEmail
	 @ params: entity id, order id , item it , user id
	 @ output: bolean true/false	 	 
	 */
	 
	 public function sendEmailAction($_template_id='', $_data=array()){
		 
		 /*if there is no template then it will return false;*/
		 if($_template_id==''){return false;}
		 /*if $_data variable has nothing do then it will return false;*/
		 if(count($_data)<1){return false;}
		
			
		
		/*loading order detail*/
		$_order = Mage::getModel('sales/order')->load($_data['order_id']);	
		/*loading customer detail*/
		$_customer = Mage::getModel('customer/customer')->load($_data['customer_id']);		
		$_mailTemplate = Mage::getModel('core/email_template');
		       
	   /* @var $mailTemplate Mage_Core_Model_Email_Template */
             
        $_translate  = Mage::getSingleton('core/translate');
		
		//$_templateId = 33; //template for sending customer data
        $_template_collection =  $_mailTemplate->load($_template_id);                               
        $_template_data = $_template_collection->getData();
		
		 
		/*checking if the tempalte data is empty or not */
		
		if(!empty($_template_data))
                    {
                        $_templateId = $_template_data['template_id'];
                        $_mailSubject = $_template_data['template_subject'];                         
                         
                        //fetch sender data from Adminend > System > Configuration > Store Email Addresses > General Contact
                        $_from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email
                        $_from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name
                 
                        $_sender = array('name'  => $_from_name,
                                        'email' => $_from_email);                                
                         
          // $_vars = array('customer'=>$_customer); 
		  //for replacing the variables in email with data         
		$_vars = array();
		$_vars['myOrderId']		= 	$_POST['order_id']; 
		$_vars['customerName']	=	$_customer->getName(); 
		$_vars['messageChat']	=	$_data['chat_data'];
		
		$_customer_email = $_customer->getEmail();
		$_auto_login_link = Mage::getSingleton('customer/session');	
		//$_customer_account = Mage::getModel('customer/account');		
		
		//print_r($_customer_account);
        
		//exit;               
						
		/*This is optional*/
        $_storeId = $this->getRequest()->getPost('store_id');
        $_model = $_mailTemplate->setReplyTo($_sender['email'])->setTemplateSubject($_mailSubject);
   
   /*default email*/
   
    $_email[] = $_customer->getEmail(); //'sweetashfaq@gmail.com';
   
    /*if admin module is enabled*/
	$_admin_enabled = $_copy_admin_emails = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/enabled');
	$_designer_enabled = $_copy_admin_emails = Mage::getStoreConfig('artworkuploaderoptions/designeralerts/enabled');
	$_client_enabled = $_copy_admin_emails = Mage::getStoreConfig('artworkuploaderoptions/clientalerts/enabled');
						
	// if admin copy to email is enabled
				
	 if($_admin_enabled==1){
		////sending copy to emails 
		$_copy_admin_emails = Mage::getStoreConfig('artworkuploaderoptions/adminalerts/copy_to');
		/*copy to admin side is not empty*/
		if($_copy_admin_emails !=''){
		$_copy_emails = explode(',',$_copy_admin_emails);
		///including bbc copy emails ///
		foreach($_copy_emails as $_copy_email){
		 $_email[] = $_copy_email;
			}	
		} //end of if statement
	 }///end of admin enabled
					  
		// if designer copy to email is enabled
				
	 if($_designer_enabled==1){
		////sending copy to emails 
		$_copy_designer_emails = Mage::getStoreConfig('artworkuploaderoptions/designeralerts/copy_to');
		/*copy to admin side is not empty*/
		if($_copy_designer_emails !=''){
		$_copy_emails = explode(',',$_copy_designer_emails);
		///including bbc copy emails ///
		foreach($_copy_emails as $_copy_email){
		 $_email[] = $_copy_email;
			}	
		} //end of if statement
	 }///end of admin enabled
	
	// if client copy to email is enabled
				
	 if($_client_enabled==1){
		////sending copy to emails 
		$_copy_client_emails = Mage::getStoreConfig('artworkuploaderoptions/clientalerts/copy_to');
		/*copy to admin side is not empty*/
		if($_copy_client_emails !=''){
		$_copy_emails = explode(',',$_copy_client_emails);
		///including bbc copy emails ///
		foreach($_copy_emails as $_copy_email){
		 $_email[] = $_copy_email;
			}	
		} //end of if statement
	 }///end of admin enabled				   
	
	////////////////////////////////////////////////
	/*getting admin group*/
	
	$_users = Mage::getModel('admin/user')->getCollection();
	
	foreach($_users as $_user){
		
		$_user = Mage::getModel('admin/user')->load($_user['user_id']);
		//$_user = $_user->getData();
		 $_user_roles = $_user->getRole()->getData();
		 
		 if($_user_roles['role_name']=='Designer'){			 
			 $_email[] = $_user['email'];		 
			 } 
		 if($_user_roles=='Administrators'){
			 $_email[] = $_user['email'];
			 }	 
		
		}
		//print_r($_email);
		$_sender_email = $_email[0];		
		$_name = $_customer->getName();
		$rem_email = array_shift($_email);
		
		$_model->addBcc($_email);
		
		//var_dump($_storeId);
		 		                                          
        $_send_model =  $_model->sendTransactional($_templateId, $_sender, $_sender_email, $_name, $_vars, $_storeId);
	 //	Zend_debug::dump($_email);
		// Zend_debug::dump($_model);
         
		 if (!$_mailTemplate->getSentSuccess()) {
                 throw new Exception();
             }
           $_translate_email =  $_translate->setTranslateInline(true);
         }
	
	} //end of function 
	 
	 
	 
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
		$_temptableDesign = Mage::getSingleton('core/resource')->getTableName('task_designer');	
		$_proof_table = Mage::getSingleton('core/resource')->getTableName('proofs');	
		$_vendor_item_table = Mage::getSingleton('core/resource')->getTableName('vendor_item');	
		
		//getting post vars		 
		
		$_entity_id 		= $_POST['entity_id'];
		$_approved_status 	= $_POST['approved_status'];
		$_item_id 			= $_POST['item_id'];
		$_order_id 			= $_POST['order_id'];
		$_signature 		= $_POST['signature'];
		$_quantity 			= $_POST['quantity'];
		$_proof_type_orign	= 'proof';
		$_total_ordered_qty = $_POST['total_ordered_quantity'];
		$_reason_txt 		= $_POST['reason_txt'];
		$_store_id 			= $_POST['store_id'];
		$_customer_id 		= $_POST['customer_id'];
		$_file 				= $_POST['file'];
		$_status			= $_POST['status'];
		$_proof_type		= $_POST['proof_type'];
		
		
		$_data = array('approved_status'=>$_approved_status,
					   'signature'=>$_signature,					   
					   'quantity'=>$_quantity,
					   'reason'=>$_reason_txt,
					   'total_ordered_qty'=>$_total_ordered_qty,
					   'approve_date'=>NOW(),	
						);
		//print_r($_data);	
		$_where = ' item_id ='.$_item_id.' 
		          AND order_quote_id ='.$_order_id.' 
				  AND entity_id='.$_entity_id.' 
				  AND proof_type="'.$_proof_type_orign.'"';
										
		/*checking before the following if design is going to approve
		
			* If Payment is being done
			* File is being seen
			* Vendor is alloted
		*/
				
		$_update = $_connectionWrite->update($_temptableDesign, $_data, $_where);
		/*defining data array */	
		
		$_proof_data = array('store_id'=>$_store_id,
							 'order_id'=>$_order_id,							 
							 'customer_id'=>$_customer_id,
							 'item_id'=>$_item_id,
							 'file'=>$_file,
							 'quantity'=>$_quantity,
							 'status'=>$_status,
							 'comment'=>'',
							 'postdate'=>NOW(),
							 'approve_date'=>NOW(),
							 'proof_type'=>$_proof_type,
							 'artwork'=>'',);
		
		/*check if the entry is already exists diapprove the proof*/
		
		$_proof_sql = $_connectionRead->select()
								  ->from($_proof_table,array('task_designer_id','status'))
								  ->where('task_designer_id= "'.$_entity_id.'" ');						
		$chkProof = $_connectionRead->fetchOne($_proof_sql);
		
		
		
		if($chkProof){
			$_where = ' task_designer_id ='.$_entity_id;		          
			
			$_connectionWrite->beginTransaction();
			$_update = $_connectionWrite->update($_proof_table, $_proof_data, $_where);
			$_connectionWrite->commit();
			//var_dump($_update); exit;
			
			if($_approved_status == 1)
					{
						
				$temptableVendorItem = Mage::getSingleton('core/resource')->getTableName('vendor_item');
				
				$_connectionWrite->beginTransaction();
				$data2 = array();
				$data2['file_recieved'] = 'no';
				$data2['proof_approved'] = 'no';
				$data2['proof_approve_date'] = Now();
				$data2['qty'] = $_data['quantity'];
				
				$_where = ' task_designer_id='.$_entity_id;
				//var_dump($_where); exit;
				$_connectionWrite->update($temptableVendorItem, $data2,$_where); //,$_where);
				$_connectionWrite->commit();
							    
			//	Mage::getModel('timeline/timeline')->UpdateTimeline('artwork_upload',$_data['order_id'],$item_1,'order');
			//	Mage::getModel('timeline/timeline')->UpdateTimeline('proof_approve',$_data['order_id'],$item_1,'order');
			//	Mage::getModel('timeline/timeline')->UpdateTimeline('production_start',$_data['order_id'],$item_1,'order');
					
				}
				
			if($_approved_status == 2)
					{
						
				$temptableVendorItem = Mage::getSingleton('core/resource')->getTableName('vendor_item');
				
				$_connectionWrite->beginTransaction();
				$data2 = array();
				$data2['file_recieved'] = 'yes';
				$data2['proof_approved'] = 'yes';
				$data2['proof_approve_date'] = Now();
				$data2['qty'] = $_data['quantity'];
				
				
				$_where = ' task_designer_id='.$_entity_id;
				//var_dump($_where); exit;
				$_connectionWrite->update($temptableVendorItem, $data2,$_where); //,$_where);
				$_connectionWrite->commit();
							    
			//	Mage::getModel('timeline/timeline')->UpdateTimeline('artwork_upload',$_data['order_id'],$item_1,'order');
			//	Mage::getModel('timeline/timeline')->UpdateTimeline('proof_approve',$_data['order_id'],$item_1,'order');
			//	Mage::getModel('timeline/timeline')->UpdateTimeline('production_start',$_data['order_id'],$item_1,'order');
					
				}	
			
			
		}else{
			/*inserting data to proof table */
			$_proof_data['task_designer_id']=$_entity_id;
			$_proof_result = $this->insertProofsAction('proofs', $_proof_data, $_approved_status );
		}
		/*
		$_vendor_data = array('proof_id'=>$_proof_id,
							  'approved_status'=>$_approved_status,
					   		  'quantity'=>$_quantity,
					   		  'total_ordered_qty'=>$_total_ordered_qty,
					   		  'approve_date'=>NOW(),	
							);		
							*/	
		
		//$vendor_alloted = $this->updateVendorItems($_order_id , $_item_id, $_vendor_data);
		
		//print_r($vendor_alloted);
		//print_r($_update);
		 
	 } //end of function update


		/*funciton : uploadImages*/
		
		public function uploadImagesAction(){
			
					   
	   // Mage::log($_FILES);
	   $_file=array();	  
	   if($this->getRequest()->getPost('heigh_res_file')==null){
	   foreach($_FILES as $fileUplaod){
		   foreach($fileUplaod as $file){			   
			    foreach($file as $key=>$fval){
					  $fileId = $key;					 
					 } 
				} 
		    }
	   }else{		   
		   foreach($_FILES as $fileUplaod){
		   $_filename = $fileUplaod['name'];
		   $_filetmp = $fileUplaod['tmp_name'];
		   }
	   }
		    
	 if($this->getRequest()->getPost('heigh_res_file')==null){
           	  $type = 'fileUpload';
		}else{
				$type = 'heigh_file';
		}
			//$target_path = $_SERVER['DOCUMENT_ROOT'].'/tablethrows/media/' ;
			//$target_path = $_SERVER['DOCUMENT_ROOT'].'/media/' ;
		 	$target_path = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/' ;		 	
			$file_path = $_POST['client_files'];			
			//$file_path = str_replace('\\','/',$target_path);
			$abs_target = strpos($file_path, 'stores');
			$target_path = $target_path.substr($file_path,$abs_target).'/';	
		try{			
			if($this->getRequest()->getPost('heigh_res_file')==null){
			$file_name = $_FILES[$type]['name'][$fileId];
			}else{
			$file_name = $_filename;
			$target_path = str_replace('print_ready_approved_files','draft_files',$target_path);			
		//	$target_path = str_replace($target_path, );
			}
			
			//echo $target_path;exit;
			//$target_path = Mage::getBaseUrl('media').'upload/';				  
			$_cur_files = scandir($target_path,0);			 
			 /*total current files*/
			 
			 if(count($_cur_files) >0 ){			 
			 
				 if(file_exists($target_path.$file_name)){				 
					 $_file_ext = strpos($file_name,'.');
					 $_file_part_1 =substr($file_name, 0,$_file_ext); 
					 $_file_part_2 =substr($file_name, $_file_ext);								
					 $file_name = $_file_part_1.'-Copy'.$_file_part_2;				 
					}
				
			 }
			 
			// $filename = $this->fileNameExists($target_path, $file_name);			 
			 
		 if($this->getRequest()->getPost('heigh_res_file')==null){		 
			 if($_uploaded = move_uploaded_file($_FILES[$type]['tmp_name'][$fileId],$target_path. $file_name)){
					echo $filename.' has been uploaded to '.$target_path. $file_name; 
				 	}else{					 
					echo $filename.' could not be uploaded to '.$target_path. $file_name; 	 
				 }
			 }else{
				 
				if($_uploaded = move_uploaded_file($_filetmp,$target_path. $file_name)){
					echo $filename.' has been uploaded to '.$target_path. $file_name; 
				 	}else{					 
					echo $filename.' could not be uploaded to '.$target_path. $file_name; 	 
				 	} 
				 }
				}catch(Exception $e){			
			  			print_r($e);	
			}           
           // echo $filename;       
    		//print_r($_FILES);				
			
			}//end of function imagefilesupload

	  /*function to sent data in proofs table
		 @ update proofs table 
		 @ Table Name: Proofs
		*/
		
		public function insertProofsAction($_table_name='', $_data='', $_approve_status=0 ){
			
		//* first check if tablename and data array is empty*/
			
		if($_table_name =='') return false;
		if(!is_array($_data)) return false;
		
		/*taking connection for reading and writing data to tables*/
		$_connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$_connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		///inserting data to proofs table
		$_proof_table = Mage::getSingleton('core/resource')->getTableName($_table_name);
		
		
		$_connectionWrite->beginTransaction();
		$_insert_proof = $_connectionWrite->insert($_proof_table, $_data);
		$_connectionWrite->commit();
		
		
		//Start 13_03_2014
		$lastInsertId  = $_connectionWrite->fetchOne('SELECT last_insert_id()'); 
		
		$order = Mage::getModel('sales/order')->load($_data['order_id']);
		$items = $order->getAllItems();
		
		
	
		/* adding items to vendor table */
		
		foreach ($items as $item1) {
		   //  echo 'sdgfdsg';exit;
		    if($item1->getId() == $_data['item_id'])
		    {
				
				//  echo 'app'.$_approve_status;
				if($_approve_status == 2 || $_approve_status == 1 )
					{
						$temptableVendor=Mage::getSingleton('core/resource')->getTableName('vendor_product');										
						$_product = Mage::getModel('catalog/product')->load($item1->getProductId());		    
						
						$sqlVendor= $_connectionRead->select()
								  ->from($temptableVendor,array('*'))
								  ->where('item_id= "'.$item1->getId().'" AND order_id = "'.$order->getId().'" ');						
						$chkVendor = $_connectionRead->fetchRow($sqlVendor);				
				
			 //	 var_dump($chkVendor); // exit;
				$temptableVendorItem = Mage::getSingleton('core/resource')->getTableName('vendor_item');
				
				if($chkVendor['vendor_id']!=''){
					$_user = $chkVendor['vendor_id'];
					$_vendor_item_id = $chkVendor['entity_id'];
				
				//exit;
				
				
				$_connectionWrite->beginTransaction();
				$data2 = array();
				
				if($_approve_status == 2  ){
				
				$data2['file_recieved'] = 'yes';
				$data2['proof_approved'] = 'yes';
				$data2['proof_approve_date'] = Now();
				$data2['qty'] = $_data['quantity'];
				}else{
					
				$data2['file_recieved'] = 'no';
				$data2['proof_approved'] = 'no';
				$data2['proof_approve_date'] = Now();
				$data2['qty'] = $_data['quantity'];	
					}
				
				
				$data2['task_designer_id']=$_data['task_designer_id'];				
				$data2['target_user']= $_user;
				$data2['proof_id'] = $lastInsertId;
				$data2['item_id'] = $item1->getId();
				$data2['order_id'] = $order->getId();
				$data2['product_id'] = $item1->getProductId();
				$data2['product_sku'] = addslashes($_product->getSku());
				$data2['file_name'] = $_POST['file_path'];	
				$data2['postdate'] = NOW();
				$data2['order_status'] = $order->getStatus();
				$data2['priority'] = $chkVendor['priority'];
				
				$_where = ' entity_id='.$_vendor_item_id.' ';
				$_connectionWrite->insert($temptableVendorItem, $data2); //,$_where);
				$_connectionWrite->commit();
							    
				Mage::getModel('timeline/timeline')->UpdateTimeline('artwork_upload',$_data['order_id'],$item1,'order');
				Mage::getModel('timeline/timeline')->UpdateTimeline('proof_approve',$_data['order_id'],$item1,'order');
				Mage::getModel('timeline/timeline')->UpdateTimeline('production_start',$_data['order_id'],$item1,'order');
					}
				}
				
		    }
		}
		
		
		
		//End 13_03_2014
			
	   // print_r($_insert_proof);
			
	}//end of function proofsUpdateAction
		
		
		/*function create a copy if exists
		 @ Function Name: fileNameExists()
		 @ Param: directory String, filename
		 @ return: newfilename
		 	
		*/
		
		
		public function fileNameExists($_dir='', $_file=''){
			
			/* if no directory name or file */
			
			if($_dir ==''){return false;}
			if($_file ==''){return false;}
			
			$_file_name = '';
			
			/*scanning directoy*/
			$_files = array();
			$_files = scandir($_dir);
			
			if( count($_files) > 0){
				
				foreach($_files as $_file){
					
						print_r($_file);
					}
				
				}
			
			return $_file_name;
			
			} //end of function fileNameExists
		
		/*
			function downloadFileAction
			@output File Download;
		*/
		
		
		public function downloadFileAction(){
			
			$_file = 'http://tablethrows.com.au'.$_REQUEST['dfile'];
			$_file_name = basename($_file);
			$_path = substr($_file, 0, strpos($_file, $_file_name));
			
			$_ext = substr($_file_name,strpos($_file_name,'.')+1,strlen($_file_name));			
			
						
			//if (file_exists($_file)) {	
				switch($_ext){
					case 'jpg':
					header("Content-Type:image/jpg");
					break;
					case 'png':
					header("Content-Type:image/png");
					break;				
					case 'gif':
					header("Content-Type:image/gif");
					break;
					case 'eps':
					header("Content-Type:image/eps");
					break;
					default:
					header("Content-Type:image/jpg");
					break;
				}
				
				header("Content-Disposition: attachment; filename=".$_file_name);
				header("Cache-control: private");
				header('X-Sendfile: '.$_path.$_file_name);
				readfile($_path.$_file_name);
				exit;
				//}
			}
			
			
		/*
			function update the vendor
			@ function name updateVendorItems
			@ output: Vendor updated Bolean True or False;
		*/	
		
		public function updateVendorItems($_order_id = 0, $_item_id=0, $_post_data=array()){
			
			// check the order id 
			if ($_order_id == 0) return false;
			if ($_item_id == 0) return false;
			
			/* loading the order and update the vendor item */
				/*taking connection for reading and writing data to tables*/
			$_connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
			$_connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
			$_vendorItemTable = Mage::getSingleton('core/resource')->getTableName('vendor_item');	
			
			/*select if there vendor is alloted or not*/
			try{
				$_where = ' item_id ='.$_item_id.' AND order_id ='.$_order_id ;				  
				$_current_item_sql = $_connectionRead->select()
											 ->from($_vendorItemTable,array('entity_id'))
											 ->where($_where);
				$_current_item = $_connectionRead->fetchRow($_current_item_sql);			
				//	Zend_debug::dump($_current_item);
			
				}catch(Exception $error)
				      {
						  	print_r($error);
					  }
			
			/*update the Vendor Items*/
			
			if($_current_item){
				
				
				$_data = array('proof_id'=>$_post_data['proof_id'],
							   'qty'=>$_post_data['quantity'],
							   'progress'=>'Processing',
							   'proof_approve_date'=>$_post_data['approve_date'],
							   'file_recieved'=>'yes',
							   'proof_approved'=>'yes',
							   'payment_recieved'=>'yes',
							   'file_path'=>$_POST['file_path'],
							   'order_status'=>'progress',
								);
				$_connectionWrite->beginTransaction();				
				$updateSql = $_connectionWrite->update($_vendorItemTable, $_data, $_where);
				$_connectionWrite->commit();
				
				return true;
				
				}else{
			
					return false;
					
			}			
						
				
		} 
		//end of function vendorUpload
		
		
	  
	}
?>