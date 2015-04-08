<?php

class GrafischDirect_Algemeen_Block_Adminhtml_System_Config_Form_Fieldset_Modules_List
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        $modules = Mage::getConfig()->getNode('modules')->children();
        $linkTitle = Mage::helper('gdalgemeen')->__('View extension page');
        foreach ($modules as $moduleName => $values) {
            if (0 !== strpos($moduleName, 'GrafischDirect_')) {
                continue;
            }
            if ($values->gd_link) {
                $field = $element->addField($moduleName, 'link', array(
                    'label' => (string) $values->gd_name,
                    'value' => (string) $values->version,
                    'href'  => (string) $values->gd_link,
                    'onclick' => 'window.open(this.href); return false;',
                    'title' => $linkTitle
                ));
            } else {
                $field = $element->addField($moduleName, 'label', array(
                    'label' => (string) $values->gd_name,
                    'value' => (string) $values->version
                ));
            }
            $html .= $field->toHtml();
        }
        $html .= $this->_getFooterHtml($element);
        return $html;
    }
}
