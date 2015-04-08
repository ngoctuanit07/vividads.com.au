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
	
	/*function getPrintingStatusCounter*/
	public function getPrintingStatusAction(){
		
		/* fetching current  date*/
		$from_date = $this->getRequest()->getPost('from_date');
		
		$current_date = new DateTime(date('Y-m-d H:i:s'));	
		$timezone = new DateTimeZone('Australia/Sydney');
		$current_date->setTimezone($timezone);
		//$_current_date = $current_date->format('Y-m-d H:i:s');
		
		/*from date */
		$from_date = new DateTime($from_date);
		$_from_date = $from_date->setTimezone($timezone);		
		//echo 'current date';
		// var_dump($current_date);
		 $date_difference =  $current_date->diff($from_date) ;
		//echo 'from date';
		//var_dump($from_date);
		//echo 'date difference';
		//var_dump($date_difference);
		
		$html = '';
		///building html
		$html.=$date_difference->days.' Days '.$date_difference->h.' hours '.$date_difference->i.' Min '.$date_difference->s.' Sec' ;
		echo $html;
		//return $_from_date;
		
		}
}