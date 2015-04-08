<?php

class Artis_Calendar_Model_Calendar extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('calendar/calendar');
    }
}