<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      1.1.7
 * @license:     5aaeipF7ooGEnIcLG9rWordPqCAsU9nCJvGBjy56tC
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @author Adjustware
 */
class AdjustWare_Deliverydate_Model_Observer
{
    protected $_allowPaypalExpress = false;
    
    public function processOrderSaved($observer){
        $order = $observer->getEvent()->getOrder();
        if($order->getId())
            Mage::getModel('adjdeliverydate/quote')->saveDeliveryForOrder($order);
        //$order->saveDeliveryDate();
    }

    public function controller_action_predispatch_adminhtml_sales_order_create_loadBlock($observer) {
        Mage::getModel('adjdeliverydate/step')->process();
    }

    public function processPaypalOrderSaved($observer) {
        if(!$this->_allowPaypalExpress) return false;
        $this->_allowPaypalExpress = false; //preveng going here once again
        return $this->processOrderSaved($observer);
    }
    
    public function allowPaypalOrderSaved($observer) {
        $this->_allowPaypalExpress = true;
    }
}