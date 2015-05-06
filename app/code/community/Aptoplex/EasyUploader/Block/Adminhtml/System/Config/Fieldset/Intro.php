<?php
/**
 * Class Aptoplex_EasyUploader_Block_Adminhtml_System_Config_Fieldset_Intro
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Block_Adminhtml_System_Config_Fieldset_Intro
    extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {

    /**
     * Template file location
     *
     * @var string
     */
    protected $_template = 'aptoplex_easyuploader/system/config/fieldset/intro.phtml';

    /**
     * Render method
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        return $this->toHtml();
    }
}