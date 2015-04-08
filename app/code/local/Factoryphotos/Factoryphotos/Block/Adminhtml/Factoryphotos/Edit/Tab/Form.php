<?php

class Factoryphotos_Factoryphotos_Block_Adminhtml_Factoryphotos_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('factoryphotos_form', array('legend'=>Mage::helper('factoryphotos')->__('Item information')));
      $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(

    array('tab_id' => 'form_section')

);
    $wysiwygConfig["files_browser_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index');
    
    $wysiwygConfig["directives_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
    
    $wysiwygConfig["directives_url_quoted"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
    
    $wysiwygConfig["widget_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index');
    
    $wysiwygConfig["files_browser_window_width"] = (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width');
    
    $wysiwygConfig["files_browser_window_height"] = (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height');
    
    $plugins = $wysiwygConfig->getData("plugins");
    
    $plugins[0]["options"]["url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin');
    
    $plugins[0]["options"]["onclick"]["subject"] = "MagentovariablePlugin.loadChooser('".Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin')."', '{{html_id}}');";
    
    $plugins = $wysiwygConfig->setData("plugins",$plugins);

     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('factoryphotos')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('factoryphotos')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('factoryphotos')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('factoryphotos')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('factoryphotos')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('contents', 'editor', array(
          'name'      => 'contents',
          'label'     => Mage::helper('factoryphotos')->__('Content'),
          'title'     => Mage::helper('factoryphotos')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => true,
	  'config'    => $wysiwygConfig,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getFactoryphotosData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFactoryphotosData());
          Mage::getSingleton('adminhtml/session')->setFactoryphotosData(null);
      } elseif ( Mage::registry('factoryphotos_data') ) {
          $form->setValues(Mage::registry('factoryphotos_data')->getData());
      }
      return parent::_prepareForm();
  }
}