<?php

class Manageprice_Putrange_Model_Mysql4_Putrange_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('putrange/putrange');
    }
}