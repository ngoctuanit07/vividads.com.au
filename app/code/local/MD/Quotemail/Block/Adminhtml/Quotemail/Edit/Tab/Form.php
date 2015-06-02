<?php

/**
 * MD_Quotemail.
 * Company: Vivid Ads Inc Australia
 * Author: AShfaq Ahmed
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.vividads.com.au
 *
 * @category   MD
 * @package    Quotemail
 * @object     MD_Quotemail_Block_Adminhtml_Quotemail_Edit_Tab_Form
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */
?>
<?php

class MD_Quotemail_Block_Adminhtml_Quotemail_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    
	
	protected function _prepareForm() {
       
	    $form = new Varien_Data_Form();
        $this->setForm($form);
        
		$fieldset = $form->addFieldset('quotemail_form', array('legend' => Mage::helper('quotemail')->__('Email Template information')));
		
		
		
        $object = Mage::getModel('quotemail/quotemail')->load($this->getRequest()->getParam('id'));

      /*  if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('cms')->__('Store View'),
                'title' => Mage::helper('cms')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }*/
		
		
		/*
		
        $fieldset->addField('category_id', 'select', array(
            'label' => Mage::helper('quotemail')->__('Category'),
            'name' => 'category_id',
            'required' => true,
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('quotemail')->__('Schools'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('quotemail')->__('Universities'),
                ),
                array(
                    'value' => 3,
                    'label' => Mage::helper('quotemail')->__('Govt. Departments'),
                ),
                array(
                    'value' => 4,
                    'label' => Mage::helper('quotemail')->__('City Councils'),
                ),
                array(
                    'value' => 5,
                    'label' => Mage::helper('quotemail')->__('Corporates'),
                ),
            ),
        ));*/
		
		
		
		
         $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('quotemail')->__('Template Model'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
        ));
	   
	    $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('quotemail')->__('Email Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
		
		   $fieldset->addField('subject', 'text', array(
            'label' => Mage::helper('quotemail')->__('Email Subject'),
            'class' => 'required-entry',
            'required' => true,
			'style'=>'width:600px;',
            'name' => 'subject',
        ));

           

		
		/*$fieldset->addField('filename', 'file', array(
            'label' => Mage::helper('quotemail')->__('File'),
            'required' => false,
            'name' => 'filename',
        ));
*/

        $fieldset->addField('url', 'text', array(
            'name' => 'url',
            'label' => Mage::helper('quotemail')->__('Url'),
            'title' => Mage::helper('quotemail')->__('Url'),
            'required' => false,
            'class' => 'validate-url',
        ));

   	$fieldset->addField('templatetype', 'select', array(
            'label' => Mage::helper('quotemail')->__('Template Type'),
            'name' => 'templatetype',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('quotemail')->__('HTML'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('quotemail')->__('TEXT'),
                ),
            ),
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('quotemail')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('quotemail')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('quotemail')->__('Disabled'),
                ),
            ),
        ));

		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('add_variables' => false, 'add_widgets' => false,'files_browser_window_url'=>$this->getBaseUrl().'admin/cms_wysiwyg_images/index/'));


		$fieldset->addField('template_text', 'editor', array(
            'name'      => 'template_text',
			'label'      => Mage::helper('quotemail')->__('Template Contents'),
			'title'      => Mage::helper('quotemail')->__('Template Contents'),
            'style'     => 'height:36em; width:1000px; ',
            'state'     => 'html',
			'config'      => $wysiwygConfig,
			'wysiwyg'   => true,
			'required'  => true,
			
        ));
		
	$_quote_id = $this->getRequest()->getParam('id');	
	$fieldset->addField('note', 'note', array(
          'text'     => '<b>Attached Files:</b> ',
		  'after_element_html' => Mage::getModel('quotemail/quotemail')->showEmailAttachements($_quote_id),
          'tabindex' => 1,
		  ''

        ));



		
		
	$fieldset->addField('file_attachments','multiupload',array(
		'name'=>'file_attachments',
		'label'=>Mage::helper('quotemail')->__('File Attachment(s)'),
		'title'=>Mage::helper('quotemail')->__('File Attachment(s)'),
		'multiple'=>true,
		
		));
	

        if (Mage::getSingleton('adminhtml/session')->getQuotemailData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getQuotemailData());
            Mage::getSingleton('adminhtml/session')->setQuotemailData(null);
        } elseif (Mage::registry('quotemail_data')) {
            $form->setValues(Mage::registry('quotemail_data')->getData());
        }
        return parent::_prepareForm();
    }
	
	

}
