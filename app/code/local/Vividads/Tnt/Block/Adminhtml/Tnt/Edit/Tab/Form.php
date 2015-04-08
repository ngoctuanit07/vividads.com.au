<?php

class Vividads_Tnt_Block_Adminhtml_Tnt_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('tnt_form', array('legend'=>Mage::helper('tnt')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('tnt')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('tnt')->__('image'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('tnt')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('tnt')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('tnt')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('tnt')->__('Content'),
          'title'     => Mage::helper('tnt')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getTntData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTntData());
          Mage::getSingleton('adminhtml/session')->setTntData(null);
      } elseif ( Mage::registry('tnt_data') ) {
          $form->setValues(Mage::registry('tnt_data')->getData());
      }
      return parent::_prepareForm();
  }
}