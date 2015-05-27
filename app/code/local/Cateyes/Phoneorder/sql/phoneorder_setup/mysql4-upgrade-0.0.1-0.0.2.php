<?php

$installer = $this;

$installer->startSetup();

$installer->run("
  ALTER TABLE `phoneorder` ADD COLUMN `date` DATETIME NULL AFTER `status`; 
");


$installer->endSetup();