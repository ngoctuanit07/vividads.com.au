<?php

class Artis_Systemalert_Block_Adminhtml_Systemalert_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('systemalert_form', array('legend'=>Mage::helper('systemalert')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('systemalert')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('systemalert')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('systemalert')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('systemalert')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('systemalert')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('systemalert')->__('Content'),
          'title'     => Mage::helper('systemalert')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getSystemalertData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSystemalertData());
          Mage::getSingleton('adminhtml/session')->setSystemalertData(null);
      } elseif ( Mage::registry('systemalert_data') ) {
          $form->setValues(Mage::registry('systemalert_data')->getData());
      }
      return parent::_prepareForm();
  }
}