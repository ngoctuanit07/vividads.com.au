<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('gallery')};
CREATE TABLE {$this->getTable('gallery')} (
  `gallery_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `model_no` varchar(25) DEFAULT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `imagefilename` varchar(255) DEFAULT '',
  `content` text NOT NULL,
  `city` varchar(25) DEFAULT '',
  `country` varchar(25) DEFAULT '',
  `tags` varchar(255) DEFAULT '',
  `meta_keyword` varchar(255) DEFAULT '',
  `meta_description` varchar(255) DEFAULT '',
  `status` smallint(6) NOT NULL DEFAULT '0',
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`gallery_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8
    ");
	
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('category')};
CREATE TABLE {$this->getTable('category')} (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `imagefilename` varchar(255) DEFAULT '',
  `content` text NOT NULL,
  `bottom_content` text,
  `meta_keyword` varchar(255) DEFAULT '',
  `meta_description` varchar(255) DEFAULT '',
  `status` smallint(6) NOT NULL DEFAULT '0',
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8
    ");
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('category_store')};
CREATE TABLE {$this->getTable('category_store')} (
  `category_id` int(11) unsigned NOT NULL,
  `store_id` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8


    ");

$installer->endSetup(); 