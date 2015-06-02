<?php
class Artis_Vendor_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/vendor?id=15 
    	 *  or
    	 * http://site.com/vendor/id/15 	
    	 */
    	/* 
		$vendor_id = $this->getRequest()->getParam('id');

  		if($vendor_id != null && $vendor_id != '')	{
			$vendor = Mage::getModel('vendor/vendor')->load($vendor_id)->getData();
		} else {
			$vendor = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($vendor == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$vendorTable = $resource->getTableName('vendor');
			
			$select = $read->select()
			   ->from($vendorTable,array('vendor_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$vendor = $read->fetchRow($select);
		}
		Mage::register('vendor', $vendor);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}