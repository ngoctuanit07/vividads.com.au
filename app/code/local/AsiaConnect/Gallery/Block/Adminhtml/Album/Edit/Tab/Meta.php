<?php

class AsiaConnect_Gallery_Block_Adminhtml_Album_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('meta_form', array('legend'=>Mage::helper('gallery')->__('Meta Data')));
      
      
      $fieldset->addField('meta_keyword', 'editor', array(
          'name'      => 'meta_keyword',
          'label'     => Mage::helper('gallery')->__('Keywords'),
          'title'     => Mage::helper('gallery')->__('Keywords'),
          'style'     => 'width:600px; height:150px;',
          'wysiwyg'   => false,
      ));
      
      $fieldset->addField('meta_description', 'editor', array(
          'name'      => 'meta_description',
          'label'     => Mage::helper('gallery')->__('Description'),
          'title'     => Mage::helper('gallery')->__('Description'),
          'style'     => 'width:600px; height:150px;',
          'wysiwyg'   => false,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getGalleryData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getGalleryData());
          Mage::getSingleton('adminhtml/session')->setGalleryData(null);
      } elseif ( Mage::registry('album_data') ) {
          $form->setValues(Mage::registry('album_data')->getData());
      }
      return parent::_prepareForm();
  }

    public function getTabLabel(){
    	return Mage::helper('gallery')->__('Meta Data');
    }
    public function getTabTitle(){
    	return Mage::helper('gallery')->__('Meta Data');
    }
    public function canShowTab(){
    	return true;
    }
    public function isHidden(){
    	return false;
    }
    
}