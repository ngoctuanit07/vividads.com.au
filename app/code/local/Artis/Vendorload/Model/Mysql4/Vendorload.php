<?php

class Artis_Vendorload_Model_Mysql4_VendorLoad extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the vendorload_id refers to the key field in your database table.
        $this->_init('vendorload/vendorload', 'vendorload_id');
    }
}