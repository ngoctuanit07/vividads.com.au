<?php

class AsiaConnect_Gallery_Model_Slideshow extends Varien_Object
{
    const PHOTOS_OF_CURRENT_PAGE	= 0;
    const ALL_PHOTOS_OF_CATEGORY	= 1;

    static public function toOptionArray()
    {
        return array(
            self::PHOTOS_OF_CURRENT_PAGE    => Mage::helper('gallery')->__('Photos of current page'),
            self::ALL_PHOTOS_OF_CATEGORY   => Mage::helper('gallery')->__('All photos of category')
        );
    }
}