<?php

class Hm_Testimonial_Model_Mysql4_Testimonial_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('testimonial/testimonial');
    }
    
    public function addEnableFilter($status)
    {
        $this->getSelect()
            ->where('status = ?', $status);
        return $this;
    }

    public function addStoreFilter($store)
    {
		if (!Mage::app()->isSingleStoreMode()) {
			if ($store instanceof Mage_Core_Model_Store) {
				$store = array($store->getId());
			}
	
			$this->getSelect()->join(
				array('store_table' => $this->getTable('testimonial_store')),
				'main_table.testimonial_id = store_table.testimonial_id',
				array()
			)
			->where('store_table.store_id in (?)', array(0, $store));
	
			return $this;
		}
		return $this;
	}    
}