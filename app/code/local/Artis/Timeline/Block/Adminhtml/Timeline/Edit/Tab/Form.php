<?php

class Artis_Timeline_Block_Adminhtml_Timeline_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('timeline_form', array('legend'=>Mage::helper('timeline')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('timeline')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('timeline')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('timeline')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('timeline')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('timeline')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('timeline')->__('Content'),
          'title'     => Mage::helper('timeline')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getTimelineData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTimelineData());
          Mage::getSingleton('adminhtml/session')->setTimelineData(null);
      } elseif ( Mage::registry('timeline_data') ) {
          $form->setValues(Mage::registry('timeline_data')->getData());
      }
      return parent::_prepareForm();
  }
}