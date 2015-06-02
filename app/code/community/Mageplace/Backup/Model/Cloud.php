<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package     Mageplace_Backup
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Model_Cloud extends Mage_Core_Model_Abstract
{
	const XML_PARAM_AUTH = 'auth';
	const DEFAULT_CLOUD_APP = 'local';
	const LOCAL_STORAGE_APP = 'local';

	static $WRONG_APP_NAMES = array('', 'Application');

	/* @var $_helper Mageplace_Backup_Helper_Data */
	protected $_helper;

	/* @var $_backup Mageplace_Backup_Model_Backup */
	protected $_backup;

	protected $_additionalInfo = array();

	protected function _construct()
	{
		parent::_construct();

		$this->_helper = Mage::helper('mpbackup');
	}

	public function checkConnection()
	{
		return false;
	}

	public function resetAuthData()
	{
		return $this;
	}

	public function getInstance($appName)
	{
		if (!$appName || in_array($appName, self::$WRONG_APP_NAMES)) {
			$appName = self::DEFAULT_CLOUD_APP;
		}

		$signature = $appName = strtolower($appName);
		$model = Mage::registry('mpbackup_cloud_' . $signature);
		if (empty($model)) {
			$model = Mage::getModel('mpbackup/cloud_' . $appName);
			if (empty($model)) {
				Mage::throwException($this->_helper->__('Invalid model "%s"', 'mpbackup/cloud_' . $appName));
			}

			$model->setData('app_name', $appName);

			$settings = $this->_helper->getAppConfig($signature);
			$model->setData('settings', $settings);

			Mage::unregister('mpbackup_cloud_' . $signature);
			Mage::register('mpbackup_cloud_' . $signature, $model);
		}

		return $model;
	}

	public function isLocalStorage()
	{
		return $this->_getData('app_name') == self::LOCAL_STORAGE_APP;
	}

	/**
	 * Get max size of file for upload to cloud server (Mb)
	 *
	 * @return int
	 */
	public function getMaxSize()
	{
		return 0;
	}

	/**
	 * Check cloud authorize callback parameters
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @param Zend_Controller_Response_Abstract $response
	 * @return bool
	 */
	public function callback($request, $response)
	{
		return true;
	}

	/**
	 * Cron jobs for support cloud data
	 */
	public function cron()
	{
		return true;
	}

	/**
	 * Uploads a new file (Must be rewritten!)
	 *
	 * @param string $path Target path (including filename)
	 * @param string $file Either a path to a file or a stream resource
	 * @return bool
	 */
	public function putFile($path, $file)
	{
		return true;
	}

	/**
	 * Delete file (Must be rewritten!)
	 *
	 * @param string $path Target path (including filename)
	 * @return bool
	 */
	public function deleteFile($path)
	{
		return true;
	}

	public function getConfigPath()
	{
		$app_name = $this->getAppName();
		return Mageplace_Backup_Model_Config::CLOUD_PATH . ($app_name ? '/' . $app_name : '');
	}

	public function needAuthorize()
	{
		if ($this->getSettings(self::XML_PARAM_AUTH)) {
			return true;
		}

		return false;
	}

	public function getSettings($key)
	{
		$settings = $this->getData('settings');

		return (array_key_exists($key, $settings) ? $settings[$key] : null);
	}

	public function getApps()
	{
		Mage::getConfig()->loadModulesConfiguration('system.xml');
		Mage::getModel('mpbackup/cloud_application');
	}

	public function setProfile($profile)
	{
		$this->setData('profile', $profile);

		$config = Mage::getModel('mpbackup/config')->getConfigValues($profile->getId(), $this->getConfigPath());
		$this->setData('config', $config);

		return $this;
	}

	public function getConfigValue($name)
	{
		if(!$this->_getData('config_value_'.$name)) {
			if (($config = (array)$this->getData('config')) && array_key_exists($name, $config)) {
				$this->setData('config_value_'.$name, $config[$name]);
			}
		}

		return $this->_getData('config_value_'.$name);
	}

	public function saveConfigValue($name, $value = '')
	{
		$profile = $this->getData('profile');
		if (!($profile instanceof Mageplace_Backup_Model_Profile) || !$profile->getId()) {
			$profile = $this->_helper->getProfile($this->_getSession()->getProfileId());
			if (!($profile instanceof Mageplace_Backup_Model_Profile) || !$profile->getId()) {
				return false;
			}

			$profile = $this->setData('profile', $profile);
		}

		return Mage::getModel('mpbackup/config')->saveConfigValue($profile->getId(), $this->getConfigPath(), $name, $value);
	}

	protected function _addAdditionalInfo($info, $node = null)
	{
		if (!is_null($node)) {
			$this->_additionalInfo[$node] = $info;
		} else {
			$this->_additionalInfo[] = $info;
		}

		return $this;
	}

	/**
	 * @param bool $refresh
	 * @return array
	 */
	public function getAdditionalInfo($refresh = false)
	{
		$return = $this->_additionalInfo;

		if ($refresh) {
			$this->_additionalInfo = array();
		}

		return $return;
	}

	public function setBackup(Mageplace_Backup_Model_Backup $backup)
	{
		$backupAdditional = $backup->getBackupAdditional();
		if (!empty($backupAdditional) && is_array($backupAdditional)) {
			$this->_additionalInfo = array_merge($this->_additionalInfo, $backupAdditional);
		} else if (is_string($backupAdditional)) {
			$addInfo = @unserialize($backupAdditional);
			if (!empty($addInfo) && is_array($addInfo)) {
				$this->_additionalInfo = array_merge($this->_additionalInfo, $addInfo);
			}
		}


		$this->_backup = $backup;

		return $this;
	}

	public function getBackup()
	{
		return $this->_backup;
	}

	public function getCallbackUrl($secure = false, $nosecret = true)
	{
		$params = array();
		if ($secure) {
			$params['_secure'] = true;
		}
		if ($nosecret) {
			$params['_nosecret'] = true;
		}

		$callback = Mage::helper("adminhtml")->getUrl('*/*/callback', $params);
		if ($secure && strpos($callback, 'http://') === 0) {
			$callback = str_replace('http://', 'https://', $callback);
		}

		return $callback;
	}

	/**
	 * @return Mageplace_Backup_Model_Session
	 */
	protected function _getSession()
	{
		return $this->_helper->getSession();
	}

	/**
	 * @param $message
	 * @throws Mageplace_Backup_Exception | Mage_Core_Exception
	 */
	protected function _throwExeption($message)
	{
		$e = Mage::exception('Mageplace_Backup', $message);
		Mage::logException($e);
		throw $e;
	}
}
