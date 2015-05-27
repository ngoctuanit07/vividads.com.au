<?php

class Sag_Gallery_Block_Left extends Mage_Core_Block_Template {
    
    public function getCategories() {
        if (!$this->hasData('lcategory')) {
            if (!Mage::app()->isSingleStoreMode()) {
                $collection = Mage::getModel('gallery/category')->getCollection()
                        ->addStoreFilter(Mage::app()->getStore()->getId());
            } else {
                $collection = Mage::getModel('gallery/category')->getCollection();
            }
            $this->setData('lcategory', $collection);
        }
        return $this->getData('lcategory');
       
    }

}