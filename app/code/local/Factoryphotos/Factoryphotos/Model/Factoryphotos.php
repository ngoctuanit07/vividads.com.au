<?php

class Factoryphotos_Factoryphotos_Model_Factoryphotos extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('factoryphotos/factoryphotos');
    }
}