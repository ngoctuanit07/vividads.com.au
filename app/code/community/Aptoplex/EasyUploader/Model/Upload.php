<?php
/**
 * Class Aptoplex_EasyUploader_Model_Upload
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex       
 */
class Aptoplex_EasyUploader_Model_Upload extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('aptoplex_easyuploader/upload');
    }

    protected function _beforeSave() {
        parent::_beforeSave();
        return $this; 
    }
	
	
	
	public function addFiletoSystem( $filedata = array() ){
		 
		Zend_debug::dump($filedata);
		exit;
		
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
		$_connectionWrite->insert($_temptableDesign, $_data);		
		$_connectionWrite->commit();		
		
		return $_lastInsertId.'Data has been written successfully';
	
	} ///end of isset data	
	
	
	public function addMessages($params = array()){
		
		 
		$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		$tableHistory = Mage::getSingleton('core/resource')->getTableName('quotation_history');       
        
        $comment = 'A new file '.$params['new_filename'].' is uploaded by the client ';
		
		$orderobj = Mage::getModel('sales/order')->loadByIncrementId($params['order_id']);
		
		if( count($orderobj->getData()) <= 0 ){
				$orderobj = Mage::getModel('Quotation/Quotation')->getCollection()
										->addFieldToFilter('increment_id',$params['order_id'])
				            ;
			 	//Zend_debug::dump($orderobj->getData());			
			    $quote = $orderobj->getData();
				$data = array();
				$data['qh_quotation_id']= $quote[0]['quotation_id'];
				$data['qh_message']=$comment;
				$data['qh_date']= NOW();
				$data['qh_user']= 'customer';
				$data['qh_readstatus']= 1;
				$data['is_customer_notified']= 0;
				$data['is_visible_on_front'] = '1';
				$data['status']='sent';
				$data['entity_name'] = 'quote';				
				//Zend_debug::dump($data);
				$result = $connectionWrite->insert($tableHistory, $data);   
				return true;
		 
		}else{
			///if this is order then add item to order ///
			
				$tableHistory = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
				$order_id = $orderobj->getOrder_id();
				$data = array();
				$data['parent_id']= $order_id;
				$data['comment']='CUSTOMER -'.$comment;
				$data['created_at']= NOW();
				$data['entity_name']= 'order';
				$data['readstatus']= 1;
				
				Zend_debug::dump($data);				
				$result = $connectionWrite->insert($tableHistory, $data);
        
			return true;
			}
		
		
		}
	
}