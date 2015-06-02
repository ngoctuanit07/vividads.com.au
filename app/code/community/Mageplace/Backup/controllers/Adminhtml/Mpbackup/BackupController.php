<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package     Mageplace_Backup
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Adminhtml_Mpbackup_BackupController extends Mage_Adminhtml_Controller_Action
{
	protected $_oldSessionId = null;

	public function preDispatch()
	{
		if ($this->getRequest()->getActionName() == 'callback') {
			Mage::getSingleton('adminhtml/url')->turnOffSecretKey();
		}

		parent::preDispatch();
	}

	/**
	 * Initialization of current view - add title and the current menu status
	 *
	 * @return Mageplace_Backup_Adminhtml_Mpbackup_BackupController
	 */
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('mpbackup/backup')
			->_title($this->__('MagePlace Backup'));

		return $this;
	}

	/**
	 * Displays the backups overview grid.
	 *
	 */
	public function indexAction()
	{
		$this->_initAction()
			->_title($this->__('Manage Backups'))
			->_addContent($this->getLayout()->createBlock('mpbackup/adminhtml_backup'))
			->renderLayout();
	}

	/**
	 * Forward to create backup form
	 */
	public function newAction()
	{
		Mage::register('mpbackupBeforeForward', 'new');
		$this->_forward('create');
	}

	/**
	 * Forward to create backup form
	 */
	public function editAction()
	{
		Mage::register('mpbackupBeforeForward', 'edit');
		$this->_forward('create');
	}

	/**
	 * Displays the create backup form.
	 */
	public function createAction()
	{
		$backup_id = (int)$this->getRequest()->getParam('backup_id');

		if (!$backup_id) {
			$profile_id = (int)$this->getRequest()->getParam('profile_id');
			Mage::getSingleton('core/session')->setProfileId($profile_id);

			/* Session must be initiated before resetAuthData !!! */
			$session = Mage::helper('mpbackup')->getSession($profile_id, true);

			$cloud_storage = Mage::helper('mpbackup')->getCloudApplication($profile_id);
			if (!is_object($cloud_storage)) {
				$this->_getSession()->addError($this->__('Check profile settings'));
				$this->_redirect('*/mpbackup_profile/index');
				return $this;
			}


			if (!$cloud_storage->checkConnection()) {
				$cloud_storage->resetAuthData();
				$session->setCloudStorage(serialize($cloud_storage));
				Mage::register('mpbackup_cloud_storage', $cloud_storage);
				if ($cloud_storage->needAuthorize()) {
					$this->_forward('auth');
					return;
				}
			}

			$session->setCloudStorage(serialize($cloud_storage));

			$profile = $cloud_storage->getProfile();
			if (!$profile_id = $profile->getId()) {
				$this->_getSession()->addWarning($this->__('Select default profile first'));
				$this->_redirect('*/mpbackup_profile/index');
				return $this;
			}
		}

		$backup = Mage::getModel('mpbackup/backup');
		if (!$backup_id) {
			$backup->setData('profile_id', $profile_id);
		} else {
			$backup->load($backup_id);
			$profile_id = $backup->getProfileId();
		}

		if (empty($profile)) {
			$profile = Mage::getModel('mpbackup/profile')->load($profile_id);
		}

		if ($backup_id && !$backup->getId()) {
			$this->_getSession()->addError($this->__("Can't get selected backup."));
			$this->_redirect('*/*/index');
			return;
		}

		if (!$profile->getId()) {
			$this->_getSession()->addError($this->__("Can't get selected profile."));
			$this->_redirect('*/*/index');
			return;
		}

		Mage::register('mpbackup_backup', $backup);
		Mage::register('mpbackup_profile', $profile);

		$this->_initAction()
			->_title($this->__('Create Backup'))
			->_addBreadcrumb($this->__('Create Backup'), $this->__('Create Backup'))
			->_addContent($this->getLayout()->createBlock('mpbackup/adminhtml_backup_create'))
			->renderLayout();
	}

	/**
	 * Auth cloud application.
	 */
	public function authAction()
	{
		try {
			$session = Mage::helper('mpbackup')->getSession();
			$redirect = $session->getRedirect();

			$cloud_storage = Mage::registry('mpbackup_cloud_storage');
			if (!is_object($cloud_storage)) {
				Mage::exception('Mageplace_Backup', $this->__('Cloud storage must be declared'));
			}

			$redirect_url = $cloud_storage->getRedirectUrl();

			if (!$redirect_url) {
				Mage::exception('Mageplace_Backup', $this->__('Error authorize url. Check storage application settings.'));
			}

			$this->_redirectUrl($redirect_url);

			return;

		} catch (Exception $e) {
			$this->_getSession()->addError($e);
			Mage::logException($e);
		}

		if (!empty($redirect)) {
			$this->_redirectUrl($redirect);
		} else {
			$this->_redirect('*/*/index');
		}
	}

	public function callbackAction()
	{
		$error = false;
		try {
			$helper = Mage::helper('mpbackup');
			$session = $helper->getSession();
			$redirect = $session->getRedirect();
			$session->setRedirect('');
			$request = $this->getRequest();
			$response = $this->getResponse();

			$cloud_storage_serialize = $session->getCloudStorage();
			if ($cloud_storage_serialize) {
				if (($cloud_storage = unserialize($cloud_storage_serialize)) && is_object($cloud_storage)) {
					if (method_exists($cloud_storage, 'callback')) {
						if (!$cloud_storage->callback($request, $response)) {
							Mage::exception('Mageplace_Backup', $helper->__('The problem in the authentication process'));
						}
					} else {
						Mage::exception('Mageplace_Backup', $helper->__('Callback method not exists'));
					}
				} else {
					Mage::exception('Mageplace_Backup', $helper->__('Error during unserializing session cloud storage'));
				}
			} else {
				Mage::exception('Mageplace_Backup', $helper->__('Cloud storage must be declared'));
			}

			$session->setAuthPassed(true);

			$this->_getSession()->addSuccess($helper->__('Cloud storage was successfully authorized'));

		} catch (Exception $e) {
			$error = true;
			$this->_getSession()->addError($e->getMessage());
		}

		if (!empty($redirect)) {
			$this->_redirectUrl($redirect);
		} else if (!$error) {
			$profile_id = '';
			if (Mage::getSingleton('core/session')->getProfileId()) {
				$profile_id = '/profile_id/' . Mage::getSingleton('core/session')->getProfileId();
			}
			$this->_redirect('*/*/create' . $profile_id);
		} else {
			$this->_redirect('*/*/index');
		}
	}

	/**
	 * Start backup action.
	 */
	public function startAction()
	{
		$profile_id = (int)$this->getRequest()->getParam('profile_id');
		if (!$profile_id) {
			Mage::helper('mpbackup')->addBackupProcessMessage(Mage::helper('mpbackup')->__('Select profile first'), true, true);
		}

		$backup_filename = $this->getRequest()->getParam('backup_filename');
		$backup_name = $this->getRequest()->getParam('backup_name');
		$backup_description = $this->getRequest()->getParam('backup_description');

		/* @var $backupModel Mageplace_Backup_Model_Backup */
		$backupModel = Mage::getModel('mpbackup/backup')
			->setProfile($profile_id)
			->setBackupFilename($backup_filename)
			->setBackupName($backup_name)
			->setBackupDescription($backup_description)
			->save();

		$backup_id = $backupModel->getId();

		$backupModel->setBackupLogFileTemplate($profile_id, $backup_id);

		$data = array(
			'backupId' => $backup_id,
			'logFile' => $backupModel->getLogMessageFileName(false)
		);

		echo Zend_Json::encode($data);
		exit(1);
	}

	public function backupAction()
	{
		//If $bid is not empty but $backup_id is empty - 500 Internal server error
		$backup_id = (int)$this->getRequest()->getParam('backup_id');
		$bid = (int)$this->getRequest()->getParam('bid');

		$sbid = $this->_getSession()->getBackupId();

		if (!$backup_id) {
			if (!$bid && !$sbid) {
				echo $this->__("Wrong backup id #%s", $backup_id);
				die();
			} else {
				$id = $bid ? $bid : $sbid;
				$error = "It's not enough time to run backup. To eliminate this error try to split the content (files and DB tables) of this backup profile into smaller parts (profiles which will include the half or less from the original profile content).";

				$this->_finishErrorBackup($id, $error);
			}
		}

		$table = '';
		$row = '';
		$filename = '';
		$skipDb = false;
		$startPoint = '';
		$toCompress = '';

		if (!is_null($this->getRequest()->getParam('multiproc'))) {
			$table = (int)$this->getRequest()->getParam('table');
			$row = (int)$this->getRequest()->getParam('row');
			$filename = $this->getRequest()->getParam('filename');
			$skipDb = $this->getRequest()->getParam('skipDb');
			$startPoint = $this->getRequest()->getParam('startPoint');
			$toCompress = $this->getRequest()->getParam('toCompress');
		}

		$backupModel = Mage::getModel('mpbackup/backup')->load($backup_id);
		if (!$backupModel->getId() || $backup_id != $backupModel->getId()) {
			echo "Can't get backup model";
			exit(1);
		}

		if ($backupModel->getBackupFinished()) {
			$error = "Backup already finished.";
			exit(1);
		}

		if ($backupModel->getBackupStarted()) {
			$error = "It's not enough time to run backup. To eliminate this error try to split the content (files and DB tables) of this backup profile into smaller parts (profiles which will include the half or less from the original profile content).";

			$this->_finishErrorBackup($sbid, $error);
		}

		$this->_getSession()->setBackupId($backup_id);

		$result = $backupModel->createBackup($table, $row, $filename, $skipDb, $startPoint, $toCompress);
		if (is_array($result)) {
			echo $result[0];
			echo '|';
			echo $result[1];
			echo '|';
			echo $result[2];
			if (isset($result[3], $result[4], $result[5])) {
				echo '|';
				echo $result[3];
				echo '|';
				echo $result[4];
				echo '|';
				echo serialize($result[5]);
			}
			exit(1);
		}

		echo "1";
		exit(1);
	}

	public function finishBackupAction()
	{
		$backup_id = (int)$this->getRequest()->getParam('backup_id');
		if (!$backup_id) {
			$backup_id = (int)$this->getRequest()->getParam('bid');
			if (!$backup_id) {
				$backup_id = $this->_getSession()->getBackupId();
			}
		}

		$error = $this->getRequest()->getParam('error');

		$this->_finishErrorBackup($backup_id, $error);
	}

	protected function _finishErrorBackup($backup_id, $error)
	{
		if (!$backup_id) {
			echo "Can't finish backup with errors: Wrong backup id " . $backup_id;
			die();
		}

		$backupModel = Mage::getModel('mpbackup/backup')->load($backup_id);
		if (!$backupModel->getId()) {
			echo "Can't finish backup with errors: Error backup model";
			exit(1);
		}

		if ($backupModel->isFinished()) {
			echo "1";
			exit(1);
		}

		$backupModel->finishBackupProcess($error);
		$this->_getSession()->unsBackupId();

		echo "1";
		exit(1);
	}

	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('mpbackup/backup');
			$model->setData($data);

			if (!$model->getBackupFiles()
				&& ($bu_files = Mage::helper('mpbackup')->getSession()->getBackupFiles())
			) {
				if (is_array($bu_files)) {
					$bu_files = implode(';', $bu_files);
				} else {
					$bu_files = (string)$bu_files;
				}
				$model->setBackupFiles($bu_files);
			}

			try {
				$model->save();

				$this->_getSession()->addSuccess($this->__('Backup was successfully saved'));
				$this->_getSession()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('backup_id' => $model->getId()));
					return;
				}
			} catch (Exception $e) {
				$this->_getSession()->addException($e, $e->getMessage());
				$this->_getSession()->setFormData($data);
				$this->_redirect('*/*/edit', array('backup_id' => $this->getRequest()->getParam('backup_id')));
				return;
			}
		}

		$this->_redirect('*/*/index');
	}

	public function deleteAction()
	{
		if ($id = $this->getRequest()->getParam('backup_id')) {
			try {
				$model = Mage::getModel('mpbackup/backup');
				$model->load($id);

				if ($model->deleteRecordAndFiles()) {
					$this->_getSession()->addSuccess($this->__('Backup was successfully deleted'));
				}

				$this->_redirect('*/*/index');
				return;

			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/index');
				return;
			}
		}

		$this->_getSession()->addError($this->__('Unable to find a Backup to delete'));

		$this->_redirect('*/*/index');
	}

	public function massDeleteAction()
	{
		$this->_redirect('*/*/index');

		$backuptableIds = $this->getRequest()->getParam('backuptable');
		if (!is_array($backuptableIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select backup(s)'));
			return;
		}

		$total_success = 0;
		$total_errors = 0;

		try {
			foreach ($backuptableIds as $backuptableId) {
				$model = Mage::getModel('mpbackup/backup')->load($backuptableId);
				if ($model->deleteRecordAndFiles()) {
					$total_success++;
				} else {
					$total_errors++;
				}
			}
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}

		if ($total_success) {
			Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('adminhtml')->__('Total of %d record(s) were deleted', $total_success)
			);
		}

		if ($total_errors) {
			Mage::getSingleton('adminhtml/session')->addError(
				$this->__('Total of %d record(s) were deleted with errors', $total_errors)
			);
		}
	}

	public function deleteRecordAction()
	{
		if ($id = $this->getRequest()->getParam('backup_id')) {
			try {
				$model = Mage::getModel('mpbackup/backup');
				$model->load($id);
				$model->delete();

				$this->_getSession()->addSuccess($this->__('Backup was successfully deleted'));
				$this->_redirect('*/*/index');
				return;

			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/index');
				return;
			}
		}

		$this->_getSession()->addError($this->__('Unable to find a Backup to delete'));

		$this->_redirect('*/*/index');
	}

	public function massDeleteRecordAction()
	{
		$this->_redirect('*/*/index');

		$backuptableIds = $this->getRequest()->getParam('backuptable');
		if (!is_array($backuptableIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select backup(s)'));
			return;
		}

		$total_success = 0;
		$total_errors = 0;

		try {
			foreach ($backuptableIds as $backuptableId) {
				$model = Mage::getModel('mpbackup/backup')->load($backuptableId);
				if ($model->delete()) {
					$total_success++;
				} else {
					$total_errors++;
				}
			}
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}

		if ($total_success) {
			Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('adminhtml')->__('Total of %d record(s) were deleted', $total_success)
			);
		}

		if ($total_errors) {
			Mage::getSingleton('adminhtml/session')->addError(
				$this->__('Total of %d record(s) were NOT deleted', $total_errors)
			);
		}
	}

	/**
	 * Simple access control
	 *
	 * @return boolean True if user is allowed to create/edit/delete backup
	 */
	protected function _isAllowed()
	{
		$action = (Mage::registry('mpbackupBeforeForward') ? Mage::registry('mpbackupBeforeForward') : $this->getRequest()->getActionName());

		if (in_array($action, array('start', 'restoreOldSession', 'new', 'auth', 'callback', 'backup'))) {
			return Mage::getSingleton('admin/session')->isAllowed('admin/mpbackup/backup/backup_create');
		}

		if (in_array($action, array('save'))) {
			return Mage::getSingleton('admin/session')->isAllowed('admin/mpbackup/backup/backup_create')
			|| Mage::getSingleton('admin/session')->isAllowed('admin/mpbackup/backup/backup_edit');
		}

		if ($action && ($action != 'index')) {
			return Mage::getSingleton('admin/session')->isAllowed('admin/mpbackup/backup/backup_' . $action);
		}

		return Mage::getSingleton('admin/session')->isAllowed('admin/mpbackup/backup');
	}
}