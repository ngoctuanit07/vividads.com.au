<?php

class Sag_Gallery_Model_Mysql4_Gallery_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('gallery/gallery');
    }
    public function addImageCategoryFilter($category_id){
        $this->getSelect()->join(
                            array('imgcats_table' => $this->getTable('image_categories')), 'main_table.gallery_id = imgcats_table.image_id', array()
                    )
                    ->where('imgcats_table.category_id in (?)', array(0, $category_id));

            return $this;
    }
}