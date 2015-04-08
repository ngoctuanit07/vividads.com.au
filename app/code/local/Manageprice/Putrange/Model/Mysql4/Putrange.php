<?php

class Manageprice_Putrange_Model_Mysql4_Putrange extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the putrange_id refers to the key field in your database table.
        $this->_init('putrange/putrange', 'putrange_id');
    }
}