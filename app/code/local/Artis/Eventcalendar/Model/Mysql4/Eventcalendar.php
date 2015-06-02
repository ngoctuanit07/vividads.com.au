<?php

class Artis_Eventcalendar_Model_Mysql4_Eventcalendar extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the eventcalendar_id refers to the key field in your database table.
        $this->_init('eventcalendar/eventcalendar', 'eventcalendar_id');
    }
}