<?php
/**
 * Mageplace Backup
 *
 * @category    Mageplace
 * @package        Mageplace_Backup
 * @copyright    Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license        http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Backup_Model_Db extends Mage_Backup_Model_Db
{
	/* @var $_profile Mageplace_Backup_Model_Profile */
	protected $_profile;

	/* @var $_profile Mageplace_Backup_Helper_Data */
	protected $_helper;

	protected $_exclude_tables = array();
	protected $_exclude_tables_rows = array();

	static protected $EXCLUDE_TABLES = array(
		
	);

	static protected $EXCLUDE_TABLES_ROWS = array(
		'mpbackup/backuplog', 'mpbackup/temp', 'core_session'
	);

	public function getExcludedTables()
	{
		$resource = Mage::getSingleton('core/resource');
		
		$exclude_tables = array();
		foreach(self::$EXCLUDE_TABLES as $table) {
			$exclude_tables[] = $resource->getTableName($table);
		}
		
		return array_merge(
			$exclude_tables,
			$this->_exclude_tables
		);
	}

	public function setExcludedTables($exclude_tables)
	{
		if (!is_array($exclude_tables)) {
			$exclude_tables = array();
		}

		$this->_exclude_tables = $exclude_tables;

		return $this;
	}

	public function getExcludedTablesRows()
	{
		$resource = Mage::getSingleton('core/resource');
		
		$exclude_tables_rows = array();
		foreach(self::$EXCLUDE_TABLES_ROWS as $table) {
			$exclude_tables_rows[] = $resource->getTableName($table);
		}
		
		return array_merge(
			$exclude_tables_rows,
			$this->_exclude_tables_rows,
			$this->getExcludedTables()
		);
	}

	public function setExcludedTablesRows($exclude_tables_rows)
	{
		if (!is_array($exclude_tables_rows)) {
			$exclude_tables_rows = array();
		}

		$this->_exclude_tables_rows = $exclude_tables_rows;

		return $this;
	}

	public function startBackup($backupfile, $multi = false, $startTable = '', $startRow = '', $profileId = NULL)
	{
		$this->_helper = Mage::helper('mpbackup');

		$resource = $this->getResource();

		//if (($startTable === 0 && $startRow === 0) || ($startTable === '' && $startRow === '')){
		$backupfile->open(true);
		$resource->beginTransaction();
		//}		


		$tables = $resource->getTables();

		if (($startTable === 0 && $startRow === 0) || ($startTable === '' && $startRow === '')) {
			$backupfile->write($resource->getHeader());
		}

		$excludedTables = $this->getExcludedTables();
		$excludedTablesRows = $this->getExcludedTablesRows();

		if ($multi) {
			$result = $this->multiBackupDb($backupfile, $tables, $resource, $excludedTablesRows, $startTable, $startRow, $profileId);
			if (is_array($result)) {
				return $result;
			}
		} else {
			$this->backupDb($backupfile, $tables, $resource, $excludedTablesRows);
		}

		$backupfile->write($resource->getTableForeignKeysSql());

		$backupfile->write($resource->getFooter());

		$backupfile->close();

		if ($multi) {
			$backupfile->writeGz();
		}

		$resource->commitTransaction();

		return $this;
	}

	protected function backupDb($backupfile, $tables, $resource, $excludedTablesRows)
	{
		foreach ($tables as $table) {
			/*if(in_array($table, $excludedTables)) {
				continue;
			}*/

			$this->_addMessage($this->_helper->__('Start "%s" table backup', $table));

			if (!in_array($table, $excludedTablesRows)) {

				$backupfile->write($resource->getTableHeader($table) . $resource->getTableDropSql($table) . "\n");
				$backupfile->write($resource->getTableCreateSql($table, false) . "\n");

				$tableStatus = $resource->getTableStatus($table);

				if (is_object($tableStatus) && $tableStatus->getRows()) {
					$backupfile->write($resource->getTableDataBeforeSql($table));

					if ($tableStatus->getDataLength() > self::BUFFER_LENGTH) {
						if ($tableStatus->getAvgRowLength() < self::BUFFER_LENGTH) {
							$limit = floor(self::BUFFER_LENGTH / $tableStatus->getAvgRowLength());
							$multiRowsLength = ceil($tableStatus->getRows() / $limit);
						} else {
							$limit = 1;
							$multiRowsLength = $tableStatus->getRows();
						}
					} else {
						$limit = $tableStatus->getRows();
						$multiRowsLength = 1;
					}

					for ($i = 0; $i < $multiRowsLength; $i++) {
						$backupfile->write($resource->getTableDataSql($table, $limit, $i * $limit));
					}

					$backupfile->write($resource->getTableDataAfterSql($table));
				}

				$this->_addMessage($this->_helper->__('Finish "%s" table backup', $table));
			} else {
				$backupfile->write($resource->getTableHeader($table)); 
				$createSql = $resource->getTableCreateSql($table, false) . "\n";
				$createSql = preg_replace('/(CREATE TABLE)(.*+)/is', '$1 IF NOT EXISTS $2', $createSql);
				$backupfile->write($createSql);
				$this->_addMessage($this->_helper->__('Skip "%s" table rows backup', $table));
			}

		}
	}

	protected function multiBackupDb($backupfile, $tables, $resource, $excludedTablesRows, $startTable, $startRow, $profileId)
	{
		$profileMultiTime = Mage::getModel('mpbackup/profile')->load($profileId)->getProfileMultiprocessTime();
		$finishTime = time() + $profileMultiTime;

		for ($startTable; $startTable < count($tables); $startTable++) {

			if ($startRow === 0) {
				$this->_addMessage($this->_helper->__('Start "%s" table backup', $tables[$startTable]));
			}


			if (!in_array($tables[$startTable], $excludedTablesRows)) {

				if ($startRow === 0) {
					$backupfile->write($resource->getTableHeader($tables[$startTable]) . $resource->getTableDropSql($tables[$startTable]) . "\n");
					$backupfile->write($resource->getTableCreateSql($tables[$startTable], false) . "\n");
				}

				$tableStatus = $resource->getTableStatus($tables[$startTable]);

				if ($tableStatus->getRows()) {
					if ($startRow === 0) {
						$backupfile->write($resource->getTableDataBeforeSql($tables[$startTable]));
					}

					$multiRowsLength = $tableStatus->getRows(); //var_dump($multiRowsLength);var_dump($tables[$startTable]);
					for ($startRow; $startRow < $multiRowsLength; $startRow++) {
						$backupfile->write($resource->getTableDataSql($tables[$startTable], 1, $startRow));
						if (time() > $finishTime) {
							return $multiParams = array($startTable, ++$startRow, $backupfile->getData('filename'));
						}
					}

					$backupfile->write($resource->getTableDataAfterSql($tables[$startTable]));
				}

				$this->_addMessage($this->_helper->__('Finish "%s" table backup', $tables[$startTable]));
			} else {
				$backupfile->write($resource->getTableHeader($tables[$startTable]));
				$createSql = $resource->getTableCreateSql($tables[$startTable], false) . "\n";
				$createSql = preg_replace('/(CREATE TABLE)(.*+)/is', '$1 IF NOT EXISTS $2', $createSql);
				$backupfile->write($createSql);
				$this->_addMessage($this->_helper->__('Skip "%s" table rows backup', $tables[$startTable]));
			}

			$startRow = 0;

		}
	}

	public function getResource()
	{

		return Mage::getModel('mpbackup/interceptor');
	}

	protected function _addMessage($message, $error = false)
	{
		$this->_helper->addBackupProcessMessage($message, $error);
	}
}