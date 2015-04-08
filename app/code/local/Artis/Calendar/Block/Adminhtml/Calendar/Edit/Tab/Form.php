<?php

class Artis_Calendar_Block_Adminhtml_Calendar_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('calendar_form', array('legend'=>Mage::helper('calendar')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('calendar')->__('Country Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
      
    $fieldset->addField('link', 'text', array(
          'label'     => Mage::helper('calendar')->__('Link'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'link',
      ));
    
    $fieldset->addField('color', 'text', array(
          'label'     => Mage::helper('calendar')->__('Color Code (eg. #C6322A)'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'color',
      ));

//      $fieldset->addField('filename', 'file', array(
//          'label'     => Mage::helper('calendar')->__('File'),
//          'required'  => false,
//          'name'      => 'filename',
//	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('calendar')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('calendar')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('calendar')->__('Disabled'),
              ),
          ),
      ));
     
      //$fieldset->addField('content', 'editor', array(
      //    'name'      => 'content',
      //    'label'     => Mage::helper('calendar')->__('Content'),
      //    'title'     => Mage::helper('calendar')->__('Content'),
      //    'style'     => 'width:700px; height:500px;',
      //    'wysiwyg'   => false,
      //    'required'  => true,
      //));
     
      if ( Mage::getSingleton('adminhtml/session')->getCalendarData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCalendarData());
          Mage::getSingleton('adminhtml/session')->setCalendarData(null);
      } elseif ( Mage::registry('calendar_data') ) {
          $form->setValues(Mage::registry('calendar_data')->getData());
      }
      return parent::_prepareForm();
  }
}