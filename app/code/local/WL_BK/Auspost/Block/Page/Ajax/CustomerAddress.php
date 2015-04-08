<?php

/**
  * @category       WL
  * @package        Auspost
  * @class          WL_Auspost_Block_Page_Ajax_CustomerAddress
  * @description    Extended from the Mage_Core_Block_Template class, give methods to get customer confirm address.
 */

class WL_Auspost_Block_Page_Ajax_CustomerAddress extends Mage_Core_Block_Template
{

/**
 *
 * Call API to get Auspost API webservice url
 * 
 * @return   string service url
 * 
 */
    public function getAPIUrl ()
    {
        $api = Mage::getModel('auspost/api');
        return $api->getAPIUrl();
    }
    
 /**
 *
 * Call API to get Auspost Auth webservice url
 * 
 * @return   string service url
 * 
 */
    public function getAuthUrl ()
    {
        $api = Mage::getModel('auspost/api');
        return $api->getAuthUrl();
    }

/**
 *
 * Call API to get Auspost Get Token webservice url
 *
 * @return   string service url
 *
 */
	public function getTokenUrl ()
	{
		$api = Mage::getModel('auspost/api');
		return $api->getTokenUrl();
	}

/**
 *
 * Call API to get merchant id 
 * 
 * @return   string merchant app id
 * 
 */
    
    public function getAppId ()
    {
        $appid = Mage::helper('auspost')->getMerchantId();
        return $appid;
    }

/**
 *
 * Call API to get array list of customer detail
 * 
 * @param    string $access_token The token result of process OAuth on AUSPOST service
 * @return   array associative array customer information
 * 
 */
    
    public function getCustomerAddress ($access_token)
    {
        $api = Mage::getModel('auspost/api');
        return $api->getCustomerDetails($access_token);
    }
}