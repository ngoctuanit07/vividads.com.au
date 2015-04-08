<?php

class Cateyes_Phoneorder_Block_Centerblock extends Mage_Catalog_Block_Product_View
{

    public function isEnabled()
    {
        return Mage::getStoreConfig('phoneorder/configuration/centerblock');   
    }

    public function getAvailabilityMsg()
    {
        return Mage::getStoreConfig('phoneorder/configuration/availabilitymsg');   
    }    

    public function getStyleName()
    {
        return Mage::getStoreConfig('phoneorder/configuration/stylename');   
    }    
}
