<?php require 'app/Mage.php';
Mage::app('admin')->setUseSessionInUrl(false);                                                                                                                 
//replace your own orders numbers here:
$test_order_ids=array(
  '517613754',


  

);
foreach($test_order_ids as $id){
    try{
        Mage::getModel('sales/order')->loadByIncrementId($id)->delete();
        echo "order #".$id." is removed".PHP_EOL;
		'<script type="text/javascript">
		alert("echo "order #".$id." is removed".PHP_EOL;");
		</script>';
    }catch(Exception $e){
        echo "order #".$id." could not be remvoved: ".$e->getMessage().PHP_EOL;
    }
}
echo "complete."

?>