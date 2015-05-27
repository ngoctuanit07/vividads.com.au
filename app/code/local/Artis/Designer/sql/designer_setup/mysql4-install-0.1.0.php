<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('designer')};
CREATE TABLE {$this->getTable('designer')} (
  `designer_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`designer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('task_designer_comment')};
CREATE TABLE IF NOT EXISTS {$this->getTable('task_designer_comment')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `user_id` INT NOT NULL,
  `user_type` VARCHAR( 255 ) NOT NULL,
  `postdate` datetime NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS {$this->getTable('task_designer')};
CREATE TABLE IF NOT EXISTS {$this->getTable('task_designer')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `order_quote_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `item_id` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `postdate` datetime NOT NULL,
  `approve_date` datetime NOT NULL,
  `proof_type` varchar(255) NOT NULL,
  `file_size`  float,
  `file_type`  varchar(20),

  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS {$this->getTable('design_service')};
CREATE TABLE IF NOT EXISTS {$this->getTable('design_service')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `type` VARCHAR( 255 ) NOT NULL,
  `revision_number` int(11) NOT NULL,
  `assign_to` INT NOT NULL,
  `postdate` datetime NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS {$this->getTable('catalog_product_designer')};
CREATE TABLE IF NOT EXISTS {$this->getTable('catalog_product_designer')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

    ");

$installer->endSetup(); 