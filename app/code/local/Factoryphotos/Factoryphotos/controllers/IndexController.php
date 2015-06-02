<?php
class Factoryphotos_Factoryphotos_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/factoryphotos?id=15 
    	 *  or
    	 * http://site.com/factoryphotos/id/15 	
    	 */
    	/* 
		$factoryphotos_id = $this->getRequest()->getParam('id');

  		if($factoryphotos_id != null && $factoryphotos_id != '')	{
			$factoryphotos = Mage::getModel('factoryphotos/factoryphotos')->load($factoryphotos_id)->getData();
		} else {
			$factoryphotos = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($factoryphotos == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$factoryphotosTable = $resource->getTableName('factoryphotos');
			
			$select = $read->select()
			   ->from($factoryphotosTable,array('factoryphotos_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$factoryphotos = $read->fetchRow($select);
		}
		Mage::register('factoryphotos', $factoryphotos);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}