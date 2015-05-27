<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */
include_once('Mage/Adminhtml/controllers/Sales/Order/EditController.php');

class MageWorx_Adminhtml_Orderspro_Order_EditController extends Mage_Adminhtml_Sales_Order_EditController {
    // for cancel reset_shipping
    protected function _processActionData($action = null) {        
        $this->getRequest()->setPost('reset_shipping', 0);
        parent::_processActionData($action);
    }    
    
    public function saveAction() {
        try {
            if (version_compare(Mage::getVersion(), '1.5.0.1', '>') && version_compare(Mage::getVersion(), '1.9.0.0', '<') || version_compare(Mage::getVersion(), '1.11.0.0', '>')) {
                $this->_processActionData('save');            
            } else {
                $this->_processData('save');   
            }            
            if (version_compare(Mage::getVersion(), '1.5.0.0', '<')) Mage::register('isSecureArea', true, true); // for 1.4.2.0
            
            
            $paymentData = $this->getRequest()->getPost('payment');            
            
            // if method=='ccsave' - must be card number to add payment data 
            if ($paymentData && isset($paymentData['method']) && ($paymentData['method']!='ccsave' || $paymentData['method']=='ccsave' && isset($paymentData['cc_number']))) {
                $this->_getOrderCreateModel()->setPaymentData($paymentData);
                $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
            }
            
            $orderModel = $this->_getOrderCreateModel()
                ->setIsValidate(true)
                ->importPostData($this->getRequest()->getPost('order'));
            $sendConfirmationFlag = $orderModel->getSendConfirmation();                        
            $order = $orderModel->setSendConfirmation(0)->createOrder();            

            $this->_getSession()->clear();
                                    
            // around the standard code magento, but to these lines
            $orderId = $this->transferParamsFromOldToNewOrder($order);                        
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('orderspro')->__('The order has been edited.'));
                        
            if ($sendConfirmationFlag && $orderId>0) {
                if (Mage::helper('orderspro')->isEditEnabled()) {
                    //Append Comments
                    $comment = '';
                    $data = $this->getRequest()->getPost('order');
                    if (isset($data['comment']['customer_note_notify']) && $data['comment']['customer_note_notify']) {
                        $comment = $data['comment']['customer_note'];
                    }                    
                    Mage::getModel('orderspro/order')->load($orderId)->sendOrderEditEmail(true, $comment);
                } else {
                    Mage::getModel('sales/order')->load($orderId)->sendNewOrderEmail();
                }    
            }
            
            $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
            // around the standard code magento
            
            
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
    
    private function transferParamsFromOldToNewOrder($order) {        
        if (!Mage::helper('orderspro')->isEditEnabled()) return $order->getId();
        
        $orderPrev = Mage::getModel('sales/order')->load($order->getRelationParentId());
        if ($orderPrev->hasInvoices()) $prevHasInvoices = true; else $prevHasInvoices = false;
        
        $orderPrevId = $orderPrev->getId();
        
        $coreResource = Mage::getSingleton('core/resource');
        $write = $coreResource->getConnection('core_write');        
                
        // transfer all previos order items
        //$write->query("UPDATE `".$coreResource->getTableName('sales_flat_order_item')."` SET `order_id` = ".$order->getId()." WHERE `order_id`=".$orderPrevId);
        
        // transfer invoice previos order items
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order_item')."` AS sfoi, `".$coreResource->getTableName('sales_flat_invoice')."` AS sfi, `".$coreResource->getTableName('sales_flat_invoice_item')."` AS sfii
            SET sfoi.`order_id` = ".$order->getId()."
            WHERE sfoi.`order_id`=".$orderPrevId." AND sfoi.`order_id`=sfi.`order_id` AND sfi.`entity_id`=sfii.`parent_id` AND sfoi.`item_id`=sfii.`order_item_id`");
        
        // transfer shipment previos order items
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order_item')."` AS sfoi, `".$coreResource->getTableName('sales_flat_shipment')."` AS sfs, `".$coreResource->getTableName('sales_flat_shipment_item')."` AS sfsi
            SET sfoi.`order_id` = ".$order->getId()."
            WHERE sfoi.`order_id`=".$orderPrevId." AND sfoi.`order_id`=sfs.`order_id` AND sfs.`entity_id`=sfsi.`parent_id` AND sfoi.`item_id`=sfsi.`order_item_id`");
        
        // transfer creditmemo previos order items
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order_item')."` AS sfoi, `".$coreResource->getTableName('sales_flat_creditmemo')."` AS sfc, `".$coreResource->getTableName('sales_flat_creditmemo_item')."` AS sfci
            SET sfoi.`order_id` = ".$order->getId()."
            WHERE sfoi.`order_id`=".$orderPrevId." AND sfoi.`order_id`=sfc.`order_id` AND sfc.`entity_id`=sfci.`parent_id` AND sfoi.`item_id`=sfci.`order_item_id`");
        

        // authorizenet for can invoice - transfer previos transaction
        $paymentData = Mage::app()->getRequest()->getPost('payment');
        if ($paymentData && isset($paymentData['method']) && ($paymentData['method']=='authorizenet' || $paymentData['method']=='paypal_direct' || $paymentData['method']=='verisign') && !isset($paymentData['cc_number'])) {
            $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_order_payment')."` WHERE `parent_id`=".$order->getId());            
            $write->query("DELETE FROM `".$coreResource->getTableName('sales_payment_transaction')."` WHERE `order_id`=".$order->getId());
            
            $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order_payment')."` SET `parent_id` = ".$order->getId()." WHERE `parent_id`=".$orderPrevId);            
            $write->query("UPDATE `".$coreResource->getTableName('sales_payment_transaction')."` SET `order_id` = ".$order->getId().", `is_closed`=0 WHERE `order_id`=".$orderPrevId);
            // remove Authorized amount of xxxx.
            $write->query("DELETE t1 FROM `".$coreResource->getTableName('sales_flat_order_status_history')."` AS t1, (SELECT (MAX(`entity_id`)) AS max_entity_id FROM `".$coreResource->getTableName('sales_flat_order_status_history')."` WHERE `parent_id` = ".$order->getId().") AS t2 WHERE t1.`entity_id`=t2.max_entity_id");
        }
        
        // edit sales_flat_quote
        if ($orderPrev->getQuoteId()) $write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_quote')."` WHERE `entity_id`=".$orderPrev->getQuoteId());
        if ($order->getQuoteId()) $write->query("UPDATE `".$coreResource->getTableName('sales_flat_quote')."` SET `reserved_order_id` = '".$orderPrev->getIncrementId()."', `remote_ip`='".$orderPrev->getRemoteIp()."' WHERE `entity_id`=".$order->getQuoteId());
        
        // edit sales_flat_order
        $order->setCreatedAt($orderPrev->getCreatedAt()); // set created data
        $order->setEditIncrement(null); // set edit_increment
        $order->setOriginalIncrementId(null); // set original_increment_id
        $order->setRelationParentId(null); // set relation_parent_id
        $order->setRelationParentRealId(null); //set relation_parent_real_id
        $order->setRemoteIp($orderPrev->getRemoteIp()); // set remote_ip        
        $order->setIncrementId($orderPrev->getIncrementId()); // set increment_id
        //$order->setShippingInvoiced($orderPrev->getShippingInvoiced());
        //$order->setBaseShippingInvoiced($orderPrev->getBaseShippingInvoiced());
        $order->setIsEdited(1); // set is_edited flag       
        
        // transfer history        
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order_status_history')."` SET `parent_id` = ".$order->getId()." WHERE (`status`<>'canceled' OR `status` IS NULL) AND `parent_id`=".$orderPrevId);
        // transfer invoice
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_invoice')."` SET `order_id` = ".$order->getId()." WHERE `order_id`=".$orderPrevId);
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_invoice_grid')."` SET `order_id` = ".$order->getId()." WHERE `order_id`=".$orderPrevId);        
        
        // transfer creditmemo
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_creditmemo')."` SET `order_id` = ".$order->getId()." WHERE `order_id`=".$orderPrevId);
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_creditmemo_grid')."` SET `order_id` = ".$order->getId()." WHERE `order_id`=".$orderPrevId);
        // transfer shipment
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_shipment')."` SET `order_id` = ".$order->getId()." WHERE `order_id`=".$orderPrevId);
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_shipment_grid')."` SET `order_id` = ".$order->getId()." WHERE `order_id`=".$orderPrevId);
        
        $write->query("UPDATE `".$coreResource->getTableName('orderspro_order_item_group')."` SET `order_id` = ".$order->getId()." WHERE `order_id`=".$orderPrevId);
        
        // Amasty_Orderattach compatibility
        if (Mage::getConfig()->getModuleConfig('Amasty_Orderattach')->is('active', true)) {
            $write->query("DELETE FROM `".$coreResource->getTableName('amasty_amorderattach_order_field')."` WHERE `order_id`=".$order->getId());
            $write->query("UPDATE `".$coreResource->getTableName('amasty_amorderattach_order_field')."` SET `order_id` = ".$order->getId()." WHERE `order_id`=".$orderPrevId);
        }
        
        // update [base_]original_price previos order items
        if (Mage::helper('orderspro')->isKeepPurchasePrice()) {
            $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order_item')."` AS sfoi, `".$coreResource->getTableName('sales_flat_order_item')."` AS oldsfoi
                SET sfoi.`original_price` = oldsfoi.`original_price`, sfoi.`base_original_price` = oldsfoi.`base_original_price`
                WHERE sfoi.`order_id`='".$order->getId()."' AND oldsfoi.`order_id`='".$orderPrevId."' AND sfoi.`product_id`=oldsfoi.`product_id` AND (sfoi.`base_price`=oldsfoi.`base_price` OR sfoi.`product_options`=oldsfoi.`product_options`)");
        }
        
        
        // save prev order_item_id
        $prevItems = $orderPrev->getAllItems();
        $itemsData = array();
        foreach($prevItems as $prevItem) {
            $itemsData[$prevItem->getProductId()] = $prevItem->getItemId();
        }        
        
        // delete old order
        //$orderPrev->delete(); 
        Mage::helper('orderspro')->deleteOrderCompletelyById($orderPrev);        
        $order->save(); // save new order
        
        // to be less support :)
        $write->query('set foreign_key_checks = 0');
        
        // change entity_id        
        $orderId = $order->getId();
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order')."` SET `entity_id` = ".$orderPrevId." WHERE `entity_id`=".$orderId);
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order_grid')."` SET `entity_id` = ".$orderPrevId." WHERE `entity_id`=".$orderId);
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order_address')."` SET `parent_id` = ".$orderPrevId." WHERE `parent_id`=".$orderId);
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order_item')."` SET `order_id` = ".$orderPrevId." WHERE `order_id`=".$orderId);
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order_payment')."` SET `parent_id` = ".$orderPrevId." WHERE `parent_id`=".$orderId);
        $write->query("UPDATE `".$coreResource->getTableName('sales_payment_transaction')."` SET `order_id` = ".$orderPrevId." WHERE `order_id`=".$orderId);
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order_status_history')."` SET `parent_id` = ".$orderPrevId." WHERE `parent_id`=".$orderId);        
        $write->query("UPDATE `".$coreResource->getTableName('orderspro_order_item_group')."` SET `order_id` = ".$orderPrevId." WHERE `order_id`=".$orderId);
        
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_invoice')."` SET `order_id` = ".$orderPrevId." WHERE `order_id`=".$orderId);
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_invoice_grid')."` SET `order_id` = ".$orderPrevId." WHERE `order_id`=".$orderId);
        
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_creditmemo')."` SET `order_id` = ".$orderPrevId." WHERE `order_id`=".$orderId);
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_creditmemo_grid')."` SET `order_id` = ".$orderPrevId." WHERE `order_id`=".$orderId);
        
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_shipment')."` SET `order_id` = ".$orderPrevId." WHERE `order_id`=".$orderId);
        $write->query("UPDATE `".$coreResource->getTableName('sales_flat_shipment_grid')."` SET `order_id` = ".$orderPrevId." WHERE `order_id`=".$orderId);
        
        $write->query("UPDATE `{$coreResource->getTableName('sales_order_tax')}` SET `order_id`=".$orderPrevId." WHERE `order_id`=".$orderId);
        
        
        
        // transfer old order invoiced
        if ($prevHasInvoices) {
            $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order')."` AS sfo, `".$coreResource->getTableName('sales_flat_order_grid')."` AS sfog,
                    (SELECT 
                        `order_id`,
                        SUM(`weight`) AS weight,
                        SUM(`base_cost`) AS base_cost,
                        SUM(`discount_invoiced`) AS discount_invoiced, 
                        SUM(`base_discount_invoiced`) AS base_discount_invoiced,
                        SUM(`tax_invoiced`) AS tax_invoiced,
                        SUM(`base_tax_invoiced`) AS base_tax_invoiced,
                        SUM(`hidden_tax_invoiced`) AS hidden_tax_invoiced,
                        SUM(`base_hidden_tax_invoiced`) AS base_hidden_tax_invoiced,
                        SUM(`row_invoiced`) AS row_invoiced,
                        SUM(`base_row_invoiced`) AS base_row_invoiced,
                        SUM(`qty_invoiced`) AS qty_invoiced
                        FROM `".$coreResource->getTableName('sales_flat_order_item')."` WHERE `order_id` = ".$orderPrevId." AND `row_invoiced`>0 GROUP BY `order_id`) AS sfoi
                SET 
                    sfo.`discount_invoiced` = IFNULL(sfo.`discount_invoiced`, 0) + sfoi.`discount_invoiced`,
                    sfo.`base_discount_invoiced` = IFNULL(sfo.`base_discount_invoiced`, 0) + sfoi.`base_discount_invoiced`,
                    sfo.`tax_invoiced` = IFNULL(sfo.`tax_invoiced`, 0) + sfoi.`tax_invoiced`,
                    sfo.`base_tax_invoiced` = IFNULL(sfo.`base_tax_invoiced`, 0) + sfoi.`base_tax_invoiced`,
                    sfo.`hidden_tax_invoiced` = IFNULL(sfo.`hidden_tax_invoiced`, 0) + sfoi.`hidden_tax_invoiced`,
                    sfo.`base_hidden_tax_invoiced` = IFNULL(sfo.`base_hidden_tax_invoiced`, 0) + sfoi.`base_hidden_tax_invoiced`,                
                    sfo.`subtotal_invoiced` = IFNULL(sfo.`subtotal_invoiced`, 0) + sfoi.`row_invoiced`,
                    sfo.`base_subtotal_invoiced` = IFNULL(sfo.`base_subtotal_invoiced`, 0) + sfoi.`base_row_invoiced`,                
                    sfo.`base_total_invoiced_cost` = IFNULL(sfo.`base_total_invoiced_cost`, 0) + sfoi.`base_cost`,
                    sfo.`total_invoiced` = IFNULL(sfo.`total_invoiced`, 0) + sfoi.`row_invoiced`,
                    sfo.`base_total_invoiced` = IFNULL(sfo.`base_total_invoiced`, 0) + sfoi.`base_row_invoiced`,
                    sfo.`total_paid` = IFNULL(sfo.`total_paid`, 0) + sfoi.`row_invoiced`,
                    sfo.`base_total_paid` = IFNULL(sfo.`base_total_paid`, 0) + sfoi.`base_row_invoiced`,                
                    sfo.`discount_amount` = IFNULL(sfo.`discount_amount`, 0) + sfoi.`discount_invoiced`,
                    sfo.`base_discount_amount` = IFNULL(sfo.`base_discount_amount`, 0) + sfoi.`base_discount_invoiced`,
                    sfo.`tax_amount` = IFNULL(sfo.`tax_amount`, 0) + sfoi.`tax_invoiced`,
                    sfo.`base_tax_amount` = IFNULL(sfo.`base_tax_amount`, 0) + sfoi.`base_tax_invoiced`,
                    sfo.`subtotal` = IFNULL(sfo.`subtotal`, 0) + sfoi.`row_invoiced`,
                    sfo.`base_subtotal` = IFNULL(sfo.`base_subtotal`, 0) + sfoi.`base_row_invoiced`,
                    sfo.`subtotal_incl_tax` = IFNULL(sfo.`subtotal_incl_tax`, 0) + sfoi.`row_invoiced` + sfoi.`tax_invoiced`,
                    sfo.`base_subtotal_incl_tax` = IFNULL(sfo.`base_subtotal_incl_tax`, 0) + sfoi.`base_row_invoiced` + sfoi.`base_tax_invoiced`,
                    sfo.`grand_total` = IFNULL(sfo.`grand_total`, 0) + sfoi.`row_invoiced`,
                    sfo.`base_grand_total` = IFNULL(sfo.`base_grand_total`, 0) + sfoi.`base_row_invoiced`,
                    sfo.`weight` = IFNULL(sfo.`weight`, 0) + sfoi.`weight`,
                    sfo.`total_qty_ordered` = IFNULL(sfo.`total_qty_ordered`, 0) + sfoi.`qty_invoiced`,

                    sfog.`tax_amount` = IFNULL(sfog.`tax_amount`, 0) + sfoi.`tax_invoiced`,
                    sfog.`base_tax_amount` = IFNULL(sfog.`base_tax_amount`, 0) + sfoi.`base_tax_invoiced`,
                    sfog.`discount_amount` = IFNULL(sfog.`discount_amount`, 0) + sfoi.`discount_invoiced`,
                    sfog.`base_discount_amount` = IFNULL(sfog.`base_discount_amount`, 0) + sfoi.`base_discount_invoiced`,
                    sfog.`total_paid` = IFNULL(sfog.`total_paid`, 0) + sfoi.`row_invoiced`,
                    sfog.`base_total_paid` = IFNULL(sfog.`base_total_paid`, 0) + sfoi.`base_row_invoiced`,
                    sfog.`grand_total` = IFNULL(sfog.`grand_total`, 0) + sfoi.`row_invoiced`,
                    sfog.`base_grand_total` = IFNULL(sfog.`base_grand_total`, 0) + sfoi.`base_row_invoiced`,
                    sfog.`weight` = IFNULL(sfog.`weight`, 0) + sfoi.`weight`,
                    sfog.`total_qty_ordered` = IFNULL(sfog.`total_qty_ordered`, 0) + sfoi.`qty_invoiced`
                WHERE sfo.`entity_id`=".$orderPrevId." AND sfo.`entity_id`=sfog.`entity_id` AND sfo.`entity_id`=sfoi.`order_id` AND sfoi.`qty_invoiced`>0");
        }
        
        foreach ($itemsData as $productId=>$orderItemId) {
            $write->query("UPDATE `".$coreResource->getTableName('sales_flat_order_item')."` SET `item_id` = ".$orderItemId." WHERE `order_id`=".$orderPrevId . ' AND `product_id`='. $productId . ' LIMIT 1');
        }
        
        // Amasty_Orderattr compatibility
        if (Mage::getConfig()->getModuleConfig('Amasty_Orderattr')->is('active', true)) {
            $write->query("DELETE FROM `".$coreResource->getTableName('amasty_amorderattr_order_attribute')."` WHERE `order_id`=".$orderPrevId);
            $write->query("UPDATE `".$coreResource->getTableName('amasty_amorderattr_order_attribute')."` SET `order_id` = ".$orderPrevId." WHERE `order_id`=".$orderId);
        }
        
        return $orderPrevId;
    }
    
   
    
}