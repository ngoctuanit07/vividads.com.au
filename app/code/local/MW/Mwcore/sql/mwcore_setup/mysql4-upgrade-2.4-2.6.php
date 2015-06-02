<?php
$installer = $this;
$installer->startSetup();
$resource =Mage::getSingleton('core/resource');
$installer->run("
DROP TABLE IF EXISTS {$resource->getTableName('mwcore/notification')};
CREATE TABLE {$resource->getTableName('mwcore/notification')} (
  `notification_id` int(11) unsigned NOT NULL auto_increment,
  `type` varchar(25) NOT NULL default '', 
  `message` text NOT NULL default '',
  `time_apply` smallint(6) NOT NULL default '1', 
  `status` smallint(6) NOT NULL default '0',   
  
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    ");
$installer->endSetup(); 
