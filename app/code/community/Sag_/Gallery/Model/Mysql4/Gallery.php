<?php

class Sag_Gallery_Model_Mysql4_Gallery extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the gallery_id refers to the key field in your database table.
        $this->_init('gallery/gallery', 'gallery_id');
    }
	
	 protected function _beforeDelete(Mage_Core_Model_Abstract $object) {

        // Cleanup stats on blog delete
        $adapter = $this->_getReadAdapter();
        
		$adapter->delete($this->getTable('gallery/core_url_rewrite'), 'id_path="gallery_image_' . $object->getId().'"');
		
		$adapter->delete($this->getTable('gallery/image_categories'), 'image_id=' . $object->getGallery_id());
		
		// Zend_Debug::dump($object->getGallery_id());
		// exit;
		
		//$adapter->delete($this->getTable('gallery/image_categories'), 'id_path="gallery_image_' . $object->getId().'"');
    }
   
}