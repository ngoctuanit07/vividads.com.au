<?php
ob_start();
include_once '../app/Mage.php';
Mage::app();
$prodid=$_REQUEST['prodid'];
//echo $prodid;
$_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$prodid);


?>
<span class="im">Product Image:</span>
<img src="<?php echo Mage::helper("catalog/image")->init($_product, 'image')->keepFrame(true)->setQuality(100)->resize(92,114); ?>"  />



