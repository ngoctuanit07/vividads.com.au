<?php
class Artis_Partialpayment_Adminhtml_PartialpaymentController extends Mage_Adminhtml_Controller_action
{
     public function paymentAction()
    {
        extract($_REQUEST);
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $order = Mage::getModel('sales/order')->load($orderid);
        
        
                
        
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
                if($connectionWrite->isTableExists($transactionTable))
                {
                    //$sqlPaymentSystem="INSERT INTO ".$transactionTable." SET orderid = '$order_id', amount = '$amount', payment_type = '$payemnt_type', received_date = '$date', postdate = NOW()";
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
                    //try {
                    //        $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
                    //} catch (Exception $e){
                    ////echo $e->getMessage();
                    //}
                }
                
                //$sqlPaymentSystem="SELECT * FROM ".$orderTable." WHERE  entity_id = '".$order_id."' ";
                $sqlPaymentSystem = $connectionRead->select()
				->from($orderTable, array('*'))
				->where('entity_id=?', $order_id);
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
                
                //$sqlPaymentSystem="UPDATE ".$orderTable." SET base_total_due = '$due', total_due = '$due', base_total_paid = '$paid', total_paid = '$paid' WHERE  entity_id = '".$order_id."' ";
                $connectionWrite->beginTransaction();
                $data = array();
                $data['base_total_due'] = $due;
                $data['total_due'] = $due;
                $data['base_total_paid'] = $paid;
                $data['total_paid'] = $paid;
                $where = $connectionWrite->quoteInto('entity_id =?', $order_id);
                $connectionWrite->update($orderTable, $data, $where);
                $connectionWrite->commit();
                //try {
                //        $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
                //        $resultsSystem = $chkSystem->fetch();
                //} catch (Exception $e){
                ////echo $e->getMessage();
                //}
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
     	       if(!$order->hasShipments() && $totalPaid > 0){
		    
		    //26-11-2013 SOC
		  
		    try {
		    //if($order->canShip()) {
			
			$shipmentid = Mage::getModel('sales/order_shipment_api')
					->create($order->getIncrementId(), array());
			
			$ship = Mage::getModel('sales/order_shipment_api')
					->addTrack($order->getIncrementId(), array());       
		    // }
		    }catch (Mage_Core_Exception $e) {
		    // print_r($e);
		    }
		  //26-11-2013 EOC
		  
		   
		    
		    ///21-11-2013 SOC
		    $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		    $tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
		    
		    $date_post = strtotime($order->getCreatedAtStoreDate()); 
		    $time=date('Y-m-d H:i:s',$date_post );
		    
		    $addressLoadId = $order->getShippingAddress()->getData();
		    $Name = $addressLoadId['firstname'].' '.$addressLoadId['lastname'];
		    
		    $connectionWrite->beginTransaction();
		    $data = array();
		    $data['store_id']= $order->getStoreId();
		    $data['total_qty']= $order->getTotalQtyOrdered();
		    $data['order_id']= $order_id;
		    $data['status']= 'Pending Shipment';
		    $data['increment_id']=$order->getIncrementId();;
		    $data['order_increment_id']=$order->getIncrementId();
		    $data['created_at']=$time;
		    $data['order_created_at']=$time;
		    $data['shipping_name']=$Name;
		    //echo "<pre>";print_r($data); exit;
		    $connectionWrite->insert($tableName, $data);
		    $connectionWrite->commit();
		    ///21-11-2013 EOC
		  
	       }
	       
	       
     	       //18-11-2013 EOC
		
		
                $order->sendNewOrderEmail();
	       if($due_price == 0)
	       {
		   $order1 = Mage::getModel('sales/order')->load($order->getId());
		   // $order1->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true);
		    $order1->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING, true);
		    $order1->save(); 
	       }
	       else if($main_price > $amount)
	       {
		    $order1 = Mage::getModel('sales/order')->load($order->getId());
		   // $order1->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true);
		    $order1->setStatus('partial_payment', true);
		    $order1->save();
	       }
	       else{
		    $order1 = Mage::getModel('sales/order')->load($order->getId());
		   // $order1->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true);
		     $order1->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING, true);
		    $order1->save();
	       }
                
		
                $this->_getSession()->clear();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The order has been saved.'));
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError($this->__('The partial payment amount are greater than order due amount.'));
        }
            //$this->_redirect('admin/sales_order/view', array('order_id' => $order->getId()));
            
            $url1 = Mage::helper('adminhtml')->getUrl("zulfe/sales_order/view/order_id/".$order_id);
            $url1 = str_replace('p//s','p/admin/s',$url1);
            Mage::log($url1); //To check if URL is correct (and it is correct)
            Mage::app()->getResponse()->setRedirect($url1);
        

            
    }
}
?>