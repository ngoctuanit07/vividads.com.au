<?php

/**
 * M-Connect Solutions.
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.mconnectsolutions.com/lab/
 *
 * @category   M-Connect
 * @package    M-Connect
 * @copyright  Copyright (c) 2009-2010 M-Connect Solutions. (http://www.mconnectsolutions.com)
 */
?>
<?php

class Mconnect_Brandlogo_Block_Adminhtml_Brandlogo_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('brandlogo_form', array('legend' => Mage::helper('brandlogo')->__('Brand Logo information')));
        $object = Mage::getModel('brandlogo/brandlogo')->load($this->getRequest()->getParam('id'));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('cms')->__('Store View'),
                'title' => Mage::helper('cms')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('brandlogo')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));

        $fieldset->addField('filename', 'file', array(
            'label' => Mage::helper('brandlogo')->__('File'),
            'required' => false,
            'name' => 'filename',
        ));


        $fieldset->addField('url', 'text', array(
            'name' => 'url',
            'label' => Mage::helper('brandlogo')->__('Url'),
            'title' => Mage::helper('brandlogo')->__('Url'),
            'required' => false,
            'class' => 'validate-url',
        ));


        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('brandlogo')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('brandlogo')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('brandlogo')->__('Disabled'),
                ),
            ),
        ));


        if (Mage::getSingleton('adminhtml/session')->getBrandlogoData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBrandlogoData());
            Mage::getSingleton('adminhtml/session')->setBrandlogoData(null);
        } elseif (Mage::registry('brandlogo_data')) {
            $form->setValues(Mage::registry('brandlogo_data')->getData());
        }
        return parent::_prepareForm();
    }

}
