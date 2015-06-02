<?php

class Ordercalculator_Calculator_Model_Calculator extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('calculator/calculator');
    }
}