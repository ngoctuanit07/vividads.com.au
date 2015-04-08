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
class AdjustWare_Deliverydate_Block_Rewrite_SalesOrderPrint extends Mage_Sales_Block_Order_Print
{
    public function getOrder()
    {
        $order = parent::getOrder();
        if (!$order->getShippingDescriptionUpdated())
        {
            $orderShippingDescription = $order->getShippingDescription();

            if ($order->getDeliveryDate())
            {
                $deliveryDate = Mage::helper('adjdeliverydate')->formatDate($order->getDeliveryDate(), 'medium', Mage::getStoreConfig('checkout/adjdeliverydate/show_time'));
                $orderShippingDescription .= '<h2>' . Mage::helper('adjdeliverydate')->__('Preferred Delivery Date') . '</h2>';
                $orderShippingDescription .= '<p>' . $deliveryDate . '</p>';
            }
            
            if ($order->getDeliveryComment()) 
            {
                $deliveryComment = $order->getDeliveryComment();
                $orderShippingDescription .= '<h2>' . Mage::helper('adjdeliverydate')->__('Comment') . '</h2>';
                $orderShippingDescription .= '<p>' . $deliveryComment . '</p>';
            }
            
            $order->setShippingDescription($orderShippingDescription);
            $order->setShippingDescriptionUpdated(true);
        }
        return $order;
    }
    
    public function escapeHtml($data, $allowedTags = null)
    {
        return $data;
    }
}