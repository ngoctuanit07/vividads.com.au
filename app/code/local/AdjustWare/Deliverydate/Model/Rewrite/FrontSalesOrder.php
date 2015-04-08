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
class AdjustWare_Deliverydate_Model_Rewrite_FrontSalesOrder extends Mage_Sales_Model_Order
{

    // override parent
    public function sendNewOrderEmail()
    {
        Mage::register('ait_send_order_email', 1); // aitoc code
        
        $oResult = parent::sendNewOrderEmail();
        
        Mage::unregister('ait_send_order_email'); // aitoc code

        return $oResult;
    }

    public function getShippingDescription()
    {
        $sText = $this->getData('shipping_description');
        
        if (Mage::registry('ait_send_order_email'))
        {
            if ($this->getDeliveryDate())
            {
                $sText .= '<br>' . Mage::helper('adjdeliverydate')->__('Preferred Delivery Date') . ': &nbsp;'; 
                $sText .= Mage::helper('adjdeliverydate')->formatDate($this->getDeliveryDate(), 'medium', Mage::getStoreConfig('checkout/adjdeliverydate/show_time')); 
            }
            
            if ($this->getDeliveryComment())
            {
                $sText .= '<br>' . Mage::helper('adjdeliverydate')->__('Comment') . ': &nbsp;'; 
                $sText .= $this->getDeliveryComment(); 
            }
        }
        
        return $sText;
    }

}