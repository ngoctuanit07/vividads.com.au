<?php
class Manageprice_Putrange_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/putrange?id=15 
    	 *  or
    	 * http://site.com/putrange/id/15 	
    	 */
    	/* 
		$putrange_id = $this->getRequest()->getParam('id');

  		if($putrange_id != null && $putrange_id != '')	{
			$putrange = Mage::getModel('putrange/putrange')->load($putrange_id)->getData();
		} else {
			$putrange = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($putrange == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$putrangeTable = $resource->getTableName('putrange');
			
			$select = $read->select()
			   ->from($putrangeTable,array('putrange_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$putrange = $read->fetchRow($select);
		}
		Mage::register('putrange', $putrange);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}