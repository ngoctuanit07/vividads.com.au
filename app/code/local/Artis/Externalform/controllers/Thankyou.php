<?php

class Artis_Externalform_ThankyouController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/externalform?id=15 
    	 *  or
    	 * http://site.com/externalform/id/15 	
    	 */
    	/* 
		$externalform_id = $this->getRequest()->getParam('id');

  		if($externalform_id != null && $externalform_id != '')	{
			$externalform = Mage::getModel('externalform/externalform')->load($externalform_id)->getData();
		} else {
			$externalform = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($externalform == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$externalformTable = $resource->getTableName('externalform');
			
			$select = $read->select()
			   ->from($externalformTable,array('externalform_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$externalform = $read->fetchRow($select);
		}
		Mage::register('externalform', $externalform);
		*/
     echo 'here'; exit;
			
		$this->loadLayout();     
		 $this->renderLayout();
    }
    
     
    
}
