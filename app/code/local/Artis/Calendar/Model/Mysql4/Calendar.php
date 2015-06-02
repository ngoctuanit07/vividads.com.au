<?php

class Artis_Calendar_Model_Mysql4_Calendar extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the calendar_id refers to the key field in your database table.
        $this->_init('calendar/calendar', 'calendar_id');
    }
}