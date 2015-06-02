<?php

/**
  * @category       WL
  * @package        Auspost
  * @class          WL_Auspost_Block_Page_Ajax_AddressValidate
  * @description    Extended from the Mage_Core_Block_Template class, give methods for address validation popup.
 */

class WL_Auspost_Block_Page_Ajax_AddressValidate extends Mage_Core_Block_Template
{

/**
 *
 * Call API to validate AUS Address
 * 
 * @param    string $addressLine1 Address line 1
 * @param    string $addressLine2 Address line 2
 * @param    string $suburb Australian suburbs (like Richmond, Manly etc)
 * @param    string $state  Australian states (valid state parameters are VIC, NSW, TAS, NT, WA, SA, QLD, ACT)
 * @param    string $postcode Australian Postcode (Numeric string at least four characters long e.g. 3021)
 * @param    string $country Australia (100 character ISO-3166-1 country name. Currently only Australia is supported!)
 * @return   array associative array of code, message and parse the suggested address
 * 
 */

    public function getAddressValidate ($addressLine1, $addressLine2, $suburb, $state, $postcode, $country)
    {
        $api = Mage::getModel('auspost/api');
        $state = Mage::helper('auspost/location')->getStateCodeByName($state);
        $country = Mage::helper('auspost/location')->getCountryNameByCode($country);
        $address = $api->getValidateAustralianAddress($addressLine1, $addressLine2, $suburb, $state, $postcode, $country);
        
        // Submit form if input address is same as suggested address
        if (!empty($address['SuggestedAddress']) && $address['IsValid'] == 1)
        {
            $tmp = $address['SuggestedAddress'];
            $suggested_state = $state = Mage::helper('auspost/location')->getStateCodeByName($tmp['state']);
            if (strtolower($tmp['addressLine1']) == strtolower($addressLine1)
                    && strtolower($tmp['addressLine2']) == strtolower($addressLine2)
                    && strtolower($tmp['suburb']) == strtolower($suburb)
                    && strtolower($suggested_state) == strtolower($state)
                    && strtolower($tmp['postcode']) == strtolower($postcode)
                    && strtolower($tmp['country']) == strtolower($country))
            {
                // set IsValid = 2 to submit form
                $address['IsValid'] = 2;
            }
        }
        return $address;
    }
}
