<?php

class Artis_Permissions_Model_Permissions extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('permissions/permissions');
    }
}