<?php
 class FMA_Reviewsplus_Block_Adminhtml_Review_Edit_Form extends Mage_Adminhtml_Block_Review_Edit_Form
{
    protected function _prepareForm()
    {
    	parent::_prepareForm();
    	 $review = Mage::registry('review_data');
    	 $form = $this->getForm();
		 $fieldset = $form->addFieldset('review',
		            array('legend'=>Mage::helper('reviewsplus')->__('Admin Reply to Review'))
		    );
		$fieldset->addField('review_reply', 'textarea', array(
		        'label'     => Mage::helper('review')->__('Reply to Review'),
		        'required'  => false,
		        'name'      => 'review_reply',
		        'index'		=>'review_reply'
		        ));
		     $this->setForm($form);
		     $form->setValues($review->getData());
		      return $this;
		     
    }
   
} 