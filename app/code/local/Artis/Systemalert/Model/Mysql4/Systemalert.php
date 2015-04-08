<?php

class Artis_Systemalert_Model_Mysql4_Systemalert extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the systemalert_id refers to the key field in your database table.
        $this->_init('systemalert/systemalert', 'systemalert_id');
    }
}