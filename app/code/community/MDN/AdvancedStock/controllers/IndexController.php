<?php

class MDN_AdvancedStock_IndexController extends Mage_Core_Controller_Front_Action {


	
    /**
     * Display mass stock editor grid
     *
     */
    public function MassStockEditorAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Return mass stock editor grid using Ajax 
     */
    public function MassStockEditorAjaxAction()
    {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('AdvancedStock/MassStockEditor_Grid');
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * apply mass stock editor changes
     *
     */
    public function MassStockSaveAction() {
        
        $datas = $this->getRequest()->getPost('mass_stock_editor_logs');
        $datas = $this->convertChangesData($datas);

        foreach($datas as $stockItemId => $data)
        {
            $stockItem = Mage::getModel('cataloginventory/stock_item')->load($stockItemId);
            foreach($data as $name => $value)
            {
                $stockItem->setData($name, $value);
            }
            $stockItem->save();
        }
        
        //confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Data saved'));
        $this->_redirect('AdvancedStock/Misc/MassStockEditor');
        
    }

    /**
     * Convert data from persistant grid to array
     * 
     * @param type $data 
     */
    protected function convertChangesData($flatDatas)
    {
        $datas = array();
        
        $flatDatas = explode(';', $flatDatas);
        foreach($flatDatas as $flatData)
        {
            $fields = explode('=', $flatData);
            if (count($fields) != 2)
                continue;
            $value = $fields[1];
            $lastUnderscore = strrpos($fields[0], '_');
            $fieldName = substr($fields[0], 0, $lastUnderscore);
            $pk = substr($fields[0], $lastUnderscore + 1);
            
            if (!isset($datas[$pk]))
                $datas[$pk] = array();
            $datas[$pk][$fieldName] = $value;
        }
        
        return $datas;
    }
    
    
    /**
     * Mass action to validate payment
     *
     */
    public function ValidatepaymentAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $order = mage::getModel('sales/order')->load($orderId);
                $order->setpayment_validated(1)->save();
            }
        }

        //Confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Payments validated'));
        $this->_redirect('adminhtml/sales_order/');
    }

    /**
     * Mass action to cancel payment
     *
     */
    public function CancelpaymentAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $order = mage::getModel('sales/order')->load($orderId);
                $order->setpayment_validated(0)->save();
            }
        }

        //Confirm & redirect
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Payments canceled'));
        $this->_redirect('adminhtml/sales_order/');
    }

    /**
     * Change sales order payment (from sales order shee)
     *
     */
    public function SavepaymentAction() {
        //recupere les infos
        $orderId = $this->getRequest()->getParam('order_id');
        $value = $this->getRequest()->getParam('payment_validated');

        //Charge la commande et modifie
        $order = mage::getModel('sales/order')->load($orderId);
        $order->setpayment_validated($value)->save();
        
        /********************* Start for update all module for order 12_03_2014 *******************************/
        if($value == '1')
        {
           // echo $value;
            $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
            $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
            
            //Start 03_03_2014
	    $invoice = $order->prepareInvoice();
            
            $invoice->register();
            Mage::getModel('core/resource_transaction')
              ->addObject($invoice)
              ->save();
            
            $invoice->sendEmail(true, '');
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
                    
                        $order_placed_date =  $created_date;
                        $artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                        $proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                        $production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                        $shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                        $delivery_date = $this->gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
			
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
        //exit;
        /********************* End for update all module for order 12_03_2014 *******************************/

        //Confirme
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Payment state updated'));

        //redirige
        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
    }
	
	
	/**
     * Change sales order payment (from sales order shee)
     *
     */
    public function SavepaymentFrontendAction() {
        //recupere les infos
        $orderId = $this->getRequest()->getParam('order_id');
        $value = $this->getRequest()->getParam('payment_validated');
		
		
		
		//Charge la commande et modifie
        $order = mage::getModel('sales/order')->load($orderId);
        $order->setpayment_validated($value)->save();
        
        /********************* Start for update all module for order 12_03_2014 *******************************/
        if($value == '1')
        {
           // echo $value;
            $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
            $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
            
            //Start 03_03_2014
	    $invoice = $order->prepareInvoice();
            
            $invoice->register();
            Mage::getModel('core/resource_transaction')
              ->addObject($invoice)
              ->save();
            
        //    $invoice->sendEmail(true, '');
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
                    
                        $order_placed_date =  $created_date;
                        $artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                        $proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                        $production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                        $shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                        $delivery_date = $this->gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
			
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
        //exit;
        /********************* End for update all module for order 12_03_2014 *******************************/

        //Confirme
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Payment state updated'));

        //redirige
      //  $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
    }
	
	/**
     * Change sales order paymentBackup (from sales order shee)
     *
     */
    public function SavepaymentBackupAction() {
        //recupere les infos
        $orderId = $this->getRequest()->getParam('order_id');
        $value = $this->getRequest()->getParam('payment_validated');

        //Charge la commande et modifie
        $order = mage::getModel('sales/order')->load($orderId);
        $order->setpayment_validated($value)->save();
        
        /********************* Start for update all module for order 12_03_2014 *******************************/
        if($value == '1')
        {
           // echo $value;
            $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
            $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
            
            //Start 03_03_2014
	    $invoice = $order->prepareInvoice();
            
            $invoice->register();
            Mage::getModel('core/resource_transaction')
              ->addObject($invoice)
              ->save();
            
            $invoice->sendEmail(true, '');
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
                    
                        $order_placed_date =  $created_date;
                        $artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                        $proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                        $production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                        $shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                        $delivery_date = $this->gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
			
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
        //exit;
        /********************* End for update all module for order 12_03_2014 *******************************/

        //Confirme
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Payment state updated'));

        //redirige
        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
    }
    
      /***************************** Add custom function ***********************************/
     public function isweekend($date){
     $date = strtotime($date);
     $date = date("l", $date);
     $date = strtolower($date);
     if($date == "sunday"){
      return 1;
     } else {
      return 0;
     }
    }
    
    public function gettimelinedate($day_delay,$created_date,$sunday,$holiday)
    {
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        if($sunday == 0 and $holiday == 0)
        {
            $artwork_date = date ( 'Y-m-j', strtotime ( '+'.$day_delay.' day' . $created_date ) );
        }
        else
        {
            if($sunday == 1)
            {
                $flag = 0;
                $artwork_date = date ( 'Y-m-j', strtotime ( '+'.$day_delay.' day' . $created_date ) );
                
                $d = $this->isweekend($artwork_date);
                if($holiday == 1)
                {
                    while($flag == 0)
                    {
                        $artwork_date = date ( 'Y-m-j', strtotime ( '+'.($day_delay+$d).' day' . $created_date ) );
                        
                        $temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
                        //$sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                        $sqlHoliday= $connectionRead->select()
                                        ->from($temptableHoliday,array('*'))
                                        ->where('h_date=?',$artwork_date);
                        $chkHoliday = $connectionWrite->fetchAll($sqlHoliday);
                        
                        if(count($chkHoliday) > 0)
                        {
                            $d++;
                        }
                        else
                        {
                           $flag = 1; 
                        }
                    }
                    
                }
                else
                {
                    $artwork_date = date ( 'Y-m-j', strtotime ( '+'.($day_delay+$d).' day' . $created_date ) );
                }
                
            }
            else if($holiday == 1)
            {
                $flag = 0;
                $d = 0;
                while($flag == 0)
                {
                    $artwork_date = date ( 'Y-m-j', strtotime ( '+'.($day_delay+$d).' day' . $created_date ) );
                    
                    $temptableHoliday=Mage::getSingleton('core/resource')->getTableName('holiday');
                   // $sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                    $sqlHoliday= $connectionRead->select()
                                        ->from($temptableHoliday,array('*'))
                                        ->where('h_date=?',$artwork_date);
                    $chkHoliday = $connectionWrite->fetchAll($sqlHoliday);
                    
                    if(count($chkHoliday) > 0 or ($sunday == 1 and $this->isweekend($artwork_date) == 1))
                    {
                        $d++;
                    }
                    else
                    {
                       $flag = 1; 
                    }
                }
            }
            
        }
        
        return $artwork_date;
    }
    /***************************** Add custom function ***********************************/


    //************************************************************************************************************************************************************
    //************************************************************************************************************************************************************
    //STOCK ERRRORS
    //************************************************************************************************************************************************************
    //************************************************************************************************************************************************************

    /**
     * Display stock error grid
     *
     */
    public function IdentifyErrorsAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Refresh stock error list
     *
     */
    public function RefreshErrorListAction() {
        mage::helper('AdvancedStock/StockError')->refresh();
    }

    /**
     * try to fix error
     *
     */
    public function FixErrorAction() {
        //retrieve data
        $stockErrorId = $this->getRequest()->getParam('se_id');

        try {
            $stockError = mage::getModel('AdvancedStock/StockError')->load($stockErrorId);
            if ($stockError->getId())
                $stockError->fix();
            else
                throw new Exception('Unable to find stock !');
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Error fixed'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occured : ') . $ex->getMessage());
        }

        //redirect
        $this->_redirect('AdvancedStock/Misc/IdentifyErrors');
    }

    /**
     * Try to fix all errors
     *
     */
    public function MassFixErrorsAction() {
        mage::helper('AdvancedStock/StockError')->fixAllErrors();
    }

    /**
     * Update is valid for all orders
     *
     */
    public function UpdateIsValidForAllOrdersAction() {
        $taskGroup = 'refresh_is_valid';
        mage::helper('BackgroundTask')->AddGroup($taskGroup, mage::helper('AdvancedStock')->__('Refresh is_valid for orders'), 'AdvancedStock/Misc/ConfirmUpdateIsValidForAllOrders');

        //plan task for each orders
        $collection = mage::getModel('sales/order')
                ->getCollection()
                ->addAttributeToFilter('state', array('nin' => array('complete', 'canceled')));
        foreach ($collection as $order) {
            $orderId = $order->getId();
            mage::helper('BackgroundTask')->AddTask('Update is_valid for order #' . $orderId, 'AdvancedStock/Sales_ValidOrders', 'UpdateIsValidWithSave', $orderId, $taskGroup
            );
        }

        //execute task group
        mage::helper('BackgroundTask')->ExecuteTaskGroup($taskGroup);
    }

    public function ConfirmUpdateIsValidForAllOrdersAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

}