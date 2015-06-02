<?php 
include_once '../app/Mage.php';
Mage::app();


	$resource = Mage::getSingleton('core/resource');
	$Readconn = $resource->getConnection('core_write');                     
	$tableName = Mage::getSingleton('core/resource')->getTableName('factoryphotos');echo $tableName;
	$SelectSubscriptioId= "ALTER  TABLE ` ".$tableName."`ADD `contents` TEXT NOT NULL";
	$Readconn->query("ALTER  TABLE `factoryphotos` ADD `contents` TEXT NOT NULL");
        
        ?>