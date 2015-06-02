<?php

class Artis_Externalform_Model_Mysql4_Externalform extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the externalform_id refers to the key field in your database table.
        $this->_init('externalform/externalform', 'externalform_id');
    }
}