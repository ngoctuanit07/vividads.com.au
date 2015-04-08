<?php   
// Now login on MAGENTO
include('../app/Mage.php');
Mage::app();

Mage::getSingleton('core/session', array('name' => 'adminhtml'));
$proId = $_REQUEST['p_id'];
$qty = $_REQUEST['qty'];
$widthquote = $_REQUEST['w_q'];
$heightquote = $_REQUEST['h_q'];
$subtotalquote = $_REQUEST['sub_q'];//echo $subtotalquote;
$finish = $_REQUEST['finish'];
$sweing = $_REQUEST['finisoption'];


//echo "R: ".$proId."<br>".$qty."<br>".$widthquote."<br>".$heightquote."<br>".$subtotalquote;
$_productInfo = Mage::getModel('catalog/product')->load($proId);
$Pname = $_productInfo->getName();
$Totaldetails= $widthquote." X".$heightquote." ".$Pname;
//echo '<li><div class="bin_image">
//	    <img class="bin-icon" alt="Bin / Delete" src=" http://192.168.0.55/tablethrows/skin/frontend/default/durabanners/images/calculator/bin.gif">	
//	  </div>';
////echo '<input type="hidden" id="hidden_take_qtyquote" value="'.$qty.'" />';
////echo '<input type="hidden" id="hidden_take_prodetailsquote" value="'.$Totaldetails.'" />';
////echo '<input type="hidden" id="hidden_take_subdetail" value="'.$subtotalquote.'" />';
////echo '<input type="hidden" id="hidden_take_crossimg" value="X" />';
//echo '<div class="qty_cal"><span class="txt_q">'.$qty.'</span></div><div class="qty_cal"><span class="txt_q">'.$Totaldetails.'</span></div>X';
//echo '</li>';
$resource = Mage::getSingleton('core/resource');
	$Readconn = $resource->getConnection('core_write');                     
	$tableName = Mage::getSingleton('core/resource')->getTableName('calculator');
	/*$SelectSubscriptioId= "SELECT * FROM ` ".$tableName."` WHERE session_id='".$sessionId."'";
	$rows = $Readconn->fetchAll($SelectSubscriptioId);*///print_r($rows);
	//if(count($rows)==0)
	{
	//echo "hello";
        $sessionId=$_REQUEST['sessionId'];//echo $finish;
        $sql="insert into `".$tableName."` (session_id,Width,Height,Sewing,Finish,proid,qty,subtotal) values ('".$sessionId."',$widthquote,$heightquote,'".$sweing."','".$finish."',$proId,$qty,'".$subtotalquote."')";

	$Readconn->query("insert into `".$tableName."` (session_id,Width,Height,Sewing,Finish,proid,qty,subtotal) values ('".$sessionId."',$widthquote,$heightquote,'".$sweing."','".$finish."',$proId,$qty,'".$subtotalquote."')");
        }
        $SelectSubscriptioId= "SELECT * FROM `".$tableName."` WHERE session_id='".$sessionId."'";
	$rows = $Readconn->fetchAll($SelectSubscriptioId);//print_r($rows);?>
        <ul class="quote">
				<li class="quoteli_main">
					<div class="bin_image_main">
						<img class="bin-icon" alt="Bin / Delete" src="http://192.168.0.55/tablethrows/skin/frontend/default/durabanners/images/calculator/bin.gif">	
					</div>
					<div class="qty_cal_main">
						<span class="txt_q"><b>Qty</b></span>
					</div>
					<div class="itam_cal_main">
						<span class="txt_item"><b>Item</b></span>
					</div>

					<div class="price_aud_main">
						<span class="txt_price"><b>Price AUD</b></span>
					</div>
				</li>
        <?php foreach($rows as $row)
        {?>
        			
				<li class="quoteli">
					<div class="bin_image">
						<img class="bin-icon" alt="<?php echo $row['calculator_id'];?>" src="http://mesh-banners.com.au//skin/frontend/default/meshbanners/images/calculator/removex.png">	
					</div>
					<div class="qty_cal">
						<span class="txt_q"><b><?php echo $row['qty'];
                                                ?></b></span>
					</div>
					<div class="itam_cal">
						<span class="txt_item"><b><?php echo $row['Width'].'mm X '.$row['Height'].'mm '.$Pname.' </b></span><span class="txt_item_custom">'.$row['Finish'].'</span>'.'<span class="txt_item_sweing">Sweing:'.$row['Sewing'].'</span>';
                                                ?>
                                                <span class="links">
                                                <a title="Download Info Sheet" class="dwninfotmpt" href="#">1. Info Sheet<span class="itemind hide">1</span></a>
                                                <a title="Download Easy Template" class="dwntemplt" href="#">2. Easy Template<span class="dwnlinkindex hide">0</span></a></span>
					</div>

					<div class="price_aud">
						<span class="txt_price"><b><?php echo $row['subtotal']?></b></span>
					</div>
				</li>

            <?php //echo $row['Sewing'].$row['session_id'];
        }?>
        <li class="item_added">
					<div class="bin_image" id="crossimg">&nbsp;
					</div>
					<div class="qty_cal" id="qty_id">&nbsp;
					</div>
					<div class="itam_cal" id="pro_quote">
						<span class="txt_item whole_txt"><b>Fill in details above and click "Add to Quote"</b></span>
					</div>

					<div class="price_aud" id="total_sub_quote">
					&nbsp;
					</div>
				</li>

				
			</ul>



<?php 
?>
