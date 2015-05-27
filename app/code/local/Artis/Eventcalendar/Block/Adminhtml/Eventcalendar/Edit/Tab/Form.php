<?php

class Artis_Eventcalendar_Block_Adminhtml_Eventcalendar_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('eventcalendar_form', array('legend'=>Mage::helper('eventcalendar')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('eventcalendar')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('eventcalendar')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('eventcalendar')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('eventcalendar')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('eventcalendar')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('eventcalendar')->__('Content'),
          'title'     => Mage::helper('eventcalendar')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getEventcalendarData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getEventcalendarData());
          Mage::getSingleton('adminhtml/session')->setEventcalendarData(null);
      } elseif ( Mage::registry('eventcalendar_data') ) {
          $form->setValues(Mage::registry('eventcalendar_data')->getData());
      }
      return parent::_prepareForm();
  }
}