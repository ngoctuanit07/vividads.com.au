<?php
class FMA_Reviewsplus_Helper_Data extends Mage_Core_Helper_Abstract
{



	protected function getTableName($tablename) {
        return Mage::getSingleton('core/resource')->getTableName($tablename);
    }
    
    public function _getTableName($tablename) {
        return $this->getTableName($tablename);
    }

   
}
	 