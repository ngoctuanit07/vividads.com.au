<?php

class Artis_Designer_Block_Adminhtml_Designer_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('designer_form', array('legend'=>Mage::helper('designer')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('designer')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('designer')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('designer')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('designer')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('designer')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('designer')->__('Content'),
          'title'     => Mage::helper('designer')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getDesignerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getDesignerData());
          Mage::getSingleton('adminhtml/session')->setDesignerData(null);
      } elseif ( Mage::registry('designer_data') ) {
          $form->setValues(Mage::registry('designer_data')->getData());
      }
      return parent::_prepareForm();
  }
}