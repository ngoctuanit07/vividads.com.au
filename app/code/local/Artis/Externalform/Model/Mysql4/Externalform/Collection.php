<?php

class Artis_Externalform_Model_Mysql4_Externalform_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('externalform/externalform');
    }
}