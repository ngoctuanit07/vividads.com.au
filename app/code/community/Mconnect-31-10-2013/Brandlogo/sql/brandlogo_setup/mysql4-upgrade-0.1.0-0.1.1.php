<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('brandlogo_store')};
CREATE TABLE {$this->getTable('brandlogo_store')} (
  `brandlogo_id` int(11) unsigned NOT NULL,
  `store_id` int(6) NOT NULL  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();