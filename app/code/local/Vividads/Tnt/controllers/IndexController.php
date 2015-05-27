<?php
class Vividads_Tnt_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/tnt?id=15 
    	 *  or
    	 * http://site.com/tnt/id/15 	
    	 */
    	/* 
		$tnt_id = $this->getRequest()->getParam('id');

  		if($tnt_id != null && $tnt_id != '')	{
			$tnt = Mage::getModel('tnt/tnt')->load($tnt_id)->getData();
		} else {
			$tnt = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($tnt == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$tntTable = $resource->getTableName('tnt');
			
			$select = $read->select()
			   ->from($tntTable,array('tnt_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$tnt = $read->fetchRow($select);
		}
		Mage::register('tnt', $tnt);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}
?>