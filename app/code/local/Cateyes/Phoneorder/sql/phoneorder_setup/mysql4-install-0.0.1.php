<?php

$installer = $this;

$installer->startSetup();

$installer->run("
	
	-- DROP TABLE IF EXISTS {$this->getTable('phoneorder')};
	CREATE TABLE {$this->getTable('phoneorder')} (
	`phoneorder_id` int(11) unsigned NOT NULL auto_increment,
	`url` varchar(255) NOT NULL default '',
	`phone` varchar(255) NOT NULL default '',
	`comment` varchar(255) NOT NULL default '',
	`status` set('0','1') NOT NULL DEFAULT '0',
	PRIMARY KEY (`phoneorder_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();