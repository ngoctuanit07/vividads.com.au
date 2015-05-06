<?php

class Aptoplex_EasyUploader_Block_Adminhtml_System_Config_Edit extends Mage_Adminhtml_Block_System_Config_Edit {

    protected function _prepareLayout() {
        $return = parent::_prepareLayout();

        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }

        return $return;
    }
}