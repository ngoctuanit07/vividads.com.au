#!/usr/local/bin/php -q
<?php 
		//ini_set("memory_limit","512M");
//set_time_limit(0);
// read from stdin 
$fd = fopen("php://stdin", "r"); 
$email = ""; 
	while (!feof($fd)) { 
	$email .= fread($fd, 1024); 
	} 
	fclose($fd); 
	
	// handle email 
	
	$lines = explode("\n", $email); 
	
	// empty vars 
	
	$from = ""; 
	$subject = ""; 
	$headers = ""; 
	$message = ""; 
	$splittingheaders = true; 
	var_dump($lines); exit;

?>