<?php
class IWD_OrderManager_Model_Order_Items extends Mage_Sales_Model_Order_Item
{
    protected $params;  
	
	
 
    public function updateOrderItems($params)  
    { 
        $this->init($params);

        if (isset($params['confirm_edit']) && !empty($params['confirm_edit'])) {
            $this->addChangesToConfirm();
        } else {
            $this->editItems();
            $this->addChangesToLog();
        }
    }

    protected function init($params)
    {
        $this->params = $params;
        if (empty($this->params) || !isset($this->params['order_id'])) {
            throw new Exception("Order id is not defined");
        }
    }

    protected function addChangesToConfirm()
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $order_id = $this->params['order_id'];
        $items = $this->params['items'];

        Mage::getModel('iwd_ordermanager/order_estimate')->estimateEditItems($order_id, $items);

        $logger->addCommentToOrderHistory($order_id, 'wait');
        $logger->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::ITEMS, $order_id, $this->params);

        $message = Mage::helper('iwd_ordermanager')
            ->__('Updates was not applied now! Customer get email with confirm link. Order will be updated after confirm.');
        Mage::getSingleton('adminhtml/session')->addNotice($message);
    }

    protected function editItems()
    {
        $order_id = isset($this->params['order_id']) ? $this->params['order_id'] : null;
        $items = isset($this->params['items']) ? $this->params['items'] : null;
        $notify = isset($this->params['notify']) ? $this->params['notify'] : null;
        $message = isset($this->params['message']) ? $this->params['message'] : null;

        $status = Mage::getModel('iwd_ordermanager/order_edit')->editItems($order_id, $items);

        if ($status == 1 && $notify) {
            $result['notify'] = Mage::getModel('iwd_ordermanager/notify_notification')->sendNotifyEmail($order_id, $message);
        }
    }

    protected function addChangesToLog()
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $order_id = $this->params['order_id'];

        $notify = isset($this->params['notify']) && !empty($this->params['notify']);

        $logger->addCommentToOrderHistory($order_id, false, $notify);
        $logger->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::ITEMS, $order_id);
    }
}