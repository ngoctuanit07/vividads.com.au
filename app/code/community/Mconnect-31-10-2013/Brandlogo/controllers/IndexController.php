<?php
class Mconnect_Brandlogo_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/brandlogo?id=15 
    	 *  or
    	 * http://site.com/brandlogo/id/15 	
    	 */
    	/* 
		$brandlogo_id = $this->getRequest()->getParam('id');

  		if($brandlogo_id != null && $brandlogo_id != '')	{
			$brandlogo = Mage::getModel('brandlogo/brandlogo')->load($brandlogo_id)->getData();
		} else {
			$brandlogo = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($brandlogo == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$brandlogoTable = $resource->getTableName('brandlogo');
			
			$select = $read->select()
			   ->from($brandlogoTable,array('brandlogo_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$brandlogo = $read->fetchRow($select);
		}
		Mage::register('brandlogo', $brandlogo);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}