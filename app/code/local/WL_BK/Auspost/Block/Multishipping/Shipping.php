<?php

/**
  * @category       WL
  * @package        Auspost
  * @class          WL_Auspost_Block_Multishipping_Shipping
  * @description    Override the Mage_Checkout_Block_Multishipping_Shipping class, remove Auspost shipping method from Multishipping checkout
 */

class WL_Auspost_Block_Multishipping_Shipping extends Mage_Checkout_Block_Multishipping_Shipping
{
    /**
     * @param $address : The Shipping address information
     * @return The shipping methods which have no auspost method.
     */
    public function getShippingRates($address)
    {
        $groups = parent::getShippingRates($address);
        if(isset($groups['auspost']))
            unset($groups['auspost']);
        return $groups;
    }
}
