<?php

class Ordercalculator_Calculator_Block_Adminhtml_Calculator_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('calculator_form', array('legend'=>Mage::helper('calculator')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('calculator')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('calculator')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('calculator')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('calculator')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('calculator')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('calculator')->__('Content'),
          'title'     => Mage::helper('calculator')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getCalculatorData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCalculatorData());
          Mage::getSingleton('adminhtml/session')->setCalculatorData(null);
      } elseif ( Mage::registry('calculator_data') ) {
          $form->setValues(Mage::registry('calculator_data')->getData());
      }
      return parent::_prepareForm();
  }
}