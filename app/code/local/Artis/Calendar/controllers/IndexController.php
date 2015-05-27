<?php
class Artis_Calendar_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/calendar?id=15 
    	 *  or
    	 * http://site.com/calendar/id/15 	
    	 */
    	/* 
		$calendar_id = $this->getRequest()->getParam('id');

  		if($calendar_id != null && $calendar_id != '')	{
			$calendar = Mage::getModel('calendar/calendar')->load($calendar_id)->getData();
		} else {
			$calendar = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($calendar == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$calendarTable = $resource->getTableName('calendar');
			
			$select = $read->select()
			   ->from($calendarTable,array('calendar_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$calendar = $read->fetchRow($select);
		}
		Mage::register('calendar', $calendar);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}