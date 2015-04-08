<?php

class AsiaConnect_Gallery_Model_Review_Status extends Varien_Object
{
    const STATUS_PENDING		= 1;
    const STATUS_APPROVED		= 2;
    const STATUS_NOT_APPROVED	= 3;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_PENDING    => Mage::helper('gallery')->__('Pending'),
            self::STATUS_APPROVED   => Mage::helper('gallery')->__('Approved'),
            self::STATUS_NOT_APPROVED   => Mage::helper('gallery')->__('Not Approved'),
        );
    }
}
