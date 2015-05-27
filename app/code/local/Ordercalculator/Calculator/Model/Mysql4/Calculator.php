<?php

class Ordercalculator_Calculator_Model_Mysql4_Calculator extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the calculator_id refers to the key field in your database table.
        $this->_init('calculator/calculator', 'calculator_id');
    }
}