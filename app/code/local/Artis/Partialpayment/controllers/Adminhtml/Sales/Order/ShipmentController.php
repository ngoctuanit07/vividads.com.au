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
require_once 'Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php';

class Artis_Partialpayment_Adminhtml_Sales_Order_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController
{
    public function saveAction()
    {
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write'); 
        
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
                
                //$sqlHistory ="SELECT * FROM ".$temptableComment." WHERE parent_id = '".$order->getId()."'";
                $sqlHistory = $connectionRead->select()
				->from($temptableComment, array('*'))
				->where('parent_id=?', $order->getId());
                $chkHistory = $connectionRead->fetchAll($sqlHistory);
                
                
                foreach($chkHistory as $history)
                {
                    
                    /*$sqlComment="INSERT INTO ".$temptableCommentIn." SET parent_id = '".$shipment->getId()."', 
                                   comment = '".addslashes($history['comment'])."' ,
                                   created_at ='".$history['created_at']."',
                                   is_customer_notified = '".$history['is_customer_notified']."',
                                   is_visible_on_front = '".$history['is_visible_on_front']."'";
                                   
                    $chkComment = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlComment);*/
                    $connectionWrite->beginTransaction();
                    $data = array();
                    $data['parent_id'] = $shipment->getId();
                    $data['comment'] = addslashes($history['comment']);
                    $data['created_at'] = $history['created_at'];
                    $data['is_customer_notified'] = $history['is_customer_notified'];
                    $data['is_visible_on_front'] = $history['is_visible_on_front'];
                    $connectionWrite->insert($temptableCommentIn, $data);
                    $connectionWrite->commit();
                }

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


    /**
     * Add comment to shipment history
     */
    public function addCommentAction()
    {
        try {
	    
	     /******************** Start to add comment 29_01_2014*******************************/
	    $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	    $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	    $tableNameComment = Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_comment');
	    $tableName1 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
	    $tableold_ship = Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment');
	    
	    $select2 = $connectionRead->select()->from($tableName1, array('*'))->where('entity_id=?',$this->getRequest()->getParam('id'));
	    $row2 = $connectionRead->fetchRow($select2);
	    
	    $select3 = $connectionRead->select()->from($tableold_ship, array('*'))->where('entity_id=?',$this->getRequest()->getParam('id'));
	    $row3 = $connectionRead->fetchAll($select3);
	    
	    if(count($row3) == 0)
	    {
		$connectionWrite->beginTransaction();
		$data2 = array();
		$data2['entity_id']= $this->getRequest()->getParam('id');
		$data2['order_id']= $row2['order_id'];
		$data2['created_at']= NOW();
		$data2['updated_at']= NOW();
		
		$connectionWrite->insert($tableold_ship, $data2);
		$connectionWrite->commit();
	    }
            /******************** End to add comment 29_01_2014*******************************/
            $this->getRequest()->setParam(
                'shipment_id',
                $this->getRequest()->getParam('id')
            );
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                Mage::throwException($this->__('Comment text field cannot be empty.'));
            }
	    
            $shipment = $this->_initShipment();
            $shipment->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );
            $shipment->sendUpdateEmail(!empty($data['is_customer_notified']), $data['comment']);
            $shipment->save();
	    
	   
            //Add the comment in the order
            $order = $shipment->getOrder();
            //$order = Mage::getModel('sales/order')->load($row2['order_id']);
            $order->addStatusHistoryComment($data['comment'])
                    ->setIsVisibleOnFront($data['is_visible_on_front'])
                    ->setIsCustomerNotified($data['is_customer_notified']);
            $order->save();
            
            
            // Add comment in the invoice
                if ($order->hasInvoices()) {
                    // "$_eachInvoice" is each of the Invoice object of the order "$order"
                    foreach ($order->getInvoiceCollection() as $_eachInvoice) {
                        //$invoiceIncrementId = $_eachInvoice->getIncrementId();
                        
                        $_eachInvoice->addComment(
                            $data['comment'],
                            isset($data['is_customer_notified']),
                            isset($data['is_visible_on_front'])
                        );
                        $_eachInvoice->save();
                    }
                }

            $this->loadLayout(false);
            $response = $this->getLayout()->getBlock('shipment_comments')->toHtml();
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage()
            );
            $response = Mage::helper('core')->jsonEncode($response);
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot add new comment.')
            );
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

}
