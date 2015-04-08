<?php

/**
  * @category       WL
  * @package        Auspost
  * @class          WL_Auspost_Helper_Delivery
  * @description    Extended from the Mage_Core_Helper_Abstract class, give methods to provide delivery functions which are used in AUSPOST extension models, blocks, templates ...
 */

class WL_Auspost_Helper_Delivery extends Mage_Core_Helper_Abstract
{
	/**
	 *
	 * Call to API to get the delivery dates
	 *
	 * @param    string $start_date The lodgement date of the delivery dates
	 * @param    string $network Specifies the delivery network (values are 01 = Standard, 02 = Express)
	 * @param    $toPostcode
	 * @param    boolean $checkTimedEnable include check whether timed delivery is enable on the date or not
	 * @return   array delivery dates
	 */
    public function getDeliveryDates($start_date, $network, $toPostcode, $checkTimedEnable = false)
    {
        $fromPostcode = Mage::helper('auspost')->getFromShippingPostcode();
	    $days_handling = Mage::helper('auspost')->getNumberOfDaysHandling();
	    if (empty($days_handling) || $days_handling<=0)
		    $days_handling = 1;

        $today = date("Y-m-d");

	    // Get available lodgement dates
	    $key = md5 ('auspost_lodgement_date_'.$fromPostcode.$toPostcode.$today.$days_handling."01");
	    if ( !isset($_SESSION[$key]) || empty($_SESSION[$key]) )
	    {
		    $api = Mage::getModel('auspost/api');
		    $lodgement_dates = $api->getDeliveryDates($fromPostcode, $toPostcode, $today, $days_handling, "01");
		    $_SESSION[$key] = $lodgement_dates;
	    } else
	    {
		    $lodgement_dates = $_SESSION[$key];
	    }
	    $possible_lodgement_date = $lodgement_dates[$days_handling-1]['DeliveryDate'];
	    // end Get available lodgement dates

        $key = md5 ('auspost_date_delivery_'.$fromPostcode.$toPostcode.$possible_lodgement_date.$days_handling.$network);
	    if ( !isset($_SESSION[$key]) || empty($_SESSION[$key]) )
        {
            $api = Mage::getModel('auspost/api');
            $dates = $api->getDeliveryDates($fromPostcode, $toPostcode, $possible_lodgement_date, 10, $network);
            $_SESSION[$key] = $dates;
        } else
        {
            $dates = $_SESSION[$key];
        }
        $rDates = array ();
        
        if (!empty($dates)) 
        {
            foreach ($dates as $date) 
            {
                if ($checkTimedEnable) 
                {
                    if ($date['TimedDeliveryEnabled'] == 'true')
                    {
                        $rDates[] = $date['DeliveryDate'];
                    }
                } 
                else {
                    $rDates[] = $date['DeliveryDate'];
                }
                    
            }
        }
        return $rDates;
    }
    
	/**
	 *
	 * Get delivery timeslot by date
	 *
	 * @param    string $date The date to get delivery timeslot
	 * @return   array Timeslot
	 *
	*/
 
    public function getDeliveryTimeslotByDate($date)
    {
        $date = strtotime($date);
        $day_type = date('N', $date);
        $res = array ();
        if (empty($_SESSION['auspost_delivery_timeslot_by_date_'.$date]))
        {
            $api = Mage::getModel('auspost/api');
            $timeslot = $api->getDeliveryTimeslots($day_type);
            if (!empty($timeslot['DayTimeslot']['TimePeriod']))
            {
                foreach ($timeslot['DayTimeslot']['TimePeriod'] as $time)
                {
                    if ($time['Duration'] == 'PT5H')
                    {
                        $res['AMPM'][] = $time['TimePeriodName'];
                    }
                    else if ($time['Duration'] == 'PT2H')
                    {
                        $res['2HRS'][] = $time['TimePeriodName'];
                    }
                }
            }  // print '<pre>'; print_r ($res); exit;
            $_SESSION['auspost_delivery_timeslot_by_date_'.$date]=$res;
        }
        else
        {
            $res = $_SESSION['auspost_delivery_timeslot_by_date_'.$date];
        }
        return $res;
    }

	/**
	 *
	 * Get delivery timeslot by day
	 *
	 * @param $day
	 * @internal param string $date The date to get delivery timeslot
	 * @return   array Timeslot
	 */
 
    public function getDeliveryTimeslotByDay($day)
    {
        $res = array ();
        if (empty($_SESSION['auspost_delivery_timeslot_by_day_'.$day]))
        {
            $api = Mage::getModel('auspost/api');
            $timeslot = $api->getDeliveryTimeslots($day);
            if (!empty($timeslot['DayTimeslot']['TimePeriod']))
            {
                foreach ($timeslot['DayTimeslot']['TimePeriod'] as $time)
                {
                    if ($time['Duration'] == 'PT5H')
                    {
                        $res['AMPM'][] = $time['TimePeriodName'];
                    }
                    else if ($time['Duration'] == 'PT2H')
                    {
                        $res['2HRS'][] = $time['TimePeriodName'];
                    }
                }
            }  // print '<pre>'; print_r ($res); exit;
            $_SESSION['auspost_delivery_timeslot_by_day_'.$day]=$res;
        }
        else
        {
            $res=$_SESSION['auspost_delivery_timeslot_by_day_'.$day];
        }
        return $res;
    }
    /**
     *
     * Call API to get capability of delivery
     *
     * @param null $postcode
     *
     * @return data of the expected delivery capabilities
     */
    public function getPostcodeCapability($postcode = null)
    {
        $isStandardDeliveryEnabled = false;
        $isTimedDeliveryEnabled = false;
        $api = Mage::getModel('auspost/api');
        $postcode_capabilities = $api->getPostcodeDeliveryCapabilities($postcode);
        if (isset($postcode_capabilities['PostcodeDeliveryCapability'])) {
            $postcodeCapabilityGroup = $postcode_capabilities['PostcodeDeliveryCapability'];

            if (count($postcodeCapabilityGroup)) {

                if (!isset($postcodeCapabilityGroup['WeekDay']) && !isset($postcodeCapabilityGroup['Postcode'])) {
                    $tempPostcodeCapabilityGroup = $postcodeCapabilityGroup;
                    foreach ($tempPostcodeCapabilityGroup as $grp) {
                        if ($grp['Postcode'] == $postcode)
                            $postcodeCapabilityGroup = $grp;

                    }
                }
                $pw = $postcodeCapabilityGroup['WeekDay'];
                $pc = $postcodeCapabilityGroup['Postcode'];
                if (count($pw)) { // && $pc == $postcodeCapabilityGroup['Postcode']) {

                    foreach ($pw as $p) {
                        if ($p['StandardDeliveryEnabled'] == 'true') {
                            $isStandardDeliveryEnabled = true;
                        }
                        if ($p['TimedDeliveryEnabled'] == 'true') {
                            $isTimedDeliveryEnabled = true;
                        }

                    }
                }


            }

        } else if (isset($postcode_capabilities['BusinessException'])) {
            return array(
                'error' => true,
                'error_code' => $postcode_capabilities['BusinessException']['Code'],
                'description' => $postcode_capabilities['BusinessException']['Description'],
                'auspost' => false
            );
        }
        return array(
            'error' => false,
            'auspost_standard' => $isStandardDeliveryEnabled,
            'auspost_delivery' => $isTimedDeliveryEnabled

        );
        /*return array(
            'auspost_auspost_' . WL_Auspost_Model_Config_ServiceMultiSelectionOptions::DATE_SELECTION =>   $isTimedDeliveryEnabled,
            'auspost_auspost_' . WL_Auspost_Model_Config_ServiceMultiSelectionOptions::AM_PM_SELECTION =>   $isTimedDeliveryEnabled,
            'auspost_auspost_' . WL_Auspost_Model_Config_ServiceMultiSelectionOptions::TWO_HRS_TIME_SLOT_SELECTION =>   $isTimedDeliveryEnabled
        );*/
    }

	/**
	 *
	 * @param $type type of shipping method
	 * @param null $postcode
	 * @return boolean shipping method is available or not
	 */
    
    public function isOptionAvailable ($type, $postcode = null)
    {
        $network = ($type<100) ? '01' : '02';
        $start_date = date ('Y-m-d');
        
        // $_SESSION['auspost_capability_'.$postcode]='';
        if (empty($_SESSION['auspost_capability_'.$postcode]))    
            $capability = $this->getPostcodeCapability($postcode);
        else
            $capability = $_SESSION['auspost_capability_'.$postcode];
        
        // Date only
        if ($type == 2 || $type == 102)
        {
            $business_days = $this->getDeliveryDates($start_date, $network, $postcode);
            if (!empty($business_days) && $capability['auspost_standard']==1) {
                return true;
            }
            return false;
        }
        
        // Date & am/pm
        else if ($type == 3 || $type == 103)
        {
            $business_days = $this->getDeliveryDates($start_date, $network, $postcode, true);
            $timeslot = $this->getDeliveryTimeslotByDay(1);            
            if(!empty($business_days) && !empty($timeslot['AMPM']) && $capability['auspost_delivery']==1)
                return true;
            return false;
        }
        
        // Date & 2hrs
        else if ($type == 4 || $type == 104)
        {
            $business_days = $this->getDeliveryDates($start_date, $network, $postcode, true);
            $timeslot = $this->getDeliveryTimeslotByDay(1);            
            if(!empty($business_days) && !empty($timeslot['2HRS']) && $capability['auspost_delivery']==1)
                return true;
            return false;
        }
        
        // Day only
        else if ($type == 5 || $type == 105)
        {
            $business_days = $this->getDeliveryDates($start_date, $network, $postcode);
            if (!empty($business_days) && $capability['auspost_standard']==1)
                return true;
            return false;
        }
        
        // Day & am/pm
        else if ($type == 6 || $type == 106)
        {
            $business_days = $this->getDeliveryDates($start_date, $network, $postcode, true);
            $timeslot = $this->getDeliveryTimeslotByDay(1);            
            if(!empty($business_days) && !empty($timeslot['AMPM']) && $capability['auspost_delivery']==1)
                return true;
            return false;
        }
        
        // Day & 2hrs
        else if ($type == 7 || $type == 107)
        {
            $business_days = $this->getDeliveryDates($start_date, $network, $postcode, true);
            $timeslot = $this->getDeliveryTimeslotByDay(1);          
            if(!empty($business_days) && !empty($timeslot['2HRS']) && $capability['auspost_delivery']==1)
                return true;
            return false;
        }
        
        // am/pm only
        else if ($type == 8 || $type == 108)
        {
            $business_days = $this->getDeliveryDates($start_date, $network, $postcode, true);
            $timeslot = $this->getDeliveryTimeslotByDay(1);            
            if(!empty($business_days) && !empty($timeslot['AMPM']) && $capability['auspost_delivery']==1)
                return true;
            return false;
        }
        
        // 2hrs only
        else if ($type == 9 || $type == 109)
        {
            $business_days = $this->getDeliveryDates($start_date, $network, $postcode, true);
            $timeslot = $this->getDeliveryTimeslotByDay(1);            
            if(!empty($business_days) && !empty($timeslot['2HRS']) && $capability['auspost_delivery']==1)
                return true;
            return false;
        }
    }
}