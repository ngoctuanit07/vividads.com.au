<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderattr
*/
class Amasty_Orderattr_Model_Mysql4_Shipping_Methods extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('amorderattr/shipping_methods', 'entity_id');
    }
}