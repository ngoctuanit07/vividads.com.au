<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package     Mageplace_Backup
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Model_Cloud_Ftp extends Mageplace_Backup_Model_Cloud
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
		/* @var $ftp Varien_Io_Ftp */
		if($ftp = $this->_getData('ftp')) {
			$ftp->close();
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
		$ftp = new Varien_Io_Ftp();

		if (empty($config)) {
			$config['host'] = $this->getConfigValue(self::HOST);
			$config['port'] = (int)$this->getConfigValue(self::PORT);
			$config['user'] = $this->getConfigValue(self::USERNAME);
			$config['password'] = $this->getConfigValue(self::PASSWORD);
			$config['timeout'] = (int)$this->getConfigValue(self::TIMEOUT);
			$config['ssl'] = $this->getConfigValue(self::SSL);
			$config['passive'] = $this->getConfigValue(self::PASSIVE);
			$config['path'] = $this->getConfigValue(self::PATH);
		}

		try {
			$ftp->open($config);
		} catch (Exception $e) {
			Mage::logException($e);
			Mage::throwException($e->getMessage());
		}

		$this->setFtp($ftp);

		return $this;
	}

	/**
	 * @return Varien_Io_Ftp
	 */
	public function getFtp()
	{
		if (!$this->_getData('ftp')) {
			$this->connect();
		}

		return $this->_getData('ftp');
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

		$ftp = $this->getFtp();

		$return = false;
		try {
			$return = $ftp->write($filename, $file);
		} catch(Exception $e) {
			Mage::logException($e);
			Mage::throwException($e->getMessage());
		}

		$path = $this->getConfigValue(self::PATH);
		$put_object = (empty($path) ? '' : $path).'/'.$filename;
		if(!$return) {
			Mage::logException(Mage::exception('Mageplace_Backup', $this->_helper->__('"%s" file is not uploaded to FTP server', $put_object)));
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
		return $this->getFtp()->rm($path);
	}
}
