<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package     Mageplace_Backup
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

include_once Mage::getConfig()->getOptions()->getDir('lib') . '/google/drive/google-api-php-client/src/Google_Client.php';
include_once Mage::getConfig()->getOptions()->getDir('lib') . '/google/drive/google-api-php-client/src/contrib/Google_DriveService.php';

/**
 * Class Mageplace_Backup_Model_Cloud_Googledrive
 * @method Mageplace_Backup_Model_Cloud_Googledrive setBackupFolder
 */
class Mageplace_Backup_Model_Cloud_Googledrive extends Mageplace_Backup_Model_Cloud
{
	const FILE_PART_MAX_SIZE = 100;

	const CLIENT_ID = 'client_id';
	const CLIENT_SECRET = 'client_secret';
	const APP_PATH = 'appPath';
	const FILEPARTMAXSIZE = 'filepartmaxsize';
	const OAUTH_ACCESS_TOKEN = 'oauth_access_token';
	const OAUTH_REFRESH_TOKEN = 'oauth_refresh_token';
	const PARAM_ACCESS_TOKEN = 'access_token';
	const PARAM_REFRESH_TOKEN = 'refresh_token';
	const PARAM_ROOTFOLDERID = 'rootFolderId';

	const MIME_TYPE_GOOGLE_FOLDER = 'application/vnd.google-apps.folder';
	const MIME_TYPE_GOOGLE_FILES = 'application/vnd.google-apps.file';
	const MIME_TYPE_TGZ = 'application/x-tgz';
	const MIME_TYPE_GZIP = 'application/x-gzip';

	static $SCOPES = array(
		'https://www.googleapis.com/auth/drive'
	);

	/**
	 * Get max size of file for upload to cloud server (Mb)
	 *
	 * @return int
	 */
	public function getMaxSize()
	{
		$filepartmaxsize = (int)$this->getConfigValue(self::FILEPARTMAXSIZE);

		return $filepartmaxsize ? $filepartmaxsize : self::FILE_PART_MAX_SIZE;
	}

	/**
	 * @return Google_Client
	 */
	public function getClient()
	{
		if (!$this->getConfigValue(self::CLIENT_ID) || !$this->getConfigValue(self::CLIENT_SECRET)) {
			$this->_throwExeption($this->_helper->__('Wrong application settings'));
		}

		if (!$this->_getData('client')) {
			$client = new Google_Client();
			$client->setClientId($this->getConfigValue(self::CLIENT_ID));
			$client->setClientSecret($this->getConfigValue(self::CLIENT_SECRET));
			$client->setRedirectUri($this->getCallbackUrl()); /*'urn:ietf:wg:oauth:2.0:oob'*/
			$client->setScopes(self::$SCOPES);
			$this->setData('client', $client);
		}

		return $this->_getData('client');
	}

	public function getService()
	{
		if (!$this->_getData('service')) {
			if (!$this->getAccessToken()) {
				$this->_throwExeption($this->_helper->__("Error access token"));
			}

			$this->getClient()->setUseObjects(true);
			$service = new Google_DriveService($this->getClient());
			$this->setData('service', $service);
		}

		return $this->_getData('service');
	}

	public function getRedirectUrl()
	{
		return $this->getClient()->createAuthUrl();
	}


	public function callback($request, $response)
	{
		try {
			$tokenJson = $this->getClient()->authenticate();
			$token = Zend_Json::decode($tokenJson);
		} catch (Exception $e) {
			$this->resetAuthData();
			Mage::log($e);
			$this->_throwExeption($e->getMessage());
		}

		if (!$token) {
			$this->resetAuthData();
			Mage::log($request->getParams());
			Mage::log($tokenJson);
			$this->_throwExeption($this->_helper->__("Error callback code"));
		}


		$this->setAccessToken($token);

		return true;
	}

	public function setAccessToken($token, $hasRefreshToken = true)
	{
		if (empty($token[self::PARAM_ACCESS_TOKEN]) || ($hasRefreshToken && empty($token[self::PARAM_REFRESH_TOKEN]))) {
			Mage::log($token);
			$this->_throwExeption($this->_helper->__("Error tokens"));
		}

		$this->saveConfigValue(self::OAUTH_ACCESS_TOKEN, $token[self::PARAM_ACCESS_TOKEN]);
		if ($hasRefreshToken) {
			$this->saveConfigValue(self::OAUTH_REFRESH_TOKEN, $token[self::PARAM_REFRESH_TOKEN]);
		}

		$this->setData('access_token', $token[self::PARAM_ACCESS_TOKEN]);

		return $this;
	}

	public function getAccessToken()
	{
		if (!$this->_getData('access_token')) {
			if (!$this->getConfigValue(self::OAUTH_REFRESH_TOKEN)) {
				$this->_throwExeption($this->_helper->__("Error refresh token"));
			}


			$this->getClient()->refreshToken($this->getConfigValue(self::OAUTH_REFRESH_TOKEN));
			$this->setAccessToken(Zend_Json::decode($this->getClient()->getAccessToken()), false);
		}

		return $this->_getData('access_token');
	}

	public function resetAuthData()
	{
		$this->saveConfigValue(self::OAUTH_ACCESS_TOKEN, '');
		$this->saveConfigValue(self::OAUTH_REFRESH_TOKEN, '');

		$this->unsetData('access_token');

		$this->_getSession()->unsetData();

		return $this;
	}

	public function getAboutInfo()
	{
		return $this->getService()->about->get();
	}

	public function checkConnection()
	{
		try {
			$this->getRootId();
			return true;
		} catch (Exception $e) {
		}

		return false;
	}

	public function getRootId()
	{
		$info = $this->getAboutInfo();
		if (!is_object($info) || empty($info->{self::PARAM_ROOTFOLDERID})) {
			Mage::log($info);
			$this->_throwExeption($this->_helper->__("Error root id"));
		}

		return $info->{self::PARAM_ROOTFOLDERID};
	}

	public function createFolder($parent, $folderName)
	{
		if (is_array($folderName)) {
			$newFolder = null;
			$parentId = $parent;
			foreach ($folderName as $name) {
				$newFolder = $this->createFolder($parentId, $name);
				if (empty($newFolder) || !is_object($newFolder) || empty($newFolder->id)) {
					$this->_throwExeption($this->_helper->__("Error during create the folder '%s'", $name));
				}
				$parentId = $newFolder->id;
			}

			return $newFolder;
		}

		$objFolder = new Google_DriveFile();
		$objFolder->setTitle($folderName);
		$objFolder->setMimeType(self::MIME_TYPE_GOOGLE_FOLDER);

		$objParent = new Google_ParentReference();
		$objParent->setId($parent);
		$objFolder->setParents(array($objParent));

		return $this->getService()->files->insert($objFolder);
	}

	public function getFolderChildren($folderId)
	{
		$result = array();
		$pageToken = NULL;

		do {
			try {
				$parameters = array();
				$parameters['q'] = 'mimeType = "' . self::MIME_TYPE_GOOGLE_FOLDER . '" and "' . $folderId . '" in parents and trashed = false';
				$parameters['fields'] = 'items(id,title),nextPageToken';
				if ($pageToken) {
					$parameters['pageToken'] = $pageToken;
				}

				$children = $this->getService()->files->listFiles($parameters);

				$result = array_merge($result, $children->getItems());
				$pageToken = $children->getNextPageToken();
			} catch (Exception $e) {
				Mage::log($e);
				break;
			}
		} while ($pageToken);

		return $result;
	}

	public function getBackupFolderId($parent, $dirs)
	{
		foreach ($dirs as $i => $d) {
			$children = $this->getFolderChildren($parent);
			if (!is_array($children) || empty($children)) {
				$newFolder = $this->createFolder($parent, array_slice($dirs, $i));
				$parent = $newFolder->id;
				break;
			}

			foreach ($children as $child) {
				if (is_object($child) && !empty($child->title) && $child->title == $d) {
					$newParent = $child->id;
					break;
				}
			}

			if (isset($newParent)) {
				$parent = $newParent;
				unset($newParent);
			} else {
				$newFolder = $this->createFolder($parent, array_slice($dirs, $i));
				$parent = $newFolder->id;
				break;
			}
		}

		return $parent;
	}

	public function getBackupFolder()
	{
		$dir = $this->_getData('backup_folder');
		if (is_null($dir)) {
			$box_dir_nat = $this->getConfigValue(self::APP_PATH);
			$box_dir = str_replace(array('\\'), array('/'), $box_dir_nat);
			$box_dir = trim($box_dir, '\\/');
			if (!$box_dir) {
				$dir = $this->getRootId();
			} else {
				$dirs = explode('/', $box_dir);
				$dir = $this->getBackupFolderId($this->getRootId(), $dirs);
			}

			if (!$dir && $box_dir_nat) {
				$this->saveConfigValue(self::APP_PATH, '');
			} else if ($box_dir_nat != $box_dir) {
				$this->saveConfigValue(self::APP_PATH, $box_dir);
			}

			$this->setBackupFolder($dir);
		}

		return $dir;
	}

	public function putFile($name, $file)
	{
		$file = realpath($file);
		if (!file_exists($file)) {
			$this->_throwExeption($this->_helper->__('File "%s" doesn\'t exist', strval($file)));
		}

		$filename = basename($name);

		$fileObject = new Google_DriveFile();
		$fileObject->setTitle($filename);

		if (substr(".tar.gz", -7)) {
			$mimeType = self::MIME_TYPE_TGZ;
		} else if (substr(".gz", -3)) {
			$mimeType = self::MIME_TYPE_GZIP;
		} else {
			$mimeType = self::MIME_TYPE_GOOGLE_FILES;
		}

		$parentId = $this->getBackupFolder();
		if ($parentId != null) {
			$parent = new Google_ParentReference();
			$parent->setId($parentId);
			$fileObject->setParents(array($parent));
		}
		$data = file_get_contents($file);

		$createdFile = $this->getService()->files->insert($fileObject, array(
			'mimeType' => $mimeType,
			'data' => $data,
		));

		$file_cloud_path = $this->getConfigValue(self::APP_PATH);
		$returnPath = $file_cloud_path . '/' . $filename;

		$this->_addAdditionalInfo($createdFile->getId(), $returnPath);

		return $returnPath;
	}

	public function deleteFile($path)
	{
		if (empty($this->_additionalInfo) || !is_array($this->_additionalInfo) || empty($this->_additionalInfo[$path])) {
			$this->_throwExeption($this->_helper->__('Wrong additional backup data'));
		}

		try {
			$this->getService()->files->delete($this->_additionalInfo[$path]);
		} catch (Exception $e) {
			Mage::log($e);
			return false;
		}

		return true;
	}
}