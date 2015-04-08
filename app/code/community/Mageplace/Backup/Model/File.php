<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package     Mageplace_Backup
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Model_File extends Mage_Backup_Model_Backup
{
	const DEFAULT_FILE_PART_SIZE = 100; /* Mb */
	const READ_FILE_PART_SIZE = 1048576; /* = 1 Mb */
	const BACKUP_EXTENSION = 'gz';
	const DEFAULT_ARCHIVER = 'gz';
	const TAPE_ARCHIVER = 'tar';

	const BACKUP_FILE_PREFIX = 'mp';

	protected $_archiver = null;
	protected $_excluded = array();
	protected $_filesForCompress = array();
	protected $_dirsForCompress = array();
	protected $_baseDir = '';
	protected $_helper = null;
	protected $_backup = null;

	protected function _construct()
	{
		$this->_baseDir = Mage::getBaseDir();
		$this->_helper = Mage::helper('mpbackup');
	}

	protected function _excludedDirs()
	{
		return array(
			Mage::getBaseDir('cache'),
			Mage::getBaseDir('session'),
			$this->getProfile()->getData('profile_backup_path'),
		);
	}

	public function addExcludedPath($path)
	{
		if (is_array($path)) {
			$this->_excluded = array_merge($this->_excluded, $path);
		} else {
			$this->_excluded[] = strval($path);
		}

		return $this;
	}

	public function getTime()
	{
		return Mage::app()->getLocale()->storeTimeStamp();
	}

	/**
	 * Return file name of backup file
	 *
	 * @return string
	 */
	public function getFileName($ext = null, $suffix = null)
	{
		$filename = $this->_getData('filename');
		if (!$filename) {
			$filename = $this->getTime();
			$this->setData('filename', $filename);
		}

		if (!$ext) {
			$ext = $this->_getData('extension');
			if (!$ext) {
				$ext = self::BACKUP_EXTENSION;
				$this->setExtension($ext);
			}
		}

		return self::BACKUP_FILE_PREFIX . $filename . "_" . $this->getType() . "." . $ext . ($suffix ? $this->_helper->__("_part%s", $suffix) : '');
	}

	public function setFilename($filename)
	{
		$this->setData('filename', preg_replace('/[^0-9a-z\-\_]/i', '_', $filename));

		return $this;
	}


	public function getMainFileName()
	{
		return $this->_getData('filename');
	}

	/**
	 * Return file location of backup file
	 *
	 * @return string
	 */
	public function getFileLocation()
	{
		return $this->getPath() . DS . $this->getFileName();
	}

	/**
	 * Sets type of file
	 *
	 * @param string $value db|files
	 */
	public function setType($value = 'db')
	{
		if (!in_array($value, array('db', 'files'))) {
			$value = 'files';
		}

		$this->setData('type', $value);

		return $this;
	}

	public function getType()
	{
		return $this->getData('type');
	}

	public function startBackup($startPoint, $profileId, $toCompress)
	{
		if (!($profile = $this->getProfile()) || !($profile instanceof Mageplace_Backup_Model_Profile)) {
			Mage::throwException($this->_helper->__('Backup profile is not specified.'));
		}

		$get_include_path = get_include_path();
		$paths = explode(PATH_SEPARATOR, $get_include_path);
		if (is_array($paths)) {
			$include_paths = array();
			$exclude_paths = array();
			foreach ($paths as $path) {
				if (stripos($path, 'pear') === false) {
					$include_paths[] = $path;
				} else {
					$exclude_paths[] = $path;
				}
			}
			$include_path = implode(PATH_SEPARATOR, $include_paths);
			$suffix = implode(PATH_SEPARATOR, $exclude_paths);
		} else {
			$include_path = $get_include_path;
			$suffix = '';
		}

		set_include_path(
			$include_path
			. PATH_SEPARATOR . Mage::getBaseDir('lib') . DS . 'PEAR'
			. PATH_SEPARATOR . Mage::getBaseDir('lib') . DS . 'PEAR' . DS . 'Archive'
			. ($suffix ? PATH_SEPARATOR . $suffix : '')
		);

		include_once "Tar.php";
		if (!class_exists('Archive_Tar')) {
			Mage::throwException($this->_helper->__('Class Archive_Tar not exists.'));
		}

		$this->setExtension(self::TAPE_ARCHIVER . '.' . self::DEFAULT_ARCHIVER);

		$this->_excluded = array_merge(
			$this->_excluded,
			$this->_excludedDirs(),
			$profile->getExcludedPath(),
			array(str_replace($this->_baseDir, '', $this->getFileLocation()))
		);

		if (is_null($startPoint) || $startPoint === '') {
			$this->_addMessage($this->_helper->__('Excluded directories and files: ') . implode(';  ', $this->_excluded));
			$this->_addMessage($this->_helper->__('Start getting files for archive'), 'INFO');
			$this->_directoryIterator($this->_baseDir);
			$this->_addMessage($this->_helper->__('Finish getting files for archive'), 'INFO');
		}

		if (is_null($toCompress) || $toCompress === '') {
			$this->_filesForCompress = array_merge($this->_filesForCompress, $this->_dirsForCompress);
		} else {
			$this->_filesForCompress = unserialize($toCompress);
		}
		if (!empty($this->_filesForCompress) && is_array($this->_filesForCompress)) {
			if (is_null($startPoint) || $startPoint === '') {
				$this->_addMessage($this->_helper->__('Start packing files'), 'INFO');
			}
			if ($startPoint !== '') {
				if (is_null($startPoint)) {
					$startPoint = 0;
				}
				$profileMultiTime = Mage::getModel('mpbackup/profile')->load($profileId)->getProfileMultiprocessTime();
				$finishTime = time() + $profileMultiTime;
				for ($startPoint; $startPoint < count($this->_filesForCompress); $startPoint++) {
					$this->addToArchive($this->_filesForCompress[$startPoint]);
					if (time() > $finishTime) {
						return $multiParams = array(0, 0, $this->_getData('filename'), true, ++$startPoint, $this->_filesForCompress);
					}
				}
			} else {
				$this->addToArchive($this->_filesForCompress);
			}
			//if (is_null($startPoint) || $startPoint === ''){
			$this->_addMessage($this->_helper->__('Finish packing files'), 'INFO');
			//}
		} else {
			Mage::throwException($this->_helper->__('Backup archive not created (Empty file list).'));
		}

		return $this;
	}

	protected function addToArchive($toCompress)
	{
		$archiveTar = new Archive_Tar($this->getFileLocation(), self::DEFAULT_ARCHIVER);

		$this->getBackup()->addMainBackupFiles($this->getFileLocation());

		$archiveTar->_separator = '|';
		$archiveTar->setErrorHandling(PEAR_ERROR_TRIGGER);
		if (!$archiveTar->addModify($toCompress, '', $this->_baseDir)) {
			Mage::throwException($this->_helper->__('Backup archive not created.'));
		}
	}

	protected function _directoryIterator($dir)
	{
		if (in_array($dir, $this->_excluded) || in_array(str_replace($this->_baseDir, '', $dir), $this->_excluded)) {
			return false;
		}

		$this->_addMessage($this->_helper->__('Add "%s" directory to archive', $dir));

		$check_add = true;
		/* @var $diItem DirectoryIterator */
		foreach (new DirectoryIterator($dir) as $diItem) {
			if ($diItem->isDot() || $diItem->isLink()) {
				continue;
			}

			if ($diItem->isDir()) {
				if ($diItem->isReadable()) {
					$this->_directoryIterator($diItem->getPathname());
				} else {
					$this->_addMessage($this->_helper->__('Directory "%s" not readable', $dir), 'WARNING');
				}

				$check_add = false;

			} elseif ($diItem->isFile() && !in_array(str_replace($this->_baseDir, '', $diItem->getPathname()), $this->_excluded)) {
				if ($diItem->isReadable()) {
					$this->_filesForCompress[] = $diItem->getPathname();
					$check_add = false;
				}
			}
		}

		if ($check_add) {
			$this->_dirsForCompress[] = $dir;
		}

		return true;
	}

	public function prepareFileToUpload($size = self::DEFAULT_FILE_PART_SIZE)
	{
		$size_byte = $size * 1024 * 1024;
		if (!$size_byte || (filesize($this->getFileLocation()) <= $size_byte)) {
			return array(array(
				'filename' => $this->getFileName(),
				'filelocation' => $this->getFileLocation()
			));
		}

		$this->_addMessage($this->_helper->__('Start splitting "%s" file into parts', $this->getFileName()), 'INFO');

		$counter = 0;
		$handle = fopen($this->getFileLocation(), 'r');
		$return = array();
		while (!feof($handle)) {
			$counter++;

			$filename = $this->getFileName(null, $counter);
			$fileloc = $this->getPath() . DS . $this->getFileName(null, $counter);

			$sizeCounter = 0;
			$handle_w = fopen($fileloc, 'a');
			$this->getBackup()->addFilesForDelete($fileloc);
			$wrote = true;
			do {
				$filepart = fread($handle, self::READ_FILE_PART_SIZE);
				if (fwrite($handle_w, $filepart) === false) {
					$wrote = false;
					break;
				}

				$sizeCounter += self::READ_FILE_PART_SIZE;
			} while ($sizeCounter < $size_byte && !feof($handle));

			fclose($handle_w);

			if ($wrote == false) {
				$this->_addMessage($this->_helper->__("Can't write file \"%s\". Split process was stopped.", $fileloc), 'WARNING');
				break;
			} else {
				$return[] = array(
					'filename' => $filename,
					'filelocation' => $fileloc
				);
			}
		}

		fclose($handle);

		$this->setFileParts($counter);

		$this->_addMessage($this->_helper->__('Finish splitting "%s" file into parts', $this->getFileName()), 'INFO');

		return $return;
	}

	public function setBackup($backup)
	{
		$this->_backup = $backup;
		return $this;
	}

	public function getBackup()
	{
		return $this->_backup;
	}

	protected function _addMessage($message, $error = false)
	{
		$this->_helper->addBackupProcessMessage($message, $error);
	}
}