<?php

class Vividads_Tnt_Model_Mysql4_Typeb extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the tnt_id refers to the key field in your database table.
        $this->_init('tnt/typeb', 'ManifestIdentifier');
    }
}
?>