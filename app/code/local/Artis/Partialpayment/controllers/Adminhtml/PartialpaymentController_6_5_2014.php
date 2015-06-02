<?php
class Artis_Partialpayment_Adminhtml_PartialpaymentController extends Mage_Adminhtml_Controller_Action
{
     public function paymentAction()
    {
	  extract($_REQUEST);
	  $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	  $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	  $order = Mage::getModel('sales/order')->load($orderid);
	  /************** Start 03_01_2014 *****************/
	  if($order->getStatusLabel() != 'Partial Paid')
	  {
	       $items = $order->getAllItems();
	       foreach ($items as $item) {
		     $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
		   
		     $sqlTimeline = $connectionRead->select()
				     ->from($temptableShipping, array('*'))
				     ->where('quote_id = "'.$orderid.'" AND item_id ="'.$item->getId().'" AND planning_type ="order"');
		     $chkTimeline = $connectionRead->fetchRow($sqlTimeline);
		     
		    if($chkTimeline['order_placed_date'] == '0000-00-00')
		    Mage::getModel('timeline/timeline')->UpdateTimeline('order_create',$order->getId(),$item,'order');
	       }
	  }
	  /************** Start 03_01_2014 *****************/
                
        
	  $main_price = $order->getGrandTotal();
	  $due_price = $order->getTotalDue();
	  if($main_price >= $amount and $due_price >= $amount)
	  {
	       if ($paymentData == $_REQUEST['payment']) {
	       $order->setGrandTotal($amount);
	       
	       $order->setPaymentData($paymentData);
	       $order->getPayment()->addData($paymentData);
	       $tableItem = Mage::getSingleton('core/resource')->getTableName('quotation_items');
	       $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
	       $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING, true);
	       }
	       else{
		  $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true);
		  $order->setStatus('partial_payment', true);
	       }
            
	       $order->setGrandTotal($main_price);
	       $order->save();
            
             
                    
	       $transactionTable=Mage::getSingleton('core/resource')->getTableName('partial_payment');
	       $orderTable=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
	       
	       
	       $order_id = $order->getId();
	       $payemnt_type = $payment['method'];
	       
	       
	       /////////////////anz payment module 25-4-2014 gc Starts///////////////////
	       $err ='';
	       $suc = '';
	       if($payemnt_type =='anz_egate'){
		   $type = 1;
		   $payment =null;
		   $param = $_REQUEST;
		   $result = Mage::getModel('anz/egate')->_call($type,$payment,$param);
		   
		   if($result === false) {
			$err = "There has been an error processing your payment. Please try later or contact us for help.";
		   }else {
		   // Check if there is a gateway error
		       switch ($result['vpc_TxnResponseCode']) {
			   case 0:
			      //$payment->setStatus(self::STATUS_APPROVED)->setLastTransId($result['vpc_TransactionNo']);
			      $suc = $result['vpc_TransactionNo'];
			      break;
			   case 1:         /* Unspecified failure */
			       
			      $err = "An error has occurred between our store and our credit card processor.  Please try again. If the error persists, please come back later. Your card has not been charged.";
			      break;
			   case 2:         /* Card declined */
			      $err = "The credit card details you provided have been declined by our credit card processor. Please review the payment details you have entered and try again. If the problem persists, please contact your card issuer.";
			      break;
			   case 3:         /* Timeout */
			      $err = "A timeout has occurred between our store and our credit card processor.  Please try again. If the error persists, please come back later. Your card has not been charged.";
			      break;
			   case 4:         /* Card expired */
			      $err = "The credit card you have entered has expired. Please review the credit card details you have entered and try again. If the problem persists, please contact your card issuer.";
			      break;
			   case 5:         /* Insufficient funds */
			      $err = "The credit card you have entered does not have sufficient funds to cover your order amount. Please check your current credit card balance, review the payment details you have entered and try again. If the problem persists, please contact your card issuer.";
			      break;
			   default:
			      $err = "An error has occurred whilst attempting to process your payment.  Please review your payment details and try again. If the problem persists, please come back later. Your card has not been charged.";
			      break;
		       }
		   }
		   
	       }
	       /////////////////anz payment module 25-4-2014 gc ends///////////////////
	       if($err ==''){
		    if($connectionWrite->isTableExists($transactionTable))
		    {
			
			$connectionWrite->beginTransaction();
			$data = array();
			$data['orderid']= $order_id;
			$data['amount']=$amount;
			if($payment_type != ""){
			  $data['payment_type']=$payment_type;   
			}
			$data['received_date']=$date;
			$data['postdate']=Now();
			$connectionWrite->insert($transactionTable, $data);
			$connectionWrite->commit(); 
			
		    }
		    $sqlPaymentSystem = $connectionRead->select()->from($orderTable, array('*'))->where('entity_id=?', $order_id);
		    try {
			 $chkSystem = $connectionWrite->query($sqlPaymentSystem);
			 $resultsSystem = $chkSystem->fetch();
		    } catch (Exception $e){
		    //echo $e->getMessage();
		    }
		    
		    if($resultsSystem['total_paid'] == 0)
		    $paid = $amount;
		    else
		    $paid = $resultsSystem['total_paid']+$amount;
		    
		    if($resultsSystem['total_due'] == '')
		    $due = $resultsSystem['base_grand_total']-$amount;
		    else
		    $due = $resultsSystem['total_due']-$amount;
		    
		    
		    $connectionWrite->beginTransaction();
		    $data = array();
		    $data['base_total_due'] = $due;
		    $data['total_due'] = $due;
		    $data['base_total_paid'] = $paid;
		    $data['total_paid'] = $paid;
		    $where = $connectionWrite->quoteInto('entity_id =?', $order_id);
		    $connectionWrite->update($orderTable, $data, $where);
		    $connectionWrite->commit();
		    
		    $order = Mage::getModel('sales/order')->load($orderid);
		    
		    $due_price = $order->getTotalDue();
		    if($due_price == 0)
		    {
			$invoice = $order->getInvoiceCollection()->getLastItem();
			$invoice->setState(2);
			$invoice->save();
			
		    }
		    
		   //18-11-2013 SOC
		   $totalPaid=$order->getTotalPaid();
		   $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		   $tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
		   if(!$order->hasShipments() && $totalPaid > 0){
			
			///21-11-2013 SOC
			///4-3-2014 S
			$totalOrder = 0;
			$total_qty = 0;
			$order_items=$order->getAllItems();
			$itemQty = 0; ///11-3-2014
			foreach($order_items as $orderDetails)
			{
			    $prodid=$orderDetails->getProductId();
			    $product = Mage::getModel('catalog/product')->load($prodid);
			    
			    if($product->getTypeId() != 'bundle'){
				
				$itemQty += $orderDetails->getQtyOrdered();
			     
			     }
			}
			$total_qty = $itemQty;
			 ///4-3-2014 E
			$select = $connectionRead->select()->from($tableName, array('*'))->where('order_id=?',$order_id);
			$row = $connectionRead->fetchAll($select);
			
			if(count($row) < 1){
			$date_post = strtotime($order->getCreatedAtStoreDate()); 
			$time=date('Y-m-d H:i:s',$date_post );
			
			$addressLoadId = $order->getShippingAddress()->getData();
			$Name = $addressLoadId['firstname'].' '.$addressLoadId['lastname'];
			
			$connectionWrite->beginTransaction();
			$data = array();
			$data['store_id']= $order->getStoreId();
			if($order->getTotalQtyOrdered() != '')
			$data['total_qty']= $total_qty; ///4-3-2014
			$data['order_id']= $order_id;
			$data['status']= 'Pending Shipment';
			$data['increment_id']=$order->getIncrementId();;
			$data['order_increment_id']=$order->getIncrementId();
			$data['created_at']=$time;
			$data['order_created_at']=$order->getCreatedAt(); ///12-3-2014
			$data['shipping_name']=$Name;
			$data['payment_status']=$order->getStatusLabel();
			//echo "<pre>";print_r($data); exit;
			$connectionWrite->insert($tableName, $data);
			$connectionWrite->commit();
			
			$lastInsertId = $connectionWrite->lastInsertId(); ///27-1-2014
			}
			///21-11-2013 EOC
		    }
		   
		    $order->sendNewOrderEmail();
		    
		    $order = Mage::getModel('sales/order')->load($order->getId());
		     $due_price = $order->getTotalDue();
		    
		   if($due_price == 0)
		   {
			$order1 = Mage::getModel('sales/order')->load($order->getId());
			$order1->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING, true);
			$order1->save();
			
			$temptableOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
		   
			$connectionWrite->beginTransaction();
			$data2 = array();
			$data2['state'] = 'processing';
			$where = $connectionWrite->quoteInto('entity_id =?', $order->getId());
			$connectionWrite->update($temptableOrder, $data2, $where);
			$connectionWrite->commit();
			
			$temptableOrder1=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
			
			$select = $connectionRead->select()->from($temptableOrder1, array('*'))->where('parent_id=?',$order->getId())->order('entity_id DESC')->limit(1);
			$row =$connectionRead->fetchRow($select);
		   
			$connectionWrite->beginTransaction();
			$data3 = array();
			$data3['status'] = 'processing';
			$where = $connectionWrite->quoteInto('entity_id =?', $row['entity_id']);
			$connectionWrite->update($temptableOrder1, $data3, $where);
			$connectionWrite->commit();
			
			///// 27-1-2014 S//////
			$connectionWrite->beginTransaction();
			$data = array();
			$data['payment_status']='Paid';
			$where1 = $connectionWrite->quoteInto('order_id =?', $order->getId());
			$connectionWrite->update($tableName, $data, $where1);
			$connectionWrite->commit();
			///// 27-1-2014 E//////
			
		   }
		   else if($main_price > $amount)
		   {
			$order1 = Mage::getModel('sales/order')->load($order->getId());
		       
			$temptableOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
		   
			$connectionWrite->beginTransaction();
			$data2 = array();
			$data2['state'] = 'processing';
			$where = $connectionWrite->quoteInto('entity_id =?', $order->getId());
			$connectionWrite->update($temptableOrder, $data2, $where);
			$connectionWrite->commit();
			
			///// 27-1-2014 S//////
			$connectionWrite->beginTransaction();
			$data = array();
			$data['payment_status']='Partial Paid';
			$where1 = $connectionWrite->quoteInto('order_id =?', $order->getId());
			$connectionWrite->update($tableName, $data, $where1);
			$connectionWrite->commit();
			///// 27-1-2014 E//////
			
		       
			
			
		   }
		   else{
			$order1 = Mage::getModel('sales/order')->load($order->getId());
		       // $order1->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true);
			 $order1->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING, true);
			$order1->save();
		   }
		   
		   /********************** Set vendor in item table *******************************/
		   $temptableVendor=Mage::getSingleton('core/resource')->getTableName('vendor_item');
		   
		   $connectionWrite->beginTransaction();
		   $data2 = array();
		   $data2['order_status'] = $order->getStatus();
		   $where = $connectionWrite->quoteInto('order_id =?', $order->getId());
		   $connectionWrite->update($temptableVendor, $data2, $where);
		   $connectionWrite->commit();
		    
		   /********************** Set vendor in item table *******************************/
		    
		    
		    $this->_getSession()->clear();
		    Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The order has been saved.'));
	       } else{
		    Mage::getSingleton('adminhtml/session')->addError($err);
	       }
	       
	       
	  }
	  else
	  {
	     Mage::getSingleton('adminhtml/session')->addError($this->__('The partial payment amount are greater than order due amount.'));
	  }
	  
	   
	  Mage::dispatchEvent('model_save_after', array('object'=>Mage::getSingleton('sales/order_payment')));
	  mage::helper('AdminLogger')->updatelog($order->getId(),'Add Partial Payment');
	  $url1 = Mage::helper('adminhtml')->getUrl("zulfe/sales_order/view/order_id/".$orderid);
	  $url1 = str_replace('p//s','p/admin/s',$url1);
	  Mage::log($url1); //To check if URL is correct (and it is correct)
	  Mage::app()->getResponse()->setRedirect($url1);
       


   }
}
?>