<?php
/**
 * Faqs extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 
 * @category   FME
 * @package    Faqs
 * @author     Kamran Rafiq Malik <kamran.malik@unitedsol.net> 
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Faqs_Block_Adminhtml_Faqs_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
	$form = new Varien_Data_Form();
	$this->setForm($form);
	$fieldset = $form->addFieldset('faqs_form', array('legend'=>Mage::helper('faqs')->__('Faq information')));
      
	$resource = Mage::getSingleton('core/resource');
	$read= $resource->getConnection('core_read');
	$topicTable = $resource->getTableName('faqs_topics');
	
	$select = $read->select()
	->from($topicTable,array('topic_id as value','title as label'))
	->order('topic_id ASC') ;
	$topics = $read->fetchAll($select);
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('faqs')->__('Question'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
    
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('faqs')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('faqs')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('faqs')->__('Disabled'),
              ),
          ),
      ));
	  
	  $fieldset->addField('type', 'select', array(
          'label'     => Mage::helper('faqs')->__('Question Type'),
          'name'      => 'type',
          'values'    => array(
              array(
                  'value'     => 'General Question',
                  'label'     => Mage::helper('faqs')->__('General Question'),
              ),

              array(
                  'value'     => 'Product Question',
                  'label'     => Mage::helper('faqs')->__('Product Question'),
              ),
          ),
      ));
      
     $fieldset->addField('topic_id', 'select', array(
          'label'     => Mage::helper('faqs')->__('Add in Topic'),
          'name'      => 'topic_id',
          'values'    => $topics,
      ));
     
     $fieldset->addField('prid', 'hidden', array(
          'label'     => Mage::helper('faqs')->__('Add in Topic'),
          'name'      => 'prid',
	  	  'id'      => 'prid',
          
      ));
	  
		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
             array('tab_id' => 'form_section')
        );
	  //make Wysiwyg Editor integrate in the form
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
		
	  $fieldset->addField('faq_answar', 'editor', array(
		  'name'      => 'faq_answar',
		  'label'     => Mage::helper('faqs')->__('Answer'),
		  'title'     => Mage::helper('faqs')->__('Answer'),
		  'style'     => 'height:20em; width:50em;',
		  'config'    => $wysiwygConfig,
		));
     
      if ( Mage::getSingleton('adminhtml/session')->getFaqsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFaqsData());
          Mage::getSingleton('adminhtml/session')->setFaqsData(null);
      } elseif ( Mage::registry('faqs_data') ) {
          $form->setValues(Mage::registry('faqs_data')->getData());
      }
      return parent::_prepareForm();
  }
}