<?php
/**
 * Mageplace Magesocial
 *
 * @category   Mageplace
 * @package    Mageplace_Magesocial
 * @copyright  Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license    http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Block_Adminhtml_Profile_Edit_Tab_Cron extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preperation of current form
	 *
	 * @return Mageplace_Backup_Block_Adminhtml_Profile_Edit_Tab_Cron
	 */
	protected function _prepareForm()
	{
		$model = Mage::registry('mpbackup_profile');

		$form = new Varien_Data_Form();

		$CRON_ENABLE = Mageplace_Backup_Model_Profile::CRON_ENABLE;
		$CRON_TIME_TYPE = Mageplace_Backup_Model_Profile::CRON_TIME_TYPE;
		$CRON_TIME = Mageplace_Backup_Model_Profile::CRON_TIME;
		$CRON_FREQUENCY = Mageplace_Backup_Model_Profile::CRON_FREQUENCY;
		$CRON_EXPR = Mageplace_Backup_Model_Profile::CRON_BACKUP_EXPR;
		
		$CRON_FAILURE_RUNNING = Mageplace_Backup_Model_Profile::CRON_FAILURE_RUNNING;

		$CRON_SUCCESS_EMAIL = Mageplace_Backup_Model_Profile::CRON_SUCCESS_EMAIL;
		$CRON_SUCCESS_EMAIL_IDENTITY = Mageplace_Backup_Model_Profile::CRON_SUCCESS_EMAIL_IDENTITY;
		$CRON_SUCCESS_EMAIL_TEMPLATE = Mageplace_Backup_Model_Profile::CRON_SUCCESS_EMAIL_TEMPLATE;
		$CRON_SUCCESS_EMAIL_LOG_LEVEL = Mageplace_Backup_Model_Profile::CRON_SUCCESS_EMAIL_LOG_LEVEL;

		$CRON_DELETE_TYPE = Mageplace_Backup_Model_Profile::CRON_DELETE_TYPE;
		$CRON_DELETE_TYPE_ROTATION_NUMBER = Mageplace_Backup_Model_Profile::CRON_DELETE_TYPE_ROTATION_NUMBER;
		$CRON_DELETE_TYPE_DELETE_OLDER_THAN_X_DAYS = Mageplace_Backup_Model_Profile::CRON_DELETE_TYPE_DELETE_OLDER_THAN_X_DAYS;

		$CRON_SUCCESS_DELETE_EMAIL = Mageplace_Backup_Model_Profile::CRON_SUCCESS_DELETE_EMAIL;
		$CRON_SUCCESS_DELETE_EMAIL_IDENTITY = Mageplace_Backup_Model_Profile::CRON_SUCCESS_DELETE_EMAIL_IDENTITY;
		$CRON_SUCCESS_DELETE_EMAIL_TEMPLATE = Mageplace_Backup_Model_Profile::CRON_SUCCESS_DELETE_EMAIL_TEMPLATE;

		$CRON_ERROR_EMAIL = Mageplace_Backup_Model_Profile::CRON_ERROR_EMAIL;
		$CRON_ERROR_EMAIL_IDENTITY = Mageplace_Backup_Model_Profile::CRON_ERROR_EMAIL_IDENTITY;
		$CRON_ERROR_EMAIL_TEMPLATE = Mageplace_Backup_Model_Profile::CRON_ERROR_EMAIL_TEMPLATE;

		/*
		 * Cron backup settings fieldset
		 */
		$fieldset = $form->addFieldset('base_fieldset',
			array(
				'legend' => $this->__('Backup settings'),
			)
		);

		$fieldset->addField($CRON_ENABLE,
			'select',
			array(
				'name' => $CRON_ENABLE,
				'label' => $this->__('Enable Cron Backup'),
				'title' => $this->__('Enable Cron Backup'),
				'class' => 'input-select',
				'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
			)
		);
		
		$fieldset->addType('crontime', Mage::getConfig()->getBlockClassName('mpbackup/form_element_crontime')); 

		$fieldset->addField($CRON_TIME,
			'crontime',
			array(
				'name' => $CRON_TIME,
				'cron_expression_type' => $model->getData($CRON_TIME_TYPE),
				'cron_expression' => $model->getData($CRON_EXPR),
				'label' => $this->__('Cron Expression'),
				'title' => $this->__('Cron Expression'),
				'label_custom' => $this->__('Custom'),
				'label_default' => $this->__('Default'),
				'cron_expr_note' => $this->__('Expression example: */5 * * * *'),
				'frequency_time_note' => $this->__('Frequency  Hours : Minutes'),
			)
		);
		
		$cron_failure_running = $model->getData($CRON_FAILURE_RUNNING);
		if(is_null($cron_failure_running) || $cron_failure_running === '') {
			$cron_failure_running = Mageplace_Backup_Model_Profile::CRON_FAILURE_RUNNING_DEFAULT;
		}
		$model->setData($CRON_FAILURE_RUNNING, $cron_failure_running);
		
		$fieldset->addField($CRON_FAILURE_RUNNING,
			'text',
			array(
				'name' => $CRON_FAILURE_RUNNING,
				'label' => $this->__('Failure if running more then'),
				'title' => $this->__('Failure if running more then'),
				'class' => 'input-select cron-short-text ',
				'after_element_html' => $this->__('minutes'),
			)
		);

		/* $fieldset->addField($CRON_FREQUENCY,
			'select',
			array(
				'name' => $CRON_FREQUENCY,
				'label' => $this->__('Frequency'),
				'title' => $this->__('Frequency'),
				'class' => 'input-select',
				'values' => Mage::getModel('adminhtml/system_config_source_cron_frequency')->toOptionArray(),
			)
		); */

		$fieldset->addField($CRON_SUCCESS_EMAIL,
			'text',
			array(
				'name' => $CRON_SUCCESS_EMAIL,
				'label' => $this->__('Success Email Recipient'),
				'title' => $this->__('Success Email Recipient'),
			)
		);

		$fieldset->addField($CRON_SUCCESS_EMAIL_IDENTITY,
			'select',
			array(
				'name' => $CRON_SUCCESS_EMAIL_IDENTITY,
				'label' => $this->__('Success Email Sender'),
				'title' => $this->__('Success Email Sender'),
				'class' => 'input-select',
				'values' => Mage::getModel('adminhtml/system_config_source_email_identity')->toOptionArray(),
			)
		);

		$fieldset->addField($CRON_SUCCESS_EMAIL_TEMPLATE,
			'select',
			array(
				'name' => $CRON_SUCCESS_EMAIL_TEMPLATE,
				'label' => $this->__('Success Email Template'),
				'title' => $this->__('Success Email Template'),
				'class' => 'input-select',
				'values' => Mage::getModel('adminhtml/system_config_source_email_template')->setPath('mpbackup_success_email_template')->toOptionArray(),
			)
		);

		$fieldset->addField($CRON_SUCCESS_EMAIL_LOG_LEVEL,
			'select',
			array(
				'name' => $CRON_SUCCESS_EMAIL_LOG_LEVEL,
				'label' => $this->__('Success Email Log Level'),
				'title' => $this->__('Success Email Log Level'),
				'class' => 'input-select',
				'values' => Mage::getModel('mpbackup/source_loglevel')->cronOptionArray(),
			)
		);


		/*
		 * Delete settings fieldset
		 */
		$delete_fieldset = $form->addFieldset('delete_fieldset',
			array(
				'legend' => $this->__('Delete settings'),
			)
		);

		$onchange = "if(this.value == '" . Mageplace_Backup_Model_Profile::CRON_DELETE_TYPE_ROTATION . "') { "
			. "$(" . $CRON_DELETE_TYPE_ROTATION_NUMBER . ").disabled = false;"
			. "$(" . $CRON_DELETE_TYPE_DELETE_OLDER_THAN_X_DAYS . ").disabled = true;"
			. "$(" . $CRON_DELETE_TYPE_ROTATION_NUMBER . ").removeClassName('disabled');"
			. "$(" . $CRON_DELETE_TYPE_DELETE_OLDER_THAN_X_DAYS . ").addClassName('disabled');"
			. " } else if(this.value == '" . Mageplace_Backup_Model_Profile::CRON_DELETE_TYPE_DELETE_OLD . "') {"
			. "$(" . $CRON_DELETE_TYPE_ROTATION_NUMBER . ").disabled = true;"
			. "$(" . $CRON_DELETE_TYPE_DELETE_OLDER_THAN_X_DAYS . ").disabled = false;"
			. "$(" . $CRON_DELETE_TYPE_ROTATION_NUMBER . ").addClassName('disabled');"
			. "$(" . $CRON_DELETE_TYPE_DELETE_OLDER_THAN_X_DAYS . ").removeClassName('disabled');"
			. " } else { "
			. "$(" . $CRON_DELETE_TYPE_ROTATION_NUMBER . ").disabled = true;"
			. "$(" . $CRON_DELETE_TYPE_DELETE_OLDER_THAN_X_DAYS . ").disabled = true;"
			. "$(" . $CRON_DELETE_TYPE_ROTATION_NUMBER . ").addClassName('disabled');"
			. "$(" . $CRON_DELETE_TYPE_DELETE_OLDER_THAN_X_DAYS . ").addClassName('disabled');"
			. " }";

		$delete_fieldset->addField($CRON_DELETE_TYPE,
			'select',
			array(
				'name' => $CRON_DELETE_TYPE,
				'label' => $this->__('Backup delete type'),
				'title' => $this->__('Backup delete type'),
				'class' => 'input-select',
				'values' => Mage::getModel('mpbackup/source_crondeletetype')->toOptionArray(),
				'after_element_html' => '<style>.disabled {background-color: #dddddd!important;} .cron-short-text{width: 50px!important;}</style>',
				'onchange' => $onchange
			)
		);

		$rotationDisabled = $model->getData($CRON_DELETE_TYPE) != Mageplace_Backup_Model_Profile::CRON_DELETE_TYPE_ROTATION ? true : false;
		$delete_fieldset->addField($CRON_DELETE_TYPE_ROTATION_NUMBER,
			'text',
			array(
				'name' => $CRON_DELETE_TYPE_ROTATION_NUMBER,
				'label' => $this->__('Max number of backups'),
				'title' => $this->__('Max number of backups'),
				'class' => 'input-select cron-short-text ' . ($rotationDisabled ? 'disabled' : ''),
				'disabled' => $rotationDisabled,
			)
		);

		$deleteAfterDisabled = $model->getData($CRON_DELETE_TYPE) != Mageplace_Backup_Model_Profile::CRON_DELETE_TYPE_DELETE_OLD ? true : false;
		$delete_fieldset->addField($CRON_DELETE_TYPE_DELETE_OLDER_THAN_X_DAYS,
			'text',
			array(
				'name' => $CRON_DELETE_TYPE_DELETE_OLDER_THAN_X_DAYS,
				'label' => $this->__('Delete backup after'),
				'title' => $this->__('Delete backup after'),
				'class' => 'input-select cron-short-text ' . ($deleteAfterDisabled ? 'disabled' : ''),
				'disabled' => $deleteAfterDisabled,
				'after_element_html' => $this->__('days'),
			)
		);

		$delete_fieldset->addField($CRON_SUCCESS_DELETE_EMAIL,
			'text',
			array(
				'name' => $CRON_SUCCESS_DELETE_EMAIL,
				'label' => $this->__('Success Email Recipient'),
				'title' => $this->__('Success Email Recipient'),
			)
		);

		$delete_fieldset->addField($CRON_SUCCESS_DELETE_EMAIL_IDENTITY,
			'select',
			array(
				'name' => $CRON_SUCCESS_DELETE_EMAIL_IDENTITY,
				'label' => $this->__('Success Email Sender'),
				'title' => $this->__('Success Email Sender'),
				'class' => 'input-select',
				'values' => Mage::getModel('adminhtml/system_config_source_email_identity')->toOptionArray(),
			)
		);

		$delete_fieldset->addField($CRON_SUCCESS_DELETE_EMAIL_TEMPLATE,
			'select',
			array(
				'name' => $CRON_SUCCESS_DELETE_EMAIL_TEMPLATE,
				'label' => $this->__('Success Email Template'),
				'title' => $this->__('Success Email Template'),
				'class' => 'input-select',
				'values' => Mage::getModel('adminhtml/system_config_source_email_template')->setPath('mpbackup_success_delete_email_template')->toOptionArray(),
			)
		);

		/*
		 * Email settings fieldset
		 */
		$email_fieldset = $form->addFieldset('email_fieldset',
			array(
				'legend' => $this->__('Error notification email settings'),
			)
		);

		$email_fieldset->addField($CRON_ERROR_EMAIL,
			'text',
			array(
				'name' => $CRON_ERROR_EMAIL,
				'label' => $this->__('Error Email Recipient'),
				'title' => $this->__('Error Email Recipient'),
			)
		);

		$email_fieldset->addField($CRON_ERROR_EMAIL_IDENTITY,
			'select',
			array(
				'name' => $CRON_ERROR_EMAIL_IDENTITY,
				'label' => $this->__('Error Email Sender'),
				'title' => $this->__('Error Email Sender'),
				'class' => 'input-select',
				'values' => Mage::getModel('adminhtml/system_config_source_email_identity')->toOptionArray(),
			)
		);

		$email_fieldset->addField($CRON_ERROR_EMAIL_TEMPLATE,
			'select',
			array(
				'name' => $CRON_ERROR_EMAIL_TEMPLATE,
				'label' => $this->__('Error Email Template'),
				'title' => $this->__('Error Email Template'),
				'class' => 'input-select',
				'values' => Mage::getModel('adminhtml/system_config_source_email_template')->setPath('mpbackup_error_email_template')->toOptionArray(),
			)
		);

		$form->setValues($model->getData());

		$this->setForm($form);

		return parent::_prepareForm();
	}
}
