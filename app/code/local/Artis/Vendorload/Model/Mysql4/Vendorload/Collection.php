<?php

class Artis_Vendorload_Model_Mysql4_VendorLoad_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorload/vendorload');
    }
}