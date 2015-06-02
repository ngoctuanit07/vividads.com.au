<?php

class MW_Mwcore_Block_Adminhtml_Notification_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('notification_form', array('legend'=>Mage::helper('mwcore')->__('Item information')));
     
  
	  $fieldset->addField('message', 'editor', array(
          'name'      => 'message',
          'label'     => Mage::helper('mwcore')->__('Message'),
          'title'     => Mage::helper('mwcore')->__('Message'),
          'style'     => 'width:700px; height:100px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
      
    $fieldset->addField('time_apply', 'text', array(
          'label'     => Mage::helper('mwcore')->__('Time Apply(days)'),
          'class'     => 'required-entry validate-number',
          'required'  => true,
          'name'      => 'time_apply',
      ));
           
   
           
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('mwcore')->__('Status'),
          'name'      => 'status',
          'values'    => array(
      		
      		array(
                  'value'     => 0,
                  'label'     => Mage::helper('mwcore')->__('Normal'),
              ),
              
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mwcore')->__('Remind'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mwcore')->__('Not Display'),
              ),
          ),
      ));
          
      if ( Mage::getSingleton('adminhtml/session')->getMwcoreData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMwcoreData());
          Mage::getSingleton('adminhtml/session')->setMwcoreData(null);
      } elseif ( Mage::registry('mwcore_data') ) {
          $form->setValues(Mage::registry('mwcore_data')->getData());
      }
      return parent::_prepareForm();
  }
}