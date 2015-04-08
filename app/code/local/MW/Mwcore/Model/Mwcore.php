<?php

class MW_Mwcore_Model_Mwcore extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mwcore/mwcore');
    }
}