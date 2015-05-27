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

/**
 * Adminhtml sales orders controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';

class Artis_Partialpayment_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
    
        /************************************ Start by dev ***********************************************/
        /**
     * Add order comment action
     */
    public function addCommentAction()
    {exit;
        
        if ($order = $this->_initOrder()) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');
                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                $order->addStatusHistoryComment($data['comment'], $data['status'])
                    ->setIsVisibleOnFront($visible)
                    ->setIsCustomerNotified($notify);

                $comment = trim(strip_tags($data['comment']));
                
                // Add comment in the invoice
                if ($order->hasInvoices()) {
                    // "$_eachInvoice" is each of the Invoice object of the order "$order"
                    foreach ($order->getInvoiceCollection() as $_eachInvoice) {
                        //$invoiceIncrementId = $_eachInvoice->getIncrementId();
                        
                        $_eachInvoice->addComment(
                            $comment,
                            isset($data['is_customer_notified']),
                            isset($data['is_visible_on_front'])
                        );
                        $_eachInvoice->save();
                    }
                }
                
                // Add comment in the shipment
                $shipment_collection = Mage::getResourceModel('sales/order_shipment_collection');
                $shipment_collection->addAttributeToFilter('order_id', $order->getId());
                foreach($shipment_collection as $sc) {
                    $sc->addComment(
                        $comment,
                        isset($data['is_customer_notified']),
                        isset($data['is_visible_on_front'])
                    );
                   
                    $sc->save();
                }

                $order->save();
                $order->sendOrderUpdateEmail($notify, $comment);

                $this->loadLayout('empty');
                $this->renderLayout();
            }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot add order history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }
    
    
    public function paymentAction()
    {
        extract($_REQUEST);
        
        
        $order = Mage::getModel('sales/order')->load($orderid);
        
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
                
        
        $main_price = $order->getGrandTotal();
        $due_price = $order->getTotalDue();
        if($main_price >= $amount and $due_price >= $amount)
        {
            if ($paymentData = $_REQUEST['payment']) {
            
            $order->setGrandTotal($amount);
            
            $order->setPaymentData($paymentData);
            $order->getPayment()->addData($paymentData);
            $tableItem = Mage::getSingleton('core/resource')->getTableName('quotation_items');
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING, true);
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
                    $data['orderid'] = $order_id;
                    $data['amount'] = $amount;
                    if($payment_type!=""){
                        $data['payment_type'] = $payment_type;
                    }
                    $data['received_date'] = $date;
                    $data['postdate'] = NOW();
                    $connectionWrite->insert($orderTable, $data);
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
                $order->sendNewOrderEmail();
                
                $this->_getSession()->clear();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The order has been saved.'));
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError($this->__('The partial payment amount are greater than order due amount.'));
        }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        

            
    }
      /************************************ End by dev ***********************************************/
}
