<?php

class Artis_Systemalert_Model_Mysql4_Systemalert_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('systemalert/systemalert');
    }
}