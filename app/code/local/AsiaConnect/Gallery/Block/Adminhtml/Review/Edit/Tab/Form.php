<?php

class AsiaConnect_Gallery_Block_Adminhtml_Review_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('review_form', array('legend'=>Mage::helper('gallery')->__('Review information')));

      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('gallery')->__('Customer Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'name',
      ));
      $fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('gallery')->__('Customer Email'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'email',
      ));
      
	  $photos = array(array('value' => '', 'label' => 'Select an Photo'));
	  $collection = Mage::getModel('gallery/gallery')->getCollection();
	  foreach ($collection as $photo) {
		 $photos[] = array('value' => $photo->getId(), 'label' => $photo->getTitle());
	  }

      $fieldset->addField('gallery_id', 'select', array(
          'label'     => Mage::helper('gallery')->__('Photo'),
          'name'      => 'gallery_id',
          'required'  => true,
          'values'    => $photos,
      ));

      $fieldset->addField('rate', 'text', array(
          'label'     => Mage::helper('gallery')->__('Rate'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'rate',
      ));
      
      $status= array(array('value'=>null,'label'=>'Select status'), array('value'=>1,'label'=>'Pending'), array('value'=>2,'label'=>'Approved'), array('value'=>3,'label'=>'Not Approved'));
      
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('gallery')->__('Status'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'status',
      	  'values'	  => $status,
      ));

      $reviewTypes= array(array('value'=>null,'label'=>'Select type'), array('value'=>1,'label'=>'Guest'), array('value'=>2,'label'=>'Customer'), array('value'=>3,'label'=>'Admin'));
      
      $fieldset->addField('review_type', 'select', array(
          'label'     => Mage::helper('gallery')->__('Review Type'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'review_type',
      	  'values'	  => $reviewTypes,
      ));
      
      $fieldset->addField('create_time', 'hidden', array(
          'name'      => 'create_time',
      ));
      
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('gallery')->__('Content'),
          'title'     => Mage::helper('gallery')->__('Content'),
          'style'     => 'width:500px; height:200px;',
          'wysiwyg'   => false
      ));
      if ( Mage::getSingleton('adminhtml/session')->getReviewData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getReviewData());
          Mage::getSingleton('adminhtml/session')->setReviewData(null);
      } elseif ( Mage::registry('review_data') ) {
          $form->setValues(Mage::registry('review_data')->getData());
      }

      return parent::_prepareForm();
  }
}