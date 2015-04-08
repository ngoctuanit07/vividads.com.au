<?php

class Artis_Vendor_Model_Status extends Varien_Object
{
    //const STATUS_ENABLED	= 1;
    //const STATUS_DISABLED	= 2;
    
    const STATUS_PROD	= 'prod';
    const STATUS_PACKED	= 'packed';
    const STATUS_SENT	= 'sent';

    static public function getOptionArray()
    {
        return array(
            //self::STATUS_ENABLED    => Mage::helper('vendor')->__('Enabled'),
            //self::STATUS_DISABLED   => Mage::helper('vendor')->__('Disabled')
            
            self::STATUS_PROD   => Mage::helper('vendor')->__('Prod'),
            self::STATUS_PACKED   => Mage::helper('vendor')->__('Packed'),
            self::STATUS_SENT   => Mage::helper('vendor')->__('Sent')
        );
    }
}