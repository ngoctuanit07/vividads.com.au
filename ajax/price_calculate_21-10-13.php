<?php   
// Now login on MAGENTO
include('../app/Mage.php');
Mage::app();
ini_set('memory_limit','1000M');
ini_set('max_execution_time', 60000);

Mage::getSingleton('core/session', array('name' => 'adminhtml'));

$proId = $_REQUEST['pro_id'];
$Qty = $_REQUEST['qty'];
$width = $_REQUEST['width'];
$Height = $_REQUEST['height'];

//echo "PID: ".$proId."<br>Q: ".$Qty."<br>H".$Height."<br>W: ".$width;
$_productInfo = Mage::getModel('catalog/product')->load($proId);
//echo "<pre>";print_r($_productInfo);
//echo "T: ".$_productInfo->getTierPrice($Qty);

$PricePro = $_productInfo->getPrice();
//$CurrencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
$CurrencySymbol = "$";
if($width >= 1 && $width<=500 || $Height >= 1 && $Height<=500 ){
   $Orgprice = $CurrencySymbol.(number_format($_productInfo->getPrice(),2));
 
} else{
    $Orgprice = 45;
}
echo '<input type="hidden" id="hidden_take_org_price" value="'.$Orgprice.'" />';

$OctPrice = $PricePro*(5/100);
$OctPrice = ($PricePro-$OctPrice);
$OctPriceSymbol = $CurrencySymbol.$OctPrice;
echo '<input type="hidden" id="hidden_take_oct_price" value="'.$OctPriceSymbol.'" />';

$discountPrice = $PricePro*(7/100);
$discountPrice = ($PricePro-$discountPrice);
$discountPriceSymbol = $CurrencySymbol.$discountPrice;
echo '<input type="hidden" id="hidden_take_discount_price" value="'.$discountPriceSymbol.'" />';

$SubTotal = ($PricePro + $OctPrice + $discountPrice);
$SubTotalSymbol = $CurrencySymbol.$SubTotal;
echo '<input type="hidden" id="hidden_subtotal_price" value="'.$SubTotalSymbol.'" />';



?>