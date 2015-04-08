<?php
// Now login on MAGENTO
include('../app/Mage.php');

extract($_REQUEST);
Mage::app($store_code);

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
foreach($confirmproduct as $k=>$v){
          echo $confirmproduct[$k];
                $_product = Mage::getModel('catalog/product')->load($confirmproduct[$k]);
                
                echo $_product->getName();
                
                if($_product->getLength() != '')
                {
                        $_product->setStoreId($store_id)->setData('length',$confirmlength[$k])->save();
                }
                
                if($_product->getWidth() != '')
                {
                        $_product->setStoreId($store_id)->setData('width',$confirmwidth[$k])->save();
                }
                
                if($_product->getHeight() != '')
                {
                        $_product->setStoreId($store_id)->setData('height',$confirmheight[$k])->save();
                }
                
                if($_product->getWeight() != '')
                {
                        $_product->setStoreId($store_id)->setData('weight',$confirmweight[$k])->save();
                }
                 echo $_product->getHeight();
               
       
}
?>