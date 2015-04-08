<?php
/**
 * Mageplace Backup
 *
 * @category   Mageplace
 * @package    Mageplace_Backup
 * @copyright  Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license    http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_BackupController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch()
	{
		Mage::getSingleton('core/session', array('name' => $this->_sessionNamespace))->start();
	}
	
	public function stageAction()
	{
		$forward_to_finish = false;
		$finish = $this->getRequest()->getParam('finish');
		if ($finish == 'true') {
			$forward_to_finish = true;
		}

		$session = Mage::helper('mpbackup')->getSession();

		$backup_id = (int)$session->getData('backup_id');
		$start_id = (int)$session->getData('start_id');
		$log_file = $session->getData('log_file');

		if (!$backup_id || !$log_file) {
			$this->_finish();
			return;
		}

		$logs = $this->_getLogsFromFile($log_file, $start_id); /*Mage::getResourceModel('mpbackup/backuplog')->getLogMessages($backup_id, $start_id);*/
		if (is_null($logs)) {
			$this->_finish();
			exit(1);
		} else if (!is_array($logs)) {
			if ($forward_to_finish) {
				$this->_finish();
			}
			echo '...<br />';
			exit(1);
		}

		$logCount = count($logs);

		$session->setData('start_id', ($start_id + $logCount));

		$echo = '';
		for ($i = $logCount - 1; $i >= 0; $i--) {
			$log = $logs[$i];

			$backuplog_type = preg_replace('/[^\[]+\[([^\]]+)\].+/is', '$1', $log);
			if ($backuplog_type != 'DEBUG') {
				$echo .= '<b';
				if ($backuplog_type == 'ERROR') {
					$echo .= ' style="background-color:red; color:white; font-size:1.2em;"';
				} else if ($backuplog_type == 'WARNING') {
					$echo .= ' style="background-color:#E2CF6A; color:white; font-size:1.2em;"';
				}
				$echo .= '>' . $log . '</b>';
			} else {
				$echo .= $log;
			}

			$echo .= '<br />';
		}

		echo $echo;


		if ($forward_to_finish) {
			$this->_finish();
		}

		exit(1);
	}

	public function startAction()
	{
		$backup_id_req = (int)$this->getRequest()->getParam('backup_id');

		$backup_log_file = $this->getRequest()->getParam('log_file');

		$session = Mage::helper('mpbackup')->getSession();
		$session->clear();

		$backup = Mage::getModel('mpbackup/backup')->getCurrentBackup();
		if (!($backup_id = $backup->getId()) || ($backup_id_req != $backup_id)) {
			$this->_finish();
			return;
		}

		$session->setData('backup_id', $backup_id);
		$session->setData('start_id', 0);
		$session->setData('log_file', $backup_log_file);
		/*		$session->setData('backup_model', $backup);*/
	}

	public function multiStartAction()
	{
		$backup_id_req = (int)$this->getRequest()->getParam('backup_id');

		$backup_log_file = $this->getRequest()->getParam('log_file');

		$session = Mage::helper('mpbackup')->getSession();
		$session->clear();


		$session->setData('backup_id', $backup_id_req);
		$session->setData('start_id', 0);
		$session->setData('log_file', $backup_log_file);
		/*		$session->setData('backup_model', $backup);*/
	}

	protected function _getLogsFromFile($log_file, $start_id)
	{
		if (!$log_file || !file_exists($log_file)) {
			return null;
		}

		$content = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if (empty($content) || !is_array($content)) {
			return '';
		}

		$logs = array_slice($content, $start_id);

		return $logs;
	}

	protected function _finish()
	{
		echo '!!!FINISH!!!';

		Mage::helper('mpbackup')->getSession()->clear();

		exit(1);
	}
	
	public function checkMemoryLimitAction()
	{
		$backupId = (int) $this->getRequest()->getParam('backup_id');
		$mBytes = (int) $this->getRequest()->getParam('mbytes');
		
		if(!$backupId || !$mBytes) {
			die('FALSE');
		}
		
		$backup = Mage::getModel('mpbackup/backup')->load($backupId);
		if(!$backup->getId() || $backup->getBackupFinished() == 1 || $backup->getBackupStarted() == 0) {
			die('FALSE');
		}
		
		$temp = Mage::getModel('mpbackup/temp')->setBackup($backup);
		
		$megabyte = str_repeat('8 bytes ', 131072); /* = 1Mb */
		
 		$test = '';
		$step = Mageplace_Backup_Model_Backup::MEMORY_LIMIT_CHECK_STEP;
		for ($i=$step; $i < $mBytes; $i+=$step) {
			echo $i . '-' . @memory_get_peak_usage(1) . '|'; 
			$test .= str_repeat($megabyte, $step);
		}
	
		echo 'OK';
		exit(1);
	}
}