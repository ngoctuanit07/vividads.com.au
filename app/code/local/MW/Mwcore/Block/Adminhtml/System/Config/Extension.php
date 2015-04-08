<?php

class MW_Mwcore_Block_Adminhtml_System_Config_Extension extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {    	    
		$html =  $this->getLayout()->createBlock('mwcore/adminhtml_system_config_extensioninfo')->setTemplate('mw_mwcore/extensions.phtml')->toHtml();
        return $html;
    }
}