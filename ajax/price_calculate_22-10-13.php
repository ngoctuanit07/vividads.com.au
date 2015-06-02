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
//echo $TierPr = $_productInfo->getTierPrice($Qty,$_productInfo);
$PricePro = $_productInfo->getPrice();
$TierPrices = $_productInfo->getTierPrice();

//$CurrencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
$CurrencySymbol ='$';
if($width >= 1 && $width<=500 || $Height >= 1 && $Height<=500 ){
    if($Qty == 1){
           $Orgprice = $CurrencySymbol.(number_format($_productInfo->getPrice(),2));

    } else {
        foreach($TierPrices as $TierPrice){
            $PriceQty = $TierPrice['price_qty'];
            $Priceval = $TierPrice['price'];
                if($Qty == $PriceQty){
                $Orgprice = $CurrencySymbol.$Priceval;
                }
    }

    }
 
} else{
    $Orgprice = 45;
}
echo '<input type="hidden" id="hidden_take_org_price" value="'.$Orgprice.'" />';
 if($Qty > 1){
            foreach($TierPrices as $TierPrice){
            $PriceQty = $TierPrice['price_qty'];
            $Priceval = $TierPrice['price'];
                if($Qty == $PriceQty){
                $PricePro =$Priceval;
                }
    }

 }
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


$TierPrices = $_productInfo->getTierPrice();
//echo "<pre>";print_r($_productInfo->getTierPrice());
if($Qty > 1){
foreach($TierPrices as $TierPrice){
    $PriceQty = $TierPrice['price_qty'];
    $Priceval = $TierPrice['price'];
    if($Qty == $PriceQty){
        echo $Orgprice = $CurrencySymbol.$Priceval;
    }
}
}

?>