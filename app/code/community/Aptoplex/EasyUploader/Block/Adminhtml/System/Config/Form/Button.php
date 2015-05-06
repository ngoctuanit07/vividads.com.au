<?php
/**
 * Class Aptoplex_EasyUploader_Block_Adminhtml_System_Config_Form_Button
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Block_Adminhtml_System_Config_Form_Button extends Mage_Adminhtml_Block_System_Config_Form_Field {
    /*
     * Set template
     */
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('aptoplex_easyuploader/system/config/button.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxMoveExistingUploadsUrl() {
        return Mage::helper('adminhtml')->getUrl('adminhtml/easyuploader/moveexistinguploads');
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml() {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'        => 'aptoplex_easyuploader_button',
                'label'     => $this->helper('adminhtml')->__('Move existing uploads'),
                'onclick'   => 'javascript:moveExistingUploads();'
            ));
        return $button->toHtml();
    }
}