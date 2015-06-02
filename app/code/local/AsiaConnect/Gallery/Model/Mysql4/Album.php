<?php

class AsiaConnect_Gallery_Model_Mysql4_Album extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('gallery/album', 'album_id');
    }
    
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
    	$condition = $this->_getWriteAdapter()->quoteInto('album_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('gallery_store'), $condition);
    }
    
	protected function _afterSave(Mage_Core_Model_Abstract $object){
		$condition = $this->_getWriteAdapter()->quoteInto('album_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('gallery_store'), $condition);

		if (!$object->getData('stores'))
		{
			$storeArray = array();
            $storeArray['album_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('gallery_store'), $storeArray);
		}
		else
		{
			foreach ((array)$object->getData('stores') as $store) {
				$storeArray = array();
				$storeArray['album_id'] = $object->getId();
				$storeArray['store_id'] = $store;
				$this->_getWriteAdapter()->insert($this->getTable('gallery_store'), $storeArray);
			}
		}

        return parent::_afterSave($object);
    }
}