<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('imageoption')} 
ADD `qty` int(11) NULL ;
  
 ");

$installer->endSetup(); 