<?php

/**
 * @category   WL
 * @package    Auspost
 * @class      WL_Auspost_Block_Cart_Shipping
 * @description override the Mage_Checkout_Block_Cart_Shipping class, give extra methods for estimating shipping tax and cost.
 */

class WL_Auspost_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
{
     /**
     * @return array the expected orders of Auspost Shipping methods
     */
    public function getEstimateRates($postcode)
    {
        $rates = parent::getEstimateRates();
        if (isset($rates['auspost'])) {
            $new_rates = array();
            foreach ($rates['auspost'] as $_rate) {
                $new_rates[$_rate->getCode()] = $_rate;
            }

            $rates['auspost'] = array();
            $default_services = WL_Auspost_Model_Config_ServiceMultiSelectionOptions::getAllOptions();
            foreach ($default_services as $code => $value) {
                if(!isset($new_rates['auspost_' . $code]))
                    continue;
                if(!Mage::helper('auspost/delivery')->isOptionAvailable($code,$postcode))
                    continue;
                $rates['auspost']['auspost_' . $code] = $new_rates['auspost_' . $code];
            }
        }
        return $rates;
    }
}