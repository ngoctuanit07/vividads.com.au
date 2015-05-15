<?php
class IWD_OrderManager_Block_System_Config_Form_Fieldset_Runarchive extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $title = Mage::helper('iwd_ordermanager')->__("Run Now");

        $_secure = Mage::app()->getStore()->isCurrentlySecure();
        $link = Mage::helper("adminhtml")->getUrl('adminhtml/sales_archive/archivemanually', array('_secure' => $_secure));

        return '<button type="button" onclick="setLocation(\''.$link.'\')">'.$title.'</button>';
    }
}