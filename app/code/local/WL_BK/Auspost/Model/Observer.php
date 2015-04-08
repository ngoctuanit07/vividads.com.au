<?php
/*
  * @category   WL
  * @package    Auspost
  * @class      WL_Auspost_Model_Observer
  * @description hook to checkout event processes to save Ausopost shipping method.
 */

class WL_Auspost_Model_Observer extends Varien_Object
{
    
/**
 * Save Auspost date selection options to Session
 * 
 * @param $evt
 */
    public function saveShippingMethod($evt){
		$request = $evt->getRequest();
        $auspost_delivery_dates = $request->getParam('auspost_delivery_dates',false);
        if($auspost_delivery_dates){
            Mage::getSingleton('checkout/session')->setAuspostDeliveryDates($auspost_delivery_dates);
        }
	}

/**
 * 
 * Get date selection options from Session and add to saved order
 * 
 * @param $evt
 */
     
    public function saveOrderAfter($evt){
        if($auspost_delivery_dates=Mage::getSingleton('checkout/session')->getAuspostDeliveryDates()) {
            $order = $evt->getOrder();
            $order->setData('auspost_extra_options', $auspost_delivery_dates);
            $order->save();
        }
	}
}