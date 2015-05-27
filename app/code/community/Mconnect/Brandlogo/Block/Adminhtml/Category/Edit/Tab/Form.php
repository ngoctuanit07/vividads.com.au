<?php

class Mconnect_Brandlogo_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('category_form', array('legend' => Mage::helper('brandlogo')->__('Category information')));
        
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('brandlogo')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
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

        if (Mage::getSingleton('adminhtml/session')->getCategoryData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getCategoryData());
            Mage::getSingleton('adminhtml/session')->setCategoryData(null);
        } elseif (Mage::registry('category_data')) {
            $form->setValues(Mage::registry('category_data')->getData());
        }
        return parent::_prepareForm();
    }

}