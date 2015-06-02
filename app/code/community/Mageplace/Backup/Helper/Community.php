<?php
/**
 * Mageplace Backup
 *
 * @category	Mageplace
 * @package	 Mageplace_Backup
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license	 http://www.mageplace.com/disclaimer.html
 */
 
class Mageplace_Backup_Helper_Community extends Mage_Core_Helper_Abstract
{
	const DEFAULT_PATH	= 'default';
	const LOCALPATH		= 'localpath';
	const APP_PREFIX	= 'app_';
	const APP_KEY		= 'app_key';
	const APP_SECRET	= 'app_secret';
	
	const BACKUP_DIR	= 'mpbackups';
	
	const SESSION_ID		= 'mpbackup';
	const SESSION_SECTION	= 'mpbackup';
	const SESSION_MESSAGES	= 'messages';
	
	const SESSION_MODEL_CLASS = 'mpbackup/session';

	protected static $_backupModel = null;
   
	public static function getSessionSingleton(array $arguments=array())
	{
		$registryKey = '_singleton/' . self::SESSION_MODEL_CLASS . md5(serialize($arguments));
		if (!Mage::registry($registryKey)) {
			Mage::register($registryKey, Mage::getModel(self::SESSION_MODEL_CLASS, $arguments));
		}
		
		return Mage::registry($registryKey);
	}

	public function getApplicationXmlPath()
	{
		return Mage::getConfig()->getModuleDir('etc', $this->_getModuleName()).DS.Mageplace_Backup_Model_Cloud_Application::APP_PATH_NAME;
	}
	
	public function getCfg($name, $path=null, $profile_id=null)
	{
		if(is_null($path)) {
			$path = self::DEFAULT_PATH;
		}
		
		if(is_null($profile_id)) {
			$profile_id = $this->getDefaultProfileId();
		}
		
		$value = Mage::getModel('mpbackup/config')->getConfigValues($profile_id, $path, $name);
		
		return $value;
	}
	
	/**
	 * OLD VERSION
	 * @return Mageplace_Backup_Model_Session
	 */
	/*public function getSession($profile_id=null, $init=false)
	{
		$session = Mage::getSingleton('mpbackup/session');
		if($init) {
			if(!$profile_id) {
				$profile_id = $this->getDefaultProfileId();
			}
			$session->setProfileId($profile_id);
		}

		return $session;
	}*/
	
	public function getSession($params=null, $init=false)
	{
		if(!is_array($params)) {
			$profile_id = $params;
			$params = array();
		} else {
			$init = false;
		}
		
		$session = self::getSessionSingleton($params);
		
		if($init) {
			if(!$profile_id) {
				$profile_id = $this->getDefaultProfileId();
			}
			$session->setProfileId($profile_id);
		}

		return $session;
	}
	
	/**
	 * Enter description here ...
	 * 
	 * @return Mageplace_Backup_Model_Profile
	 */
	public function getDefaultProfile()
	{
		return Mage::getModel('mpbackup/profile')->getDefault();
	}
	
	/**
	 * Enter description here ...
	 * 
	 * @return int
	 */
	public function getDefaultProfileId()
	{
		static $id;
		
		if(is_null($id)) {
			$profile = $this->getDefaultProfile();
			if(!($profile instanceof Mageplace_Backup_Model_Profile) || !($id = $profile->getId())) {
				$id = 0;
			}
		}
		
		return $id;
	}
	
	/**
	 * Enter description here ...
	 * 
	 * @return Mageplace_Backup_Model_Profile
	 */
	public function getProfile($profile_id=null)
	{
		if(!$profile_id) {
			$profile = $this->getDefaultProfile();
		} else {
			$profile = Mage::getModel('mpbackup/profile')->load($profile_id);
		}
		
		return $profile;
	}
	
	public function getCloudApplication($profile)
	{
		if(is_int($profile)) {
			$profile = $this->getProfile($profile);
		}
		
		if(!($profile instanceof Mageplace_Backup_Model_Profile) || !$profile->getId())	{
			return null;
		}
		
		$profile_cloud_app = $profile->getData('profile_cloud_app');
		$cloud_storage = Mage::getModel('mpbackup/cloud')->getInstance($profile_cloud_app);
		$cloud_storage->setProfile($profile);
		
		return $cloud_storage;
	}
	
	public function getSessionKey()
	{
		return self::CONFIG_PATH;
	}
		
	public function getApps()
	{
		return Mage::getModel('mpbackup/cloud_application')->getAppsArray();
	}
	
	public function getAppConfig($app_name)
	{
		if(strtolower($app_name) == Mageplace_Backup_Model_Cloud::DEFAULT_CLOUD_APP) {
			return array();
		}
		
		$apps = $this->getApps();
		
		return (array_key_exists($app_name, $apps) ? $apps[$app_name] : null);
	}
	
	public function getAppsOptionArray()
	{
		static $options = array();
		
		if(empty($options)) {
			$options[] = array(
				'value' => '',
				'label' => $this->__('Local Storage')
			);
			
			foreach($this->getApps() as $app) {
				$options[] = array(
					'value' => $app['name'],
					'label' => $app['label']
				);
			}
		}
		
		return $options;
	}
	
	public function getAppsArray()
	{
		static $options = array();
		
		if(empty($options)) {
			$options[''] = $this->__('Local Storage');

			foreach($this->getApps() as $app) {
				$options[$app['name']] = $app['label'];
			}
		}
		
		return $options;
	}
	
	public function getLocalBackupLocation() {
		
		if (Mage::getStoreConfig('system/dropboxbackup/localdir')) {
			return Mage::getStoreConfig('system/dropboxbackup/localdir');
		} else {
			return Mage::getBaseDir() . DS . "var" . DS . "backups";
		}
		
	}
	
	public function addBackupProcessMessage($message, $error=false)
	{
		if(is_null(self::$_backupModel)) {
			if($backup_id = Mage::getSingleton('adminhtml/session')->getBackupId()) {
				self::$_backupModel = Mage::getModel('mpbackup/backup')
					->load($backup_id)
					->initBackupData();	
			}
		}
		
		if(self::$_backupModel instanceof Mageplace_Backup_Model_Backup) {
			self::$_backupModel->addBackupProcessMessage($message, $error);
		}
	}

	public function resetBackupProcessMessage()
	{
		self::$_backupModel = null;
	}

	public function getBytes($val)
	{
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		switch($last) {
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	}
	
	public function copyDirectory($source, $destination)
	{
		if (is_dir( $source )) {
			@mkdir($destination);
			$directory = dir($source);
			
			while (FALSE !== ($readdirectory = $directory->read())) {
				if ($readdirectory == '.' || $readdirectory == '..') {
					continue;
				}
				
				$PathDir = $source . DS . $readdirectory; 
				if (is_dir($PathDir)) {
					$this->copyDirectory($PathDir, $destination . DS . $readdirectory);
					continue;
				}
				
				@copy($PathDir, $destination . DS . $readdirectory);
			}
	 
			$directory->close();
		} else {
			@copy($source, $destination);
		}
	}
	
	public function deleteDirectory($dir)
	{
		if (!file_exists($dir)) {
			return true;
		}
		
		if (!is_dir($dir) || is_link($dir)) {
			return @unlink($dir);
		}
		
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') {
				continue;
			}
			
			if (!$this->deleteDirectory($dir . "/" . $item)) {
				@chmod($dir . "/" . $item, 0777);
				if (!$this->deleteDirectory($dir . "/" . $item)) {
					return false;
				}
			};
		}
		
		return @rmdir($dir);
	}
	
	public function checkMemoryLimit($backupId, $bytes=null)
	{
		if(is_null($bytes)) {
			$mBytes = Mageplace_Backup_Model_Backup::MEMORY_LIMIT;
		} else {
			$mBytes = intval($bytes);
		}
		
		if(!$mBytes) {
			Mage::throwException($this->__("Wrong input data"));
		}
		
		$client = new Varien_Http_Client();
		$client->setUri(Mage::getUrl('mpbackup/backup/checkMemoryLimit'))
			->setConfig(array('timeout' => 30))
			->setHeaders('accept-encoding', '')
			->setParameterGet(array('backup_id' => $backupId, 'mbytes' => $mBytes))
			->setMethod(Zend_Http_Client::GET);
        $response = $client->request();
		
		$body = $response->getRawBody();
		
		$steps = explode('|', $body);
		if(count($steps)) {
			$last = array_pop($steps);
			if($last === 'OK') {
				return true;
			} else if($last) {
				$error = trim($last);
				if(stripos($error, 'error') !== false) {
					//Mage::throwException($error);
				}
			}
			
			$last = array_pop($steps);			
			if(!$last) {
				Mage::throwException($this->__("Extension can't get memory limit"));
			}
			
			@list($iterator, $mpu) = explode('-', $last);
			
			if($mpu) {
				$memoryLimit = number_format(($mpu/1024/1024), 2, '.', '');
			} else {
				$memoryLimit = (int)$iterator;
			}
			
			if($memoryLimit < 400) {
				Mage::throwException($this->__("Memory limit too low (%s Mb)", $memoryLimit));
			} else if($memoryLimit < 500) {
				Mage::throwException($this->__("The peak of memory usage, that's been allocated to backup process is %s Mb", $memoryLimit));
			}
				
			return $memoryLimit;
			
		} else {
			Mage::throwException($this->__("Extension can't get memory limit"));
		}
	}
}