<?php
class Artis_Partialpayment_Adminhtml_PartialpaymentController extends Mage_Adminhtml_Controller_action
{
     public function paymentAction()
    {
        extract($_REQUEST);
        
        
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
                $order = Mage::getModel('sales/order')->load($orderid);
                
                $due_price = $order->getTotalDue();
                if($due_price == 0)
                {
                    $invoice = $order->getInvoiceCollection()->getLastItem();
                    $invoice->setState(2);
                    $invoice->save();
                    
                }
                $order->sendNewOrderEmail();
                
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