<?php
/**
 * Mageplace Backup
 *
 * @category	Mageplace
 * @package	 Mageplace_Backup
 * @copyright   Copyright (c) 2013 Mageplace. (http://www.mageplace.com)
 * @license	 http://www.mageplace.com/disclaimer.html
 */
 
class Mageplace_Backup_Helper_Const
{
	const CRON_SCHEDULES_RUN_LIFETIME_CYCLE = 360; //One week - 6*60 minutes
	const STATUS_PENDING = 'pending';
	const STATUS_RUNNING = 'running';
	const STATUS_SUCCESS = 'success';
	const STATUS_MISSED = 'missed';
	const STATUS_ERROR = 'error';
}