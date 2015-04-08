<?php
/**
 * Mageplace Backup
 *
 * @category   Mageplace
 * @package    Mageplace_Backup
 * @copyright  Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license    http://www.mageplace.com/disclaimer.html
 */

/**
 * @method string getBackupErrors()
 * @method datetime getBackupFinishDate()
 * @method mixed getBackupAdditional()
 */

class Mageplace_Backup_Model_Backup extends Mage_Core_Model_Abstract
{
	const MEMORY_LIMIT = 512; /* Mb */	
	const MEMORY_LIMIT_CHECK_STEP = 10; /* Mb */

	const LOG_LEVEL_ALL = 'ALL';
	const LOG_LEVEL_INFO = 'INFO';
	const LOG_LEVEL_DEBUG = 'DEBUG';
	const LOG_LEVEL_WARNING = 'WARNING';
	const LOG_LEVEL_ERROR = 'ERROR';
	const LOG_LEVEL_OFF = 'OFF';

	const LOG_FILENAME = 'mpblog';
	const MESSAGE_TYPE_TEMPLATE = '[%s]';
	const MESSAGE_TYPE_REGEXP_TEMPLATE = '\[%s\]';
	const MESSAGE_TEMPLATE = '%1$s %2$s[%3$s] %4$s';


	/* @var $_profile Mageplace_Backup_Model_Backup */
	protected $_profile;

	/* @var $_temp Mageplace_Backup_Model_Temp */
	protected $_temp;

	protected $_profileId;
	protected $_backupId;
	protected $_logMessageFileName = '';
	protected $_logDB = true;
	protected $_enabledTempLogFile = false;

	protected $_deleteErrors = array();

	protected function _construct()
	{
		parent::_construct();

		$this->_init('mpbackup/backup');
	}

	public function initBackupData()
	{
		if(!is_null($this->_backupId)) {
			return $this;
		}
		
		$this->_backupId = $this->getId();

		if (!$this->_profile && $this->getProfileId()) {
			$this->setProfile($this->getProfileId());
		}

		$this->_temp = Mage::getModel('mpbackup/temp')->setBackup($this);

		return $this;
	}

	public function setProfile($profile)
	{
		if (!($profile instanceof Mageplace_Backup_Model_Profile)) {
			$profile = Mage::getModel('mpbackup/profile')->load(intval($profile));
			if (!$profile->getId()) {
				Mage::throwException($this->_getHelper()->__('Profile ID#%s not founded.', intval($profile)));
			}
		}

		$this->_profile = $profile;
		$this->_profileId = $profile->getId();

		$this->setProfileId($this->_profileId);

		return $this;
	}

	public function disableDBLog()
	{
		$this->_logDB = false;
	}

	public function createBackup($table, $row, $filename, $skipDb, $startPoint, $toCompress)
	{
		$this->initBackupData();

		if (!$this->_profile || !$this->_profileId) {
			throw Mage::exception('Mageplace_Backup', $this->_getHelper()->__('Select profile first'));
		}

		if (($table === 0 && $row === 0) || ($table === '' && $row === '')) {
			if (!$skipDb) {
				$this->_startBackupProcess();
			}
		}

		$profileType = $this->_profile->getData('profile_type');

		$db_files = array();
		$bu_files_loc = array();

		$error = false;
		try {
			/* @var $cloudStorage Mageplace_Backup_Model_Cloud|mixed */
			$cloudStorage = $this->_getHelper()->getCloudApplication($this->_profile);
			$app_name = $cloudStorage->getAppName();
			$backup_path = $this->_profile->getData('profile_backup_path');
			
			if (!file_exists($this->_profile->profile_backup_path) && !@mkdir($this->_profile->profile_backup_path)) {
				Mage::throwException($this->_getHelper()->__("Can't create backup directory"));
			} elseif (file_exists($this->_profile->profile_backup_path) && !is_writable($this->_profile->profile_backup_path)) {
				Mage::throwException($this->_getHelper()->__("Backup directory is not writable"));
			}

			/* Check memory limit */
			if($this->_profile->getData('profile_check_memory_limit')) {
				$this->addBackupProcessMessage($this->_getHelper()->__('Start check memory limit'), 'INFO');

				$memory_limit = ini_get('memory_limit');
				$memory_limit_error = false;
				if ($memory_limit != -1) {
					$needed_memory_limit = self::MEMORY_LIMIT;
					$needed_memory_limit_byte = self::MEMORY_LIMIT * 1024 * 1024;
					if ($this->_getHelper()->getBytes($memory_limit) < $needed_memory_limit_byte) {
						ini_set('memory_limit', $needed_memory_limit . 'M');
					}
					
					$memory_limit = ini_get('memory_limit');
					if ($this->_getHelper()->getBytes($memory_limit) < $needed_memory_limit_byte) {
						$this->addBackupProcessMessage($this->_getHelper()->__('Memory limit too low (%sb)', $memory_limit), 'WARNING');
						$memory_limit_error = true;
//						Mage::throwException($this->_getHelper()->__("Extension doesn't have permissions to change memory limit."));
					}
				} else {
					$memory_limit = self::MEMORY_LIMIT;
				}
				
				if (!$memory_limit_error) {
					try {
						$memoryLimit = $this->_getHelper()->checkMemoryLimit($this->_backupId, $memory_limit);
						if($memoryLimit !== true) {
							$this->addBackupProcessMessage($this->_getHelper()->__("The peak of memory usage, that's been allocated to backup process is %s Mb", $memoryLimit), 'INFO');
						}
					} catch(Exception $e) {
						$this->addBackupProcessMessage($e->getMessage(), 'WARNING');
					}				
				}			
				
				$this->addBackupProcessMessage($this->_getHelper()->__('Finish check memory limit'), 'INFO');
			}

			/* Check for free space */
			$check_space_value = (int)$this->_profile->getData('profile_free_disk_space');
			if ($check_space_value > 0) {
				$this->addBackupProcessMessage($this->_getHelper()->__('Start check free space'), 'INFO');

				$tmp = Mage::getBaseDir('tmp');
				$filename = $tmp . DS . uniqid('space_check_file_') . '.tmp';

				try {
					if ((disk_free_space($tmp) / 1024 / 1024) < $check_space_value) {
						Mage::throwException($this->_getHelper()->__('Total number of bytes on the corresponding filesystem or disk partition is too small'));
					}

					$megabyte = str_repeat('8 bytes ', 131072);  /* = 1Mb */

					if (file_exists($filename)) {
						@unlink($filename);
					}

					$fh = fopen($filename, 'a');
					for ($i = 1; $i <= $check_space_value; $i++) {
						$write = fwrite($fh, $megabyte);
						if (!$write) {
							@fclose($fh);							
							@unlink($filename);
							Mage::throwException($this->_getHelper()->__('Insufficient free space. Total free space %s Mb.', $i));
						}
					}
					fclose($fh);
					
					$isDeleted = @unlink($filename);
					if(!$isDeleted) {
						$this->addBackupProcessMessage($this->_getHelper()->__('File "%s" has not been deleted. Please, delete it manually.', $filename), 'WARNING');
					}

					$this->addBackupProcessMessage($this->_getHelper()->__('Finish check free space'), 'INFO');

				} catch (Exception $e) {
					if ($fh) {
						fclose($fh);
					}

					if ($filename && file_exists($filename)) {
						@unlink($filename);
					}

					throw $e;
				}
			}

			////start db////
			if ($profileType != 'files') {

				if (($table === 0 && $row === 0) || ($table === '' && $row === '')) {
					if (!$skipDb) {
						$this->addBackupProcessMessage($this->_getHelper()->__('Start DB tables backup'), 'INFO');
					}
				}

				if ($table === '' && $row === '') {
					/* @var $backup_file_db Mageplace_Backup_Model_File */
					$backup_file_db = Mage::getModel('mpbackup/file')
						->setBackup($this)
						->setFilename($this->getBackupFilename())
						->setPath($backup_path)
						->setType('db');

					$this->addMainBackupFiles($backup_file_db->getFileLocation());

					/* @var $db_model Mageplace_Backup_Model_Db */
					$db_model = Mage::getModel('mpbackup/db')
						->setExcludedTables($this->_profile->getExcludedTables())
						->startBackup($backup_file_db);
				} else {
					/* @var $backup_file_db Mageplace_Backup_Model_Multidb */
					$backup_file_db = Mage::getModel('mpbackup/multidb')
						->setType('db')
						->setFilename($filename)
						->setPath($backup_path);
					if (!$skipDb) {
						/* @var $db_model Mageplace_Backup_Model_Db */
						$db_model = Mage::getModel('mpbackup/db')
							->setExcludedTables($this->_profile->getExcludedTables());
						$result = $db_model->startBackup($backup_file_db, true, $table, $row, $this->_profileId);
						if (is_array($result)) {
							return $result;
						}
					}
				}

				$this->addFilesForDelete($backup_file_db->getFileLocation());

				$this->addBackupFiles($backup_file_db->getFileName());

				$db_files[] = $backup_file_db->getFileLocation();

				if (!$skipDb) {
					if (!$cloudStorage->isLocalStorage()) {

						$files_arr_db = $backup_file_db->prepareFileToUpload($cloudStorage->getMaxSize());
						//First foreach - get files for delete and if in second foreach break with erros - then programm delete not unliked files 
						if (intval($backup_file_db->getFileParts()) > 0) {
							foreach ($files_arr_db as $dbfileinfo) {
								if (empty($dbfileinfo['filename']) || empty($dbfileinfo['filelocation'])) {
									continue;
								}

								$this->addFilesForDelete($dbfileinfo['filelocation']);
							}
						}

						foreach ($files_arr_db as $dbfileinfo) {
							$this->addBackupProcessMessage($this->_getHelper()->__('Start "%s" DB backup file upload to cloud server', $dbfileinfo['filename']), 'INFO');
							$cloud_file = $cloudStorage->putFile($dbfileinfo['filename'], $dbfileinfo['filelocation']);
							if ($cloud_file) {
								$addInfo = $cloudStorage->getAdditionalInfo(true);
								$this->addCloudFiles(
									(is_string($cloud_file) ? $cloud_file : null),
									(is_array($addInfo) && !empty($addInfo) ? $addInfo : null)
								);

								$this->addBackupProcessMessage($this->_getHelper()->__('Finish "%s" DB backup file upload to cloud server', $dbfileinfo['filename']), 'INFO');

								if (intval($backup_file_db->getFileParts()) > 0) {
									$this->addBackupProcessMessage($this->_getHelper()->__('Deleting "%s" file from the server', $dbfileinfo['filename']), 'INFO');
									if (!@unlink($dbfileinfo['filelocation'])) {
										$this->addBackupProcessMessage($this->_getHelper()->__('File is not deleted', $dbfileinfo['filename']), 'WARNING');
									}
								}
							} else {
								Mage::throwException($this->_getHelper()->__('"%s" DB backup file is not uploaded to cloud server', $dbfileinfo['filename']));
							}

							$db_files[] = $dbfileinfo['filelocation'];
						}
						unset($cloud_file);
					}

					$this->addBackupProcessMessage($this->_getHelper()->__('Finish DB tables backup'), 'INFO');
				}
			}
			////end db////

			if (isset($backup_file_db)) {
				$backup_filename = $backup_file_db->getMainFileName();
			} else {
				$backup_filename = Mage::app()->getLocale()->storeTimeStamp();
			}

			////start file///
			if ($profileType != 'db') {
				if (!$skipDb) {
					$this->addBackupProcessMessage($this->_getHelper()->__('Start directories and files backup'), 'INFO');
				}

				/* @var $backup_file_files Mageplace_Backup_Model_File */
				$backup_file_files = Mage::getModel('mpbackup/file')
					->setBackup($this)
					->setFilename($backup_filename)
					->setPath($backup_path)
					->setType('files')
					->setProfile($this->_profile)
					->addExcludedPath($db_files)
					->startBackup($startPoint, $this->_profileId, $toCompress);

				if (is_array($backup_file_files)) {
					return $backup_file_files;
				}

				$this->addFilesForDelete($backup_file_files->getFileLocation());

				$this->addBackupFiles($backup_file_files->getFileName());

				if (!$cloudStorage->isLocalStorage()) {
					$files_arr = $backup_file_files->prepareFileToUpload($cloudStorage->getMaxSize());

					foreach ($files_arr as $fileinfo) {
						if (empty($fileinfo['filename']) || empty($fileinfo['filelocation'])) {
							continue;
						}

						$this->addBackupProcessMessage($this->_getHelper()->__('Start "%s" file upload to cloud server', $fileinfo['filename']), 'INFO');
						$cloud_file = $cloudStorage->putFile($fileinfo['filename'], $fileinfo['filelocation']);
						if ($cloud_file) {
							$addInfo = $cloudStorage->getAdditionalInfo(true);
							$this->addCloudFiles(
								(is_string($cloud_file) ? $cloud_file : null),
								(is_array($addInfo) && !empty($addInfo) ? $addInfo : null)
							);

							$this->addBackupProcessMessage($this->_getHelper()->__('Finish "%s" file upload to cloud server', $fileinfo['filename']), 'INFO');

							if (intval($backup_file_files->getFileParts()) > 0) {
								$this->addBackupProcessMessage($this->_getHelper()->__('Deleting "%s" file from the server', $fileinfo['filename']), 'INFO');
								if (!@unlink($fileinfo['filelocation'])) {
									$this->addBackupProcessMessage($this->_getHelper()->__('File is not deleted', $fileinfo['filename']), 'WARNING');
								}
							}
						} else {
							Mage::throwException($this->_getHelper()->__('"%s" file is not uploaded to cloud server', $fileinfo['filename']));
						}
					}
				}

				$this->addBackupProcessMessage($this->_getHelper()->__('Finish directories and files backup'), 'INFO');
			}

			/////end files////

		} catch (Exception $e) {
			$error = true;
			Mage::logException($e);
			$this->addBackupProcessMessage($e->getMessage(), true);
			$this->setBackupErrors($e->getMessage());
		}

		$this->finishBackupProcess($error);

		if (isset($e)) {
			return $e;
		} else {
			$this->rotationDelete();
			return $this;
		}
	}

	public function finishBackupProcess($errorCheck = false)
	{
		$this->initBackupData();

		if (is_string($errorCheck)) {
			$errorCheck = strip_tags(nl2br($errorCheck));

			$this->addBackupProcessMessage($errorCheck, true);
			$this->setBackupErrors($errorCheck);
		}

		if (!($errorCheck && !$this->_profile->getData('profile_backup_error_delete_local'))) {
			$bu_files_loc = $this->_getMainBackupFiles(true);
			if (!$this->_profile->getProfileLocalCopy() && !empty($bu_files_loc)) {
				foreach ($bu_files_loc as $delfile) {
					if (file_exists($delfile)) {
						$this->addBackupProcessMessage($this->_getHelper()->__('Deleting "%s" file from the server', basename($delfile)), 'INFO');
						if (!@unlink($delfile)) {
							$this->addBackupProcessMessage($this->_getHelper()->__('File is not deleted', basename($delfile)), 'WARNING');
						}
					}
				}
			}
		}

		if ($errorCheck) {
			$this->deleteUnnecessaryFiles();
		}

		if ($this->_profile->getProfileLocalCopy() || ($errorCheck && !$this->_profile->getData('profile_backup_error_delete_local'))) {
			if ($backupFiles = $this->getBackupFiles()) {
				if (is_string($backupFiles)) {
					$backupFiles = explode(';', $backupFiles);
				} else if (!is_array($backupFiles)) {
					$backupFiles = array();
				}
			} else {
				$backupFiles = array();
			}

			$bu_files = $this->_getBackupFiles();
			if (!empty($bu_files) && is_array($bu_files)) {
				$backupFiles = array_merge($backupFiles, $bu_files);
			}

			if ($errorCheck && !$this->_profile->getData('profile_backup_error_delete_local')) {
				$mergeFiles = array();
				$filesForDelete = $this->_getFilesForDelete();
				if (!empty($filesForDelete) && is_array($filesForDelete)) {
					$filesForDelete = array_unique($filesForDelete);
					foreach ($filesForDelete as $fileForDelete) {
						if (file_exists($fileForDelete)) {
							$mergeFiles[] = basename($fileForDelete);
						}
					}
				}
				$backupFiles = array_merge($backupFiles, $mergeFiles);
			}

			$this->setBackupFiles(implode(';', $backupFiles));
		}


		$cloud = $this->_getCloudFiles(true);
		if (is_array($cloud)) {
			$cloudFiles = !empty($cloud['files']) && is_array($cloud['files']) ? $cloud['files'] : array();
			$cloudInfo = !empty($cloud['infos']) && is_array($cloud['infos']) ? $cloud['infos'] : array();

			$this->setBackupCloudFiles(implode(';', $cloudFiles));
			$this->setBackupAdditional(serialize($cloudInfo));
		}

		$logLevel = $this->getLogLevel();
		if ($logLevel != self::LOG_LEVEL_OFF) {
			$logFile = $this->getLogMessageFileName();
			$this->setBackupLogFile($logFile ? basename($logFile) : '');
		}

		$this->setBackupStarted(0);
		$this->setBackupFinished(1);
		$this->setBackupFinishDate(Mage::getSingleton('core/date')->gmtDate());

		Mage::getModel('mpbackup/interceptor')->checkMethodCall($this, 'save');

		if ($errorCheck) {
			$this->addBackupProcessMessage($this->_getHelper()->__('Backup has been saved with errors'), 'WARNING');
		} else {
			$this->addBackupProcessMessage($this->_getHelper()->__('Backup was successfully saved'), 'INFO');
		}

		$this->addBackupProcessMessage($this->_getHelper()->__('Backup process finished'), 'INFO');

		$this->_getSession()->unsLogMessageFileName();

		$this->_temp->clearMessages();

		$this->_getHelper()->resetBackupProcessMessage();

		$this->_clearSession();
	}

	/**
	 * Add backup process message
	 *
	 * @param string $message
	 * @param bool|string $error Boolean or ERROR|WARNING|INFO|DEBUG
	 * @return Varien_Data_Form_Element_Abstract
	 */
	public function addBackupProcessMessage($message, $error = false, $id = null)
	{
		$logLevel = $this->getLogLevel();
		if ($logLevel == self::LOG_LEVEL_OFF) {
			return $this;
		}

		$message_type = $this->getMessageType($error);
		if ($logLevel != self::LOG_LEVEL_ALL) {
			if (($logLevel == self::LOG_LEVEL_WARNING) && (($message_type != self::LOG_LEVEL_ERROR) || ($message_type != self::LOG_LEVEL_WARNING))) {
				return $this;
			} else if (($logLevel == self::LOG_LEVEL_INFO) && ($message_type == self::LOG_LEVEL_DEBUG)) {
				return $this;
			}
		}

		if (!$id) {
			$id = $this->getId();
		}

		$date = Mage::app()->getLocale()->storeDate(null, null, true);
		$content = $this->getBackupProcessMessage(strip_tags($message), $date, $error) . "\r\n";
		if (!($logFile = $this->getLogMessageFileName()) || !file_put_contents($logFile, $content, FILE_APPEND | LOCK_EX)) {
			Mage::logException(new Exception('Message "' . $content . '" is not writed to log file'));
		}

		return $this;
	}

	public function getLogLevel()
	{
		static $logLevel = null;

		if (is_null($logLevel)) {
			if ($this->_profile && $this->_profile->getProfileLogLevel()) {
				$logLevel = $this->_profile->getProfileLogLevel();
			} else {
				$logLevel = self::LOG_LEVEL_ALL;
			}
		}

		return $logLevel;
	}

	/**
	 * Return: ERROR | WARNING | INFO | DEBUG
	 *
	 * @return string
	 */
	public function getMessageType($error = false)
	{
		return strtoupper($error ? (is_bool($error) ? self::LOG_LEVEL_ERROR : $error) : self::LOG_LEVEL_DEBUG);
	}

	public function getBackupProcessMessage($message, $date, $error = false)
	{
		return sprintf(self::MESSAGE_TEMPLATE,
			$date,
			sprintf(self::MESSAGE_TYPE_TEMPLATE, $this->_getHelper()->__($this->getMessageType($error))),
			@memory_get_peak_usage(1) / (1024 * 1024) . 'Mb',
			$message);
	}

	public function getLogMessagesFromFile($backup_id, $start_id)
	{

	}

	protected function _startBackupProcess()
	{
		@ini_set('max_execution_time', '10800');
		@set_time_limit(10800);

		if ($this->_getResource()->trySetStatusAtomic($this->_backupId, 'backup_started', 1)) {
			$this->setBackupStarted(1);
		}

		$this->addBackupProcessMessage($this->_getHelper()->__('Backup process started'), 'INFO');
	}

	public function getLogMessageFileName($session = true)
	{
		if ($session && !$this->_logMessageFileName) {
			$this->_logMessageFileName = $this->_getSession()->getLogMessageFileName();
		}

		if (!$this->_logMessageFileName) {
			$this->initBackupData();

			if ($this->_profile && $this->_profileId) {
				$logDir = $this->_profile->getData('profile_log_path');

				$this->_logMessageFileName = $logDir . DS . $this->getBackupLogFile();
				if (!file_exists($this->_logMessageFileName)) {
					if (!file_put_contents($this->_logMessageFileName, '', FILE_APPEND | LOCK_EX)) {
						Mage::logException(new Exception('Log file is not created'));
					}
				}
				$this->_getSession()->setLogMessageFileName($this->_logMessageFileName);
			}
		}

		return $this->_logMessageFileName;
	}
	
	public function getBackupLogFile()
	{
		$backup_log_file = $this->_getData('backup_log_file');
		if(!is_null($backup_log_file)) {
			return $backup_log_file;
		}
		
		$this->initBackupData();
		$backup_log_file = $this->getBackupLogFileTemplate($this->_profileId, $this->_backupId);
		$this->setData('backup_log_file', $backup_log_file);
		
		return $backup_log_file;
	}
	
	public function getBackupLogFileTemplate($profileId, $backupId)
	{
		return self::LOG_FILENAME . '-' . $profileId . '-' . $backupId . ' [' . date('Y-m-d H-i-s') . '].log';
	}
	
	
	public function setBackupLogFileTemplate($profileId, $backupId)
	{
		$logFileName = $this->getBackupLogFileTemplate($profileId, $backupId);
		$this->setData('backup_log_file', $logFileName);
		$this->save();
		
		return $this;
	}
	

	public function getLogFilePath()
	{
		if (!$this->isFinished()) {
			return $this->getLogMessageFileName();
		}

		if (!$logFile = $this->_getData('backup_log_file')) {
			return null;
		}

		$logDir = $this->_profile->getData('profile_log_path');

		return $logDir . DS . $logFile;
	}

	public function isFinished()
	{
		$finished = $this->getBackupFinishDate();

		return !empty($finished) && ($finished != '0000-00-00 00:00:00');
	}

	public function isSuccessFinished()
	{
		$errors = $this->getBackupErrors();

		return $this->isFinished() && !$this->getBackupErrors();
	}


	public function deleteRecordAndFiles($session = true)
	{
		$this->initBackupData();

		if (!$this->_profile) {
			throw Mage::exception('Mageplace_Backup', $this->_getHelper()->__('Select profile first'));
		}

		$errors = false;

		/* @var $cloudStorage Mageplace_Backup_Model_Cloud|mixed */
		$cloudStorage = $this->_getHelper()->getCloudApplication($this->_profile);
		if (is_object($cloudStorage) && !$cloudStorage->isLocalStorage()
			&& ($strCloudFiles = $this->getBackupCloudFiles())
			&& ($cloudFiles = explode(';', $strCloudFiles))
		) {
			$cloudStorage->setBackup($this);

			foreach ($cloudFiles as $file) {
				if (!$file) {
					continue;
				}
				try {
					$delete = $cloudStorage->deleteFile($file);
				} catch (Exception $e) {
					if ($session) {
						$this->_getAdminSession()->addError($e->getMessage());
					}
					$this->addDeleteError($e->getMessage());
					$delete = false;
				}

				if (!$delete) {
					$message = $this->_getHelper()->__('File "%s" does not deleted from the cloud server', $file);
					if ($session) {
						$this->_getAdminSession()->addWarning($message);
					}
					$this->addDeleteError($message);
					$errors = true;
				}
			}
		}

		if (($strBackupFiles = $this->getBackupFiles())
			&& ($backupFiles = explode(';', $strBackupFiles))
		) {
			$backupDir = $this->_profile->getData('profile_backup_path');

			foreach ($backupFiles as $file) {
				if (!$file) {
					continue;
				}

				$deleted = false;
				$backupPath = $backupDir . DS . $file;
				if (file_exists($backupPath)) {
					$deleted = @unlink($backupPath);
				} else {
					$message = $this->_getHelper()->__('File "%s" does not exist on the local server', $backupPath);
					if ($session) {
						$this->_getAdminSession()->addWarning($message);
					}
					$this->addDeleteError($message);
					$errors = true;
					continue;
				}

				if (!$deleted) {
					$message = $this->_getHelper()->__('File "%s" does not deleted from the local server', $backupPath);
					if ($session) {
						$this->_getAdminSession()->addWarning($message);
					}
					$this->addDeleteError($message);
					$errors = true;
				}
			}
		}

		if ($logFile = $this->getBackupLogFile()) {
			$logPath = $this->_profile->getData('profile_log_path') . DS . $logFile;
			if (file_exists($logPath)) {
				if (!@unlink($logPath)) {
					$message = $this->_getHelper()->__('File "%s" does not deleted from the local server', $logPath);
					if ($session) {
						$this->_getAdminSession()->addWarning($message);
					}
					$this->addDeleteError($message);
					$errors = true;
				}
			} else {
				$message = $this->_getHelper()->__('File "%s" does not exist on the local server', $logPath);
				if ($session) {
					$this->_getAdminSession()->addWarning($message);
				}
				$this->addDeleteError($message);
				$errors = true;
			}
		}

		$this->delete();

		return !$errors;
	}

	public function rotationDelete()
	{
		$this->initBackupData();

		try {
			if (!$this->_profile) {
				throw Mage::exception('Mageplace_Backup', $this->_getHelper()->__('Select profile first'));
			}

			if ($this->_profile->getData(Mageplace_Backup_Model_Profile::CRON_DELETE_TYPE) != Mageplace_Backup_Model_Profile::CRON_DELETE_TYPE_ROTATION) {
				return;
			}

			$number = $this->_profile->getData(Mageplace_Backup_Model_Profile::CRON_DELETE_TYPE_ROTATION_NUMBER);
			if ($number < 1) {
				return;
			}

			/* @var $collection Mageplace_Backup_Model_Mysql4_Backup_Collection */
			$collection = $this->getCollection()
				->addFilter('profile_id', $this->_profileId)
				->addOrder('backup_finish_date', Mageplace_Backup_Model_Mysql4_Backup_Collection::SORT_ORDER_ASC);

			$total = $collection->count();
			if ($total <= $number) {
				return;
			}

			$stat = array(
				Mageplace_Backup_Model_Cron::DELETE_STAT_DELETED => 0,
				Mageplace_Backup_Model_Cron::DELETE_STAT_BACKUPS => array(),
				Mageplace_Backup_Model_Cron::DELETE_STAT_ERRORS => array(),
			);

			/* @var $backup Mageplace_Backup_Model_Backup */
			foreach ($collection->getIterator() as $backup) {
				if ($total <= $number) {
					break;
				}

				$name = $backup->getBackupName();
				if ($backup->deleteRecordAndFiles(false)) {
					$stat[Mageplace_Backup_Model_Cron::DELETE_STAT_DELETED]++;
					$stat[Mageplace_Backup_Model_Cron::DELETE_STAT_BACKUPS][] = $name;

					$total--;
				}

				$stat[Mageplace_Backup_Model_Cron::DELETE_STAT_ERRORS] = array_merge($stat[Mageplace_Backup_Model_Cron::DELETE_STAT_ERRORS], $backup->getDeleteErrors());
			}

			Mage::getModel('mpbackup/cron')->sendSuccessDeleteEmail($this->_profile, $stat);
		} catch (Exception $e) {
			Mage::logException($e);
			if (isset($this->_profile)) {
				Mage::getModel('mpbackup/cron')->sendErrorsEmail($this->_profile);
			}
		}
	}

	public function addDeleteError($error)
	{
		$this->_deleteErrors[] = $error;
	}

	public function getDeleteErrors()
	{
		return $this->_deleteErrors;
	}

	public function  getCurrentBackup()
	{
		$id = $this->_getResource()->getCurrentBackupId();
		if ($id) {
			$this->load($id);
		}

		return $this;
	}

	public function getLogs($logLevel = null)
	{
		$logs = array();

		if (is_null($logLevel) || $logLevel == self::LOG_LEVEL_OFF) {
			return $logs;
		}

		$file = $this->getLogFilePath();
		if (!file_exists($file)) {
			return $logs;
		}

		$logsArr = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		if ($logLevel == self::LOG_LEVEL_ALL || $logLevel == '') {
			return $logsArr;
		}

		$logs = preg_grep('/^(.*)(' . sprintf(self::MESSAGE_TYPE_REGEXP_TEMPLATE, self::LOG_LEVEL_DEBUG) . ')(.*)$/', $logsArr, PREG_GREP_INVERT);

		return $logs;
	}

	/**
	 * @return Mageplace_Backup_Helper_Data
	 */
	protected function _getHelper()
	{
		static $helper = null;

		if (is_null($helper)) {
			$helper = Mage::helper('mpbackup');
		}

		return $helper;
	}

	public function addMainBackupFiles($file)
	{
		return $this->_temp->addMainBackupFile($file);
	}

	protected function _getMainBackupFiles()
	{
		return $this->_temp->getMainBackupFile();
	}

	public function addBackupFiles($file)
	{
		return $this->_temp->addBackupFileNames($file);
	}

	protected function _getBackupFiles()
	{
		return $this->_temp->getBackupFileNames();
	}

	public function addCloudFiles($file, $info)
	{
		$files = $this->_temp->addCloudFiles($file, $info);
	}

	protected function _getCloudFiles()
	{
		return $this->_temp->getCloudFiles();
	}

	public function addFilesForDelete($file)
	{
		return $this->_temp->addFilesForDelete($file);
	}

	public function _getFilesForDelete()
	{
		return $this->_temp->getFilesForDelete();
	}

	public function deleteUnnecessaryFiles()
	{
		if ($this->_profile->getData('profile_backup_error_delete_local')) {
			$files = $this->_getFilesForDelete();
			if (!empty($files) && is_array($files)) {
				$files = array_unique($files);
				foreach ($files as $file) {
					if (file_exists($file)) {
						$this->addBackupProcessMessage($this->_getHelper()->__('Deleting unnecessary file "%s" from the server', basename($file)), 'INFO');
						if (!@unlink($file)) {
							$this->addBackupProcessMessage($this->_getHelper()->__('File is not deleted', basename($file)), 'WARNING');
						}
					}
				}
			}
		}

		if ($this->_profile->getData('profile_backup_error_delete_cloud')) {
			$cloud = $this->_getCloudFiles();
			if (is_array($cloud)) {
				$cloudFiles = !empty($cloud['files']) && is_array($cloud['files']) ? $cloud['files'] : array();
				$cloudInfo = !empty($cloud['infos']) && is_array($cloud['infos']) ? $cloud['infos'] : array();

				$cloudStorage = $this->_getHelper()->getCloudApplication($this->_profile);
				if (is_object($cloudStorage) && !$cloudStorage->isLocalStorage() && is_array($cloudFiles)) {
					$backupAdditional = $this->getBackupAdditional();
					$this->setBackupAdditional($cloudInfo);
					$cloudStorage->setBackup($this);
					$this->setBackupAdditional($backupAdditional);

					foreach ($cloudFiles as $file) {
						try {
							$this->addBackupProcessMessage($this->_getHelper()->__('Deleting unnecessary cloud file "%s"', $file), 'INFO');
							$delete = $cloudStorage->deleteFile($file);
						} catch (Exception $e) {
							$this->addBackupProcessMessage($e->getMessage(), 'WARNING');
							$delete = false;
						}

						if (!$delete) {
							$this->addBackupProcessMessage($this->_getHelper()->__('File "%s" does not deleted from the cloud server', $file), 'WARNING');
						}
					}
				}
			}
		}
	}

	/**
	 * @return Mage_Adminhtml_Model_Session
	 */
	public function _getSession($clear = false)
	{
		if ($this->_profileId && $this->_backupId) {
			$session = Mage::registry('mpbackup_' . $this->_profileId . '_' . $this->_backupId);
			if (is_null($session)) {
				$session = $this->_getHelper()->getSession(array($this->_profileId, $this->_backupId));
				
				Mage::register('mpbackup_' . $this->_profileId . '_' . $this->_backupId, $session);
			}

		} else {
			$session = $this->_getHelper()->getSession();
		}

		if ($clear) {
			$session->clear();
			return;
		}

		return $session;
	}

	protected function _clearSession()
	{
		$this->_getSession(true);
	}

	/**
	 * @return Mage_Adminhtml_Model_Session
	 */
	protected function _getAdminSession()
	{
		return Mage::getSingleton('adminhtml/session');
	}
}
