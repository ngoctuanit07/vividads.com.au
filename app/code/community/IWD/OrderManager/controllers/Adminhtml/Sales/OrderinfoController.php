<?php
class IWD_OrderManager_Adminhtml_Sales_OrderinfoController extends Mage_Adminhtml_Controller_Action
{
    public function getEditInfoFormAction()
    {
        $result['status'] = 1;

        try {
            $order_id = $this->getRequest()->getPost('order_id');
            $order = Mage::getModel('sales/order')->load($order_id);

            if ($order && $order->getEntityId()) {
                $result['form'] = $this->getLayout()
                    ->createBlock('iwd_ordermanager/adminhtml_sales_order_info_form')
                    ->setData('order', $order)
                    ->toHtml();
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result['status'] = 0;
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function editInfoAction()
    {
        try {
            $params = $this->getRequest()->getParams();
            $result = array('status' => 1);
            Mage::getModel('iwd_ordermanager/order_info')->updateOrderInfo($params);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}