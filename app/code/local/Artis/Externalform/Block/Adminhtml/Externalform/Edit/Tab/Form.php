<?php

class Artis_Externalform_Block_Adminhtml_Externalform_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('externalform_form', array('legend'=>Mage::helper('externalform')->__('Create Iframe')));
     
      /*$fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('externalform')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));*/
      
      
      
      $fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('externalform')->__('Store View'),
                'title'     => Mage::helper('externalform')->__('Store View'),
                'onchange'  => "create_iframe(this.value);",
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(true, false),
            ));

      /*$fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('externalform')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));*/
		
     /* $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('externalform')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('externalform')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('externalform')->__('Disabled'),
              ),
          ),
      ));*/
     
     $tmp=Mage::getBaseUrl()."externalform/?store_id=";
     
      $fieldset->addField('iframe_val', 'textarea', array(
          'name'      => 'iframe_val',
	  'id'      => 'iframe_val',
          'label'     => Mage::helper('externalform')->__('Copy Iframe'),
          'title'     => Mage::helper('externalform')->__('Copy Iframe'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
	  //'readonly'  => 'readonly',
	  'onclick'   => 'this.select();',
	  
	  
	 
          
      ))->setAfterElementHtml("<script type=\"text/javascript\">
                            function create_iframe(selectElement){
			     
			      var str='';
			      
			      str='<iframe src=\'".$tmp."'+selectElement+'\' height=\'1000\' width=\'1310\'></iframe>';
			     
			     
				document.getElementById('iframe_val').value=str;
			      
                              
                            }
                         </script>");
     
      if ( Mage::getSingleton('adminhtml/session')->getExternalformData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getExternalformData());
          Mage::getSingleton('adminhtml/session')->setExternalformData(null);
      } elseif ( Mage::registry('externalform_data') ) {
          $form->setValues(Mage::registry('externalform_data')->getData());
      }
      return parent::_prepareForm();
  }
}
