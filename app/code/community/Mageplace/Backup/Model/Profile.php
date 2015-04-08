<?php
/**
 * Mageplace Magesocial
 *
 * @category   Mageplace
 * @package    Mageplace_Magesocial
 * @copyright  Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license    http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Model_Profile extends Mage_Core_Model_Abstract
{
	const EXCLUDED_PATH = 'excluded_path';
	const EXCLUDED_TABLES = 'excluded_tables';

	const CRON_ENABLE = 'cron_enable';
	const CRON_BACKUP_EXPR = 'cron_backup_expr';
	const CRON_TIME_TYPE = 'cron_time_type';
	const CRON_TIME = 'cron_time';
	const CRON_FREQUENCY = 'cron_frequency';
	
	const CRON_FAILURE_RUNNING = 'cron_failure_running';
	const CRON_FAILURE_RUNNING_DEFAULT = 120;

	const CRON_SUCCESS_EMAIL = 'cron_success_email';
	const CRON_SUCCESS_EMAIL_IDENTITY = 'cron_success_email_identity';
	const CRON_SUCCESS_EMAIL_TEMPLATE = 'cron_success_email_template';
	const CRON_SUCCESS_EMAIL_LOG_LEVEL = 'cron_success_email_log_level';

	const CRON_DELETE_TYPE = 'cron_delete_type';
	const CRON_DELETE_TYPE_ROTATION = 'rotation';
	const CRON_DELETE_TYPE_DELETE_OLD = 'delete_old';
	const CRON_DELETE_TYPE_ROTATION_NUMBER = 'cron_delete_type_rotation_number';
	const CRON_DELETE_TYPE_DELETE_OLDER_THAN_X_DAYS = 'cron_delete_type_delete_older_than_x_days';
	const CRON_ROTATION_EXPR = 'cron_rotation_expr';

	const CRON_SUCCESS_DELETE_EMAIL = 'cron_success_delete_email';
	const CRON_SUCCESS_DELETE_EMAIL_IDENTITY = 'cron_success_delete_email_identity';
	const CRON_SUCCESS_DELETE_EMAIL_TEMPLATE = 'cron_success_delete_email_template';

	const CRON_ERROR_EMAIL = 'cron_error_email';
	const CRON_ERROR_EMAIL_IDENTITY = 'cron_error_email_identity';
	const CRON_ERROR_EMAIL_TEMPLATE = 'cron_error_email_template';



	/**
	 * Constructor
	 */
	protected function _construct()
	{
		parent::_construct();

		$this->_init('mpbackup/profile');
	}

	public function getName()
	{
		return $this->getProfileName();
	}

	public function getDefault()
	{
		$config_items = $this->getCollection()->addFilter('profile_default', 1)->getItems();

		return (empty($config_items) || !is_array($config_items) ? $this : array_pop($config_items));
	}

	public function getSessionProfileExcluded($sessionId=null)
	{
		if(is_null($sessionId)) {
			$sessionId = $this->getSessionId();
		}
		
		if($sessionId) {
			$excluded = Mage::helper('mpbackup')->getSession(array($sessionId))->getProfilePath();
		} else {
			$excluded = Mage::helper('mpbackup')->getSession()->getProfilePath();
		}

		if (!is_array($excluded)) {
			$excluded = array();
		}

		return $excluded;
	}

	public function getExcludedPath()
	{
		$excluded = $this->_getData(self::EXCLUDED_PATH);
		if (!is_array($excluded)) {
			$excluded = array();
		}

		return $excluded;
	}

	public function getExcludedTables()
	{
		$excluded = $this->_getData(self::EXCLUDED_TABLES);
		if (!is_array($excluded)) {
			$excluded = array();
		}

		return $excluded;
	}
}
