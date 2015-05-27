<?php

/**
 * @category       WL
 * @package        Auspost
 * @class          WL_Auspost_Helper_Data
 * @description    Extended from the Mage_Core_Helper_Abstract class, give methods to get system configurations and common functions which are used in AUSPOST extension models, blocks, templates ...
 */

class WL_Auspost_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     *
     * Gets the status of extension
     *
     * @return   boolean true/false
     *
     */

    public function getEnabled()
    {
        return Mage::getStoreConfig('carriers/auspost/enabled');
    }

    /**
     *
     * Gets the runing mode of extension is test mode or not
     *
     * @return   boolean true/false
     *
     */

    public function getEnabledTestMode()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_enabled_test_mode');
    }

    /**
     *
     * Gets the title of extension storing in the system config
     *
     * @return   string of the title
     *
     */
    public function getTitle()
    {
        return Mage::getStoreConfig('carriers/auspost/title');
    }

    /**
     *
     * Gets the method of extension
     *
     * @return   string of the method
     *
     */

    public function getMethodName()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_method_name');
    }

    /**
     *
     * Gets the merchant id of the Auspost api service
     *
     * @return   boolean true/false
     *
     */
    public function getMerchantID()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_merchant_id');
    }

    /**
     *
     * Gets the username of AUSPOST API storing in system config
     *
     * @return   string username of api
     *
     */

    public function getAPIUser()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_api_user');
    }

    /**
     *
     * Gets the password of AUSPOST API storing in system config
     *
     * @return   string password of api
     *
     */

    public function getAPIPassword()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_api_password');
    }

    /**
     *
     * Gets the number of days handling the delivery to calculate the delivery dates
     *
     * @return   string number of days
     *
     */

    public function getNumberOfDaysHandling()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_days_handling');
    }

    /**
     *
     * Gets the from shipping post code to calculate the delivery dates
     *
     * @return   string postcode
     *
     */

    public function getFromShippingPostcode()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_from_shipping_postcode');
    }

    /**
     *
     * Gets the available services of AUSPOST extension
     *
     * @return   array of services
     *
     */

    public function getEnabledServices()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_enable_services');
    }
    
    /**
     *
     * Check whether validate Australian Address is enabled
     *
     * @return boolean
     */
     
    public function getEnableValidateAddress()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_enable_validate_shipping_address');
    }
    
    /**
     *
     * Check whether Fetch AUSPOST Address is enabled
     *
     * @return boolean
     */
     
    public function getEnableFetchAUSPOSTAddress()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_enable_fetch_address');
    }

    /**
     *
     * Check whether collection point is enabled
     *
     * @return boolean
     */
    public function getEnableCollectionPoint()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_enable_collection_point');
    }
    /**
     *
     * Gets the price of standard or express shipping method
     *
     * @return   string price
     *
     */

    public function getShippingPrice($code)
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_shipping_price_' . $code);
    }

    /**
     *
     * Gets the error message of the extension
     *
     * @return   string message
     *
     */

    public function getErrorMessage()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_error_message');
    }

    /**
     *
     * Gets the shipping applicable countries
     *
     * @return   string
     *
     */

    public function getShipApplicableCountries()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_ship_applicable_countries');
    }

    /**
     *
     * Gets list of countries to select one
     *
     * @return   array of countries
     *
     */

    public function getShipSpecificCountries()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_ship_specific_countries');
    }

    /**
     *
     * Gets sort order stored in system config
     *
     * @return   string sort order
     *
     */

    public function getSortOrder()
    {
        return Mage::getStoreConfig('carriers/auspost/auspost_sort_order');
    }


    /**
     *
     * Parse stored delivery dates stored string to array dates
     *
     * @param    string $date The date which is need to convert
     * @return   string nice date
     *
     */

    public function parseDeliveryDates($str)
    {
        $dates = explode(',', $str);
        $type = array_shift($dates);
        switch($type){
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::AUSPOST_STANDARD:
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::AUSPOST_EXPRESS:
                return '';
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::DATE_STANDARD:
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::DATE_EXPRESS:
                foreach ($dates as $date)
                {
                    if (empty($date))
                        continue;
                    $newDate = date('jS F, Y', strtotime($date));
                    $result[] = $newDate;
                }
                break;
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::DATE_AND_AM_PM_STANDARD:
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::DATE_AND_AM_PM_EXPRESS:
                foreach ($dates as $date)
                {
                    if (empty($date))
                        continue;
                    $date = explode('_', $date);
                    if(count($date) != 2)
                       continue;

                    $newDate = date('jS F, Y', strtotime($date[1])) .  ' ' . strtoupper($date[0]);
                    $result[] = $newDate;
                }
                break;
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::DATE_AND_2_HOURS_STANDARD:
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::DATE_AND_2_HOURS_EXPRESS:
                foreach ($dates as $date)
                {
                    if (empty($date))
                        continue;
                    $date = explode('_', $date);
                    if(count($date) != 3)
                       continue;

                    $newDate = date('jS F, Y', strtotime($date[2])) .  ' ' . strtoupper($date[0]) . strtoupper($date[1]);
                    $result[] = $newDate;
                }
                break;
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::DAY_STANDARD:
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::DAY_EXPRESS:
                foreach ($dates as $date)
                {
                    if (empty($date))
                        continue;


                    $newDate = ucfirst($date);
                    $result[] = $newDate;
                }
                break;
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::DAY_AND_AM_PM_STANDARD:
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::DAY_AND_AM_PM_EXPRESS:
                foreach ($dates as $date)
                {
                    if (empty($date))
                        continue;
                    $date = explode('_', $date);
                    if(count($date) != 2)
                       continue;

                    $newDate = ucfirst($date[1]) . ' ' . strtoupper($date[0]);
                    $result[] = $newDate;
                }
                break;
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::DAY_AND_2_HOURS_STANDARD:
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::DAY_AND_2_HOURS_EXPRESS:
                foreach ($dates as $date)
                {
                    if (empty($date))
                        continue;
                    $date = explode('_', $date);
                    if(count($date) != 3)
                       continue;

                    $newDate = ucfirst($date[2]) .  ' ' . $date[0] . strtoupper($date[1]);
                    $result[] = $newDate;
                }
                break;
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::AM_PM_STANDARD:
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::AM_PM_EXPRESS:
                foreach ($dates as $date)
                {
                    if (empty($date))
                        continue;
                    $newDate = strtoupper($date);
                    $result[] = $newDate;
                }
                break;
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::TWO_HOURS_STANDARD:
            case WL_Auspost_Model_Config_ServiceMultiSelectionOptions::TWO_HOURS_EXPRESS:
                foreach ($dates as $date)
                {
                    if (empty($date))
                        continue;
                    $date = explode('_', $date);
                    if(count($date) != 2)
                       continue;

                    $newDate = $date[0] . ' ' . strtoupper($date[1]);
                    $result[] = $newDate;
                }
                break;
        }
        return ' [' . implode(' ; ',$result) . ']';
    }

    /**
     *
     * Return a nice format date
     *
     * @param    string $date The date which is need to convert
     * @return   string nice date
     *
     */

    public function niceDateFormat($date)
    {
        $str = substr(date('l', strtotime($date)), 0, 3);
        $str .= date(', j F', strtotime($date));
        return $str;
    }
    
    /**
     *
     * Return a day string by day number
     *
     * @param    string $day The day which is need to convert
     * @return   string nice date
     *
     */

    public function getDateStr($day)
    {
        $days = array (0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday');
        if (isset($days[$day]))
            return $days[$day];
        return null;
    }
}