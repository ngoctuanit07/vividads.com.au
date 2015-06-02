<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('testimonial_store')};
CREATE TABLE {$this->getTable('testimonial_store')} (
  `testimonial_id` int(11) unsigned NOT NULL,
  `store_id` int(6) NOT NULL  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();