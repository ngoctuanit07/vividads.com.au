<?php

$installer = $this;

$installer->startSetup();

$installer->run("


DROP TABLE IF EXISTS {$this->getTable('partial_payment')};
CREATE TABLE IF NOT EXISTS {$this->getTable('partial_payment')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` int(11) NOT NULL,
  `amount` double NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `received_date` datetime NOT NULL,
  `postdate` datetime NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
SET FOREIGN_KEY_CHECKS=1;

    ");

$installer->endSetup(); 