<?php

class Artis_Vendorload_Block_Adminhtml_VendorLoad_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('vendorload_form', array('legend'=>Mage::helper('vendorload')->__('Vendor Load information')));
     
      $fieldset->addField('attribute_id', 'select', array(
          'label'     => Mage::helper('vendorload')->__('Product Attribute'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'attribute_id',
          'values'   => Mage::helper('vendorload')->ListAttributes()
      ));

      $fieldset->addField('admin_id', 'select', array(
          'label'     => Mage::helper('vendorload')->__('Admin'),
          'required'  => true,
          'name'      => 'admin_id',
        'disabled'  => true,
           'values'   => Mage::helper('vendorload')->ListVendors(),
	  ));
		
    $fieldset->addField('load', 'text', array(
          'label'     => Mage::helper('vendorload')->__('Load'),
          'required'  => true,
          'name'      => 'load',
      )); 
      if ( Mage::getSingleton('adminhtml/session')->getVendorLoadData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getVendorLoadData());
          Mage::getSingleton('adminhtml/session')->setVendorLoadData(null);
      } elseif ( Mage::registry('vendorload_data') ) {
          $form->setValues(Mage::registry('vendorload_data')->getData());
      }
      return parent::_prepareForm();
  }
}
