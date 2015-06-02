<?php   
// Now login on MAGENTO
include('../app/Mage.php');
Mage::app();
extract($_REQUEST);
 
$zipcode = '2000';
$country = 'AU';

// Update the cart's quote.
$cart = Mage::getSingleton('checkout/cart');
$address = $cart->getQuote()->getShippingAddress();
$address->setCountryId($country)
        ->setPostcode($zipcode)
        ->setCollectShippingrates(true);
$cart->save();

// Find if our shipping has been included.
$rates = $address->collectShippingRates()
                 ->getGroupedAllShippingRates();

foreach ($rates as $carrier) {
    foreach ($carrier as $rate) {
       // print_r($rate->getData());
        $allData = $rate->getData();
        echo '<input type="radio" name="ship_method" id="'.$allData['code'].'" value="'.$allData['code'].'"/>'. $allData['carrier_title'].' '.$allData['price'];
    }
}

?>