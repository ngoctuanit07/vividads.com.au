<?php
class Artis_Timeline_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/timeline?id=15 
    	 *  or
    	 * http://site.com/timeline/id/15 	
    	 */
    	/* 
		$timeline_id = $this->getRequest()->getParam('id');

  		if($timeline_id != null && $timeline_id != '')	{
			$timeline = Mage::getModel('timeline/timeline')->load($timeline_id)->getData();
		} else {
			$timeline = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($timeline == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$timelineTable = $resource->getTableName('timeline');
			
			$select = $read->select()
			   ->from($timelineTable,array('timeline_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$timeline = $read->fetchRow($select);
		}
		Mage::register('timeline', $timeline);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}