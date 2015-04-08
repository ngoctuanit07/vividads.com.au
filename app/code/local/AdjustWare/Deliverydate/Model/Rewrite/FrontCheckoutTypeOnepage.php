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
class AdjustWare_Deliverydate_Model_Rewrite_FrontCheckoutTypeOnepage extends Mage_Checkout_Model_Type_Onepage
//class AdjustWare_Deliverydate_Model_Checkout_Type_Onepage extends AdjustWare_Giftreg_Model_Checkout_Onepage
{
    public function saveShippingMethod($shippingMethod)
    {
        $errors = Mage::getModel('adjdeliverydate/step')->process();
        if ($errors)
            return $errors;

        $quoteAddress = $this->getQuote()->getShippingAddress();
        Mage::getModel('adjdeliverydate/quote')->saveDelivery($quoteAddress);

        return parent::saveShippingMethod($shippingMethod);
    }
    
    public function saveOrder()
    {
        if(Mage::helper('adjdeliverydate')->isOPCEnabled()) {
            $errors = Mage::getModel('adjdeliverydate/step')->process();
            if ($errors)
               return $errors;
        }
       
        return parent::saveOrder();
    }
}