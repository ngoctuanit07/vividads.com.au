<?php

class Artis_Vendor_Model_Mysql4_Vendor extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the vendor_id refers to the key field in your database table.
        $this->_init('vendor/vendor', 'entity_id');
    }
}