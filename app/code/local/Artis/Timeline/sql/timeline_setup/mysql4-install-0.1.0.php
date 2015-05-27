<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('timeline')};
CREATE TABLE {$this->getTable('timeline')} (
  `timeline_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`timeline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('product_timeline')};
CREATE TABLE IF NOT EXISTS {$this->getTable('product_timeline')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `artwork_day` int(11) NOT NULL,
  `proof_day` int(11) NOT NULL,
  `production_day` int(11) NOT NULL,
  `delivary_day` int(11) NOT NULL,
  `shipping_day` int(11) NOT NULL,
  `sunday_artwork` int(11) NOT NULL,
  `holiday_artwork` int(11) NOT NULL,
  `sunday_proof` int(11) NOT NULL,
  `holiday_proof` int(11) NOT NULL,
  `sunday_production` int(11) NOT NULL,
  `holiday_production` int(11) NOT NULL,
  `sunday_delivary` int(11) NOT NULL,
  `holiday_delivary` int(11) NOT NULL,
  `sunday_shipping` int(11) NOT NULL,
  `holiday_shipping` int(11) NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS {$this->getTable('subadmin_task_number')};
CREATE TABLE IF NOT EXISTS {$this->getTable('subadmin_task_number')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_number` int(11) NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

    ");

$installer->endSetup(); 