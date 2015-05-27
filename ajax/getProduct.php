<?php
ob_start();
include_once '../app/Mage.php';
$store= Mage::app()->getStore($_REQUEST['storeid']);

Mage::app($store->getCode());
$catid=$_REQUEST['catid'];
//echo 'ajax';
$category = Mage::getModel('catalog/category')->setStore($_REQUEST['storeid'])->setStoreId($_REQUEST['storeid'])->load($catid);
$products = $category->getProductCollection()->addCategoryFilter($category)
        ->addAttributeToFilter('status', 1)
        ->addAttributeToFilter('visibility', 4);

//echo count($products);


//$_product = Mage::getModel('catalog/product')->load($prod_id);


?>
<span class="lab">
    Products:
</span>
<span class="inp">
    <select name="products[]" class="prod" onchange="getProdImage(this.value,this);">
    
        <option value="">Select a product</option>
    <?php
    foreach($products as $_product){
              
          
    $_product1 = Mage::getModel('catalog/product')->setStore($_REQUEST['storeid'])->setStoreId($_REQUEST['storeid'])->load($_product->getId());
     if($_product1->getStatus() == 1)
     {
    ?>
    
        <option value="<?php echo $_product1->getSku();?>"><?php echo $_product1->getName(); ?></option>
    
    
    <?php
     }
              
              
              
    }
    
    
    
    
    ?>
    
    </select>
</span>