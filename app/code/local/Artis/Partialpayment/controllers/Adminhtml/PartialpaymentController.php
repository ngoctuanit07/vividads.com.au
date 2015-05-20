<?php
class Artis_Partialpayment_Adminhtml_PartialpaymentController extends Mage_Adminhtml_Controller_Action
{
     public function paymentAction()
    {
	  extract($_REQUEST);
	  $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	  $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	  $order = Mage::getModel('sales/order')->load($orderid);
	  
	  $hasinvoice = $order->hasInvoices();
	  ///saving payment ///
	  if(!$hasinvoice){
	  	$this->Savepayment($order->getId(), 1);
	  }
	  
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
	       ///commented on 6-5-2014
	//       else{
	//	    $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true);
	//	    $order->setStatus('partial_payment', true);
	//       }
            
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
   
   
   /**
     * Change sales order payment (from sales order shee)
     *
     */
    public function Savepayment($order_id=0, $payment_validated=0) {
        //recupere les infos
        
		//$orderId = $this->getRequest()->getParam('order_id');
        //$value = $this->getRequest()->getParam('payment_validated');
		$orderId = $order_id;
		$value  =  $payment_validated; 		
		
        //Charge la commande et modifie
        $order = mage::getModel('sales/order')->load($orderId);
		$order_validated = $order->getpayment_validated();
		
        /********************* Start for update all module for order 12_03_2014 *******************************/
        if($value == '1' && $order_validated !=1 )
        {
           // echo $value;
            $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
            $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
      		
			 $order->setpayment_validated($value)->save();  
		   
		    //Start 03_03_2014
	        $invoice = $order->prepareInvoice();
            
            $invoice->register();
            Mage::getModel('core/resource_transaction')
              ->addObject($invoice)
              ->save();
            
          //  $invoice->sendEmail(true, '');
	    //End 03_03_2014
            
            $items = $order->getAllItems();
            
			foreach ($items as $item) {
                
                $ProductId = $item->getProductId();
                
                $temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
                if($connectionWrite->isTableExists($temptableOrganiger))
                {
                    if($connectionWrite->isTableExists($temptableOrganiger))
                    {
                        //$sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_entity_type = 'product' AND ot_entity_id ='".$ProductId."'";
                        $sqlOrganiger = $connectionRead->select()
					->from($temptableOrganiger, array('*'))
					->where("ot_entity_type = 'product' AND ot_entity_id ='".$ProductId."'");
                        $chkOrganiger = $connectionRead->fetchAll($sqlOrganiger);
                    }
                    
                    if($chkOrganiger)
                    {
                        if($connectionWrite->isTableExists($temptableOrganiger))
                        {
                           
                           $connectionWrite->beginTransaction();
                                           $data = array();
                                           $data['ot_created_at'] = NOW(); 
                                           $data['ot_author_user'] = $chkOrganiger[0]['ot_author_user'];
                                           $data['ot_target_user'] = $chkOrganiger[0]['ot_target_user']; 
                                           $data['ot_caption']= addslashes($chkOrganiger[0]['ot_caption']); 
                                           $data['ot_description'] = addslashes($chkOrganiger[0]['ot_description']); 
                                           $data['ot_deadline'] = $chkOrganiger[0]['ot_deadline']; 
                                           $data['ot_notify_date'] = $chkOrganiger[0]['ot_notify_date']; 
                                           $data['ot_priority'] = $chkOrganiger[0]['ot_priority']; 
                                           $data['ot_finished'] = $chkOrganiger[0]['ot_finished'];
                                           $data['ot_read'] =$chkOrganiger[0]['ot_read']; 
                                           $data['ot_origin'] =$chkOrganiger[0]['ot_origin']; 
                                           $data['ot_category'] = $chkOrganiger[0]['ot_category']; 
                                           $data['ot_entity_type'] ='order'; 
                                           $data['ot_entity_id'] = $order->getId(); 
                                           $data['ot_entity_description'] = addslashes($chkOrganiger[0]['ot_entity_description']); 
                                           $data['ot_notification_read'] = $chkOrganiger[0]['ot_notification_read'];
                                           $data['ot_task_type'] = $chkOrganiger[0]['ot_task_type'];
                                           $connectionWrite->insert($temptableOrganiger, $data);
                                           $connectionWrite->commit(); 
                        }
                        
                        //For chain task
                        $last_id = $connectionWrite->fetchOne('SELECT last_insert_id()');
                        
                        $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
                        if($connectionWrite->isTableExists($temptableChain))
                        {
                            $connectionWrite->beginTransaction();
                                $data = array();
                                $data['task_id']=$last_id;
                                $data['order_quote_id']=$order->getId();
                                $data['product_id']=$chkOrganiger[0]['ot_entity_id'];
                                $data['task_type']=$chkOrganiger[0]['ot_task_type'];
                                $connectionWrite->insert($temptableChain,$data);
                                $connectionWrite->commit();
                        }
                    }
                }
                
                /*********************** add planning auto *********************************/
                $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                if($connectionWrite->isTableExists($temptableShipping))
                {
                    if($connectionWrite->isTableExists($temptableShipping))
                    {
                        //$sqlShipping="SELECT * FROM  ".$temptableShipping." WHERE quote_id = '".$order->getId()."' AND item_id ='".$item->getId()."' AND product_id = '".$ProductId."' AND planning_type = 'order' ";
                        $sqlShipping = $connectionRead->select()
                                                ->from($temptableShipping, array('*'))
                                                ->where("quote_id = '".$order->getId()."' AND item_id ='".$item->getId()."' AND product_id = '".$ProductId."' AND planning_type = 'order' ");
                        $chkShipping = $connectionRead->fetchAll($sqlShipping);
                    }
                    
                    if(count($chkShipping) == 0)
                    {
                    
                        $created_date = $order->getCreatedAt();
                        
                        $Product = Mage::getModel('catalog/product')->load($ProductId);
                        
                        $temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
                       // $sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$ProductId."' ";
                        $sqlTimeline = $connectionRead->select()
                                        ->from($temptableTimeline, array('*'))
                                        ->where('product_id=?', $ProductId);
                        $chkTimeline = $connectionRead->fetchAll($sqlTimeline);
			 
			if($order->getTotalPaid() > 0)
                        {
                    
                        $stockobj = new MDN_AdvancedStock_MiscController();
						$order_placed_date =  $created_date;
                        $artwork_date = $stockobj->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                        $proof_date = $stockobj->gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                        $production_start_date = $stockobj->gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                        $shipping_date = $stockobj->gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                        $delivery_date = $stockobj->gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
			
			}
			else
			{
			    $order_placed_date = '';
			     $artwork_date = '';
			    $proof_date = '';
			    $production_start_date = '';
			    $shipping_date = '';
			    $delivery_date = '';
			}
                        
                                          
                        $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                        
                        if($connectionWrite->isTableExists($temptableShipping))
                        {
                            //$sqlShipping="INSERT INTO  ".$temptableShipping." SET quote_id = '".$order->getId()."', item_id ='".$item->getId()."', product_id = '".$ProductId."', planning_type = 'order', order_placed_date = '$order_placed_date', artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' ";
                            //$chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
                            
                            $connectionWrite->beginTransaction();
                            $data = array();
                            $data['quote_id']= $order->getId();
                            $data['item_id'] = $item->getId();
                            $data['product_id'] = $ProductId; 
                            $data['planning_type'] = 'order'; 
                            $data['order_placed_date'] = $order_placed_date; 
                            $data['artwork_date'] = $artwork_date; 
                            $data['proof_date'] = $proof_date; 
                            $data['start_date'] = $production_start_date; 
                            $data['shipping_date'] = $shipping_date; 
                            $data['delivery_date']= $delivery_date; 
                            $connectionWrite->insert($temptableShipping, $data);
                            $connectionWrite->commit();
                        }
                    }
                }
                
                     $ProductId = $item->getproductId();
                    $_options = $item->getProductOptions();
                   //if(!empty($_options))
                   //{
                   //
                   //        //print_r($_options['options']);
                   //   
                   //        foreach($_options['options'] as $option){
                   //               
                   //           
                   //                if($option['label'] == 'Graphic Design Service'){
                   //                   
                   //                    if($option['value'] != '')
                   //                    {
                   //                        $title = explode(' ',$option['value']);
                   //                        
                   //                        if (is_numeric($title[0]))
                   //                        $revison_number = $title[0];
                   //                        else
                   //                        $revison_number = 10000;
                   //                    }
                   //                }
                   //           
                   //        }
                           
                           $temptableProduct=Mage::getSingleton('core/resource')->getTableName('catalog_product_designer');
                           if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProduct))
                           {
                               $sqlProduct="SELECT * FROM ".$temptableProduct." WHERE product_id = '".$ProductId."'";
                               $chkProduct = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlProduct);
                           }
                           
                           $adminid = $chkProduct[0]['user_id'];
                            
                           $temptableDesign=Mage::getSingleton('core/resource')->getTableName('design_service');
                           if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableDesign))
                           {
                               $sqlDesign="INSERT INTO  ".$temptableDesign." SET order_id = '".$order->getId()."', type='order', item_id ='".$item->getId()."', product_id = '".$ProductId."', revision_number = '100', assign_to = '".$adminid."', postdate = NOW() ";
                               $chkDesign = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlDesign);
                           }
                  // }
                   
                    /************************** Add the vendor option to individual item in order ********************************************/

                       
                       $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
                       $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
                       $temptableProofs = Mage::getSingleton('core/resource')->getTableName('proofs');
                       
                       $select = $connectionRead->select()
                       ->from($temptableProofs, array('*'))
                       ->where('order_id=?',$order->getId())
                       ->where('item_id=?',$item->getId())
                       ->where('proof_type=?','order');
                       //$row =$connectionRead->fetchRow($select);
                       $result = $connectionRead->fetchAll($select);
                       
                       if(count($result) > 0)
                       $file_recieved = 'yes';
                       else
                       $file_recieved = 'no';
                       
                       if($result[0]['status'] == 'Approved')
                       $proof_approved = 'yes';
                       else
                       $proof_approved = 'no';
                       
                       if($item->getProductType() != 'bundle')//31_01_2014
                       {                  
                               $temptableVendor=Mage::getSingleton('core/resource')->getTableName('vendor_item');
                               
                               $_product = Mage::getModel('catalog/product')->load($item->getProductId());
                               
                               $name = $_product->getAttributeText('vendor_id');
                               $target_vendor = Mage::getResourceModel('catalog/product')->getAttribute("vendor_id")->getSource()->getOptionId($name);
                               
                               $connectionWrite->beginTransaction();
                               $data2 = array();
                               $data2['target_user']= $target_vendor;
                               $data2['item_id'] = $item->getId();
                               $data2['order_id'] = $order->getId();
                               $data2['product_id'] = $item->getProductId();
                               $data2['product_sku'] = addslashes($_product->getSku());
                               $data2['postdate'] = NOW();
                               $data2['order_status'] = $order->getStatus();
                               
                               $data2['file_recieved'] = $file_recieved;
                               $data2['proof_approved'] = $proof_approved;
                               $data2['proof_approve_date'] = Now();
                               if($row['quantity'] != '')
                               $data2['qty'] = $result[0]['quantity'];
                               
                               $connectionWrite->insert($temptableVendor, $data2);
                               $connectionWrite->commit();
                       }
                   /************************** Add the vendor option to individual item in order ********************************************/
                
               
                        
            }
            
            


            $_order = Mage::getModel('sales/order')->load($order->getId());
            $paymentName = $_order->getPayment()->getMethodInstance()->getCode();
            
            ///18-11-2013 SOC
            $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
            $tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
            if($_order->getTotalPaid() > 0){
                    //26-11-2013 SOC
                    try {
                        //if($order->canShip()) {
                            
                            //$shipmentid = Mage::getModel('sales/order_shipment_api')
                            //                ->create($order->getIncrementId(), array());
                            //
                            //$ship = Mage::getModel('sales/order_shipment_api')
                            //                ->addTrack($order->getIncrementId(), array());       
                       // }
                    }catch (Mage_Core_Exception $e) {
                    // print_r($e);
                    }
                    //26-11-2013 EOC
                    
                    ///4-3-2014 S ///11-3-2014
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
                    
                    //21-11-2013 SOC
                    //$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
                    //$tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
                    
                    $date_post = strtotime($order->getCreatedAtStoreDate()); 
                    $time=date('Y-m-d H:i:s',$date_post );
                    
                    $addressLoadId = $order->getShippingAddress()->getData();
                    $Name = $addressLoadId['firstname'].' '.$addressLoadId['lastname'];
                    
                    $connectionWrite->beginTransaction();
                    $data = array();
                    $data['store_id']= $order->getStoreId();
                    $data['total_qty']= $total_qty; ///4-3-2014
                    $data['order_id']= $order->getId();
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
                    //21-11-2013 EOC
                    $lastInsertId = $connectionWrite->lastInsertId(); ///27-1-2014
            }
            ///18-11-2013 EOC
            
            
            
            
            if($_order->getTotalDue() == 0)
            {
                $_order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                $_order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                
                    ///// 27-1-2014 S//////
                    $connectionWrite->beginTransaction();
                    $data = array();
                    $data['payment_status']='Paid';
                    $where = $connectionWrite->quoteInto('order_id =?', $_order->getId());
                    $connectionWrite->update($tableName, $data, $where);
                    $connectionWrite->commit();
                    ///// 27-1-2014 E//////
                
                
            }
            else if($_order->getTotalPaid() == 0)
            {
               // $_order->setState('new', true);
               
                $temptableOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
                
                 $connectionWrite->beginTransaction();
                $data3 = array();
               $_order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                
                if($paymentName == 'purchaseorder')
                {
                    $_order->setStatus('purchaseorder_pending_payment', true);
                     $data3['status'] = 'purchaseorder_pending_payment';
                }
                elseif($paymentName == 'checkmo')
               {
                    $_order->setStatus('checkmo_pending_payment', true);
                    $data3['status'] = 'checkmo_pending_payment';
                }
                elseif($paymentName == 'directdeposit_au')
                 {
                    $_order->setStatus('awaiting_direct_deposit', true);
                    $data3['status'] = 'awaiting_direct_deposit';
                }
                else
                {
                    $_order->setStatus('pending', true);
                    $data3['status'] = 'pending';
                }
                
                 $where = $connectionWrite->quoteInto('parent_id =?', $order->getId());
                $connectionWrite->update($temptableOrder, $data3, $where);
                $connectionWrite->commit();
            }
            else if($_order->getTotalDue() > 0)
            {
                $temptableOrder=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
               // $_order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                $_order->setStatus('partial_payment', true);
                $data3 = array();
                $data3['status'] = 'partial_payment';
                
                $where = $connectionWrite->quoteInto('parent_id =?', $order->getId());
                $connectionWrite->update($temptableOrder, $data3, $where);
                $connectionWrite->commit();
                
                     ///// 27-1-2014 S//////
                    $connectionWrite->beginTransaction();
                    $data = array();
                    $data['payment_status']='Partial Paid';
                    $where = $connectionWrite->quoteInto('order_id =?', $_order->getId());
                    $connectionWrite->update($tableName, $data, $where);
                    $connectionWrite->commit();
                    ///// 27-1-2014 E//////
            }
            $_order->save();
        }
		$order->setpayment_validated($value)->save();
        //exit;
        /********************* End for update all module for order 12_03_2014 *******************************/
		
        //Confirme
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Payment state updated'));
		$output = 1;
		echo $output;			   
		return $output;
        //redirige
       // $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
    }
   
   
}
?>