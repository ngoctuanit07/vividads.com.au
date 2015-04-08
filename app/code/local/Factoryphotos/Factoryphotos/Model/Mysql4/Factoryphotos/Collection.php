<?php

class Factoryphotos_Factoryphotos_Model_Mysql4_Factoryphotos_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('factoryphotos/factoryphotos');
    }
}