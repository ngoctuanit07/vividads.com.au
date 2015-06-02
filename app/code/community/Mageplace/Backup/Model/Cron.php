<?php
/**
 * Mageplace Backup
 *
 * @category   Mageplace
 * @package    Mageplace_Backup
 * @copyright  Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license    http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Model_Cron extends Mage_Core_Model_Abstract
{
	const REGISTER_KEY_CHECK_RUN = 'mpbackup_cron_run';

	const CACHE_KEY_LAST_SCHEDULE_GENERATE_AT = 'mpbackup_cron_last_schedule_generate_at';
	const CACHE_KEY_LAST_HISTORY_CLEANUP_AT = 'mpbackup_cron_last_history_cleanup_at';

	const XML_CRON_EXPR_DELETE = 'mpbackup/cron/expr_delete';
	const XML_CRON_EXPR_CHECK_RUNNING = 'mpbackup/cron/check_running';

	const JOB_CODE_BACKUP = 'backup';
	const JOB_CODE_DELETE = 'delete';
	const JOB_CODE_CHECK_RUNNING = 'check_running';

	const DELETE_STAT_DELETED = 'deleted';
	const DELETE_STAT_ERRORS = 'errors';
	const DELETE_STAT_BACKUPS = 'backups';

	protected static $JOBS = array(
		self::JOB_CODE_BACKUP,
		self::JOB_CODE_DELETE,
		self::JOB_CODE_CHECK_RUNNING,
	);
	

	/**
	 * Error messages
	 *
	 * @var array
	 */
	protected $_errors = array();
	protected $_pendingSchedules;

	/**
	 * Send Backup Errors
	 *
	 * @return Mageplace_Backup_Model_Cron
	 */
	public function sendErrorsEmail($profile)
	{
#		Mage::log('MPBACKUP CRON RUN SEND ERROR EMAIL');
		if (!$this->_errors) {
#			Mage::log('MPBACKUP CRON SEND EMAIL NO ERRORS');
			return $this;
		}

		try {
			if (!($profile instanceof Mageplace_Backup_Model_Profile)) {
				Mage::throwException(Mage::helper('mpbackup')->__('Cron profile is wrong.'));
			}

			if (!$profile->getData(Mageplace_Backup_Model_Profile::CRON_ERROR_EMAIL)) {
#				Mage::log('MPBACKUP CRON SEND ERROR EMAIL NOT SELECTED');
			} else {
				/* @var $emailTemplate Mage_Core_Model_Email_Template */
				$emailTemplate = Mage::getModel('core/email_template');
				$emailTemplate->setDesignConfig(array('area' => 'backend'))
					->sendTransactional(
					$profile->getData(Mageplace_Backup_Model_Profile::CRON_ERROR_EMAIL_TEMPLATE),
					$profile->getData(Mageplace_Backup_Model_Profile::CRON_ERROR_EMAIL_IDENTITY),
					$profile->getData(Mageplace_Backup_Model_Profile::CRON_ERROR_EMAIL),
					null,
					array(
						'profile_id' => $profile->getId(),
						'profile_name' => $profile->getProfileName(),
						'warnings' => join("\n", $this->_errors),
						
					)
				);
			}

		} catch (Exception $e) {
			Mage::logException($e);
			Mage::log('MPBackup cron send error email has errors: ' . $e->getMessage());
		}

#		Mage::log('MPBACKUP CRON FINISH SEND ERROR EMAIL');

		return $this;
	}

	/**
	 * Send backup success email
	 *
	 * @param $profile Mageplace_Backup_Model_Profile
	 * @param $backup Mageplace_Backup_Model_Backup
	 *
	 * @return Mageplace_Backup_Model_Cron
	 */
	public function sendSuccessEmail($profile, $backup)
	{
#		Mage::log('MPBACKUP CRON RUN SEND SUCCESS EMAIL');

		try {
			if (!($profile instanceof Mageplace_Backup_Model_Profile)) {
				Mage::throwException(Mage::helper('mpbackup')->__('Cron profile is wrong.'));
			}

			if (!$profile->getData(Mageplace_Backup_Model_Profile::CRON_SUCCESS_EMAIL)) {
#				Mage::log('MPBACKUP CRON SEND SUCCESS EMAIL NOT SELECTED');
			} else {
				$logLevel = $profile->getData(Mageplace_Backup_Model_Profile::CRON_SUCCESS_EMAIL_LOG_LEVEL);
				$logs = $backup->getLogs($logLevel);

				/* @var $emailTemplate Mage_Core_Model_Email_Template */
				$emailTemplate = Mage::getModel('core/email_template');
				$emailTemplate->setDesignConfig(array('area' => 'backend'))->sendTransactional(
					$profile->getData(Mageplace_Backup_Model_Profile::CRON_SUCCESS_EMAIL_TEMPLATE),
					$profile->getData(Mageplace_Backup_Model_Profile::CRON_SUCCESS_EMAIL_IDENTITY),
					$profile->getData(Mageplace_Backup_Model_Profile::CRON_SUCCESS_EMAIL),
					null,
					array(
						'profile_id' => $profile->getId(),
						'profile_name' => $profile->getProfileName(),
						'backup_id' => $backup->getId(),
						'logs' => join("\n", $logs)
					)
				);
			}

		} catch (Exception $e) {
			Mage::logException($e);
			Mage::log('MPBackup cron send success email has errors: ' . $e->getMessage());
		}

#		Mage::log('MPBACKUP CRON FINISH SEND SUCCESS EMAIL');

		return $this;
	}

	/**
	 * Send backup success delete email
	 *
	 * @param $profile Mageplace_Backup_Model_Profile
	 * @param $stat array
	 *
	 * @return Mageplace_Backup_Model_Cron
	 */
	public function sendSuccessDeleteEmail($profile, $stat)
	{
#		Mage::log('MPBACKUP CRON DELETE RUN SEND SUCCESS EMAIL');

		try {
			if (!($profile instanceof Mageplace_Backup_Model_Profile)) {
				Mage::throwException(Mage::helper('mpbackup')->__('Cron profile is wrong.'));
			}

			if (!$profile->getData(Mageplace_Backup_Model_Profile::CRON_SUCCESS_DELETE_EMAIL)) {
#				Mage::log('MPBACKUP CRON DELETE SEND SUCCESS EMAIL NOT SELECTED');
			} else {
				/* @var $emailTemplate Mage_Core_Model_Email_Template */
				$emailTemplate = Mage::getModel('core/email_template');
				$emailTemplate->setDesignConfig(array('area' => 'backend'))->sendTransactional(
					$profile->getData(Mageplace_Backup_Model_Profile::CRON_SUCCESS_DELETE_EMAIL_TEMPLATE),
					$profile->getData(Mageplace_Backup_Model_Profile::CRON_SUCCESS_DELETE_EMAIL_IDENTITY),
					$profile->getData(Mageplace_Backup_Model_Profile::CRON_SUCCESS_DELETE_EMAIL),
					null,
					array(
						'profile_id' => $profile->getId(),
						'profile_name' => $profile->getProfileName(),
						'backups' => join("\n", $stat[self::DELETE_STAT_BACKUPS]),
						'errors' => !empty($stat[self::DELETE_STAT_ERRORS]) ? join("\n", $stat[self::DELETE_STAT_ERRORS]) : Mage::helper('mpbackup')->__('No errors'),
					)
				);
			}

		} catch (Exception $e) {
			Mage::logException($e);
			Mage::log('MPBackup cron delete send success email has errors: ' . $e->getMessage());
		}

#		Mage::log('MPBACKUP CRON DELETE FINISH SEND SUCCESS EMAIL');

		return $this;
	}


	/**
	 * Clean logs
	 *
	 * @param $scheduleMpbackup Mage_Cron_Model_Schedule
	 *
	 * @return Mageplace_Backup_Model_Cron
	 */
	public function run($backupSchedule)
	{
		$this->_generateJobs();

#		Mage::log('MPBACKUP CRON RUN');

		$this->_errors = array();

		$executedAt = strtotime($backupSchedule->getData('executed_at'));

		$schedules = $this->_getBackupPendingSchedules();
		$scheduleLifetime = Mage::getStoreConfig(Mage_Cron_Model_Observer::XML_PATH_SCHEDULE_LIFETIME) * 60;
		/* @var $schedule Mageplace_Backup_Model_Cron_Schedule */
		foreach ($schedules->getIterator() as $schedule) {
#			Mage::log('MPBACKUP CRON SCHEDULE ID#' . $schedule->getId());
#			Mage::log('MPBACKUP CRON SCHEDULE JOB CODE ' . $schedule->getData('job_code'));
			
			$time = strtotime($schedule->getData('scheduled_at'));
			if ($time > $executedAt) {
				continue;
			}

			try {
				$errorStatus = Mageplace_Backup_Helper_Const::STATUS_ERROR;

				if ($time < $executedAt - $scheduleLifetime) {
					$errorStatus = Mageplace_Backup_Helper_Const::STATUS_MISSED;
					Mage::throwException(Mage::helper('cron')->__('Too late for the schedule.') . 'Schedule ID#.' . $schedule->getId());
				}

				if (!$schedule->tryJobLock()) {
					// another cron started this job intermittently, so skip it
					continue;
				}

				$schedule->setStatus(Mageplace_Backup_Helper_Const::STATUS_RUNNING)
					->setExecutedAt(strftime('%Y-%m-%d %H:%M:%S', time()))
					->save();

				$profile_id = $schedule->getProfileId();
#				Mage::log('MPBACKUP CRON PROFILE ID ' . $profile_id);
				if (!$profile_id) {
					continue;
				}

				$profile = Mage::getModel('mpbackup/profile')->load($profile_id);
				if (!is_object($profile) || !$profile->getId()) {
					Mage::throwException(Mage::helper('mpbackup')->__('Profile ID#%s not founded.', $profile_id));
				}
			
				switch ($schedule->getData('job_code')) {
					case self::JOB_CODE_BACKUP:
						$this->runBackup($profile, $schedule);
						break;

					case self::JOB_CODE_DELETE:
						$this->deleteOldBackups($profile);
						break;

					case self::JOB_CODE_CHECK_RUNNING:
						$this->checkRunningBackups($profile);
						break;
				}

				$schedule->setStatus(Mageplace_Backup_Helper_Const::STATUS_SUCCESS)
					->setFinishedAt(strftime('%Y-%m-%d %H:%M:%S', time()));

			} catch (Exception $e) {
				$schedule->setStatus($errorStatus)->setMessages($e->__toString());
				Mage::logException($e);
				Mage::log('MPBACKUP CRON ERRORS: ' . $e->getMessage());
				if ($errorStatus != Mageplace_Backup_Helper_Const::STATUS_MISSED) {
					$this->_errors[] = $e->__toString();
				}
			}

			$schedule->save();
			
#			Mage::log('MPBACKUP CRON SCHEDULE ID#' . $schedule->getId() . ' END');
		}

		if (isset($profile) && !empty($this->_errors)) {
			$this->sendErrorsEmail($profile);
		}

		$this->_cleanupJobs();

#		Mage::log('MPBACKUP CRON FINISH');

		return $this;
	}

	public function runBackup($profile, $schedule, $test=false)
	{
#		Mage::log('MPBACKUP CRON RUN BACKUP START');

		$profileId = $profile->getId();
		if($test) {
			$backupName = Mage::helper('mpbackup')->__('TEST Backup - %s', Mage::app()->getLocale()->storeDate(null, null, true));
			$backupDescription = Mage::helper('mpbackup')->__('Current backup was automatically created by TEST cron script');
		} else {
			$backupName = Mage::helper('mpbackup')->__('Backup - %s', Mage::app()->getLocale()->storeDate(null, null, true));
			$backupDescription = Mage::helper('mpbackup')->__('Current backup was automatically created by cron script');
		}

		/* @var $backupModel Mageplace_Backup_Model_Backup */
		$backupModel = Mage::getModel('mpbackup/backup')
			->setProfile($profileId)
			->setBackupName($backupName)
			->setBackupDescription($backupDescription)
			->save();
		
		$backupId = $backupModel->getId();
		
		$backupModel->setBackupLogFileTemplate($profileId, $backupId);
		
		$schedule->setBackupId($backupId)->save();

		$backupModel->disableDBLog();

		$session = Mage::helper('mpbackup')->getSession();
		$session->setBackupModel($backupModel);

		Mage::getSingleton('adminhtml/session')->setBackupId($backupId);

		$backup = $backupModel->createBackup('', '', '', false, '', '');

#		Mage::log('MPBACKUP CRON FINISH BACKUP');

		
		if ($backup instanceof Exception) {
//			$backupModel->finishBackupProcess($backup->getMessage());
			Mage::throwException($backup->getMessage());
		}

		$this->sendSuccessEmail($profile, $backup);

#		Mage::log('MPBACKUP CRON RUN BACKUP END');
	}

	public function deleteOldBackups($profile)
	{
#		Mage::log('MPBACKUP CRON DELETE OLD BACKUP START');

		$backupsLifetime = (int)$profile->getData(Mageplace_Backup_Model_Profile::CRON_DELETE_TYPE_DELETE_OLDER_THAN_X_DAYS);
		if ($backupsLifetime < 1) {
#			Mage::log('MPBACKUP CRON DELETE OLD BACKUP LIFETIME 0');
			return;
		}

		$ts = Mage::getSingleton('core/date')->gmtTimestamp() - $backupsLifetime * 24 * 60 * 60;

		$backups = $this->_getOldBackups($profile->getId(), date('Y-m-d H:i:s', $ts));
		$count = $backups->count();
#		Mage::log('MPBACKUP CRON DELETE OLD BACKUP COUNT ' . $count);
		if ($count) {
			$stat = array(
				self::DELETE_STAT_DELETED => 0,
				self::DELETE_STAT_BACKUPS => array(),
				self::DELETE_STAT_ERRORS => array(),
			);

			/* @var $backup Mageplace_Backup_Model_Backup */
			foreach ($backups as $backup) {
				$name = $backup->getBackupName();
				if ($backup->deleteRecordAndFiles(false)) {
					$stat[self::DELETE_STAT_DELETED]++;
					$stat[self::DELETE_STAT_BACKUPS][] = $name;
				}

				$stat[self::DELETE_STAT_ERRORS] = array_merge($stat[self::DELETE_STAT_ERRORS], $backup->getDeleteErrors());
			}

			$this->sendSuccessDeleteEmail($profile, $stat);
		}

#		Mage::log('MPBACKUP CRON DELETE OLD BACKUP END');
	}
	
	public function checkRunningBackups($profile)
	{
#		Mage::log('MPBACKUP CRON CHECK RUNNING SCHEDULES');

		$scedulesLifetime = $profile->getData(Mageplace_Backup_Model_Profile::CRON_FAILURE_RUNNING);
		if (!$scedulesLifetime && $scedulesLifetime !== 0) {
			$scedulesLifetime = Mageplace_Backup_Model_Profile::CRON_FAILURE_RUNNING_DEFAULT;
		}
		
		if ($scedulesLifetime < 1) {
#			Mage::log('MPBACKUP CRON CHECK RUNNING SCHEDULES LIFETIME 0');
			return;
		}

		$ts = Mage::getSingleton('core/date')->gmtTimestamp() - $scedulesLifetime * 60;

		$schedules = $this->_getFailureRunningShedules($profile->getId(), date('Y-m-d H:i:s', $ts));
		if ($schedules->count()) {
			/* @var $backup Mageplace_Backup_Model_Backup */
			foreach ($schedules as $schedule) {
#				Mage::log('MPBACKUP CRON CHECK RUNNING SCHEDULE ID: ' . $schedule->getId());
				
				$message = Mage::helper('mpbackup')->__("Schedule has 'running' status for too long");
				if($backupId = $schedule->getBackupId()) {
#					Mage::log('MPBACKUP CRON CHECK RUNNING SCHEDULES BACKUP ID: ' . $backupId);
					$backup = Mage::getModel('mpbackup/backup')->load($backupId);					
#					Mage::log('MPBACKUP CRON CHECK RUNNING SCHEDULES BACKUP MODEL LOADED');
					if(is_object($backup) && $backup->getId()) {
#						Mage::log('MPBACKUP CRON CHECK RUNNING SCHEDULES BACKUP MODEL FINISH PROCESS');
						$backup->finishBackupProcess($message);						
					}
				}
				
				$schedule->setStatus(Mageplace_Backup_Helper_Const::STATUS_ERROR)
					->setMessages($message)
					->save();
			}
		}

#		Mage::log('MPBACKUP CRON CHECK RUNNING SCHEDULES END');
	}

	protected function _getBackupPendingSchedules()
	{
		if (is_null($this->_pendingSchedules)) {
			/* @var $collection Mageplace_Backup_Model_Mysql4_Cron_Schedule_Collection */
			$collection = Mage::getModel('mpbackup/cron_schedule')->getCollection();

			$this->_pendingSchedules = $collection->addPendingFilter()
				->addDirectionOrder()
				->load();

/*			Mage::log('MPBACKUP CRON SCHEDULE COLLECTION LOADED');
			Mage::log('MPBACKUP CRON PENDING SCHEDULE COLLECTION:');
			Mage::log($this->_pendingSchedules->getItems());
*/
		}

		return $this->_pendingSchedules;
	}

	protected function _getOldBackups($profileId, $date)
	{
		/* @var $collection Mageplace_Backup_Model_Mysql4_Backup_Collection */
		$collection = Mage::getModel('mpbackup/backup')->getCollection()
			->addFilter('profile_id', $profileId)
			->addFieldToFilter('backup_finish_date', array('to' => $date))
			->addFieldToFilter('backup_finish_date', array('neq' => '0000-00-00 00:00:00'))
			->addFieldToFilter('backup_finish_date', array('notnull' => true));

		return $collection;
	}

	protected function _getFailureRunningShedules($profileId, $date)
	{
		/* @var $collection Mageplace_Backup_Model_Mysql4_Backup_Collection */
		$collection = Mage::getModel('mpbackup/cron_schedule')->getCollection()
			->addFilter('profile_id', $profileId)
			->addFilter('status', Mageplace_Backup_Helper_Const::STATUS_RUNNING)			
			->addFieldToFilter('executed_at', array('to' => $date))
			->addFieldToFilter('executed_at', array('neq' => '0000-00-00 00:00:00'))
			->addFieldToFilter('executed_at', array('notnull' => true));
		
#		Mage::log($collection->getSelect()->assemble());

		return $collection;
	}

	protected function _generateJobs()
	{
        $lastRun = Mage::app()->loadCache(self::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT);
        if ($lastRun > time() - Mage::getStoreConfig(Mage_Cron_Model_Observer::XML_PATH_SCHEDULE_GENERATE_EVERY)*60) {
            return $this;
        }

#		Mage::log('MPBACKUP CRON GENERATE JOBS');
		
		$skipJobs = array();

		$pendingSchedules = $this->_getBackupPendingSchedules();

		/* @var $pendingSchedule Mageplace_Backup_Model_Cron_Schedule */
		foreach ($pendingSchedules->getIterator() as $pendingSchedule) {
			$skipJobs[$pendingSchedule->getProfileId() . '/' . $pendingSchedule->getJobCode() . '/' . $pendingSchedule->getScheduledAt()] = 1;
		}
		
#		Mage::log('MPBACKUP CRON GENERATE JOBS SKIP: ' . print_r($skipJobs, true));

		$profileIds = Mage::getModel('mpbackup/profile')->getCollection()->getAllIds();
		foreach ($profileIds as $profileId) {
			/* @var $profile Mageplace_Backup_Model_Profile */
			$profile = Mage::getModel('mpbackup/profile')->load($profileId);

			/* @var $schedule Mageplace_Backup_Model_Cron_Schedule */
			$schedule = Mage::getModel('mpbackup/cron_schedule');
			$schedule->setProfileId($profileId)
				->setStatus(Mageplace_Backup_Helper_Const::STATUS_PENDING);

			$scheduleAheadFor = Mage::getStoreConfig(Mage_Cron_Model_Observer::XML_PATH_SCHEDULE_AHEAD_FOR) * 60;

			foreach (self::$JOBS as $jobCode) {
				$skip = true;
				$cronExpr = null;
				switch ($jobCode) {
					case self::JOB_CODE_BACKUP:
						$cronEnable = $profile->getData(Mageplace_Backup_Model_Profile::CRON_ENABLE);
						$skip = empty($cronEnable);
						$cronExpr = $profile->getData(Mageplace_Backup_Model_Profile::CRON_BACKUP_EXPR);
						break;

					case self::JOB_CODE_DELETE:
						$skip = $profile->getData(Mageplace_Backup_Model_Profile::CRON_DELETE_TYPE) != Mageplace_Backup_Model_Profile::CRON_DELETE_TYPE_DELETE_OLD;
						$cronExpr = Mage::getStoreConfig(self::XML_CRON_EXPR_DELETE);
						break;

					case self::JOB_CODE_CHECK_RUNNING:
						$cronEnable = $profile->getData(Mageplace_Backup_Model_Profile::CRON_ENABLE);
						$skip = empty($cronEnable) || $profile->getData(Mageplace_Backup_Model_Profile::CRON_FAILURE_RUNNING) == 0;
						$cronExpr = Mage::getStoreConfig(self::XML_CRON_EXPR_CHECK_RUNNING);
						break;
				}

				if ($skip || !$cronExpr) {
					continue;
				}

				$schedule->setCronExpr($cronExpr)->setJobCode($jobCode);

				$now = time();
/*				$now = time() + 60; // +60 - to fix the bug with double cron schedules */
				$timeAhead = $now + $scheduleAheadFor;
				for ($time = $now; $time < $timeAhead; $time += 60) {
					$ts = strftime('%Y-%m-%d %H:%M:00', $time);
					if (!empty($skipJobs[$profileId . '/' . $jobCode . '/' . $ts])) {
						// already scheduled
						continue;
					}

					if (!$schedule->trySchedule($time)) {
						// time does not match cron expression
						continue;
					}

					$schedule->unsCronId();
					$schedule->save();
				}
			}
		}
		
#		Mage::log('MPBACKUP CRON GENERATE JOBS END');
 
		Mage::app()->saveCache(time(), self::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT, array('crontab'), null);

		return $this;
	}


	public function _cleanupJobs()
	{
		// check if history cleanup is needed
		$lastCleanup = Mage::app()->loadCache(self::CACHE_KEY_LAST_HISTORY_CLEANUP_AT);
		if ($lastCleanup > time() - Mage::getStoreConfig(Mage_Cron_Model_Observer::XML_PATH_HISTORY_CLEANUP_EVERY) * 60) {
			return $this;
		}

		Mage::getModel('mpbackup/cron_schedule')->clean();

		Mage::app()->saveCache(time(), self::CACHE_KEY_LAST_HISTORY_CLEANUP_AT, array('crontab'), null);

		return $this;
	}
}
