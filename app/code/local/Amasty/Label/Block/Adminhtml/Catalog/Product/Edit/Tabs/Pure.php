<?php
if ('true' == (string)Mage::getConfig()->getNode('modules/Amasty_Purchases/active')){
    class Amasty_Label_Block_Adminhtml_Catalog_Product_Edit_Tabs_Pure extends Amasty_Purchases_Block_Adminhtml_Catalog_Product_Edit_Tabs {}
} 
else {
    class Amasty_Label_Block_Adminhtml_Catalog_Product_Edit_Tabs_Pure extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs {}
}