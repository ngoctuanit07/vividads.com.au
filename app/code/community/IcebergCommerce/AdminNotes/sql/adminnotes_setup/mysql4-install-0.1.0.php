<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_AdminNotes
 * @copyright  Copyright (c) 2010 Iceberg Commerce
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

  DROP TABLE IF EXISTS {$this->getTable('adminnotes/note')};
CREATE TABLE {$this->getTable('adminnotes/note')} (
  `note_id` int(11) unsigned NOT NULL auto_increment,
  `path_id` varchar(255) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  `title` varchar(60) NOT NULL default '',
  `note` text NOT NULL default '',
  `created_by` int(11) unsigned NOT NULL default 0,
  `created_at` datetime default NULL,
  `type` VARCHAR(50) NOT NULL default 'note',
  PRIMARY KEY (`note_id`),
  KEY `path` (`path_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('adminnotes/note_user_relation')};
CREATE TABLE {$this->getTable('adminnotes/note_user_relation')} (
  `note_id` int(11) unsigned NOT NULL default 0,
  `user_id` int(11) unsigned NOT NULL default 0,
  `status` int(4) NOT NULL default 0,
  PRIMARY KEY (`note_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();

