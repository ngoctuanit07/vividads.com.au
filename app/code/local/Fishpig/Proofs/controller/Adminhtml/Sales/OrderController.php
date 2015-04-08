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
class Fishpig_Proofs_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = array('view', 'index');

    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Sales');
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Mage_Adminhtml_Sales_OrderController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/order')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
        return $this;
    }

    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }

    /**
     * Orders grid
     */
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Orders'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Order grid
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * View order detale
     */
    public function viewAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Orders'));

        if ($order = $this->_initOrder()) {
            $this->_initAction();

            $this->_title(sprintf("#%s", $order->getRealOrderId()));

            $this->renderLayout();
        }
    }

    /**
     * Notify user
     */
    public function emailAction()
    {
exit('yahi to hai');
        if ($order = $this->_initOrder()) {
            try {
                $order->sendNewOrderEmail();
                $historyItem = Mage::getResourceModel('sales/order_status_history_collection')
                    ->getUnnotifiedForInstance($order, Mage_Sales_Model_Order::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }
                $this->_getSession()->addSuccess($this->__('The order email has been sent.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Failed to send the order email.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
    }
    /**
     * Cancel order
     */
    public function cancelAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $order->cancel()
                    ->save();
                $this->_getSession()->addSuccess(
                    $this->__('The order has been cancelled.')
                );
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order has not been cancelled.'));
                Mage::logException($e);
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Hold order
     */
    public function holdAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $order->hold()
                    ->save();
                $this->_getSession()->addSuccess(
                    $this->__('The order has been put on hold.')
                );
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order was not put on hold.'));
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Unhold order
     */
    public function unholdAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $order->unhold()
                    ->save();
                $this->_getSession()->addSuccess(
                    $this->__('The order has been released from holding status.')
                );
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order was not unheld.'));
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Manage payment state
     *
     * Either denies or approves a payment that is in "review" state
     */
    public function reviewPaymentAction()
    {
        try {
            if (!$order = $this->_initOrder()) {
                return;
            }
            $action = $this->getRequest()->getParam('action', '');
            switch ($action) {
                case 'accept':
                    $order->getPayment()->accept();
                    $message = $this->__('The payment has been accepted.');
                    break;
                case 'deny':
                    $order->getPayment()->deny();
                    $message = $this->__('The payment has been denied.');
                    break;
                case 'update':
                    $order->getPayment()
                        ->registerPaymentReviewAction(Mage_Sales_Model_Order_Payment::REVIEW_ACTION_UPDATE, true);
                    $message = $this->__('Payment update has been made.');
                    break;
                default:
                    throw new Exception(sprintf('Action "%s" is not supported.', $action));
            }
            $order->save();
            $this->_getSession()->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Failed to update the payment.'));
            Mage::logException($e);
        }
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
    }

    /**
     * Add order comment action
     */
    public function addCommentAction()
    {
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

    /**
     * Generate invoices grid for ajax request
     */
    public function invoicesAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_invoices')->toHtml()
        );
    }

    /**
     * Generate shipments grid for ajax request
     */
    public function shipmentsAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_shipments')->toHtml()
        );
    }

    /**
     * Generate creditmemos grid for ajax request
     */
    public function creditmemosAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_creditmemos')->toHtml()
        );
    }

    /**
     * Generate order history for ajax request
     */
    public function commentsHistoryAction()
    {
        $this->_initOrder();
        $html = $this->getLayout()->createBlock('adminhtml/sales_order_view_tab_history')->toHtml();
        /* @var $translate Mage_Core_Model_Translate_Inline */
        $translate = Mage::getModel('core/translate_inline');
        if ($translate->isAllowed()) {
            $translate->processResponseBody($html);
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Cancel selected orders
     */
    public function massCancelAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countCancelOrder = 0;
        $countNonCancelOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->canCancel()) {
                $order->cancel()
                    ->save();
                $countCancelOrder++;
            } else {
                $countNonCancelOrder++;
            }
        }
        if ($countNonCancelOrder) {
            if ($countCancelOrder) {
                $this->_getSession()->addError($this->__('%s order(s) cannot be canceled', $countNonCancelOrder));
            } else {
                $this->_getSession()->addError($this->__('The order(s) cannot be canceled'));
            }
        }
        if ($countCancelOrder) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been canceled.', $countCancelOrder));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Hold selected orders
     */
    public function massHoldAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countHoldOrder = 0;
        $countNonHoldOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->canHold()) {
                $order->hold()
                    ->save();
                $countHoldOrder++;
            } else {
                $countNonHoldOrder++;
            }
        }
        if ($countNonHoldOrder) {
            if ($countHoldOrder) {
                $this->_getSession()->addError($this->__('%s order(s) were not put on hold.', $countNonHoldOrder));
            } else {
                $this->_getSession()->addError($this->__('No order(s) were put on hold.'));
            }
        }
        if ($countHoldOrder) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been put on hold.', $countHoldOrder));
        }

        $this->_redirect('*/*/');
    }

    /**
     * Unhold selected orders
     */
    public function massUnholdAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countUnholdOrder = 0;
        $countNonUnholdOrder = 0;

        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->canUnhold()) {
                $order->unhold()
                    ->save();
                $countUnholdOrder++;
            } else {
                $countNonUnholdOrder++;
            }
        }
        if ($countNonUnholdOrder) {
            if ($countUnholdOrder) {
                $this->_getSession()->addError($this->__('%s order(s) were not released from holding status.', $countNonUnholdOrder));
            } else {
                $this->_getSession()->addError($this->__('No order(s) were released from holding status.'));
            }
        }
        if ($countUnholdOrder) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been released from holding status.', $countUnholdOrder));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Change status for selected orders
     */
    public function massStatusAction()
    {

    }

    /**
     * Print documents for selected orders
     */
    public function massPrintAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $document = $this->getRequest()->getPost('document');
    }

    /**
     * Print invoices for selected orders
     */
    public function pdfinvoicesAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($invoices->getSize() > 0) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print shipments for selected orders
     */
    public function pdfshipmentsAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($shipments->getSize()) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print creditmemos for selected orders
     */
    public function pdfcreditmemosAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'creditmemo'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print all documents for selected orders
     */
    public function pdfdocsAction(){
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($invoices->getSize()){
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }

                $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($shipments->getSize()){
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }

                $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)){
                        $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'docs'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf',
                    $pdf->render(), 'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Atempt to void the order payment
     */
    public function voidPaymentAction()
    {
        if (!$order = $this->_initOrder()) {
            return;
        }
        try {
            $order->getPayment()->void(
                new Varien_Object() // workaround for backwards compatibility
            );
            $order->save();
            $this->_getSession()->addSuccess($this->__('The payment has been voided.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Failed to void the payment.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/view', array('order_id' => $order->getId()));
    }

    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'hold':
                $aclResource = 'sales/order/actions/hold';
                break;
            case 'unhold':
                $aclResource = 'sales/order/actions/unhold';
                break;
            case 'email':
                $aclResource = 'sales/order/actions/email';
                break;
            case 'cancel':
                $aclResource = 'sales/order/actions/cancel';
                break;
            case 'view':
                $aclResource = 'sales/order/actions/view';
                break;
            case 'addcomment':
                $aclResource = 'sales/order/actions/comment';
                break;
            case 'creditmemos':
                $aclResource = 'sales/order/actions/creditmemo';
                break;
            case 'reviewpayment':
                $aclResource = 'sales/order/actions/review_payment';
                break;
            default:
                $aclResource = 'sales/order';
                break;

        }
        return Mage::getSingleton('admin/session')->isAllowed($aclResource);
    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     * Order transactions grid ajax action
     *
     */
    public function transactionsAction()
    {
        $this->_initOrder();
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Edit order address form
     */
    public function addressAction()
    {
        $addressId = $this->getRequest()->getParam('address_id');
        $address = Mage::getModel('sales/order_address')
            ->getCollection()
            ->addFilter('entity_id', $addressId)
            ->getItemById($addressId);
        if ($address) {
            Mage::register('order_address', $address);
            $this->loadLayout();
            // Do not display VAT validation button on edit order address form
            $addressFormContainer = $this->getLayout()->getBlock('sales_order_address.form.container');
            if ($addressFormContainer) {
                $addressFormContainer->getChild('form')->setDisplayVatValidationButton(false);
            }

            $this->renderLayout();
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Save order address
     */
    public function addressSaveAction()
    {
        $addressId  = $this->getRequest()->getParam('address_id');
        $address    = Mage::getModel('sales/order_address')->load($addressId);
        $data       = $this->getRequest()->getPost();
        if ($data && $address->getId()) {
            $address->addData($data);
            try {
                $address->implodeStreetAddress()
                    ->save();
                $this->_getSession()->addSuccess(Mage::helper('sales')->__('The order address has been updated.'));
                $this->_redirect('*/*/view', array('order_id'=>$address->getParentId()));
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('sales')->__('An error occurred while updating the order address. The address has not been changed.')
                );
            }
            $this->_redirect('*/*/address', array('address_id'=>$address->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }
    
        /************************************ Start by dev ***********************************************/
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
                
                try {
                        $chkSystem = $connectionRead->query($sqlPaymentSystem);
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
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        

            
    }
    
    //Proof create update for order
    public function proofsAction()
    {
        extract($_REQUEST);
        
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $order = Mage::getModel('sales/order')->load($orderid);
        $storId =  $order->getStoreId();
        $customer_id =  $order->getCustomerId();
      
        
            foreach($_FILES['item_file']['name'] as $key=>$value)
            {
                
                if(isset($_FILES['item_file']['name'][$key]) and (file_exists($_FILES['item_file']['tmp_name'][$key]))) {
                   
                    $file_name=$_FILES['item_file']['name'][$key];
                    
                    $expFilename=explode(".",$file_name);
                    $fileNameVal=time().".".end($expFilename);
                    
                    
                    $mediaPath=Mage::getBaseDir('media') . DS ;
                    //$path2 = $mediaPath.'upload_image/'.$fileNameVal;
                    $path2 = $mediaPath.'proofs/'.$fileNameVal;
                    chmod($path2,0777);
                    $filepath = $fileNameVal;
                    
                    //file_put_contents($path2, $_FILES['item_file']['tmp_name'][$key]);
                    if(move_uploaded_file($_FILES['item_file']['tmp_name'][$key],$path2))
                    {
                        //$tableName = Mage::getSingleton('core/resource')->getTableName('upload_image');
                        $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');
                        
                        //$sqlProofsSystem="INSERT INTO ".$tableName."  SET store_id = '".$storId."', order_id = '".$order->getId()."', customer_id = '".$customer_id."' , item_id = '".$item[$key]."', file = '".$filepath."', status = 'Awaiting Proof Approval',   postdate = NOW(), proof_type = 'order'";
                       // $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
                        //$sqlPaymentSystem="INSERT INTO ".$tableName."  SET customer_id = '".$customer_id."' , filename = '".$file_name."',status = 2, created_time = NOW()";
                       // $sqlPaymentSystem="INSERT INTO ".$tableName."  SET customer_id = '".$customer_id."' , filename = '".$fileNameVal."',status = 2, created_time = NOW()";
                        $connectionWrite->beginTransaction();
                        $data = array();
                        $data['store_id']= $storId;
                        $data['order_id']=$order->getId();
                        $data['customer_id']= $customer_id;
                        $data['item_id']=$item[$key];
                        $data['file']= $filepath;
                        $data['status']='Awaiting Proof Approval';
                        $data['postdate']=Now();
                        $data['proof_type'] = 'quote';
                        $connectionWrite->insert($tableName, $data);
                        $connectionWrite->commit();
                      
                    }
                }
            }
            
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
    }
    
    //Proof create update for quote
    public function proofsquoteAction()
    {
        extract($_REQUEST);
        
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $quote = Mage::getModel('Quotation/Quotation')->load($quoteid);
        $storId =  $quote->getStoreId();
        $customer_id =  $quote->getCustomerId();
      
        
            foreach($_FILES['item_file']['name'] as $key=>$value)
            {
                
                if(isset($_FILES['item_file']['name'][$key]) and (file_exists($_FILES['item_file']['tmp_name'][$key]))) {
                   
                    $file_name=$_FILES['item_file']['name'][$key];
                    
                    $expFilename=explode(".",$file_name);
                    $fileNameVal=time().".".end($expFilename);
                    
                    
                    $mediaPath=Mage::getBaseDir('media') . DS ;
                    //$path2 = $mediaPath.'upload_image/'.$fileNameVal;
                    $path2 = $mediaPath.'proofs/'.$fileNameVal;
                    chmod($path2,0777);
                    $filepath = $fileNameVal;
                    
                    //file_put_contents($path2, $_FILES['item_file']['tmp_name'][$key]);
                    if(move_uploaded_file($_FILES['item_file']['tmp_name'][$key],$path2))
                    {
                        //$tableName = Mage::getSingleton('core/resource')->getTableName('upload_image');
                        $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');
                        
                       // $sqlProofsSystem="INSERT INTO ".$tableName."  SET store_id = '".$storId."', order_id = '".$quoteid."', customer_id = '".$customer_id."' , item_id = '".$item[$key]."', file = '".$filepath."', status = 'Awaiting Proof Approval',   postdate = NOW(), proof_type = 'quote'";
                       // $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
                        //$sqlPaymentSystem="INSERT INTO ".$tableName."  SET customer_id = '".$customer_id."' , filename = '".$file_name."',status = 2, created_time = NOW()";
                       // $sqlPaymentSystem="INSERT INTO ".$tableName."  SET customer_id = '".$customer_id."' , filename = '".$fileNameVal."',status = 2, created_time = NOW()";
                        
                      $connectionWrite->beginTransaction();
                        $data = array();
                        $data['store_id']= $storId;
                        $data['order_id']=$quoteid;
                        $data['customer_id']= $customer_id;
                        $data['item_id']=$item[$key];
                        $data['file']= $filepath;
                        $data['status']='Awaiting Proof Approval';
                        $data['postdate']=Now();
                        $data['proof_type'] = 'quote';
                        $connectionWrite->insert($tableName, $data);
                        $connectionWrite->commit();
                    }
                }
            }
            
        $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteid));
    }
    
    public function downloadAction()
    {
        $file_path=Mage::getBaseDir('media').'/proofs/'.$this->getRequest()->getParam('file');
    
    
    //Call the download function with file path,file name and file type
    //download_file($file_path, ''.$_REQUEST['file'].'', 'text/plain');
    $this->download_file($file_path, ''.$this->getRequest()->getParam('file').'', 'text/plain');
    
    
    }
    
    public function download_file($file, $name, $mime_type='')
    {
       
        $size = filesize($file);
        $name = rawurldecode($name);
       
        $known_mime_types=array(
               "pdf" => "application/pdf",
               "txt" => "text/plain",
               "html" => "text/html",
               "htm" => "text/html",
               "exe" => "application/octet-stream",
               "zip" => "application/zip",
               "doc" => "application/msword",
               "xls" => "application/vnd.ms-excel",
               "ppt" => "application/vnd.ms-powerpoint",
               "gif" => "image/gif",
               "png" => "image/png",
               "jpeg"=> "image/jpg",
               "jpg" =>  "image/jpg",
               "php" => "text/plain"
        );
       
        if($mime_type==''){
                $file_extension = strtolower(substr(strrchr($file,"."),1));
                if(array_key_exists($file_extension, $known_mime_types)){
                       $mime_type=$known_mime_types[$file_extension];
                } else {
                       $mime_type="application/force-download";
                };
        };
       
        @ob_end_clean(); 
       
        // required for IE, otherwise Content-Disposition may be ignored
        if(ini_get('zlib.output_compression'))
         ini_set('zlib.output_compression', 'Off');
       
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; file="'.$name.'"');
        header("Content-Transfer-Encoding: binary");
        header('Accept-Ranges: bytes');
        header("Cache-control: private");
        header('Pragma: private');
        readfile($file); 
    }
    
    //Proof quantity update for quote
    public function quantityupdateAction()
    {
        extract($_REQUEST);
        //print_r($_REQUEST);
        $flag = 0;
        
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        foreach($quantity as $key=>$qty)
        {
            $tableItemName = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
            //$sqlItemSystem="SELECT * FROM ".$tableItemName."  WHERE item_id = '".$item[$key]."'";
            $sqlItemSystem = $connectionRead->select()
				->from($tableItemName, array('*'))
				->where('item_id =?', $item[$key]);
            $chkItem = $connectionRead->query($sqlItemSystem);
            $fetchItem = $chkItem->fetch();
            
            if($fetchItem['qty_ordered'] >= $qty)
            {
                $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');
                //$sqlProofsSystem="UPDATE ".$tableName."  SET quantity = '".$qty."' WHERE entity_id = '".$key."'";
               // $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
                
                $connectionWrite->beginTransaction();
                $data = array();
                $data['quantity'] = $qty;
                $where = $connectionWrite->quoteInto('entity_id =?', $key);
                $connectionWrite->update($tableName, $data, $where);
                $connectionWrite->commit();
            }
            else
            {
                $flag =1;
            }
        }
        if($flag == 1)
        Mage::getSingleton('adminhtml/session')->addError($this->__('Proofs quantity are not more than item quantity.'));
            
        $this->_redirect('*/sales_order/view', array('order_id' => $orderid));
    }
    
    //Proof quantity update for order
     public function quantityupdatequoteAction()
    {
        extract($_REQUEST);
        //print_r($_REQUEST);
        $flag = 0;
        foreach($quantity as $key=>$qty)
        {
            $tableItemName = Mage::getSingleton('core/resource')->getTableName('quotation_items');
           // $sqlItemSystem="SELECT * FROM ".$tableItemName."  WHERE quotation_item_id = '".$item[$key]."'";
            $sqlItemSystem = $connectionRead->select()
				->from($tableItemName, array('*'))
				->where('quotation_item_id=?', $item[$key]);
            $chkItem = $connectionRead->query($sqlItemSystem);
            $fetchItem = $chkItem->fetch();
            
            if($fetchItem['qty'] >= $qty)
            {
                $tableName = Mage::getSingleton('core/resource')->getTableName('proofs');
               // $sqlProofsSystem="UPDATE ".$tableName."  SET quantity = '".$qty."' WHERE entity_id = '".$key."'";
               // $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
                
                $connectionWrite->beginTransaction();
                $data = array();
                $data['quantity'] = $qty;
                $where = $connectionWrite->quoteInto('entity_id =?', $key);
                $connectionWrite->update($tableName, $data, $where);
                $connectionWrite->commit();
            }
            else
            {
                $flag =1;
            }
        }
        if($flag == 1)
        Mage::getSingleton('adminhtml/session')->addError($this->__('Proofs quantity are not more than item quantity.'));
            
       $this->_redirect('Quotation/Admin/edit', array('quote_id' => $quoteid));
    }
    
     public function UpdateAction()
	{
		//create planning
                
                extract($_REQUEST);
                $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
                $connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
                
		$orderId = $_REQUEST['order_id'];
		
		try 
		{
			$order = Mage::getModel('sales/order')->load($orderId);
			$created_date = $order->getCreatedAt();
			foreach($order_date as $key=>$value)
                        {
			
                            $temptableShipping=Mage::getSingleton('core/resource')->getTableName('quote_planning');
                            //$sqlShipping="UPDATE  ".$temptableShipping." SET order_placed_date = '$value', artwork_date = '$artwork[$key]', proof_date = '$proof[$key]', start_date ='$start[$key]', shipping_date = '$shipping_date[$key]', delivery_date = '$delivery_date[$key]' WHERE entity_id = '".$key."'";
                            //$chkShipping = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlShipping);
                            
                            $connectionWrite->beginTransaction();
                            $data = array();
                            $data['order_placed_date'] = $value;
                            $data['artwork_date'] = $artwork[$key];
                            $data['proof_date'] = $proof[$key];
                            $data['start_date'] = $start[$key];
                            $data['shipping_date'] = $shipping_date[$key];
                            $data['delivery_date'] = $delivery_date[$key];
                            $where = $connectionWrite->quoteInto('entity_id =?', $key);
                            $connectionWrite->update($temptableShipping, $data, $where);
                            $connectionWrite->commit();
                        }
			

			
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Planning created'));
		}
		catch (Exception $ex)
		{
			Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
		}
		
		//redirect
        $url = Mage::helper("adminhtml")->getUrl("admin/sales_order/view/order_id/".$orderId);
        $url = str_replace('p//s','p/admin/s',$url);
        
        Mage::log($url);
        Mage::app()->getResponse()->setRedirect($url);
        
    	//$this->_redirect($url, array('order_id' => $orderId));
    	
	}
    /************************************ End by dev ***********************************************/
}
