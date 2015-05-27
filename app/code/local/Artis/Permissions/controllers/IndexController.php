<?php
class Artis_Permissions_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/permissions?id=15 
    	 *  or
    	 * http://site.com/permissions/id/15 	
    	 */
    	/* 
		$permissions_id = $this->getRequest()->getParam('id');

  		if($permissions_id != null && $permissions_id != '')	{
			$permissions = Mage::getModel('permissions/permissions')->load($permissions_id)->getData();
		} else {
			$permissions = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($permissions == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$permissionsTable = $resource->getTableName('permissions');
			
			$select = $read->select()
			   ->from($permissionsTable,array('permissions_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$permissions = $read->fetchRow($select);
		}
		Mage::register('permissions', $permissions);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}