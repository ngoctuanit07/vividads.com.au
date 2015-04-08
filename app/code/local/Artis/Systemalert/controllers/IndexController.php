<?php
class Artis_Systemalert_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/systemalert?id=15 
    	 *  or
    	 * http://site.com/systemalert/id/15 	
    	 */
    	/* 
		$systemalert_id = $this->getRequest()->getParam('id');

  		if($systemalert_id != null && $systemalert_id != '')	{
			$systemalert = Mage::getModel('systemalert/systemalert')->load($systemalert_id)->getData();
		} else {
			$systemalert = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($systemalert == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$systemalertTable = $resource->getTableName('systemalert');
			
			$select = $read->select()
			   ->from($systemalertTable,array('systemalert_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$systemalert = $read->fetchRow($select);
		}
		Mage::register('systemalert', $systemalert);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function cronalertAction()
    {
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
      $orders = Mage::getModel('sales/order')->getCollection()->addAttributeToSelect("*")->addAttributeToFilter('status', array('processing','pending','partial_payment','pending_payment'));
			
	foreach($orders as $order)
	{
		//echo $order->getId();
		$items = $order->getAllItems();
		foreach($items as $item)
		{
		  //  echo $item->getId();
		    
		    $tableItemName = Mage::getSingleton('core/resource')->getTableName('proofs');
		    //$sqlItemSystem="SELECT * FROM ".$tableItemName."  WHERE quotation_item_id = '".$item[$key]."'";
		    $sqlItemSystem = $connectionRead->select()
					->from($tableItemName, array('*'))
					->where('item_id=? and proof_type = "order"', $item->getId());
					
		    $chkItem = $connectionRead->fetchAll($sqlItemSystem);
		   // $fetchItem = $chkItem->fetch();
		   
		   foreach($chkItem as $proofItem)
		   {
		    
			if($proofItem['status'] != 'Approved')
			{
			    //echo $proofItem['status'].'-'.$order->getId().'-'.$item->getId();
			    //echo '<br/>';
			    
			    $tablePlanning = Mage::getSingleton('core/resource')->getTableName('quote_planning');
			    //$sqlItemSystem="SELECT * FROM ".$tableItemName."  WHERE quotation_item_id = '".$item[$key]."'";
			    $sqlItemSystem = $connectionRead->select()
						->from($tablePlanning, array('*'))
						->where('item_id=? AND planning_type="order"', $item->getId());
						
			    $chkPlanning = $connectionRead->fetchRow($sqlItemSystem);
			    
			   //echo $chkPlanning['proof_date'];
			    $cureentdat =date('Y-m-d');
			    
			    if($cureentdat > $chkPlanning['proof_date'])
			    {
				//$user = Mage::getSingleton('admin/session');
				//$userId = $user->getUser()->getUserId();
				
				$dataall= array(//'user_id'=>$userId,
						'target_id'=>'',
						'caption'=>'Proof approfe date are over.',
						'description'=>'Proof date are over for the file '.$proofItem['file'].' of order '.$order->getIncrementId().' . ',
						//'type'=>'order',
						//'entity_id'=>$order->getId(),
						//'entity_description'=>'order #'.$order->getIncrementId(),
						'task_type'=>'Independent',
						'task_create'=>'no',
						'admin'=>'yes',
						'customer_email'=>$order->getCustomerEmail(),
						'customer_name'=>$order->getCustomerName()
						);
				
				Mage::getModel('systemalert/systemalert')->sendallalert($dataall);
			    }
			    
			}
		   }
		   
		}
		
	}
    }
}