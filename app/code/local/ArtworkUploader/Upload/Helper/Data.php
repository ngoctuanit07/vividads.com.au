<?php

class ArtworkUploader_Upload_Helper_Data extends Mage_Core_Helper_Abstract
{
		
		
		/**
     * get already updated proof files from 
     *
     * @return resultset for proof files
     */
    public function getProof_files($_order_id=0, $_item_id=0 )
    {
        //echo 'here';
		
		$_resource = Mage::getSingleton('core/resource');
			$_read= $_resource->getConnection('core_read');
			$_designerTable = $_resource->getTableName('task_designer');
			
			$_select = $_read->select()
			   ->from($_designerTable,array('entity_id','file','file_type','file_size','postdate','status','approved_status','quantity'))
			   ->where('proof_type=?','proof')
			   ->where('order_quote_id=?',$_order_id)
			   ->where('item_id=?',$_item_id)
			   ->order('entity_id ASC');			
			$_proof_files = $_read->fetchAll($_select);
			
		/*printing the query */			
			//echo $_select->__toString();
		
		/*registering the */
		
		// Mage::register('proof_files', $_proof_files);
		
		return $_proof_files;
    }
	
/**
     * get already updated client files from 
     *
     * @return resultset for proof files
     */
    public function getCustomer_files($_order_id=0, $_item_id=0, $_proof_type='customer')
    {
        $_resource = Mage::getSingleton('core/resource');
			$_read= $_resource->getConnection('core_read');
			$_designerTable = $_resource->getTableName('task_designer');
			
			
			
			
			$_select = $_read->select()
			   ->from($_designerTable,array('entity_id','file','file_type','file_size','postdate','status','approve_date'))
			   ->where('proof_type=?',$_proof_type)
			   ->where('order_quote_id=?',$_order_id)
			   ->where('item_id=?',$_item_id)			   
			   ->order('entity_id ASC');			
			$_proof_files = $_read->fetchAll($_select);
			
		/*printing the query */			
			// echo $_select->__toString();
			 // exit;
		
		/*registering the */
		
		//Mage::register('proof_files', $_proof_files);
		
		return $_proof_files;
    }	
	
	
	/**
     * get already approved files client files from 
     *
     * @return resultset for Approved files
     */
    public function getApproved_files($_order_id=0, $_item_id=0)
    {
        $_resource = Mage::getSingleton('core/resource');
			$_read= $_resource->getConnection('core_read');
			$_designerTable = $_resource->getTableName('task_designer');
			
			$_select = $_read->select()
			   ->from($_designerTable,array('entity_id','file','file_type','file_size','postdate','status','approve_date','approved_status','quantity','signature','reason','total_ordered_qty'))
			   ->where('proof_type=?','proof')
			   ->where('order_quote_id=?',$_order_id)
			   ->where('item_id=?',$_item_id)			   
			   ->order('entity_id ASC');			
			$_approved_files = $_read->fetchAll($_select);			
		/*printing the query */			
			//echo $_select->__toString();		
		/*registering the */		
		//Mage::register('approved_files', $_approved_files);		
		return $_approved_files;
    }	

	
		
}