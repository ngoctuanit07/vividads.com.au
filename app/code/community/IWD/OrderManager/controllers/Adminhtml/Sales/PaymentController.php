<?php
class IWD_OrderManager_Adminhtml_Sales_PaymentController extends Mage_Adminhtml_Controller_Action
{
    public function editPaymentAction()
    {
        $result = array('status' => 1);

        try {
            $params = $this->getRequest()->getParams();
            Mage::getModel('iwd_ordermanager/payment_payment')->updateOrderPayment($params);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function getEditPaymentFormAction()
    {
        $result = array('status' => 0);

        try {
            $order_id = $this->getRequest()->getPost('order_id');
            if ($order_id) {
                $order = Mage::getModel('sales/order')->load($order_id);
                if ($order) {
                    $form = $this->getLayout()
                        ->createBlock('iwd_ordermanager/adminhtml_sales_order_payment_form')
                        ->setData('order', $order)
                        ->toHtml();

                    $result['form'] = preg_replace('/(<input.+value)="[0-9]+"(.*>)/i', '$1$2', $form);
                    $result['status'] = 1;
                }
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