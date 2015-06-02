<?php

/**
  * @category       WL
  * @package        Auspost
  * @class          WL_Auspost_Block_Order_Tracking
  * @description    Override the Mage_Core_Block_Template class, give extra methods for my order page.
 */

class WL_Auspost_Block_Order_Tracking extends Mage_Core_Block_Template
{

/**
 *
 * Call API to get tracking information
 * 
 * @param    string $code The tracking code
 * @return   array associative array tracking information
 * 
 */
    
    public function getTracking($code)
    {
        $api = Mage::getModel('auspost/api');
        return $api->getTracking($code);
    }
}