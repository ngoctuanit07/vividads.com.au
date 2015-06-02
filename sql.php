<?php 
include_once '../../app/Mage.php';
Mage::app();


	$resource = Mage::getSingleton('core/resource');
	$Readconn = $resource->getConnection('core_write');                     
	$tableName = Mage::getSingleton('core/resource')->getTableName('factoryphotos');
	$SelectSubscriptioId= "ALTER  TABLE ` ".$tableName."` ADD `contents` TEXT NOT NULL";
	$SelectSubscriptioId->query();
        
        ?>