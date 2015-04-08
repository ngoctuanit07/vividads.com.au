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
        $PriceSq = number_format($_productInfo->getPriceSqFeet(),2);
        $CountDimension = $width * $Height;
        
        $ReacConn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $PricerangetableName = Mage::getSingleton('core/resource')->getTableName('putrange');
        $SelSql = "SELECT discount FROM $PricerangetableName WHERE $CountDimension BETWEEN `from` AND `to`";
        //SELECT discount FROM putrange WHERE 40 BETWEEN `from` AND `to`
        try {
                $chkSystem = $ReacConn->query($SelSql);
                $fetchall = $chkSystem->fetch();
        } catch (Exception $e){
                echo $e->getMessage();
        }
        $Discount = $fetchall['discount'];
        $incluePrice = ($CountDimension * $PriceSq);
        $Orgprice1 = ($incluePrice * $Discount)/100;
        $Orgprice = $Orgprice1 * $Qty;
        //$CurrencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        $CurrencySymbol = '$';
        echo '<input type="hidden" id="hidden_take_org_price" value="'.$CurrencySymbol.$Orgprice.'" />';
        $OctPrice = $Orgprice1*(5/100);
        $OctPrice1 = ($Orgprice1-$OctPrice);
        $OctPrice = $OctPrice1 * $Qty;
        $OctPriceSymbol = $CurrencySymbol.$OctPrice;
        echo '<input type="hidden" id="hidden_take_oct_price" value="'.$OctPriceSymbol.'" />';
        
        $discountPrice = $Orgprice1*(7/100);
        $discountPrice1 = ($Orgprice1-$discountPrice);
        $discountPrice = $discountPrice1 * $Qty;
        $discountPriceSymbol = $CurrencySymbol.$discountPrice;
        echo '<input type="hidden" id="hidden_take_discount_price" value="'.$discountPriceSymbol.'" />';
        
        $SubTotal = ($Orgprice + $OctPrice + $discountPrice);
        $SubTotalSymbol = $CurrencySymbol.$SubTotal;
        echo '<input type="hidden" id="hidden_subtotal_price" value="'.$SubTotalSymbol.'" />';


?>