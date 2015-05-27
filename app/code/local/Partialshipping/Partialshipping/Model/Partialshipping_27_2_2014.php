<?php

class Partialshipping_Partialshipping_Model_Partialshipping extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('partialshipping/partialshipping');
    }
}