<?php

class Partialshipping_Partialshipping_Block_Adminhtml_Partialshipping_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('partialshipping_form', array('legend'=>Mage::helper('partialshipping')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('partialshipping')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('partialshipping')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('partialshipping')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('partialshipping')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('partialshipping')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('partialshipping')->__('Content'),
          'title'     => Mage::helper('partialshipping')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getPartialshippingData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPartialshippingData());
          Mage::getSingleton('adminhtml/session')->setPartialshippingData(null);
      } elseif ( Mage::registry('partialshipping_data') ) {
          $form->setValues(Mage::registry('partialshipping_data')->getData());
      }
      return parent::_prepareForm();
  }
}