<?php

class Manageprice_Putrange_Model_Putrange extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('putrange/putrange');
    }
}