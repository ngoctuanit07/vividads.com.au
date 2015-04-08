<?php   
// Now login on MAGENTO
include('../app/Mage.php');
Mage::app();
extract($_REQUEST);
 
$tableName = Mage::getSingleton('core/resource')->getTableName('proofs');

$tableName = Mage::getSingleton('core/resource')->getTableName('proofs');              
$sqlProofsSystem="SELECT * FROM ".$tableName." WHERE entity_id = '".$proof_id."'";
$chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlProofsSystem);

if($proof_type == 'quote')
{
$tableItemName = Mage::getSingleton('core/resource')->getTableName('quotation_items');
$sqlItemSystem="SELECT * FROM ".$tableItemName."  WHERE quotation_item_id = '".$chkSystem[0]['item_id']."'";
$chkItem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlItemSystem);
}
else if($proof_type == 'order'){
     $tableItemName = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
    $sqlItemSystem="SELECT * FROM ".$tableItemName."  WHERE item_id = '".$chkSystem[0]['item_id']."'";
    $chkItem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlItemSystem);
}
$fetchItem = $chkItem->fetch();

if($qty != '')
{echo $fetchItem['qty'];
    if($fetchItem['qty'] >= $qty)
    {
        $sqlProofsSystem="UPDATE ".$tableName."  SET  quantity = '".$qty."' WHERE entity_id = '".$proof_id."'";
        $chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);
    }
}

$sqlProofsSystem="UPDATE ".$tableName."  SET  status = '".$status."', comment = '".$comment."', approve_date=NOW() WHERE entity_id = '".$proof_id."'  AND quantity != 0";
$chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlProofsSystem);


$tableName = Mage::getSingleton('core/resource')->getTableName('proofs');              
$sqlProofsSystem="SELECT * FROM ".$tableName." WHERE entity_id = '".$proof_id."'";
$chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlProofsSystem);


if($proof_type == 'quote')
{
    $tableNamePlanner = Mage::getSingleton('core/resource')->getTableName('quote_planning');              
    $sqlPlannerSystem="SELECT * FROM ".$tableNamePlanner."   WHERE quote_id = '".$chkSystem[0]['order_id']."' AND item_id = '".$chkSystem[0]['item_id']."' AND planning_type = 'quote' ";
    $chkSystemPlanner = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlPlannerSystem);
   
     $_product = Mage::getModel('catalog/product')->load($chkSystemPlanner[0]['product_id']);
     $today = date('Y-m-d');
     $approval_date = $chkSystemPlanner[0]['proof_date'];
     
     if($approval_date<=$today and $status == 'Disapproved')
     {
        $artwork_date = date ( 'Y-m-j', strtotime ( '+'.$_product->getArtworkDelay().' day' . $today ) );
        $proof_date = date ( 'Y-m-j', strtotime ( '+'.$_product->getProofDelay().' day' . $today ) );
        $production_start_date = date ( 'Y-m-j', strtotime ( '+'.$_product->getProductionDelay().' day' . $today ) );
        $shipping_date = date ( 'Y-m-j', strtotime ( '+'.$_product->getShippingDelay().' day' . $today ) );
        $delivery_date = date ( 'Y-m-j', strtotime ( '+'.$_product->getDeliveryDelay().' day' . $today ) );
     
     
    $temptablePlanning=Mage::getSingleton('core/resource')->getTableName('quote_planning');
    $sqlPlanning="UPDATE  ".$temptablePlanning." SET  artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' WHERE quote_id = '".$chkSystem[0]['order_id']."' AND item_id = '".$chkSystem[0]['item_id']."' AND planning_type = 'quote'";
    $chkPlanning = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlPlanning);
    }
}
else if($proof_type == 'order')
{
    $tableNamePlanner = Mage::getSingleton('core/resource')->getTableName('quote_planning');              
    $sqlPlannerSystem="SELECT * FROM ".$tableNamePlanner."   WHERE quote_id = '".$chkSystem[0]['order_id']."' AND item_id = '".$chkSystem[0]['item_id']."' AND planning_type = 'order' ";
    $chkSystemPlanner = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlPlannerSystem);
    
     $_product = Mage::getModel('catalog/product')->load($chkSystemPlanner[0]['product_id']);
     $today = date('Y-m-d');
     $approval_date = $chkSystemPlanner[0]['proof_date'];
     
     if($approval_date>=$today and $status == 'Disapproved')
     {
        $artwork_date = date ( 'Y-m-j', strtotime ( '+'.$_product->getArtworkDelay().' day' . $today ) );
        $proof_date = date ( 'Y-m-j', strtotime ( '+'.$_product->getProofDelay().' day' . $today ) );
        $production_start_date = date ( 'Y-m-j', strtotime ( '+'.$_product->getProductionDelay().' day' . $today ) );
        $shipping_date = date ( 'Y-m-j', strtotime ( '+'.$_product->getShippingDelay().' day' . $today ) );
        $delivery_date = date ( 'Y-m-j', strtotime ( '+'.$_product->getDeliveryDelay().' day' . $today ) );
     
     
    $temptablePlanning=Mage::getSingleton('core/resource')->getTableName('quote_planning');
    $sqlPlanning="UPDATE  ".$temptablePlanning." SET  artwork_date = '$artwork_date', proof_date = '$proof_date', start_date ='$production_start_date', shipping_date = '$shipping_date', delivery_date = '$delivery_date' WHERE quote_id = '".$chkSystem[0]['order_id']."' AND item_id = '".$chkSystem[0]['item_id']."' AND planning_type = 'order'";
    $chkPlanning = Mage::getSingleton('core/resource')->getConnection('core_read')->query($sqlPlanning);
    }
}

?>