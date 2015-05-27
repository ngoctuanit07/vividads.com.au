<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package        Mageplace_Backup
 * @copyright    Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license        http://www.mageplace.com/disclaimer.html
 */

/**
 * @method Zend_Service_Amazon_S3 getS3()
 * @method string getRoot()
 */
class Mageplace_Backup_Model_Cloud_Amazons3 extends Mageplace_Backup_Model_Cloud
{
	const FILE_PART_MAX_SIZE = 100;

	const ACCESS_KEY = 'accessKey';
	const SECRET_KEY = 'secretKey';
	const BUCKET = 'bucketPath';
	const BUCKET_DIRECTORY = 'appPath';
	const TIMEOUT = 'connTimeOut';
	const FILEPARTMAXSIZE = 'filepartmaxsize';

	protected function _construct()
	{
		parent::_construct();
	}

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

	public function initS3()
	{
		require_once 'Zend/Service/Amazon/S3.php';

		try {
			$s3 = new Zend_Service_Amazon_S3($this->getConfigValue(self::ACCESS_KEY), $this->getConfigValue(self::SECRET_KEY));
		} catch (Exception $e) {
			Mage::logException($e);
			Mage::throwException($e->getMessage());
		}

		$this->setS3($s3);

		$this->setRoot($this->getConfigValue(self::BUCKET));

		$timeOut = (int)$this->getConfigValue(self::TIMEOUT);
		if ($timeOut > 0) {
			$httpClient = new Zend_Http_Client();
			$httpClient->setConfig(array('timeout' => $timeOut));
			Zend_Service_Amazon_S3::setHttpClient($httpClient);
		}
	}

	/**
	 * Uploads a new file
	 *
	 * @param string $path Target path (including filename)
	 * @param string $file Either a path to a file or a stream resource
	 * @param string $root Use this to override the default root(bucket) path
	 * @return bool
	 */
	public function putFile($path, $file)
	{
		$this->initS3();

		$directory = dirname($path);
		$filename = basename($path);

		if ($directory === '.') {
			$directory = '';
		}

		$root = trim($this->getRoot(), '/');

		if (!$root || !$this->getS3()->isBucketAvailable($root)) {
			$exc = Mage::exception('Mageplace_Backup', $this->_helper->__('Bucket "%s" not available', $root));
			Mage::logException($exc);
			Mage::throwException($exc->getMessage());
		}

		$directory = trim($directory, '/');

		$bucket_dir = trim($this->getConfigValue(self::BUCKET_DIRECTORY), '/');

		if (is_string($file)) {
			$file = fopen($file, 'r');
		} elseif (!is_resource($file)) {
			Mage::throwException($this->_helper->__('File "%s" must be a file-resource or a string', strval($file)));
		}

		$content = stream_get_contents($file);
		if ($content === FALSE) {
			$meta = stream_get_meta_data($file);
			Mage::throwException($this->_helper->__("Can't get content of file '%s'.", (!empty($meta['uri']) ? $meta['uri'] : strval($file))));
		}

		$file_cloud_path = ($bucket_dir ? $bucket_dir . '/' : '') . $directory;
		$file_cloud_path = trim($file_cloud_path, '/');

		$put_object = $root . '/' . $file_cloud_path . '/' . $filename;
		try {
			$s3_return = $this->getS3()->putObject($put_object, $content);
		} catch (Exception $e) {
			Mage::logException($e);
			Mage::throwException($e->getMessage());
		}

		if (!$s3_return) {
			Mage::logException(Mage::exception('Mageplace_Backup', $this->_helper->__('Object "%s" not uploaded to cloud server', $put_object)));
		}

		return $s3_return ? $put_object : false;
	}

	/**
	 * Delete file
	 *
	 * @param string $path Target path (including filename)
	 * @return bool
	 */
	public function deleteFile($path)
	{
		$this->initS3();
		return $this->getS3()->removeObject($path);
	}
}
