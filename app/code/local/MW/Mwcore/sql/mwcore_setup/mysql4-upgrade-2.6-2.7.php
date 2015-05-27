<?php
$installer = $this;
$installer->startSetup();
$resource =Mage::getSingleton('core/resource');
$installer->run("

Alter TABLE {$resource->getTableName('mwcore/notification')} Add column   `message_id` int(11), 
															Add column `extension_key` varchar(25) NULL,
															Add column   `current_display` smallint(6) NOT NULL default '0', 
															Modify column `time_apply` datetime NULL;
    ");


$installer->endSetup(); 