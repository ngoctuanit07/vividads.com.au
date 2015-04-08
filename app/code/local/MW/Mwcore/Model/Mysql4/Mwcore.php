<?php

class MW_Mwcore_Model_Mysql4_Mwcore extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the mcore_id refers to the key field in your database table.
        $this->_init('mwcore/mwcore', 'mwcore_id');
    }
}