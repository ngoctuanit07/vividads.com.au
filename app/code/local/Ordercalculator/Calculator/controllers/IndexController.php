<?php
class Ordercalculator_Calculator_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/calculator?id=15 
    	 *  or
    	 * http://site.com/calculator/id/15 	
    	 */
    	/* 
		$calculator_id = $this->getRequest()->getParam('id');

  		if($calculator_id != null && $calculator_id != '')	{
			$calculator = Mage::getModel('calculator/calculator')->load($calculator_id)->getData();
		} else {
			$calculator = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($calculator == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$calculatorTable = $resource->getTableName('calculator');
			
			$select = $read->select()
			   ->from($calculatorTable,array('calculator_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$calculator = $read->fetchRow($select);
		}
		Mage::register('calculator', $calculator);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}