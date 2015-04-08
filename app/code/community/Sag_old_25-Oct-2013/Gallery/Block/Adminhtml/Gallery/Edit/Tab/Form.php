<?php

class Sag_Gallery_Block_Adminhtml_Gallery_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('gallery_form', array('legend' => Mage::helper('gallery')->__('Item information')));






        $_cats = Mage::getModel('gallery/category')->getCollection();
        foreach ($_cats as $item) {
            if ($item->getParent == NULL) {
                $_categories[] = array(
                    'value' => $item->getCategoryId(),
                    'label' => $item->getTitle(),
                );
            }
        }

        $fieldset->addField('category', 'select', array(
            'label' => Mage::helper('gallery')->__('Category'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'category',
            'values' => $_categories,
        ));

        
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('gallery')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
        $fieldset->addField('model_no', 'text', array(
            'label' => Mage::helper('gallery')->__('Model No'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'model_no',
        ));

        $fieldset->addField('filename', 'image', array(
            'label' => Mage::helper('gallery')->__('Image'),
            'required' => true,
            'name' => 'filename',
        ));
        $fieldset->addField('imagefilename', 'text', array(
            'label' => Mage::helper('gallery')->__('Image file name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'imagefilename',
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
        
        
        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('gallery')->__('City'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'city',
        ));
        $fieldset->addField('country', 'text', array(
            'label' => Mage::helper('gallery')->__('Country'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'country',
        ));
        $fieldset->addField('tags', 'text', array(
            'label' => Mage::helper('gallery')->__('Tags'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'tags',
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

        if (Mage::getSingleton('adminhtml/session')->getGalleryData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getGalleryData());
            Mage::getSingleton('adminhtml/session')->setGalleryData(null);
        } elseif (Mage::registry('gallery_data')) {
            $form->setValues(Mage::registry('gallery_data')->getData());
        }
        return parent::_prepareForm();
    }

}