<?php

class Magestore_Imageoption_Model_Mysql4_Product extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('imageoption/product', 'entity_id');
    }
	
}