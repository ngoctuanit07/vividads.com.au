<?php

class Artis_Vendorload_Model_VendorLoad extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendorload/vendorload');
    }
}