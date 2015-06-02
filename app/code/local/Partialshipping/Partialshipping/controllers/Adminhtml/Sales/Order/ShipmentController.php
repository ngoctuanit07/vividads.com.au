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
 * Adminhtml sales order shipment controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once 'Artis/Partialpayment/controllers/Adminhtml/Sales/Order/ShipmentController.php';


class Partialshipping_Partialshipping_Adminhtml_Sales_Order_ShipmentController extends Artis_Partialpayment_Adminhtml_Sales_Order_ShipmentController
{
   

    /**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     *
     * @return null
     */
    public function saveAction()
    {
        ///echo "__gdfgdf"; exit;
        
        
        $data = $this->getRequest()->getPost('shipment');
        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        try {
            $shipment = $this->_initShipment();
            if (!$shipment) {
                $this->_forward('noRoute');
                return;
            }

            $shipment->register();
            $comment = '';
            if (!empty($data['comment_text'])) {
                $shipment->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
            }

            if (!empty($data['send_email'])) {
                $shipment->setEmailSent(true);
            }

            $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
            $responseAjax = new Varien_Object();
            $isNeedCreateLabel = isset($data['create_shipping_label']) && $data['create_shipping_label'];

            if ($isNeedCreateLabel && $this->_createShippingLabel($shipment)) {
                $responseAjax->setOk(true);
            }

            $this->_saveShipment($shipment);

            $shipment->sendEmail(!empty($data['send_email']), $comment);

            $shipmentCreatedMessage = $this->__('The shipment has been created.');
            $labelCreatedMessage    = $this->__('The shipping label has been created.');
            
            // For adding the comment masage from order to invoice
                $order = $shipment->getOrder();
                $temptableComment = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history');
                $temptableCommentIn = Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_comment');
                
                $sqlHistory ="SELECT * FROM ".$temptableComment." WHERE parent_id = '".$order->getId()."'";
                $chkHistory = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlHistory);
                
                foreach($chkHistory as $history)
                {
                    
                    $sqlComment="INSERT INTO ".$temptableCommentIn." SET parent_id = '".$shipment->getId()."', 
                                   comment = '".addslashes($history['comment'])."' ,
                                   created_at ='".$history['created_at']."',
                                   is_customer_notified = '".$history['is_customer_notified']."',
                                   is_visible_on_front = '".$history['is_visible_on_front']."'";
                                   
                    $chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);
                }

            
            
            
        /************************** 8-11-2013 SOC ********************************/
        
        //$data = $this->getRequest()->getPost('shipment');
        $shipAd=$_REQUEST['shipAddress'];
        $shipMd=$_REQUEST['shipmethod'];
        $ordId=$_REQUEST['orderId'];
        $ordincrId=$_REQUEST['orderIncrementId'];
        $custmerId=$_REQUEST['customerId'];
        $shM=explode("__",$shipMd);
        $shMethod=$shM[0];
        $shPrice=$shM[1];
        
        
        $order = Mage::getModel('sales/order')->load($ordId);
        $shpAmt=$order->getShippingAmount();
        
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $tableName2 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
        
        $select2 = $connectionRead->select()->from($tableName2, array('*'))->where('entity_id=?',$ordId);
        $row2 = $connectionRead->fetchRow($select2);
        $baseTotalDue=$row2['base_total_due'];
        $TotalDue=$row2['total_due'];
        $baseGrandTotal=$row2['base_grand_total'];
        $GrandTotal=$row2['grand_total'];
        
        //echo "old : ".$baseTotalDue; echo "<br>";
        //echo "old : ".$TotalDue; echo "<br>";
        
        if($shPrice > $shpAmt){
            $diff=$shPrice-$shpAmt;
            $baseTotalDueN=$baseTotalDue + $diff;
            $TotalDueN=$TotalDue + $diff;
            $baseGrandTotalN=$baseGrandTotal + $diff;
            $GrandTotalN=$GrandTotal + $diff;
            
        }elseif($shpAmt > $shPrice){
            $diff=$shpAmt - $shPrice;
            $baseTotalDueN=$baseTotalDue - $diff;
            $TotalDueN=$TotalDue - $diff;
            $baseGrandTotalN=$baseGrandTotal - $diff;
            $GrandTotalN=$GrandTotal - $diff;
            
        }else{
            
        }
        
        if($baseTotalDueN != $baseTotalDue){
            
            $connectionWrite->beginTransaction();
            $data = array();
            $data['base_total_due'] = $baseTotalDueN;
            $data['total_due'] = $TotalDueN;
            $data['base_grand_total'] = $baseGrandTotalN;
            $data['grand_total'] = $GrandTotalN;
            
            $where = $connectionWrite->quoteInto('entity_id =?', $ordId);
            $connectionWrite->update($tableName2, $data, $where);
            $connectionWrite->commit();
            
        }
        
        
        if($shipAd !='' || $shipMd !='' ){
           $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
           $tableName1 = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
           
           $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
           $tableName = Mage::getSingleton('core/resource')->getTableName('partialshipping_details');
            
           foreach($_REQUEST['shipment']['items'] as $key=>$qty){
                if($qty != 0){
                    
                    $select = $connectionRead->select()->from($tableName1, array('*'))->where('item_id=?',$key);
                    $row = $connectionRead->fetchRow($select);
                    
                    $connectionWrite->beginTransaction();
                    $data = array();
                    $data['order_id']= $ordId;
                    $data['order_increment_id']=$ordincrId;
                    $data['shipping_address_id']=$shipAd;
                    $data['shipping_method']=$shMethod;
                    $data['shiping_price']=$shPrice;
                    $data['product_id']=$row['product_id'];
                    $data['product_qty']=$qty;
                    $data['customer_id']=$custmerId;
                    $connectionWrite->insert($tableName, $data);
                    $connectionWrite->commit();
                }
            
           }
        }
        
        
        /************************** 8-11-2013 EOC ********************************/
        
         $this->_getSession()->addSuccess($isNeedCreateLabel ? $shipmentCreatedMessage . ' ' . $labelCreatedMessage
                : $shipmentCreatedMessage);
            Mage::getSingleton('adminhtml/session')->getCommentText(true);   
                    
        } catch (Mage_Core_Exception $e) {
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage($e->getMessage());
            } else {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
            }
        } catch (Exception $e) {
            Mage::logException($e);
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage(
                    Mage::helper('sales')->__('An error occurred while creating shipping label.'));
            } else {
                $this->_getSession()->addError($this->__('Cannot save shipment.'));
                $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
            }

        }
        if ($isNeedCreateLabel) {
            $this->getResponse()->setBody($responseAjax->toJson());
        } else {
            $this->_redirect('*/sales_order/view', array('order_id' => $shipment->getOrderId()));
        }
    }
    

    
}
?>