<?php   
error_reporting(E_ALL);
// Now login on MAGENTO
include('../app/Mage.php');
Mage::app();

$input=Array ( "status" => "None", "comment" => "aaaaa", "entity_name" => "store_id" => 4, "parent_id" => 9 )
 
$customer = Mage::getModel('Quotation/Statushistory')->getCollection();
if($customer)
	   $customer->addItem($input); 
else 
	echo "error";

?>
