<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
require_once 'Mage/Adminhtml/controllers/Sales/Order/CreateController.php';
/**
 * Adminhtml sales orders creation process controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Artis_Vendor_Adminhtml_Sales_Order_CreateController extends Mage_Adminhtml_Sales_Order_CreateController
{
    

    /**
     * Saving quote and create order
     */
    public function saveAction()
    {
        try {
            extract($_REQUEST);
            $this->_processActionData('save');
            $main_price = $this->_getOrderCreateModel()->getQuote()->getGrandTotal();
            if($main_price >= $amount)
            {
                
            if ($paymentData = $this->getRequest()->getPost('payment')) {
                //print_r($this->_getOrderCreateModel()->getQuote()->getGrandTotal());
                
                $this->_getOrderCreateModel()->getQuote()->setGrandTotal($amount);
               // exit();
                $this->_getOrderCreateModel()->setPaymentData($paymentData);
                $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
            }
            
            $this->_getOrderCreateModel()->getQuote()->setGrandTotal($main_price);
            
            
            $order = $this->_getOrderCreateModel()
                ->setIsValidate(true)
                ->importPostData($this->getRequest()->getPost('order'))
                ->createOrder();
                
            /************************************ Start by dev ***********************************************/
                
            $transactionTable=Mage::getSingleton('core/resource')->getTableName('partial_payment');
            $orderTable=Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
            
            $order_id = $order->getId();
            $payemnt_type = $payment['method'];
            if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($transactionTable))
            {
                $sqlPaymentSystem="INSERT INTO ".$transactionTable." SET orderid = '$order_id', amount = '$amount', payment_type = '$payemnt_type', received_date = '$date', postdate = NOW()";
                try {
                        $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
                } catch (Exception $e){
                //echo $e->getMessage();
                }
            }
            
            $sqlPaymentSystem="SELECT * FROM ".$orderTable." WHERE  entity_id = '".$order_id."' ";
            try {
                    $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
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
            
            $sqlPaymentSystem="UPDATE ".$orderTable." SET base_total_due = '$due', total_due = '$due', base_total_paid = '$paid', total_paid = '$paid' WHERE  entity_id = '".$order_id."' ";
            try {
                    $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlPaymentSystem);
                    $resultsSystem = $chkSystem->fetch();
            } catch (Exception $e){
            //echo $e->getMessage();
            }
            
            
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            $order->save();
            
            $order1 = Mage::getModel('sales/order')->load($order->getId());

            $order1->sendNewOrderEmail();
            
            //Create auto invoice
            $invoice = $order1->prepareInvoice();
            
            $invoice->register();
            Mage::getModel('core/resource_transaction')
              ->addObject($invoice)
              ->save();
            
            $invoice->sendEmail(true, '');
            
            $due_price = $order1->getTotalDue();
            if($due_price == 0)
            {
                $invoice->setState(2);
                $invoice->save();
                
            }
            /************************************ End by dev ***********************************************/
            
            /************************************* Start by Dev(create task from product) *********************************************/
                      
            $items = $order->getAllItems();
            foreach ($items as $item) {
                
                $ProductId = $item->getProductId();
                
                $temptableOrganiger = Mage::getSingleton('core/resource')->getTableName('organizer_task');
                
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
                {
                    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
                    {
                        $sqlOrganiger="SELECT * FROM ".$temptableOrganiger." WHERE ot_entity_type = 'product' AND ot_entity_id ='".$ProductId."'";
                        $chkOrganiger = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlOrganiger);
                    }
                    
                    if($chkOrganiger)
                    {
                        
                        foreach($chkOrganiger as $chkOrganiger1)
                        {
                            if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableOrganiger))
                            {
                                if($chkOrganiger1['ot_day'] != '')
                                $finished_date = date ( 'Y-m-j', strtotime ( '+'.$chkOrganiger1['ot_day'].' day' . date('Y-m-d') ) );
                                else
                                $finished_date = date('Y-m-d');
                                
                                
                                $temptableNumber=Mage::getSingleton('core/resource')->getTableName('subadmin_task_number');
                                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableNumber))
                                {
                                    $sqlNumber="SELECT * FROM ".$temptableNumber." WHERE entity_id = '1' ";
                                    $chkNumber = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlNumber);
                                }
                                
                                $flag = 0;
                                
                                while($flag == 0)
                                {
                                    $sqlTask2="SELECT * FROM ".$temptableOrganiger." WHERE ot_target_user = '".$chkOrganiger1['ot_target_user']."' AND ot_deadline ='".$finished_date."'";
                                    $chkTask2 = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlTask2);
                                    
                                    if(count($chkTask2) > $chkNumber[0]['task_number'])
                                    {
                                        $finished_date = date ( 'Y-m-j', strtotime ( '+1 day' . $finished_date ) );
                                        
                                    }
                                    else{
                                        $flag = 1;
                                    }
                                }
                                
                                $sqlOrganiger1="INSERT INTO ".$temptableOrganiger." SET ot_created_at = NOW(), 
                                               ot_author_user = '".$chkOrganiger1['ot_author_user']."' ,
                                               ot_target_user ='".$chkOrganiger1['ot_target_user']."', 
                                               ot_caption= '".addslashes($chkOrganiger1['ot_caption'])."', 
                                               ot_description = '".addslashes($chkOrganiger1['ot_description'])."', 
                                               ot_deadline = '".$finished_date."', 
                                               ot_notify_date = '".$chkOrganiger1['ot_notify_date']."', 
                                               ot_priority = '".$chkOrganiger1['ot_priority']."', 
                                               ot_finished = '".$chkOrganiger1['ot_finished']."', 
                                               ot_read ='".$chkOrganiger1['ot_read']."', 
                                               ot_origin ='".$chkOrganiger1['ot_origin']."', 
                                               ot_category = '".$chkOrganiger1['ot_category']."', 
                                               ot_entity_type ='order', 
                                               ot_entity_id = '".$order->getId()."', 
                                               ot_entity_description = '".addslashes($chkOrganiger1['ot_entity_description'])."', 
                                               ot_notification_read = '".$chkOrganiger1['ot_notification_read']."',
                                               ot_task_type = '".$chkOrganiger1['ot_task_type']."'";
                                               
                               $chkOrganiger2 = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlOrganiger1);
                            }
                        
                        //For chain task
                        $last_id = Mage::getSingleton('core/resource')->getConnection('core_write')->lastInsertId();
                        
                            $temptableChain = Mage::getSingleton('core/resource')->getTableName('task_chain');
                            if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableChain))
                            {
                                $sqlChain="INSERT INTO ".$temptableChain." SET task_id = '$last_id', 
                                                order_quote_id = '".$order->getId()."' ,
                                                product_id ='".$chkOrganiger1['ot_entity_id']."', 
                                                task_type = '".$chkOrganiger1['ot_task_type']."'";
                                                
                                $chkChain = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlChain);
                            }
                        }
                    }
                }
                
             
                 /*********************** add planning auto *********************************/
                $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
                {
                    if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
                    {
                        $sqlShipping="SELECT * FROM  ".$temptableShipping." WHERE quote_id = '".$order->getId()."' AND item_id ='".$item->getId()."' AND product_id = '".$ProductId."' AND planning_type = 'order' ";
                        $chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlShipping);
                    }
                    
                    if(count($chkShipping) == 0)
                    {
                    
                        $created_date = $order->getCreatedAt();
                        $Product = Mage::getModel('catalog/product')->load($ProductId);
                        
                        $temptableTimeline=Mage::getSingleton('core/resource')->getTableName('product_timeline');
                        if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableTimeline))
                        {
                        $sqlTimeline="SELECT * FROM ".$temptableTimeline." WHERE product_id = '".$ProductId."' ";
                        $chkTimeline = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlTimeline);
                        }
                        $order_placed_date =  $created_date;
                        
                        $artwork_date = $this->gettimelinedate($chkTimeline[0]['artwork_day'],$created_date,$chkTimeline[0]['sunday_artwork'],$chkTimeline[0]['holiday_artwork']);
                        $proof_date = $this->gettimelinedate($chkTimeline[0]['proof_day'],$created_date,$chkTimeline[0]['sunday_proof'],$chkTimeline[0]['holiday_proof']);
                        $production_start_date = $this->gettimelinedate($chkTimeline[0]['production_day'],$created_date,$chkTimeline[0]['sunday_production'],$chkTimeline[0]['holiday_production']);
                        $shipping_date = $this->gettimelinedate($chkTimeline[0]['shipping_day'],$created_date,$chkTimeline[0]['sunday_shipping'],$chkTimeline[0]['holiday_shipping']);
                        $delivery_date = $this->gettimelinedate($chkTimeline[0]['delivary_day'],$created_date,$chkTimeline[0]['sunday_delivary'],$chkTimeline[0]['holiday_delivary']);
                        
                                          
                        $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                        if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableShipping))
                        {
                            $sqlShipping="INSERT INTO  ".$temptableShipping." SET quote_id = '".$order->getId()."', item_id ='".$item->getId()."', product_id = '".$ProductId."', planning_type = 'order', order_placed_date = '$order_placed_date', artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' ";
                            $chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
                        }
                    }
                }
                
               
            
                /*********************** add planning auto *********************************/
                
                /************************ Get custom option value ******************************/
                
                $_options = $item->getProductOptions();
           
                foreach($_options as $o => $option){
                   
                    foreach($option as $optionvalue)
                    {
                        if($optionvalue['label'] == 'Graphic Design Service'){
                           
                            if($optionvalue['value'] != '')
                            {
                                $title = explode(' ',$optionvalue['value']);
                                
                                if (is_numeric($title[0]))
                                $revison_number = $title[0];
                                else
                                $revison_number = 10000;
                            }
                        }
                    }
                }
                
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
                    $sqlDesign="INSERT INTO  ".$temptableDesign." SET order_id = '".$order->getId()."', type='order', item_id ='".$item->getId()."', product_id = '".$ProductId."', revision_number = '".$revison_number."', assign_to = '".$adminid."', postdate = NOW() ";
                    $chkDesign = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlDesign);
                }
                
                /************************ Get custom option value ******************************/
                
                /************************** Add the vendor option to individual item in order ********************************************/
                $temptableProduct=Mage::getSingleton('core/resource')->getTableName('vendor_product');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProduct))
                {
                      $sqlProduct="SELECT * FROM ".$temptableProduct." WHERE product_id = '".$ProductId."'";
                      $chkProduct = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlProduct);
                }
                  
                $vendorid = $chkProduct[0]['vendor_id'];
                 
                $temptableVendor=Mage::getSingleton('core/resource')->getTableName('vendor_order');
                if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableVendor))
                {
                    $sqlVendor="INSERT INTO  ".$temptableVendor." SET order_id = '".$order->getId()."', item_id ='".$item->getId()."', product_id = '".$ProductId."', revision_number = '".$revison_number."', assign_to = '".$vendorid."', postdate = NOW() ";
                    $chkVendor = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlVendor);
                }
                /************************** Add the vendor option to individual item in order ********************************************/
                
            }  
            //exit;
            /************************************* End by dev ********************************************/

            $this->_getSession()->clear();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The order has been created.'));
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError($this->__('The partial payment amount are greater than order amount.'));
            $this->_redirect('*/*/');
        }
        
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $this->_getOrderCreateModel()->saveQuote();
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        } catch (Mage_Core_Exception $e){
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        }
        catch (Exception $e){
            $this->_getSession()->addException($e, $this->__('Order saving error: %s', $e->getMessage()));
            $this->_redirect('*/*/');
        }
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
                        $sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                        $chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlHoliday);
                        
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
                    $sqlHoliday="SELECT * FROM ".$temptableHoliday." WHERE h_date = '".$artwork_date."' ";
                    $chkHoliday = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlHoliday);
                    
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

}
