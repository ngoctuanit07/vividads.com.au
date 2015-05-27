<?php
class IWD_OrderManager_Adminhtml_Sales_ShippingController extends Mage_Adminhtml_Controller_Action
{
    public function editShippingAction()
    {
        $result = array('status' => 1);

        try {
            $params = $this->getRequest()->getParams();
            Mage::getModel('iwd_ordermanager/shipping')->updateOrderShipping($params);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error'=>$e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function getEditShippingFormAction()
    {
        $result['status'] = 0;

        try {
            $order_id = $this->getRequest()->getPost('order_id');
            $order = Mage::getModel('sales/order')->load($order_id);

            if ($order) {
                $result['form'] = $this->getLayout()
                    ->createBlock('iwd_ordermanager/adminhtml_sales_order_shipping_form')
                    ->setData('order', $order)
                    ->toHtml();

                $result['status'] = 1;
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}