<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('vendorload')};
CREATE TABLE {$this->getTable('vendorload')} (
  `vendorload_id` int(11) unsigned NOT NULL auto_increment,
  `admin_id` int(11) NOT NULL ,
  `attribute_id` int(11) NOT NULL,
  `load` text NOT NULL ,
  PRIMARY KEY (`vendorload_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
