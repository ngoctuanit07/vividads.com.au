<?php

class Partialshipping_Partialshipping_Model_Partialshipping extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('partialshipping/partialshipping');
    }
    
    public function XML2Array(SimpleXMLElement $parent){
	    $array = array();
	
	    foreach ($parent as $name => $element) {
		($node = & $array[$name])
		    && (1 === count($node) ? $node = array($node) : 1)
		    && $node = & $node[];
	
		$node = $element->count() ? $this->XML2Array($element) : trim($element);
	    }
	
	    return $array;
    }
    
    
    public function getTNTlebal($box,$conNoteNumber,$addressLoadId,$request,$method,$shipId,$invIncrementID='',$order)
    {
        extract($request);
       
        /////4-3-2014 S
	$shM=explode("__",$method);
	$shMethod=$shM[0];
	$shPrice=$shM[1];
	/////4-3-2014 E
	
	//11-3-2014 S
	$pos1 = strpos($shMethod, 'TNT');
	if($pos1 !== false) {
	    
	    $shMethod = str_replace("TNT"," ",$shMethod);
	    $shMethod = trim($shMethod);
	    
	}
	//11-3-2014 E
	
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $collection = Mage::getModel('sales/order_shipment_comment')->getCollection()->addFieldToFilter('parent_id',array('eq' => $shipId));
        foreach($collection as $_collection){
                $commentId = $_collection->getId(); 	
                $commentModel = Mage::getModel('sales/order_shipment_comment')->load($commentId);
                $comment = $commentModel->getComment();
        }
        
        $style = array(
                        'position' => '',
                        'align' => 'C',
                        'stretch' => false,
                        'fitwidth' => true,
                        'cellfitalign' => '',
                        'border' => true,
                        'hpadding' => 'auto',
                        'vpadding' => 'auto',
                        'fgcolor' => array(0,0,0),
                        'bgcolor' => false, //array(255,255,255),
                        'text' => true,
                        'font' => 'helvetica',
                        'fontsize' => 8,
                        'stretchtext' => 4					
                );
        require_once('tcpdf/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetFont('helvetica', '', 8);
        
        $postCode = $addressLoadId['postcode'];
        $city = $addressLoadId['city'];
        $firstName = $addressLoadId['firstname'];
        $lastName = $addressLoadId['lastname'];
        $region = $addressLoadId['region'];
        $regionId = $addressLoadId['region_id'];
        
        $regionModel = Mage::getModel('directory/region')->load($regionId);
        $regCode=$regionModel->getCode();
        
        $street = $addressLoadId['street'];
        $telephone = $addressLoadId['telephone'];
        $company = $addressLoadId['company'];
        $country_code=$addressLoadId['country_id'];
        
        $countryModel = Mage::getModel('directory/country')->loadByCode($country_code);
        $destCountry = $countryModel->getName();
        
        $vivid['identifier']='VIVID';
        $vivid['account']='21664906';
        $vivid['company']='VIVID ADS';
        $vivid['address1'] ='302 BRIDGE STREET';
        $vivid['address2']='';
        $vivid['city']='PORT MELBOURNE' ;
        $vivid['state']='VIC';
        $vivid['zip']='3207';
        $vivid['name']='DESPATCH';
        $vivid['email']="support@vividads.com.au";
        $date = date('d-m-Y');
        
        if($company) {$cmp=",<br>".$company; $cmp1=",".$company;} else{$cmp='';$cmp1='';}
        
        $status = $order->getStatus();
        $subtotal = $order->getSubtotal();
        $totalamt = $order->getGrandTotal();			
        $totalDues = $order->getTotalDue();
        $paid = $totalamt-$totalDues;
        if($paid < $totalamt)					 
                $stamp_url=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."tnt/NOT PAID.gif";				
        if($paid > 0 && $paid == $totalamt)								
                $stamp_url=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."tnt/Paid.gif";
        if($paid > 0 && $paid < $totalamt)								
                $stamp_url=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."tnt/Partial Paid.gif";
        
        
        $b=$l=0;
        $box = explode('@@@@',$box);
        $total = count($box)-1;
	
        foreach($box as $q=>$boxvalue)
        {	$b++; ///24-2-2014
        
                if($b > 1){ ///24-2-2014
                
                $boxdata = explode('__',$boxvalue);
               
                //26-2-2014 S	
                $descrp = "BOX";
                $pad_string = " ";
                $descrp = str_pad($descrp, strlen($descrp)+23, $pad_string, STR_PAD_RIGHT);
                //26-2-2014 E	
                foreach($boxdata as $boxdataall)
                {
                        $boxdatain = explode(':',$boxdataall);
                        $boxdata_item[$boxdatain[0]] = $boxdatain[1];
                }
                               
                //for($j ; $j<=$boxdata_item['box'] ; $j++){
                
                        
                        //$conNoteNumber="VVD000055714";
                        $conNote=str_replace("VVD","00313113",$conNoteNumber);// It wil be dynamic
                        $barcode = "6104".$conNote.(str_pad($q,3,"0",STR_PAD_LEFT))."0".(str_pad($postCode,5,"0",STR_PAD_RIGHT));						   
                        $params = TCPDF_STATIC::serializeTCPDFtagParameters(array($barcode, 'C128', '', '', 80, 30, 0.4, array('position'=>'S', 'border'=>false, 'padding'=>4, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4), 'N'));
                        //$itemno = (str_pad($q,3,"0",STR_PAD_LEFT)) . $conNote . (str_pad($j,3,"0",STR_PAD_LEFT)); ///4-3-32014
			
			$itemno = $conNote . (str_pad($j,3,"0",STR_PAD_LEFT)) . (str_pad($q,3,"0",STR_PAD_LEFT));
			
			
                        //$itemno =  $key;	
                        //$pdf->AddPage("P","A4");		
        $tbl[] = <<<EOD
        <table width="240" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
        <td colspan="1" align="left" ><table width="100%" border="" align="center" cellpadding="0" cellspacing="0">
        <tr>
        <td width="50%" style="border-bottom:#000 2px solid"><table  border="0" align="left" cellpadding="0" cellspacing="0" height="10px">
        <tr>
        <td align="left" valign="top"><span style="line-height:0.8em;font-size:38;font-weight:bold">{$postCode}</span>
        <span style="font-size:13;font-weight:bold">{$city} </span></td>
        </tr>
        </table></td>
        <td  align="right" style="border-bottom:#000 2px solid"><table border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td align="right" style="font-size:15"><strong>LV3</strong></td>
        </tr>
        <tr>
        <td align="right" style="font-size:13"><strong>{$conNoteNumber}</strong></td>
        </tr>
        <tr>
        <td align="right" valign="bottom" style="font-size:8">Itm:$itemno </td>
        </tr>
        </table></td>
        </tr>
        <tr>
        <td style="font-size:13"><strong>{$shMethod}</strong></td>
        <td align="right">
        <table border="0" cellspacing="0" cellpadding="0" align="right">
        <tr>
        <td style="font-size:7">Sort</td>
        <td rowspan="2" style="font-size:13"><strong>30015</strong></td>
        </tr>
        <tr>
        <td style="font-size:7">Bin:</td>
        </tr>
        </table>
        </td>
        </tr>
        <tr>
        <td colspan="2"  style="border-top:#000 2px solid"><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td align="left" style="font-size:9">$date</td>
        <td align="center" style="font-size:9">{$q} of {$total}</td>
        <td align="center"style="font-size:9">Item Wt:{$boxdata_item['weight']} Kg</td>
        <td align="right" style="font-size:9">Ex LV3</td>
        </tr>
        </table></td>
        </tr>
        <tr>
        <td colspan="2" align="center" style="border-bottom:#000 2px solid;border-top:#000 2px solid;font-size:9">Does not contain any dangerous goods</td>
        </tr>
        <tr>
        <td colspan="2" align="left"  >
        <table width="100%" border="0" align="left">
        <tr>
        <td valign="top" style="font-size:9" width="20">To:</td>
        <td style="font-size:14;font-weight:bold;" width="220" height="80">{$firstName} {$lastName} {$cmp},<br/> {$street} , {$city}, {$region}<br/> {$destCountry}<br/></td>
        </tr>
        </table>
        </td>
        </tr>
        <tr>
        <td valign="top" width="30" style="border-top:#000 2px solid;font-size:8">From:</td>
        <td width="220" style="border-top:#000 2px solid;font-size:7">{$vivid['company']}, {$vivid['address1']}, {$vivid['city']} {$vivid['state']}<br>
        Senders Ref: {$invIncrementID}</td>
        </tr>
        <tr> <td colspan="2"></td></tr>
        <tr>
        <td colspan="2" >
        <table border="0" width="230" align="center" cellpadding="0" cellspacing="0" >
        <tr>
        <td colspan="2" align="left" style="font-size:7" height="20">
        <strong>Special Instructions: {$comment}</strong>
        </td>
        </tr>
        <tr>
        <td  width="50%" valign="top" height="75" style="line-height:0.9;border:#000 2px solid;font-size:7;" align="left"><strong>CN:{$conNoteNumber}<br>Itm:{$itemno}<br>{$q} of {$total}<br>TO:</strong><BR>{$firstName} {$lastName} {$cmp1}, {$street}, {$city}, {$region} {$postcode} ,{$destCountry}</td>
        <td width="50%" valign="top" align="left" style="line-height:0.9;border-bottom:#000 2px solid; border-right:#000 2px solid;font-size:6;border-top:#000 2px solid"><strong>Service Title here<br>Con Note Wt.: {$boxdata_item['weight']} Kg.<br><br>FROM:</span><BR>{$vivid['company']}, {$vivid['address1']}, {$vivid['city']} {$vivid['state']} {$vivid['zip']}</td></tr>
        
        <tr><td colspan="2"></td></tr>       
        <tr>
        <td colspan="2"><br><tcpdf method="write1DBarcode" params="{$params}" /></td>
        </tr>
        </table>
        </td></tr>
        </table>
        </td>
        </tr>
        </table>
EOD;
        
        
$paidstamp=<<<EOD
        <table width="240" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr><td align="left">Company:</td><td align="left" ><strong> {$company}</strong></td></tr>
        <tr><td align="left">Name:</td><td align="left" ><strong> {$firstName} {$lastName}</strong></td></tr>
        <tr><td align="left">Phone:</td><td align="left" ><strong> {$telephone}</strong></td></tr>
        <tr><td align="left"><h1 style="font-size:28">{$invoicecode}</h1></td></tr>
        <tr><td align="left" colspan="2"><h1 style="font-size:28">{$invIncrementID}</h1></td></tr>
        <tr><td align="left"><img width="100" height="100" border="0" src="{$stamp_url}"></td></tr>
        <tr><td align="left"><h1 style="font-size:35">{$pickup}{$urgent}</h1></td></tr></table>
EOD;
        
        
        
        
        
        
                    if($counter==1)
                    {
                    
                    
                        $order_items=$order->getAllItems();
                        																
                        foreach($order_items as $orderDetails)
                        {
                            $prodid=$orderDetails->getProductId();
                            $product = Mage::getModel('catalog/product')->load($prodid);															
                            $prodname=$product->getName();	
                            $quantity = $product->getQty();											
                                           
                            $o_items_img.=<<<EOD
                            <tr>			
                            <td><strong>$quantity</strong>
                            <img src="{$thumbnail}" alt="Image not shown" width="75px" height="75px" />{$prodname}			
                            </td>		
                            </tr>
EOD;
                        
                       
                        }//end of foreach	
                    
                    
                    }//END of if
                    
                    $counter++;
        
              
                    $j++;
                }
        }
   
                $l++;
        
        $chunks=array_chunk($tbl,2);
        $pdf->SetMargins(12,7,0,0);
        $pdf->SetPageOrientation("P",true,0);
        $size="A4";
        
        
        
        $i=0;
            foreach($chunks as $page)
            {
            
                $pdf->AddPage("P",$size);
                //$firstpage="";
                if($i == 1)
                $paidstamp1="<h1>Ordered Items</h1>".$o_items_img;
                else 
                $paidstamp1=$paidstamp;
                
                $firstpage=<<<EOD
                <tr><td height="30"></td><td></td><td></td></tr>
                <tr><td height="30"></td><td></td><td></td></tr>
                <tr><td valign="middle">
                {$paidstamp1}</td><td></td><td>{$paidstamp}</td>
                </tr>
EOD;
                
                $outtable=<<<EOD
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                <tr><td>$page[0]</td><td width="90"> </td><td>$page[1]</td></tr>
                {$firstpage}
                </table>
EOD;
                
                $pdf->writeHTML($outtable, true, false, false, false, '');
                $i++;
            
            
            }
      

        $tableName3 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
        $sel = $connectionRead->select()->from($tableName3, array('increment_id'))->where('entity_id=?',$shipId);
        $ro = $connectionRead->fetchRow($sel);
        $shipmenIncrmnttId = $ro['increment_id'];
        
        $filename=$shipmenIncrmnttId.".pdf";
        $path = Mage::getBaseDir('media') . DS ."shiplabel" . DS .$shipmenIncrmnttId.".pdf";
        $pdf->Output($path,'F');
    }

    public function getTNTintllebal($box,$conNoteNumber,$addressLoadId,$request,$method,$shipId,$invIncrementID='',$order){
	
	extract($request);
	$box = explode('@@@@',$box);
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	$logo=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."tnt/logo.jpg";
	$txt=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."tnt/txt-img.jpg";
	$url = "https://express.tnt.com/expresslabel/documentation/getlabel";
	
	$firstName = $addressLoadId['firstname'];
        $lastName = $addressLoadId['lastname'];
	$name = $firstName." ".$lastName;
	$postCode = $addressLoadId['postcode'];
        $city = $addressLoadId['city'];
        $region = $addressLoadId['region'];
        $regionId = $addressLoadId['region_id'];
        
        $regionModel = Mage::getModel('directory/region')->load($regionId);
        $regCode=$regionModel->getCode();
        
        $street = $addressLoadId['street'];
        $telephone = $addressLoadId['telephone'];
        $company = $addressLoadId['company'];
        $country_code=$addressLoadId['country_id'];
        
        $countryModel = Mage::getModel('directory/country')->loadByCode($country_code);
        $destCountry = $countryModel->getName();
        
	if($company) $cusref = $company;
	else $cusref = $name;
        $date = date('d-m-Y');
	
        $total = count($box)-1;
	$l = $j = $k= $i= $m=0;
	$qty = $weight = $height = $width = $length = 0;
	foreach($box as $a){
		$l++;
		$arrc1 = explode("__",$a);					
		if($l > 1){
	
		$boxArr=explode("__",$a);
		
		$qtyPos =stripos($boxArr[1], ":");
		$qtyP = $qtyPos+1;
		$qty=$qty + substr($boxArr[1],$qtyP);
		
		}

	}
	
	$data = '<?xml version="1.0" encoding="UTF-8"?>
	<labelRequest>
	    <consignment key="CON1">
		<consignmentIdentity>
		    <consignmentNumber>'.$conNoteNumber.'</consignmentNumber>
		    <customerReference>'.$cusref.'</customerReference>
		</consignmentIdentity>
	    <collectionDateTime>2014-06-12T13:00:00</collectionDateTime>
	    <sender>
		<name>Vivid Ads</name>
		<addressLine1>302 BRIDGE STREET</addressLine1>
		<addressLine2></addressLine2>
		<addressLine3></addressLine3>
		<town>PORT MELBOURNE</town>
		<exactMatch>Y</exactMatch>
		<province>Victoria</province>
		<postcode>3207</postcode>
		<country>AU</country>
	    </sender>
	    <delivery>
		<name>'.$name.'</name>
		<addressLine1>'.$street.'</addressLine1>
		<addressLine2></addressLine2>
		<town>'.$city.'</town>
		<exactMatch>Y</exactMatch>
		<province>'.$regCode.'</province>
		<postcode>'.$postCode.'</postcode>
		<country>'.$country_code.'</country>
	    </delivery>
	    <product>
		<lineOfBusiness>2</lineOfBusiness>
		<groupId>0</groupId>
		<subGroupId>0</subGroupId>
		<id>EX</id>
		<type>N</type>
		<option>PR</option>
	    </product>
	    <account>
		<accountNumber>21664906</accountNumber>
		<accountCountry>AU</accountCountry>
	    </account>
	    <totalNumberOfPieces>'.$qty.'</totalNumberOfPieces>';
	    
	    
	    foreach($box as $a){
		$k++;
		
		$arrc1 = explode("__",$a);					
		if($k > 1){
		$j++;
		$boxArr=explode("__",$a);

		$lenPos =stripos($boxArr[2], ":");
		$lenP = $lenPos+1;
		$length=substr($boxArr[2],$lenP);
	
		$heighPos =stripos($boxArr[3], ":");
		$heighP = $heighPos+1;
		$height=substr($boxArr[3],$heighP);
		
		$widPos =stripos($boxArr[4], ":");
		$widP = $widPos+1;
		$width= substr($boxArr[4],$widP);
		
		$weiPos =stripos($boxArr[5], ":");
		$weiP = $weiPos+1;
		$weight=substr($boxArr[5],$weiP);
		
		$skuPos =stripos($boxArr[6], ":");
		$skuP = $skuPos+1;
		$sku=substr($boxArr[6],$skuP); 
		
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
	 
		$data .= '
		<pieceLine>
		<identifier>'.$j.'</identifier>
		<goodsDescription>boxes</goodsDescription>
		<pieceMeasurements>
		    <length>'.$length.'</length>
		    <width>'.$width.'</width>
		    <height>'.$height.'</height>
		    <weight>'.$weight.'</weight>
		</pieceMeasurements>
		<pieces>
		    <sequenceNumbers>'.$j.'</sequenceNumbers>
		    <pieceReference>Product</pieceReference>
		</pieces>
	    </pieceLine>';
		
		}

	    }
 
	$data .= '</consignment>
	</labelRequest>';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	if($data) {
		curl_setopt($ch, CURLOPT_POST,1);  
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
	}  
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	$contents = curl_exec ($ch); 
	$xml1=simplexml_load_string($contents);
    	curl_close ($ch);
    	$array = $this->XML2Array($xml1);
	
	
	$conNumber = $array['consignment']['consignmentLabelData']['consignmentNumber']; if($conNumber == '') $conNumber=$conNoteNumber;
	$int = $array['consignment']['consignmentLabelData']['marketDisplay']; if($int == '') $int="&nbsp;";
	$air = $array['consignment']['consignmentLabelData']['transportDisplay']; if($air == '') $air="&nbsp;";
	$xray = $array['consignment']['consignmentLabelData']['xrayDisplay'];  if($xray == '') $xray="&nbsp;";
	$freeCir = $array['consignment']['consignmentLabelData']['freeCirculationDisplay']; if($freeCir == '') $freeCir="&nbsp;";
	$sort = $array['consignment']['consignmentLabelData']['sortSplitText'];   if($sort == '') $sort="&nbsp;";
	$product = $array['consignment']['consignmentLabelData']['product'];   if($product == '') $product="&nbsp;";
	$option = $array['consignment']['consignmentLabelData']['option'];   if($option == '') $option="&nbsp;";
	$clustercode = $array['consignment']['consignmentLabelData']['clusterCode'];   if($clustercode == '') $clustercode=$postCode;
	$orgDepot = $array['consignment']['consignmentLabelData']['originDepot']['depotCode'];   if($orgDepot == '') $orgDepot="&nbsp;";
	$ipckUpdate = $array['consignment']['consignmentLabelData']['collectionDate'];   if($ipckUpdate == '') $ipckUpdate="&nbsp;";
	$transitDepot1 = $array['consignment']['consignmentLabelData']['transitDepots']['transitDepot'][0]['depotCode'];
	if($transitDepot1 == '') $transitDepot1="&nbsp;";
	$transitDepot2 = $array['consignment']['consignmentLabelData']['transitDepots']['transitDepot'][1]['depotCode'];
	if($transitDepot2 == '') $transitDepot2="&nbsp;";
	$destDepot = $array['consignment']['consignmentLabelData']['destinationDepot']['depotCode']; if($destDepot == '') $destDepot="&nbsp;";
	$duedateofMonth = $array['consignment']['consignmentLabelData']['destinationDepot']['dueDayOfMonth']; if($duedateofMonth == '') $duedateofMonth="&nbsp;";
	
	
	
	$delname = $array['consignment']['consignmentLabelData']['delivery']['name'];   if($delname == '') $delname=$cusref;
	$delStreet = $array['consignment']['consignmentLabelData']['delivery']['addressLine1'];   if($delStreet == '') $delStreet=$street;
	$delCity = $array['consignment']['consignmentLabelData']['delivery']['town'];   if($delCity == '') $delCity=$city;
	$delState = $array['consignment']['consignmentLabelData']['delivery']['province'];   if($delState == '') $delState=$region;
	$delPostcode = $array['consignment']['consignmentLabelData']['delivery']['postcode'];   if($delPostcode == '') $delPostcode=$postCode;
	$delcountry = $array['consignment']['consignmentLabelData']['delivery']['country'];   if($delcountry == '') $delcountry=$country_code;
	
	
	
	require_once('tcpdf/tcpdf.php');
	include('Barcode.php');
	///for pdf
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetFont('helvetica', '', 8);
	$pdf->SetMargins(12,7,0,0);
	$pdf->SetPageOrientation("P",true,0);
    	$size="A4";
	//for barcode
	$fontSize = 10;   // GD1 in px ; GD2 in point
	$marge    = 10;   // between barcode and hri in pixel
	$x        = 100;  // barcode center
	$y        = 65;  // barcode center
	$height   = 50;   // barcode height in 1D ; module size in 2D
	$width    = 2;    // barcode height in 1D ; not use in 2D
	$angle    = 0;   // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation
	
	$type     = 'std25';
	
	$im     = imagecreatetruecolor(450, 150);
	$black  = ImageColorAllocate($im,0x00,0x00,0x00);
	$white  = ImageColorAllocate($im,0xff,0xff,0xff);
	$red    = ImageColorAllocate($im,0xff,0x00,0x00);
	$blue   = ImageColorAllocate($im,0x00,0x00,0xff);
	imagefilledrectangle($im, 0, 0, 300, 300, $white);
	/////////////
	$codes = '';
	$m=0;$n=0;
	foreach($box as $a){
	$i++;
	
	
	if($i > 1){
	$m++;
	$n=0;
	$boxArr=explode("__",$a);
	$weiPos =stripos($boxArr[5], ":");
	$weiP = $weiPos+1;
	$weight=substr($boxArr[5],$weiP);
	$pdf->AddPage("P",$size);
	
	if($m == 1) $code = $array['consignment']['pieceLabelData']['barcode']; // barcode, of course ;)
	
	elseif($m > 1) $code = $array['consignment']['pieceLabelData'][$n]['barcode'];
	//$codes .= "__".$code;
//	$data = Barcode::gd($im, $black, $x, $y, $angle, $type, array('code'=>$code), $width, $height);
//	header('Content-type: image/gif');
//	//$barCode = imagegif($im);
//	
//	$barImg = $code.".png";
//	$barfile = Mage::getBaseDir('media') . DS ."shiplabel" . DS .$barImg;
//    	imagegif($im, $barfile);

	$barcode = $code;						   
        $params = TCPDF_STATIC::serializeTCPDFtagParameters(array($barcode, 'C128', '', '', 80, 30, 0.4, array('position'=>'S', 'border'=>false, 'padding'=>4, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4), 'N'));
	
	$str = <<<EOD
	<table cellpadding="0" cellspacing="0" border="0" width="420" align="center" style="border: 1px solid #000; margin: 0 auto;">
        <tr>
            <td colspan="2">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style=" border-right: 1px solid #000;"><img src="{$logo}" alt="" /></td>
                        <td style="width:172px; font: bold 18px Arial; color: #000; padding-left: 5px; border-right: 1px solid #000; border-bottom: 1px solid #000;">{$int}/{$air}<br/><br/>{$xray}</td>
                        <td valign="middle" style="width:50px; background: #000; color: #000; text-align: center; font: bold 50px Arial; border-right: 1px solid #000;border-bottom: 1px solid #000;">{$freeCir}</td>
                        <td valign="middle" style="width:50px; background: #fff; color: #000; text-align: center; font: bold 50px Arial; border-bottom: 1px solid #000;">{$sort}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                        <td style="width: 48%; float: left; padding-left: 2%; padding-bottom: 10px; padding-top: 10px; border-top: 1px solid #000;">
                            <h3 style="width:100%; color: #000; text-align: left; font: normal 20px Arial; padding: 0; margin: 0;">Con no.</h3>
                            <h1 style="width:100%; color: #000; text-align: left; font: bold 30px Arial; padding: 0; margin: 0;">{$conNumber}</h1>
                            <h2 style="width: 50%; float: left; padding: 0; margin: 0;">
                                <p style="width: 100%; float: left; font: 13px Arial; color: #000; margin: 0;">Piece</p>
                                <span style="width: 100%; float: left; font: 20px Arial; color: #000;">{$m} of {$total}</span>
                            </h2>
                            <h2 style="width: 50%; float: left; margin: 0; padding: 0;">
                                <p style="width: 100%; float: left; font: 13px Arial; color: #000; margin: 0;">Weight</p>
                                <span style="width: 100%; float: left; font: 20px Arial; color: #000;">{$weight}kg</span>
                            </h2>
                        </td>
                        <td style="width: 48%; float: left; background: #000; padding-left: 2%; padding-bottom: 10px; padding-top: 10px;">
                            <h3 style="width:100%; color: #000; text-align: left; font: normal 20px Arial; padding: 0; margin: 0;">Service</h3>
                            <h1 style="width:100%; color: #000; text-align: left; font: bold 25px Arial; padding: 0; margin: 0;">{$product}</h1>
                            <h3 style="width:100%; color: #000; text-align: left; font: normal 20px Arial; padding: 0; margin: 0;">Option</h3>
                            <h1 style="width:100%; color: #000; text-align: left; font: bold 25px Arial; padding: 0; margin: 0;">{$option}</h1>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; float: left; padding-left: 0; border-right: 1px solid #000;">
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                        <td style="width: 98%; float: left; padding-left: 2%; padding-top: 10px; padding-bottom: 10px; border-top: 1px solid #000;">
                            <h3 style="width:100%; color: #000; text-align: left; font: bold 13px Arial; padding: 0; margin: 0;">Customer Reference</h3>
                            <h3 style="width:100%; color: #000; text-align: left; font: bold 13px Arial; padding: 0; margin: 0;">{$cusref}</h3>
                            <h3 style="width:100%; color: #000; text-align: left; font: bold 12px Arial; padding: 0; margin: 0;">S/R Account No 21664906</h3>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 98%; float: left; padding-left: 2%; border-top: 1px solid #000; padding-top: 10px; padding-bottom: 10px;">
                            <h3 style="width:100%; color: #000; text-align: left; font: normal 18px Arial; padding: 0; margin: 0;">Sender Adress</h3>
                            <p style="width: 80%; float: left; font: 13px Arial; color: #000; margin-top: 0; padding-left: 20%;">
                            VIVID ADS<br/>
                            302 BRIDGE STREET<br/>
                            PORT MELBOURNE<br/>
                            VICTORIA<br/>
                            AU
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 98%; float: left; padding-left: 2%; border-top: 1px dashed #000; padding-top: 10px; padding-bottom: 10px;">
                            <h3 style="width:100%; color: #000; text-align: left; font: normal 18px Arial; padding: 0; margin: 0;">Delivery Adress</h3>
                            <p style="width: 80%; float: left; font: 13px Arial; color: #000; margin-top: 0; padding-left: 20%;">
                                {$delname}<br/>
                                {$delStreet}<br/>
                                {$delCity}<br/>
                                {$delState} {$delPostcode} <br/>
                                {$delcountry}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 98%; float: left;  border-top: 1px solid #000; padding-top: 0; padding-bottom: 0;">
                            <h3 style="width:40%; color: #000; text-align: left; font: bold 13px Arial; padding: 0; margin: 0; float: left; display: block;">Postcode/<br/>Cluster Code</h3>
                            <span style="width:60%; color: #fff; text-align: left; font: bold 50px Arial; padding: 0; margin: 0; background: #000; float: left; display: block;">{$clustercode}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 48%; float: left; padding-left: 1%; border: 0;" >
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                        <td style="width: 100%;  float: left;">
                            <h3 style="width:20%; color: #000; text-align: left; font: normal 13px Arial; padding: 0; margin: 0; float: left;">Origin</h3>
                            <h1 style="width:35%; color: #000; text-align: left; font: bold 30px Arial; padding: 0; margin: 0; float: left;">{$orgDepot}</h1>
                            <h3 style="width:45%; color: #000; text-align: left; font: normal 13px Arial; padding: 0; margin: 0; float: left;">Pickup Date <br/>{$ipckUpdate}</h3>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 100%;  float: left; border-top: 1px dashed #000; height: 140px;">
                            <h3 style="width:25%; color: #000; text-align: left; font: normal 13px Arial; padding: 0; margin: 0; float: left;">Routing</h3>
                            <h1 style="width:75%; color: #000; text-align: left; font: bold 50px Arial; padding: 0; margin: 0; float: left;">{$transitDepot1} {$transitDepot2}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 100%;  float: left; border-top: 1px dashed #000;">
                            <h3 style="width:25%; color: #000; text-align: left; font: normal 13px Arial; padding: 0; margin: 0; float: left;">Routing</h3>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 100%; float: left; border-top: 1px dashed #000;">
                            <h3 style="width:25%; color: #000; text-align: left; font: normal 13px Arial; padding: 0; margin: 0; float: left;">Sort</h3>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 100%; float: left; border-top: 1px dashed #000;">
                            <h3 style="width:25%; color: #000; text-align: left; font: normal 13px Arial; padding: 0; margin: 0; float: left;">Dest<br/>Depot</h3>
                            <h1 style="width:75%; color: #000; text-align: left; font: bold 30px Arial; padding: 0; margin: 0; float: left;">{$destDepot}-{$duedateofMonth}</h1>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" valign="middle" style="text-align: center; border-top: 1px solid #000;"><br><tcpdf method="write1DBarcode" params="{$params}" /></td>
        </tr>
    </table>
EOD;
       
	$pdf->writeHTML($str, true, false, false, false, '');
	$n++;
	}
	
	}
	
	
	$tableName3 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
        $sel = $connectionRead->select()->from($tableName3, array('increment_id'))->where('entity_id=?',$shipId);
        $ro = $connectionRead->fetchRow($sel);
        $shipmenIncrmnttId = $ro['increment_id'];
        
        $filename=$shipmenIncrmnttId.".pdf";
	//$filename="test.pdf";
        $path = Mage::getBaseDir('media') . DS ."shiplabel" . DS .$filename;
        $pdf->Output($path,'F');
	
	
	//echo "<pre>";print_r($array);echo "<pre>";
	//exit;
	//return $codes;
    
	
    }
    
    
    public function getTNTet($box,$conNoteNumber,$addressLoadId,$request,$method,$shipId,$invIncrementID='',$order,$addr)
    {
        
	
	$tnt_methos = array('Express'=>'EX','Fashion Express'=>'FE','General'=>'GE','Sameday'=>'701','9:00 Express'=>'712','10:00 Express'=>'X10','12:00 Express'=>'X12','CIT Pay As You Use'=>'73','Overnight Express'=>'75','Road Express'=>'76','Air/Road Combo'=>'77','Technology Express'=>'717','Fashion Express'=>'718');
        $ordId=$order->getId();
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $vivid['identifier']='VIVID';
        $vivid['account']='21664906';
        $vivid['company']='VIVID ADS';
        $vivid['address1'] ='302 BRIDGE STREET';
        $vivid['address2']='';
        $vivid['city']='PORT MELBOURNE' ;
        $vivid['state']='VIC';
        $vivid['zip']='3207';
        $vivid['name']='DESPATCH';
        $vivid['email']="support@vividads.com.au";	
        $date = date('d-m-Y');
        
        $postCode = $addressLoadId['postcode'];
        $city = $addressLoadId['city'];
        $firstName = $addressLoadId['firstname'];
        $lastName = $addressLoadId['lastname'];
        $region = $addressLoadId['region'];
        $regionId = $addressLoadId['region_id'];
        
        $regionModel = Mage::getModel('directory/region')->load($regionId);
        $regCode=$regionModel->getCode();
        
        $street = $addressLoadId['street'];
        $telephone = $addressLoadId['telephone'];
        $company = $addressLoadId['company'];
        $country_code=$addressLoadId['country_id'];
        
        $countryModel = Mage::getModel('directory/country')->loadByCode($country_code);
        $destCountry = $countryModel->getName();
        
        if($company) {$cmp=",<br>".$company; $cmp1=",".$company;} else{$cmp='';$cmp1='';} ///24-2-2014
        
        
        $collection = Mage::getModel('sales/order_shipment_comment')->getCollection()->addFieldToFilter('parent_id',array('eq' => $shipId));
        foreach($collection as $_collection){
                $commentId = $_collection->getId(); 	
                $commentModel = Mage::getModel('sales/order_shipment_comment')->load($commentId);
                $comment = $commentModel->getComment();
	}
        
        $date1 = date('dMY');
        do{
                $file = Mage::getBaseDir('media') . DS ."shiplabel" . DS ."ET-".$date1.$f.'.txt';
                $remote_file = '/outbox/'."ET-".$date1.$f.'.txt';
                $f++;
        }while(file_exists($file));

        $handle = fopen($file, 'w') or die('Cannot open file:  '.$file);
        $manifat_number = 1297;
        
	//4-3-2014 S
	$shM=explode("__",$method);
	$shMethod=$shM[0];
	$shPrice=$shM[1];
	
	$shMethod_1=trim(str_replace("TNT"," ", $shM[0]));
    	
        //$shMethod=$method[0];
        //$shPrice=$method[1];
        //4-3-2014 E
	
	$tot = (count($box) -1); //7-3-2014
	
        $tableName3 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
        $sel = $connectionRead->select()->from($tableName3, array('increment_id'))->where('entity_id=?',$shipId);
        $ro = $connectionRead->fetchRow($sel);
        $shipmenIncrmnttId = $ro['increment_id'];
        
        $created_at = date("Y-m-d H:i:s", $t);
        $date_post = strtotime($order->getCreatedAtDate()); 
        $Ordtime=date('Y-m-d H:i:s',$date_post );
        
        /******************* Start to insert the shipment table *************************/
        $tableName4 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment');
        $connectionWrite->beginTransaction();
        $data3=array();
        
        
        if(($f-1) == 0)
        $data3['et_file']= "ET-".$date1.'.txt';
        else
        $data3['et_file']= "ET-".$date1.$f.'.txt';
        
        
        
        
        $where3 = $connectionWrite->quoteInto("order_id = '".$ordId."' AND increment_id= '".$shipmenIncrmnttId."'");					
        $connectionWrite->update($tableName4, $data3, $where3);
        $connectionWrite->commit();
        /******************* End to insert the shipment table*************************/
        
        
        $k=$j=1;
        $data = "A".(str_pad($manifat_number,20,"0",STR_PAD_LEFT))."                                          TNT".date("YmdHi")."12        192";
        $l++;
        //26-2-2014 S					
        $text = "VIVID";
        $pad_string = " ";
        $text = str_pad($text, strlen($text)+10, $pad_string, STR_PAD_RIGHT);
        
        
        $vividCompny = str_pad($vivid['company'], strlen($vivid['company'])+(2*strlen($pad_string)), $pad_string, STR_PAD_LEFT);
        $vividAddrs = str_pad($vivid['address1'], strlen($vivid['address1'])+(21*strlen($pad_string)), $pad_string, STR_PAD_LEFT);
        $vividCity = str_pad($vivid['city'], strlen($vivid['city'])+(43*strlen($pad_string)), $pad_string, STR_PAD_LEFT);
        $vividState = str_pad($vivid['state'], strlen($vivid['state'])+(6*strlen($pad_string)), $pad_string, STR_PAD_LEFT);
        
        $data .="\n"."B".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$text."21664906".$vividCompny.$vividAddrs.$vividCity.$vividState.$vivid['zip'];
        $l++;
        
        
        
        //$data .="\n"."C".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad($counter,3,"0",STR_PAD_LEFT))."                         ".$firstName."    ".$lastName."     ".$company."      ".$street.'      '.$city."     ".$regCode."        ".$postcode."  ".$firstName."    ".$lastName."  ".$telephone."  ".date("dmY").$tnt_methos[$method[0]]."  0S0000000000 ";
        
        if($company) $nameT = $company;
        else $nameT = $firstName." ".$lastName;
        
	
	//echo "PC : ".$postCode;
	//exit;
	
        $nameT = str_pad($nameT, strlen($nameT)+(30-strlen($nameT)), $pad_string, STR_PAD_RIGHT);
        $street = str_pad($street, strlen($street)+(60-strlen($street)), $pad_string, STR_PAD_RIGHT);
        $city = str_pad($city, strlen($city)+(20-strlen($city)), $pad_string, STR_PAD_RIGHT);
        $regCode = str_pad($regCode, strlen($regCode)+(3-strlen($regCode)), $pad_string, STR_PAD_RIGHT);
        $postcode = str_pad($postCode, strlen($postCode)+(4-strlen($postCode)), $pad_string, STR_PAD_RIGHT);
        $cname = $firstName." ".$lastName;
        $cname = str_pad($cname, strlen($cname)+(20-strlen($cname)), $pad_string, STR_PAD_RIGHT);
        $telephone = str_pad($telephone, strlen($telephone)+(13-strlen($telephone)), $pad_string, STR_PAD_RIGHT);
        $date = str_pad(date("dmY"), strlen(date("dmY"))+(8-strlen(date("dmY"))), $pad_string, STR_PAD_RIGHT);
        
        
        
        
        $data .="\n"."C".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad($j,3,"0",STR_PAD_LEFT))."                         ".$nameT.$street.$city.$regCode.$postcode.$cname.$telephone.$date.$tnt_methos[$shMethod_1]."  0S0000000000 ";
        
        //echo $data; exit;
        
        //26-2-2014 E
        $l++;
        
        
        foreach($box as $q=>$boxvalue)
        {	$b++; ///24-2-2014
                if($b > 1){ ///24-2-2014
                
                $boxdata = explode('__',$boxvalue);
                //26-2-2014 S	
                $descrp = "BOX";
                $pad_string = " ";
                $descrp = str_pad($descrp, strlen($descrp)+23, $pad_string, STR_PAD_RIGHT);
                //26-2-2014 E	
                foreach($boxdata as $boxdataall)
                {
                        $boxdatain = explode(':',$boxdataall);
                        $boxdata_item[$boxdatain[0]] = $boxdatain[1];
                }
                
		
                if($boxvalue != '')
                {
                        $data .="\n"."F".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad($k,3,"0",STR_PAD_LEFT)).(str_pad($ordincrId,15,"0",STR_PAD_LEFT)).$descrp.(str_pad($boxdata_item['qty'],5,"0",STR_PAD_LEFT)).(str_pad($boxdata_item['weight'],9,"0",STR_PAD_LEFT))."KG".(str_pad($boxdata_item['length'],7,"0",STR_PAD_LEFT)).(str_pad($boxdata_item['width'],7,"0",STR_PAD_LEFT)).(str_pad($boxdata_item['height'],7,"0",STR_PAD_LEFT))."CM"
                        .(str_pad(($boxdata_item['height']*$boxdata_item['width']*$boxdata_item['length']),10,"0",STR_PAD_LEFT))."CC"
                        ;
                        
                        //echo $data; exit;
                        $l++;
                        $k++;
                }
                
                //for($j ; $j<=$tot ; $j++){
                
                        
                        //$conNoteNumber="VVD000055714";
                        $conNote=str_replace("VVD","00313113",$conNoteNumber);// It wil be dynamic
                        $barcode = "6104".$conNote.(str_pad($counter,3,"0",STR_PAD_LEFT))."0".(str_pad($postCode,5,"0",STR_PAD_RIGHT));						   
                        
                        //$itemno = (str_pad($q,3,"0",STR_PAD_LEFT)) . $conNote . (str_pad($j,3,"0",STR_PAD_LEFT)); ///4-3-2014
			
			$itemno = $conNote . (str_pad($j,3,"0",STR_PAD_LEFT)) . (str_pad($q,3,"0",STR_PAD_LEFT));
                        
			
			/////////commented on 7-3-2014 /////////
			
                        //if($j%7 == 0)
                        //{
                        //
                        //$allitem .= $conNote . (str_pad($j,3,"0",STR_PAD_LEFT));
                        //$data .="\n"."H".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad($q,3,"0",STR_PAD_LEFT)).$allitem;
                        //$l++;
                        //$allitem = '';
                        //
                        //}
                        //elseif($j == $boxdata_item['box'])
                        //{
                        //
                        //$allitem .= $conNote . (str_pad($j,3,"0",STR_PAD_LEFT));
                        //$data .="\n"."H".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad(($q+1),3,"0",STR_PAD_LEFT)).$allitem;
                        //$l++;
                        //$allitem = '';
                        //}
                        //else
                        //$allitem .= $conNote . (str_pad($j,3,"0",STR_PAD_LEFT));
                        
			//////////////////
			
                        
                        $counter++;
                        
		//}
			$j++;
                }
        }
	
	
	//////////7-3-2014 S
	
	    $z=1;
	    for($i=1 ; $i<=$tot ; $i++){
		
		$hSrting .= $conNote . (str_pad($i,3,"0",STR_PAD_LEFT));
//		if($i %7 == 0){
//    		    $data .="\n"."H".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad($z,3,"0",STR_PAD_LEFT)) . $hSrting;
//		    $hSrting = '';
//		    
//		}elseif( $i == $tot){
		    
		    
		    
		//}
	    
	    }
	    $data .="\n"."H".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad($z,3,"0",STR_PAD_LEFT)) . $hSrting;
	    
	   
	
	//////////7-3-2014 E
	
	
        $l++;
        $data .="\n"."Z".(str_pad($manifat_number,20,"0",STR_PAD_LEFT)).(str_pad($l,5,"0",STR_PAD_LEFT));
        fwrite($handle, $data);
        
        chmod($file, 0777);
        $fp = fopen($file, 'r');
        
        fclose($fp);
        // close the connection
        ftp_close($conn_id);

	/********************* End upload file to ft server *****************************/



        
    }

}