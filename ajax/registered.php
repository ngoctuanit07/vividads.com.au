<?php   
// Now login on MAGENTO
include('../app/Mage.php');
Mage::app();
extract($_REQUEST);
 
$customer = Mage::getModel('customer/customer');
    if (Mage::app()->getWebsite()->getId()) {
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
    }
    
    $customer->loadByEmail($email);
    if ($customer->getId()) {
        echo 'This email is already exist1.';
    }
    

?>
