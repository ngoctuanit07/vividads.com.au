<?php

class Manageprice_Putrange_Block_Adminhtml_Putrange_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('putrange_form', array('legend'=>Mage::helper('putrange')->__('Price information')));
     
      $fieldset->addField('from', 'text', array(
          'label'     => Mage::helper('putrange')->__('From'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'from',
      ));

      $fieldset->addField('to', 'text', array(
          'label'     => Mage::helper('putrange')->__('To'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'to',
      ));
		
      $fieldset->addField('discount', 'text', array(
          'label'     => Mage::helper('putrange')->__('Discount'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'discount',
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