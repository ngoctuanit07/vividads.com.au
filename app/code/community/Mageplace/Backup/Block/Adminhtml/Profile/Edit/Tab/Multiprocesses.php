<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package     Mageplace_Backup
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */
class Mageplace_Backup_Block_Adminhtml_Profile_Edit_Tab_Multiprocesses extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$model = Mage::registry('mpbackup_profile');

		$form = new Varien_Data_Form();

		$fieldset = $form->addFieldset('multiprocess_fieldset',
			array(
				'legend'	=> $this->__('Multiprocesses Settings'),
				'class'		=> 'fieldset-wide'
			)
		);
		
		$isNew = !$model->getProfileId() ? true : false;

		$fieldset->addField('profile_multiprocess_enable',
			'select',
			array(
				'name'		=> 'profile_multiprocess_enable',
				'label'		=> $this->__('Enable'),
				'title'		=> $this->__('Enable'),
				'class' 	=> 'input-select',
				'values'	=> Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
			)
		);
		
		$fieldset->addField('profile_multiprocess_time',
			'text',
			array(
				'name'		=> 'profile_multiprocess_time',
				'label'		=> $this->__('Process Time'),
				'title'		=> $this->__('Process Time'),
				'note'		=> $this->__('If empty, Process Time will be 60 seconds.'),
			)
		);
		
		/*$fieldset->addField('profile_multiprocess_size',
			'text',
			array(
				'name'		=> 'profile_multiprocess_size',
				'label'		=> $this->__('Size of File\'s Part'),
				'title'		=> $this->__('Size of File\'s Part'),
				'note'		=> $this->__('If empty, Size of File\'s Part will be %s MB.', Mageplace_Backup_Model_File::DEFAULT_FILE_PART_SIZE),
			)
		);*/

		$form->setValues($model->getData());

		$this->setForm($form);

		return parent::_prepareForm();
	}	
	
}