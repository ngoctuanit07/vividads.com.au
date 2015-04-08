<?php

/*
  * @category       WL
  * @package        Auspost
  * @class          WL_Auspost_Model_Order
  * @description    Overwrite the Mage_Sales_Model_Order class to parse Shipping Information
 */

class WL_Auspost_Model_Order extends Mage_Sales_Model_Order
{
    /**
     * @return the combine information of Auspost Delivery Date Options and Shipping Description Info
     */
    public function getShippingDescription(){
        if($delivery_dates =  $this->getAuspostExtraOptions())       {

            $delivery_dates = Mage::helper('auspost')->parseDeliveryDates($delivery_dates);
            //$delivery_dates = ' [' . implode(' ; ',$delivery_dates_arr) . ']';
        }
        return parent::getShippingDescription() . $delivery_dates;
    }
}
