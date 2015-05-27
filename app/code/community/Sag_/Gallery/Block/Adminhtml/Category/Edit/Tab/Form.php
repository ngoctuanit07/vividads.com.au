<?php

class Sag_Gallery_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('category_form', array('legend' => Mage::helper('gallery')->__('Category information')));
        
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
            'label' => Mage::helper('gallery')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));


        $fieldset->addField('filename', 'image', array(
            'label' => Mage::helper('gallery')->__('Image'),
            'required' => true,
            'name' => 'filename',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('gallery')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('gallery')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('gallery')->__('Disabled'),
                ),
            ),
        ));

        $fieldset->addField('content', 'editor', array(
            'name' => 'content',
            'label' => Mage::helper('gallery')->__('Content'),
            'title' => Mage::helper('gallery')->__('Content'),
            'style' => 'width:700px; height:200px;',
            'wysiwyg' => false,
            'required' => true,
        ));
        $fieldset->addField('bottom_content', 'editor', array(
            'name' => 'bottom_content',
            'label' => Mage::helper('gallery')->__('Bottom Content'),
            'title' => Mage::helper('gallery')->__('Bottom Content'),
            'style' => 'width:700px; height:200px;',
            'wysiwyg' => false,
            'required' => true,
        ));
        /* -- SEO Part --*/
        $fieldset->addField('meta_keyword', 'editor', array(
            'name' => 'meta_keyword',
            'label' => Mage::helper('gallery')->__('Meta keyword'),
            'title' => Mage::helper('gallery')->__('Meta_keyword'),
            'style' => 'width:700px; height:200px;',
            'wysiwyg' => false,
            'required' => true,
        ));
        $fieldset->addField('meta_description', 'editor', array(
            'name' => 'meta_description',
            'label' => Mage::helper('gallery')->__('Meta description'),
            'title' => Mage::helper('gallery')->__('Meta description'),
            'style' => 'width:700px; height:200px;',
            'wysiwyg' => false,
            'required' => true,
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