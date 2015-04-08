<?php

class Ordercalculator_Calculator_Model_Mysql4_Calculator_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('calculator/calculator');
    }
}