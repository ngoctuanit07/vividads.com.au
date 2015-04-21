<?php

/**
 * MD_Vividslider.
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
 * @package    Vividslider
 * @object     MD_Vividslider_Block_Adminhtml_Vividslider_Edit_Tab_Form
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */
?>
<?php

class MD_Vividslider_Block_Adminhtml_Vividslider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    
	
	protected function _prepareForm() {
       
	    $form = new Varien_Data_Form();
        $this->setForm($form);
        
		$fieldset = $form->addFieldset('vividslider_form', array('legend' => Mage::helper('vividslider')->__('Email Template information')));
		
		
		
        $object = Mage::getModel('vividslider/vividslider')->load($this->getRequest()->getParam('id'));

         if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'select', array(
                'name' => 'store_id',
                'label' => Mage::helper('cms')->__('Select Store '),
                'title' => Mage::helper('cms')->__('Select Store '),
                'required' => true,
				'onchange'=>'updateStoreInfo(this.value,\'store\')',
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        } 
		///loading on change store info
		$script = '
		<script>
		function updateStoreInfo(val=0, type=0){
			jQuery(document).ready(function(){
				var form_vars = {formkey		:"'.Mage::getSingleton('core/session')->getFormKey().'",
									 sid	:val,
									 s_type		:type,								 
									 };
						
						jQuery.ajax({
							
						// The link we are accessing.
						url: "'.Mage::getUrl().'vividslider/index/getstore/",	
						type: "post",
						dataType: "json",
						data:form_vars,
						error: function(){							
						},						
						beforeSend: function(){
						
						},						
						complete: function(strData){
													
						},						
						success: function( strData ){
							// Load the content in to the page.
							 console.log( strData );
							 if(strData.store_name){
							 	jQuery(\'#store_name\').val(strData.store_name);
							 }else{
								 jQuery(\'#category_name\').val(strData.category_name);
								 }
						}
					
					});
				});
			}
		</script>
		
		';
		 
		echo $script;
		 
		
        $fieldset->addField('category_id', 'select', array(
            'label' => Mage::helper('vividslider')->__('Select Category'),
            'name' => 'category_id',
            'required' => true,
			'onchange' =>'updateStoreInfo(this.value,\'category\')',
            'values' => $this->getCategoriesArray(),
        )); 
		
		$fieldset->addField('title', 'text', array(
            'label' => Mage::helper('vividslider')->__('Slider Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
			'style'=>'font-size:14px; font-weight:bold; color:red; width:400px;',
        ));
	   
	    $fieldset->addField('store_name', 'text', array(
            'label' => Mage::helper('vividslider')->__('Store Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'store_name',
        ));
		
		   $fieldset->addField('category_name', 'text', array(
            'label' => Mage::helper('vividslider')->__('Category Name'),
            'class' => 'required-entry',
            'required' => true,
			//'style'=>'width:600px;',
            'name' => 'category_name',
        ));

           

		
		/*$fieldset->addField('filename', 'file', array(
            'label' => Mage::helper('vividslider')->__('File'),
            'required' => false,
            'name' => 'filename',
        ));
*/

        $fieldset->addField('pageurl', 'text', array(
            'name' => 'pageurl',
            'label' => Mage::helper('vividslider')->__('Page URL'),
            'title' => Mage::helper('vividslider')->__('Page URL'),
            'required' => false,
            'class' => 'validate-url',
        ));
		
	     $fieldset->addField('width', 'text', array(
            'name' => 'width',
            'label' => Mage::helper('vividslider')->__('Width in Pixels'),
            'title' => Mage::helper('vividslider')->__('Width in Pixels'),
            'required' => false,
            'class' => 'required-entry',
			'style'=>'width:70px; text-align:right; padding-right:10px;',
			'after_element_html' =>'<span style="font-size:11px; color:green">Width in Pixel(s)</span>',
        ));	
	     
		 $fieldset->addField('height', 'text', array(
            'name' => 'height',
            'label' => Mage::helper('vividslider')->__('Height in Pixels'),
            'title' => Mage::helper('vividslider')->__('Height in Pixels'),
            'required' => false,
			'style'=>'width:70px; text-align:right; padding-right:10px;',
            'class' => 'required-entry',
			'after_element_html' =>'<span style="font-size:11px; color:green">Height in Pixel(s)</span>',
        ));	
   

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('vividslider')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('vividslider')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('vividslider')->__('Disabled'),
                ),
            ),
        ));

		/*
		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('add_variables' => false, 'add_widgets' => false,'files_browser_window_url'=>$this->getBaseUrl().'admin/cms_wysiwyg_images/index/'));


		$fieldset->addField('template_text', 'editor', array(
            'name'      => 'template_text',
			'label'      => Mage::helper('vividslider')->__('Template Contents'),
			'title'      => Mage::helper('vividslider')->__('Template Contents'),
            'style'     => 'height:36em; width:1000px; ',
            'state'     => 'html',
			'config'      => $wysiwygConfig,
			'wysiwyg'   => true,
			'required'  => true,
			
        ));
		*/
		
	$_slider_id = $this->getRequest()->getParam('id');	
	$fieldset->addField('note', 'note', array(
          'text'     => '<b>Slider Uploaded Images:</b> ',
		  'after_element_html' => Mage::getModel('vividslider/vividslider')->showVividSliderImages($_slider_id),
          'tabindex' => 1,
		  

        ));

	$fieldset->addField('file_attachments','multiupload',array(
		'name'=>'file_attachments',
		'label'=>Mage::helper('vividslider')->__('Vivid Slider Files(s)'),
		'title'=>Mage::helper('vividslider')->__('Vivid Slider Files(s)'),
		'multiple'=>true,
		
		));
	

        if (Mage::getSingleton('adminhtml/session')->getVividsliderData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getVividsliderData());
            Mage::getSingleton('adminhtml/session')->setVividsliderData(null);
        } elseif (Mage::registry('vividslider_data')) {
            $form->setValues(Mage::registry('vividslider_data')->getData());
        }
		
		//var_dump(Mage::registry('vividslider_data')->getData()); exit;
		
        return parent::_prepareForm();
    }
	
	///get CategoriesArray ///
	public function getCategoriesArray() {
		
	 $categoriesArray = Mage::getModel('catalog/category')
            ->getCollection()
            ->addAttributeToSelect('name')
			->addAttributeToSort('path', 'asc')
			->addAttributeToFilter('is_active',1)
            ->load()
            ->toArray();

    $c_entity = htmlentities('-',ENT_QUOTES);
	//$nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
	$nonEscapableNbspChar = html_entity_decode($c_entity, ENT_NOQUOTES, 'UTF-8');
    
	foreach ($categoriesArray as $categoryId => $category) {
        
		if (isset($category['name']) && isset($category['level'])) {
           
		   
				$category_label =  $category['name']; 			 
		   
			/*if($category['level']==2){ 
				$category_label =  str_repeat($nonEscapableNbspChar,4).$category['name'];
				continue;
			}
			if($category['level']==3){ 
				$category_label =  str_repeat($nonEscapableNbspChar,8).$category_label;
				continue;
			}
			 
			if($category['level']==4){ 
				$category_label =  str_repeat($nonEscapableNbspChar,12).$category_label;
			}
			if($category['level']==5){ 
				$category_label =  str_repeat($nonEscapableNbspChar,16).$category_label;
				continue;
			}
			
			if($category['level']==6){ 
				$category_label =  str_repeat($nonEscapableNbspChar,20).$category_label;
			}
			if($category['level']==7){ 
				$category_label =  str_repeat($nonEscapableNbspChar,24).$category_label;
				continue;
			}*/
			 
			 $categories[] = array(
				//	'label' => $category_label.'('.$category['level'].')',
					'label' => $category_label.' level('.$category['level'].')'.' Id ('.$categoryId.')',
					'level'  =>$category['level'],
					'value' => $categoryId
				);
			
        }
		
    }
	//Zend_debug::dump($categories); exit;
	 

    return $categories;
}

}
