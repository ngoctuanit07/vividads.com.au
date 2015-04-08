<?php

/**
  * @category       WL
  * @package        Auspost
  * @class          WL_Auspost_Block_Page_Ajax_DeliverySchedule
  * @description    Extended from the Mage_Core_Block_Template class, give methods to get delivery schedule table ajax action.
 */

class WL_Auspost_Block_Page_Ajax_DeliverySchedule extends Mage_Core_Block_Template
{

/**
 *
 * Check a date is already in the past or not
 * 
 * @param    string $date The date which is need to check
 * @return   boolean true/false
 * 
 */

    private function isPasted($date)
    {
        $todays_date = date("Y-m-d");
        $today = strtotime($todays_date);
        $exp_date = strtotime($date);
        return ($exp_date<$today);
    }

/**
 *
 * Buil 2 hours timeslot string
 * 
 * @param    string $time_period_name
 * @return   string 2 hours timeslot
 * 
 */
    
    public function buildTimeSlotString($time_period_name)
    {
        $a = explode(' ', $time_period_name);
        return $a[0].'-'.$a[3].'_'.strtolower($a[4]);
    }

}