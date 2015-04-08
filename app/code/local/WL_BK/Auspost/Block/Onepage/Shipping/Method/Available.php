<?php

/**
  * @category       WL
  * @package        Auspost
  * @class          WL_Auspost_Block_Onepage_Shipping_Method_Available
  * @description    Override the Mage_Checkout_Block_Onepage_Shipping_Method_Available class, give extra methods for shipping information page.
 */
 
class WL_Auspost_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    /**
     * @return the shipping methods with default selected method - in case you select a collection point.
     */
    public function getShippingRates()
    {
        $street1 = '';
        $rates = parent::getShippingRates();

        if(isset($rates['auspost'])) {
            if (count($data = $this->getAddress()->getStreet()))
                $street1 = $data[0];

            if (preg_match('/\A\[Collection Point #[\d]+\]\Z/i', trim($street1))) {
                $return_method = array();
                foreach($rates['auspost'] as $_rate){
                    if($_rate->getCode() == 'auspost_' . WL_Auspost_Model_Config_ServiceMultiSelectionOptions::COLLECTION_POINT)
                        $return_method['auspost']['auspost_' . WL_Auspost_Model_Config_ServiceMultiSelectionOptions::COLLECTION_POINT] = $_rate;
                }
                return $return_method;
            }

            $new_rates = array();
            foreach($rates['auspost'] as $_rate){
                $new_rates[$_rate->getCode()] = $_rate;
            }

            $rates['auspost'] = array();
            $default_services = WL_Auspost_Model_Config_ServiceMultiSelectionOptions::getAllOptions();
            foreach($default_services as $code=>$value){
                if(!isset($new_rates['auspost_' . $code]))
                    continue;
                if($code == WL_Auspost_Model_Config_ServiceMultiSelectionOptions::COLLECTION_POINT)
                    continue;
                $rates['auspost']['auspost_' . $code] = $new_rates['auspost_' . $code];
            }
        }
        return $rates;
    }
    
    /**
     * @return the quickest estimated due date.
     */
     
    public function getQuickestEstimatedDate ()
    {
        $start_date = date('Y-m-d');
        $fromPostcode = Mage::helper('auspost')->getFromShippingPostcode();
        $shipping_data = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getData();
        $toPostcode = !empty($shipping_data['postcode']) ? $shipping_data['postcode'] : null;
        
        // $fromPostcode = '3000'; $toPostcode = '3015';
        $api = Mage::getModel('auspost/api');
        $day = $api->getDeliveryDates($fromPostcode, $toPostcode, $start_date, 1);
        return !empty($day[0]['DeliveryDate']) ? $day[0]['DeliveryDate'] : null;
    }
}
