<?php
class Artis_Eventcalendar_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/eventcalendar?id=15 
    	 *  or
    	 * http://site.com/eventcalendar/id/15 	
    	 */
    	/* 
		$eventcalendar_id = $this->getRequest()->getParam('id');

  		if($eventcalendar_id != null && $eventcalendar_id != '')	{
			$eventcalendar = Mage::getModel('eventcalendar/eventcalendar')->load($eventcalendar_id)->getData();
		} else {
			$eventcalendar = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($eventcalendar == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$eventcalendarTable = $resource->getTableName('eventcalendar');
			
			$select = $read->select()
			   ->from($eventcalendarTable,array('eventcalendar_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$eventcalendar = $read->fetchRow($select);
		}
		Mage::register('eventcalendar', $eventcalendar);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}