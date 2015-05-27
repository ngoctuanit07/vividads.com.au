<?php

class AsiaConnect_Gallery_Block_Adminhtml_Album_Edit_Tab_Configuration extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('album_configuration_form', array('legend'=>Mage::helper('gallery')->__('Album information')));
       
	  $fieldset->addField('default_config', 'select', array(
          'label'     => Mage::helper('gallery')->__('Use Default Album Configuration'),
          'required'  => false,
          'name'      => 'default_config',
     	  'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('gallery')->__('Yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('gallery')->__('No'),
              ),
          ),
     	  'onchange'	  =>'
     	  show_hide_checkbox_fields(\'thumbnail_size\',this.value);
     	  show_hide_checkbox_fields(\'photo_slide_show_size\',this.value);
     	  show_hide_checkbox_fields(\'show_photo_title\',this.value);
     	  show_hide_checkbox_fields(\'show_photo_description\',this.value);
     	  show_hide_checkbox_fields(\'show_photo_link\',this.value);
     	  show_hide_checkbox_fields(\'show_photo_update_date\',this.value);',
      ));
      
     $fieldset->addField('thumbnail_size', 'text', array(
          'label'     => Mage::helper('gallery')->__('Photo Thumbnail Size (w-h)px'),
     	  'disabled'  => Mage::registry('album_data')->getDefaultConfig()||!Mage::registry('album_data')->getId()?'disabled':'',
          'required'  => false,
          'name'      => 'thumbnail_size',
      ));
      
     $fieldset->addField('photo_slide_show_size', 'text', array(
          'label'     => Mage::helper('gallery')->__('Slideshow Size (w-h)px'),
          'disabled'  => Mage::registry('album_data')->getDefaultConfig()||!Mage::registry('album_data')->getId()?'disabled':'',
          'required'  => false,
          'name'      => 'photo_slide_show_size',
      ));
      
	$fieldset->addField('show_photo_title', 'select', array(
          'label'     => Mage::helper('gallery')->__('Show Photo Title'),
          'name'      => 'show_photo_title',
		  'disabled'  => Mage::registry('album_data')->getDefaultConfig()||!Mage::registry('album_data')->getId()?'disabled':'',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('gallery')->__('Yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('gallery')->__('No'),
              ),
          ),
      ));
     
	$fieldset->addField('show_photo_description', 'select', array(
          'label'     => Mage::helper('gallery')->__('Show Photo Description'),
          'name'      => 'show_photo_description',
		  'disabled'  => Mage::registry('album_data')->getDefaultConfig()||!Mage::registry('album_data')->getId()?'disabled':'',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('gallery')->__('Yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('gallery')->__('No'),
              ),
          ),
      ));
      
	$fieldset->addField('show_photo_link', 'select', array(
          'label'     => Mage::helper('gallery')->__('Enable Photo Link'),
          'name'      => 'show_photo_link',
		  'disabled'  => Mage::registry('album_data')->getDefaultConfig()||!Mage::registry('album_data')->getId()?'disabled':'',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('gallery')->__('Yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('gallery')->__('No'),
              ),
          ),
      ));
      
	$fieldset->addField('show_photo_update_date', 'select', array(
          'label'     => Mage::helper('gallery')->__('Show Photo Update Date'),
          'name'      => 'show_photo_update_date',
		  'disabled'  => Mage::registry('album_data')->getDefaultConfig()||!Mage::registry('album_data')->getId()?'disabled':'',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('gallery')->__('Yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('gallery')->__('No'),
              ),
          ),
      ));
     $fieldset->addField('url_rewrite_id', 'hidden', array(
          'name'      => 'url_rewrite_id',
      ));
      
      if ( Mage::getSingleton('adminhtml/session')->getGalleryData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getGalleryData());
          Mage::getSingleton('adminhtml/session')->setGalleryData(null);
      } elseif ( Mage::registry('album_data')->getId() ) {
          $form->setValues(Mage::registry('album_data')->getData());
      }else{
      	$data = array('default_config'=>1);
      	$form->setValues($data);
      }
      return parent::_prepareForm();
  }
}