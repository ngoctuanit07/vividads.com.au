<?php

class Hm_Testimonial_Block_Adminhtml_Testimonial_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
/*	public function __construct(){
        parent::__construct();
        $this->setTemplate('hm_testimonial/form.phtml');
        $this->setDestElementId('edit_form');
        $this->setShowGlobalIcon(false);
	}*/
		
	  protected function _prepareForm()
	  {
	      $form = new Varien_Data_Form();
	      $this->setForm($form);
	      $fieldset = $form->addFieldset('testimonial_form', array('legend'=>Mage::helper('testimonial')->__('Item information')));
	       /**
	         * Check is single store mode
	         */
	      $fieldset->addType('view_media','Hm_Testimonial_Lib_Varien_Data_Form_Element_Viewmedia');
	      
	      if (!Mage::app()->isSingleStoreMode()) {
	            $fieldset->addField('store_id', 'multiselect', array(
	                'name'      => 'stores[]',
	                'label'     => Mage::helper('cms')->__('Store View'),
	                'title'     => Mage::helper('cms')->__('Store View'),
	                'required'  => true,
	                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
	            ));
	        }
	      $fieldset->addField('client_name', 'text', array(
	          'label'     => Mage::helper('testimonial')->__('Client Name'),
	          'class'     => 'required-entry',
	          'required'  => true,
	          'name'      => 'client_name',
	      ));
	      
	      $fieldset->addField('company', 'text', array(
	          'label'     => Mage::helper('testimonial')->__('Client Company'),
	          'required'  => false,
	          'name'      => 'company',
	      ));
	      
	      $fieldset->addField('website', 'text', array(
	          'label'     => Mage::helper('testimonial')->__('Client Website'),
	          'required'  => false,
	          'name'      => 'website',
	      ));
	      
	      $fieldset->addField('email', 'text', array(
	          'label'     => Mage::helper('testimonial')->__('Client Email'),
	          'required'  => false,
	          'name'      => 'email',
	      ));
	         
		 $fieldset->addField('address', 'text', array(
	          'label'     => Mage::helper('testimonial')->__('Address'),
	          'required'  => false,
	          'name'      => 'address',
	      ));
	      
	      $fieldset->addField('media', 'file', array(
	          'label'     => Mage::helper('testimonial')->__('Upload Media(Video, Image)'),
	          'required'  => false,
	          'name'      => 'media',
		  ));
              //custom by hoanx
              $fieldset->addField('media_url', 'text', array(
	          'label'     => Mage::helper('testimonial')->__('Media(Video, Image) URL'),
	          'required'  => false,
              'class' => 'MW_validate_media',
	          'name'      => 'media_url',
		  ));
              
              // end custom
              //custom by namdg
          $fieldset->addField('testimonial_id', 'view_media', array(
          		'label'         => 'View uploaded media',
              	'name'          => 'testimonial_id',
              	'required'      => false,
              	'bold'      =>  true,
          ));
          	// end custom
	      $fieldset->addField('status', 'select', array(
	          'label'     => Mage::helper('testimonial')->__('Status'),
	          'name'      => 'status',
	          'values'    => array(
	              array(
	                  'value'     => 1,
	                  'label'     => Mage::helper('testimonial')->__('Enabled'),
	              ),
	
	              array(
	                  'value'     => 2,
	                  'label'     => Mage::helper('testimonial')->__('Disabled'),
	              ),
	          ),
	      ));
	     
	      $fieldset->addField('description', 'editor', array(
	          'name'      => 'description',
	          'label'     => Mage::helper('testimonial')->__('Content'),
	          'title'     => Mage::helper('testimonial')->__('Content'),
	          'style'     => 'width:700px; height:400px;',
	          'wysiwyg'   => false,
	          'required'  => true,
	      ));      
	     
	      if ( Mage::getSingleton('adminhtml/session')->getTestimonialData() )
	      {
	          $form->setValues(Mage::getSingleton('adminhtml/session')->getTestimonialData());
	          Mage::getSingleton('adminhtml/session')->setTestimonialData(null);
	      } elseif ( Mage::registry('testimonial_data') ) {
	      	  $testimonial = Mage::registry('testimonial_data')->getData();
	          $form->setValues($testimonial);
	          if (!Mage::app()->isSingleStoreMode()) {
				  if(Mage::registry('testimonial_data')->getTestimonialId()){
			         // get array of selected store_id 
						$collection =  Mage::getModel('testimonial/testimonial')->getCollection();
						$collection->join('testimonial_store', 'testimonial_store.testimonial_id = main_table.testimonial_id AND main_table.testimonial_id='.$testimonial['testimonial_id'], 'testimonial_store.store_id');
						
						$arrStoreId = array();
				        foreach($collection->getData() as $col){
				        	$arrStoreId[] = $col['store_id'];	
				        }
			        
			         // set value for store view selected:
			         $form->getElement('store_id')->setValue($arrStoreId);
				  }
	          }
	      }
	      return parent::_prepareForm();
  		}
}