<?php

class MW_Mwcore_Model_Mysql4_Mwcore_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mwcore/mwcore');
    }
}