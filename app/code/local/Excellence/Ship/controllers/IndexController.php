<?php
class Excellence_Ship_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/ship?id=15 
    	 *  or
    	 * http://site.com/ship/id/15 	
    	 */
    	/* 
		$ship_id = $this->getRequest()->getParam('id');

  		if($ship_id != null && $ship_id != '')	{
			$ship = Mage::getModel('ship/ship')->load($ship_id)->getData();
		} else {
			$ship = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($ship == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$shipTable = $resource->getTableName('ship');
			
			$select = $read->select()
			   ->from($shipTable,array('ship_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$ship = $read->fetchRow($select);
		}
		Mage::register('ship', $ship);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}