<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package     Mageplace_Backup
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Model_Cloud_Sftp extends Mageplace_Backup_Model_Cloud
{
	const FILE_PART_MAX_SIZE = 100;

	const HOST = 'host';
	const PORT = 'port';
	const USERNAME = 'username';
	const PASSWORD = 'password';
	const TIMEOUT = 'timeout';
	const SSL = 'ssl';
	const PASSIVE = 'passive';
	const PATH = 'path';
	const FILEPARTMAXSIZE = 'filepartmaxsize';

	public function __destruct()
	{
		/* @var $sftp Varien_Io_Sftp */
		if($sftp = $this->_getData('sftp')) {
			$sftp->close();
		}
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

	public function connect(array $config = array())
	{
		$sftp = new Varien_Io_Sftp();

		if (empty($config)) {
			$config['host'] = $this->getConfigValue(self::HOST);
			if ($port = (int)$this->getConfigValue(self::PORT)) {
				$config['host'] += ':' + $port;
			}
			$config['username'] = $this->getConfigValue(self::USERNAME);
			$config['password'] = $this->getConfigValue(self::PASSWORD);
			$config['timeout'] = (int)$this->getConfigValue(self::TIMEOUT);
		}

		try {
			$sftp->open($config);
			if ($path = $this->getConfigValue(self::PATH)) {
				if (!$sftp->cd($path)) {
					throw Mage::exception('Mageplace_Backup', 'Invalid path');
				}
			}
		} catch (Mageplace_Backup_Exception $mpbe) {
			Mage::logException($mpbe);
			throw $mpbe;
		} catch (Exception $e) {
			Mage::logException($e);
			if (strpos($e->getMessage(), 'Cannot connect to') !== false) {
				throw Mage::exception('Mageplace_Backup', 'Could not establish SFTP connection, invalid host or port');
			} else {
				throw $e;
			}
		}

		$this->setSftp($sftp);

		return $this;
	}

	/**
	 * @return Varien_Io_Sftp
	 */
	public function getSftp()
	{
		if (!$this->_getData('sftp')) {
			$this->connect();
		}

		return $this->_getData('sftp');
	}

	/**
	 * Uploads a new file
	 *
	 * @param string $path Target path (including filename)
	 * @param string $file Either a path to a file or a stream resource
	 * @return bool
	 */
	public function putFile($path, $file)
	{
		$filename = basename($path);
		if (empty($filename)) {
			$e = Mage::exception('Mageplace_Backup', 'Error file name');
			Mage::logException($e);
			throw $e;
		}

		$sftp = $this->getSftp();

		$return = false;
		try {
			$content = file_get_contents($file);
			$return = $sftp->write($filename, $content);
		} catch (Exception $e) {
			Mage::logException($e);
			Mage::throwException($e->getMessage());
		}

		$path = $this->getConfigValue(self::PATH);
		$put_object = (empty($path) ? '' : $path) . '/' . $filename;
		if (!$return) {
			Mage::logException(Mage::exception('Mageplace_Backup', $this->_helper->__('"%s" file is not uploaded to SFTP server', $put_object)));
		}

		return $return ? $put_object : false;
	}

	/**
	 * Delete file
	 *
	 * @param string $path Target path (including filename)
	 * @return bool
	 */
	public function deleteFile($path)
	{
		return $this->getSftp()->rm($path);
	}
}
