<?php
include_once("Mage/Adminhtml/controllers/Sales/OrderController.php");
class Vividads_Autologin_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
    protected $_publicActions = array('view', 'index');
    public function emailAction()
    {
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
}
