<?php

class Artis_Designer_Model_Mysql4_Designer extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the designer_id refers to the key field in your database table.
        $this->_init('designer/designer', 'designer_id');
    }
}