<?php

class Manageprice_Putrange_Block_Adminhtml_Putrange_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('putrange_form', array('legend'=>Mage::helper('putrange')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('putrange')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('putrange')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('putrange')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('putrange')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('putrange')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('putrange')->__('Content'),
          'title'     => Mage::helper('putrange')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getPutrangeData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPutrangeData());
          Mage::getSingleton('adminhtml/session')->setPutrangeData(null);
      } elseif ( Mage::registry('putrange_data') ) {
          $form->setValues(Mage::registry('putrange_data')->getData());
      }
      return parent::_prepareForm();
  }
}