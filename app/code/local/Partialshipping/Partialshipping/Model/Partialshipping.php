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
    
    ////modified on 11-4-2014 S
    public function getTNTlebal($box,$conNoteNumber,$addressLoadId,$request,$method,$shipId,$invIncrementID='',$order)
    {
        extract($request);
        $storephone=Mage::getStoreConfig('general/store_information/phone');
        ///4-3-2014 S
	$shM=explode("__",$method);
	$shMethod=$shM[0];
	$shPrice=$shM[1];
	$shCode = $shM[2];
	/////4-3-2014 E
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
	
	$csv_name = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."tntrouting/Customer_own_routing.csv";
	
	if (($handle = fopen($csv_name, "r")) !== FALSE) {
        $lineArr=array("AEXP","APRI","ASSV","ATE","ARTW");
        $csvLineNoArr=array();
        $i=-1;
        $lIndex=0;
        
        $csvLineNoArr[$lineArr[$lIndex]]['from'] = 1;
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $i++;
            if($lIndex<sizeof($lineArr)) {
                $rowDataStr=$data[0];
                $chkLine=$lineArr[$lIndex];
                if (substr_count($rowDataStr,'D9999')>0) {
                    $csvLineNoArr[$lineArr[$lIndex]]['to'] = $i;
                    $lIndex++;
                    if($lIndex<sizeof($lineArr)) $csvLineNoArr[$lineArr[$lIndex]]['from'] = $i+1;
                }
            }
        }
        fclose($handle);
	}
	
	if( $shCode ==73 || $shCode ==75 ) {$serv ="APRI"; $carrier = "Priority";} ///21-4-2014
	elseif( $shCode == '717B'|| $shCode == 718) {$serv ="ASSV"; $carrier = "Specialised";} ///21-4-2014
	else {$serv ="AEXP"; $carrier = "Express";}  ///21-4-2014
	
	if($serv == 'AEXP'){$start = $csvLineNoArr['AEXP']['from']; $end = $csvLineNoArr['AEXP']['to']; }
	elseif($serv == 'ASSV'){$start = $csvLineNoArr['ASSV']['from']; $end = $csvLineNoArr['ASSV']['to']; }
	if($serv == 'APRI'){$start = $csvLineNoArr['APRI']['from']; $end = $csvLineNoArr['APRI']['to']; }
	
	$serachString = 'C'.strtoupper($city).'|'.strtoupper($regCode).'|'.$postCode;
	
	$csv_row = array();
	$mixArr=array("/r","/n");
	$csvLineArr=file($csv_name);
	for($i=$start;$i<=$end;$i++){
	    $rowDataStr=trim($csvLineArr[$i]);
	    $rowDataStr=str_replace($mixArr,"",$rowDataStr);
	    if (substr_count($rowDataStr,$serachString)>0) {
		$csv_row[] = $rowDataStr;
	    }
	}
	$str = $csv_row[0];
	$srtArr = explode('|',$str);
	
	$orgDepot = $srtArr[3];
	$gatewayDepot = $srtArr[4];
	$onfrwrdGatewayDepot = $srtArr[5];
	$srtBin = $srtArr[6];
	
	if ($orgDepot =='') $orgDepot = $gatewayDepot;
	
	if($gatewayDepot != $onfrwrdGatewayDepot) {$gatewaystr = 'via '.$gatewayDepot.' to '.$onfrwrdGatewayDepot;}
	else{ $gatewaystr = $gatewayDepot; }
	
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
        $mandate = date('d/m/Y');
	$dattime = date('H:i');
	
	$condate = now(); ///21-4-2014
	
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
	$j = 1;
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
                 
		$tot_weight =0;
		$tot_vol =0;
		$tot_weight=$tot_weight + $boxdata_item['weight'];
		$vol = ($boxdata_item['hght'] * $boxdata_item['width'] * $boxdata_item['len'])/100;
		$tot_vol = $tot_vol + $vol;
                //for($j ; $j<=$boxdata_item['box'] ; $j++){
                
                        
                        //$conNoteNumber="VVD000055714";
                        $conNote=str_replace("VVD","00313113",$conNoteNumber);// It wil be dynamic
                        $barcode = "6104".$conNote.(str_pad($q,3,"0",STR_PAD_LEFT))."0".(str_pad($postCode,5,"0",STR_PAD_RIGHT));						   
                        $params = TCPDF_STATIC::serializeTCPDFtagParameters(array($barcode, 'C128', '', '', 80, 30, 0.4, array('position'=>'S', 'border'=>false, 'padding'=>4, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4), 'N'));
                        //$itemno = (str_pad($q,3,"0",STR_PAD_LEFT)) . $conNote . (str_pad($j,3,"0",STR_PAD_LEFT)); ///4-3-32014
			
			//$itemno = $conNote . (str_pad($j,3,"0",STR_PAD_LEFT)) . (str_pad($q,3,"0",STR_PAD_LEFT));  ///13-3-2014
			$itemno = $conNote . (str_pad($j,3,"0",STR_PAD_LEFT));  
			
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
        <td align="right" style="font-size:15"><strong>{$gatewaystr}</strong></td>
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
        <td rowspan="2" style="font-size:13"><strong>{$srtBin}</strong></td>
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
        <td align="right" style="font-size:9">Ex {$orgDepot}</td>
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
        <td  width="50%" valign="top" height="75" style="line-height:0.9;border:#000 2px solid;font-size:7;" align="left"><strong>CN:{$conNoteNumber}<br>Itm:{$itemno}<br>{$q} of {$total}<br>TO:</strong><BR>{$firstName} {$lastName} {$cmp1}, {$street}, {$city}, {$region} {$postCode} ,{$destCountry}</td>
        <td width="50%" valign="top" align="left" style="line-height:0.9;border-bottom:#000 2px solid; border-right:#000 2px solid;font-size:6;border-top:#000 2px solid"><strong>{$shMethod}<br>Con Note Wt.: {$boxdata_item['weight']} Kg.<br><br>FROM:</span><BR>{$vivid['company']}, {$vivid['address1']}, {$vivid['city']} {$vivid['state']} {$vivid['zip']}</td></tr>
        
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

/////////////////Inser data in manifest table starts 21-4-2014//////////////////////
	$tableManifest = Mage::getSingleton('core/resource')->getTableName('tnt_manifest_data');
	$connectionWrite->beginTransaction();   
	$datamanifest = array();
	$datamanifest['currier']= 'TNT'.$carrier;
	$datamanifest['service']=$shMethod;
	$datamanifest['servcode']=$shCode;
	$datamanifest['routing_version']='123 - (080800)';
	$datamanifest['nineam_file_version']= '108- ('.$mandate.')';
	$datamanifest['consignment_date']= $date;
	$datamanifest['consignment_time']= $dattime;
	$datamanifest['consignment_number']= $conNoteNumber;
	$datamanifest['sender_reference']= $invIncrementID;
	$datamanifest['receiver_name']= $firstName.' '.$lastName;
	$datamanifest['destination']= $city.$regCode.$postCode;
	$datamanifest['items']= $total;
	$datamanifest['weight']= $tot_weight;
	$datamanifest['volume']= $vol;
	
	$connectionWrite->insert($tableManifest, $datamanifest);
	$connectionWrite->commit();
/////////////////Inser data in manifest table ends 21-4-2014//////////////////////



        $tableName3 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
        $sel = $connectionRead->select()->from($tableName3, array('increment_id'))->where('entity_id=?',$shipId);
        $ro = $connectionRead->fetchRow($sel);
        $shipmenIncrmnttId = $ro['increment_id'];
        
        $filename=$shipmenIncrmnttId.".pdf";
        $path = Mage::getBaseDir('media') . DS ."shiplabel" . DS .$shipmenIncrmnttId.".pdf";
        $pdf->Output($path,'F');
    }

    //method to create manifest 21-4-2014 starts
    public function getManifest($serv,$mdate=''){
	
	require_once('tcpdf/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetFont('helvetica', '', 8);
	
	
	if($mdate){
	    $date = date("d-m-Y", strtotime($mdate));
	}else{
	    $date = date('d-m-Y');
	}
	$total_items = 0;
	$total_weight = 0;
	$total_volumn = 0;
	
	$tableManifest = Mage::getSingleton('core/resource')->getTableName('tnt_manifest_data');
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	$storephone = Mage::getStoreConfig('general/store_information/phone');
	
	$select = $connectionRead->select()->from($tableManifest, array('*'))->where("service = '".$serv."' AND consignment_date='".$date."'");
	$row = $connectionRead->fetchAll($select);
	$cnt = count($row);
	if(count($row) > 0){
	    
	    $carrier = $row[0]['currier'];
	    $shMethod = $row[0]['service'];
	    $shCode = $row[0]['servcode'];
	    $dattime = date('H:i');
	
	$tbl_m = <<<EOD
	<table  border="0" width="100%" cellpadding="0" cellspacing="0">
	  <tr>
	    <td width="100%" align="center"><h2>DESPATCH MANIFEST - 3</h2></td>
	  </tr>
	  <tr>
	    <td><table width="100%" border="0" cellpadding="4" cellspacing="0">
	      <tr>
	        <td width="180"> <table  border="0"   cellpadding="0" cellspacing="0">
	      <tr>
	        <td bgcolor="#CCCCCC"; style="font-size:8px;border-top:#000 2px solid;border-right:#000 2px solid;border-left:#000 2px solid"><strong>Company: </strong></td>
	        </tr>
	        <tr>
	        <td style="font-size:8px;border-bottom:#000 2px solid;border-right:#000 2px solid;border-left:#000 2px solid" > <strong> VIVID ADS</strong> <br />
	          302 BRIDGE STREET <br />
	          PORT MELBOURNE VIC 3207 <br />Contact: AMER <br /> Phone: {$storephone} </td>
	        
	      </tr>
	    </table></td>
	    <td width="220"><table  border="0"   cellpadding="0" cellspacing="0">
	      <tr style="font-size:8px;">
	        <td bgcolor="#CCCCCC" style="border-top:#000 2px solid;border-left:#000 2px solid"><strong>Sender:21664906  </strong></td><td align="left" bgcolor="#CCCCCC" style="border-top:#000 2px solid;border-right:#000 2px solid;" ><strong>ETC Sender Code:VIVID  </strong></td>
	        </tr>
	        <tr>
	        <td colspan="2" style="font-size:8px;border-bottom:#000 2px solid;border-right:#000 2px solid;border-left:#000 2px solid"> <strong>            
	          VIVID ADS</strong> <br>
	          302 BRIDGE STREET <br>           
	          PORT MELBOURNE VIC 3207 <br>          
	          Contact: DESPATCH <br>            
	          Phone: {$storephone}</td>
	      </tr>
	    </table></td>
	        <td valign="top" width="210" align="right">
		<table cellpadding="0" cellspacing="0" width="210" border="0"  >
	        <tr>
	          <td  style="font-size:8px;"><strong>Carrier</strong></td>
	          <td align="center" style="font-size:8px"><strong>: </strong></td>
	          <td style="font-size:8px;">{$carrier}</td>
		  <td style="font-size:8px;"></td>
	        </tr>
	        <tr>
	          <td style="font-size:8px;"><strong>Service </strong></td>
	          <td align="center"><strong>:</strong></td>
	          <td style="font-size:8px;">{$shMethod} -{$shCode}</td>
	        </tr>
	        <tr>
	          <td style="font-size:8px;"><strong>Consignment Date </strong></td>
	          <td align="center"><strong>:</strong></td>
	          <td style="font-size:8px">{$date}</td>
	        </tr>
	        <tr>
	          <td style="font-size:8px;"><strong>Routing Version</strong></td>
	          <td align="center"><strong>:</strong></td>
	          <td style="font-size:8px;">123 - (080800)</td>
	        </tr>
	        <tr>
	          <td style="font-size:8px;"><strong>9amfile Version</strong></td>
	          <td align="center"><strong>:</strong></td>
	          <td style="font-size:8px;">108- ({$date})</td>
	        </tr>
		
	        </table></td>
	        <td valign="top"><table width="190" border="0" cellpadding="0" cellspacing="0">
	        <tr style="font-size:8px;">
	          <td style="font-size:8px;"><strong>Date 
	          </strong></td>
	          <td style="font-size:8px;"><strong>:
	          </strong></td>
	          <td style="font-size:8px;">{$date} </td>
	        </tr>
	        <tr>
	          <td style="font-size:8px;"><strong>TIME</strong></td>
	          <td style="font-size:8px;"><strong>:</strong></td>
	          <td style="font-size:8px;">{$dattime}</td>
	        </tr>
	        <tr>
	          <td style="font-size:8px;"><strong>PAGE</strong></td>
	          <td style="font-size:8px;">:</td>
	          <td style="font-size:8px;">1 of 1</td>
	        </tr>
	        </table></td>
	      </tr>
	    </table>
	      
	      
	      
	      </td>
	  </tr>
	  <tr>
	    <td><table width="98%" border="0" cellpadding="4" cellspacing="0" >
	      <tr  style="font-size:8px;">
	        <td width="4%" bgcolor="#CCCCCC" style="border:#000 2px solid">DG</td>
	        <td width="17%" bgcolor="#CCCCCC" style="border:#000 2px solid">Consignment Number</td>
	        <td width="2%" bgcolor="#CCCCCC" style="border:#000 2px solid">P</td>
	        <td width="11%" bgcolor="#CCCCCC" style="border:#000 2px solid">Senders Ref</td>
	        <td width="16%" bgcolor="#CCCCCC" style="border:#000 2px solid">Receiver Account</td>
	        <td width="12%" bgcolor="#CCCCCC" style="border:#000 2px solid">Receiver Name</td>
	        <td width="14%" bgcolor="#CCCCCC" style="border:#000 2px solid">Destiniation</td>
	        <td width="6%" align="right" bgcolor="#CCCCCC" style="border:#000 2px solid">Items</td>
	        <td width="6%"  align="right" bgcolor="#CCCCCC" style="border:#000 2px solid">KG</td>
	        <td width="6%" align="right" bgcolor="#CCCCCC" style="border:#000 2px solid">Cubic</td>
	        <td width="6%" bgcolor="#CCCCCC" style="border:#000 2px solid">EPP ID</td>      
	      </tr>
EOD;
	      
	      
	      foreach($row as $r){
	      
	      $tbl_m .= <<<EOD
	      <tr style="font-size:8px;" >
	        <td width="4%" style=""></td>
	        <td width="17%" style="">{$r['consignment_number']}</td>
	        <td width="2%" style="">S</td>
	        <td width="11%" style="">{$invIncrementID}</td>
	        <td width="16%" style="">N/A</td>
	        <td width="12%" style="">{$r['receiver_name']}</td>
	        <td width="14%" style="">{$r['destination']}</td>
	        <td width="6%" align="right" style="">{$r['items']}</td>
	        <td width="6%"  align="right" style="">{$r['weight']}</td>
	        <td width="6%" align="right" style="">{$r['volume']}</td>
	        <td width="6%" style="">&nbsp;</td>
	      </tr>
EOD;

	    $total_items = $total_items + $r['items'];
	    $total_weight = $total_weight + $r['weight'];
	    $total_volumn = $total_volumn + $r['volume'];
	    }
		$tbl_m .= <<<EOD
		
		<tr >
	            <td colspan="11" style="">&nbsp;</td>
	          </tr>
	          <tr style="font-size:8px;">
	        <td colspan="7" style="border-bottom:#000 4px solid;border-top:#000 4px solid;"><strong>GRAND TOTAL : {$cnt}</strong></td>
	        <td width="6%" align="right" style="border-bottom:#000 4px solid;border-top:#000 4px solid"><strong>{$total_items}</strong></td>
	        <td width="6%"  align="right" style="border-bottom:#000 4px solid;border-top:#000 4px solid"><strong>{$total_weight}</strong></td>
	        <td width="6%" align="right" style="border-bottom:#000 4px solid;border-top:#000 4px solid"><strong>{$total_volumn}</strong></td>
	        <td width="6%" style="border-bottom:#000 2px solid;border-top:#000 2px solid">&nbsp;</td>
	      </tr>
	
	</table></td></tr>
	  <tr>
	    <td>         <strong style="font-size:8px;">*=Fashion Bag / Garment consignment detail line</strong></td>
	        </tr>
	      <tr >
	        <td colspan="11"><table width="500" border="0"  cellpadding="0" cellspacing="0">
	          <tr>
	            <td style="border-top:#000 2px solid;border-left:#000 2px solid;border-right:#000 2px solid"><strong style="font-size:8px;">These consignments DO NOT contain Dangerous Goods</strong> <br /><table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr style="font-size:8px;">
	    <td width="14%"><strong>Driver`s signature:</strong></td>
	    <td width="40%">______________________________</td>
	    <td width="3%"><strong>id:</strong></td>
	    <td width="16%">______________</td>
	    <td width="6%"><strong>Date:</strong></td>
	    <td width="21%">___ /___ /_______</td>
	  </tr>
	</table>
	</td>
	          </tr>
	<tr>
	<td style="border-bottom:#000 2px solid;border-left:#000 2px solid;border-right:#000 2px solid"></td>
	</tr>
	        </table></td>
	      </tr>
	    </table></td>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
	  </tr>
	</table>
EOD;
	$pdf->AddPage("L",$size);
	$pdf->writeHTML($tbl_m, true, false, false, false, '');
	
	$filename="manifest-".$serv."-".date('d-m-Y').".pdf";
        $path = Mage::getBaseDir('media') . DS ."shiplabel" . DS .$filename;
        
	
	if (!file_exists($path)) {
	   $pdf->Output($path,'F');
	   return  $filename;
	}else{
	   return  $filename; 
	}
	
	}
	
    }
    ///method to create manifest 21-4-2014 ends
    
    
    ///modified on 11-4-2014
    public function getTNTintllebal($box,$conNoteNum,$addressLoadId,$request,$method,$shipId,$invIncrementID='',$order){
	
	extract($request);
	$box = explode('@@@@',$box);
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	$currencycode = $order->getOrderCurrencyCode();
	$shM=explode("__",$method);
	$shMethod=$shM[0];
	$shPrice=$shM[1];
	
	$logo=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."tnt/logo.jpg";
	$txt=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."tnt/txt-img.jpg";
	$url = "https://express.tnt.com/expresslabel/documentation/getlabel";
	
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$connectionWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        $collection = Mage::getModel('sales/order_shipment_comment')->getCollection()->addFieldToFilter('parent_id',array('eq' => $shipId));
        foreach($collection as $_collection){
                $commentId = $_collection->getId(); 	
                $commentModel = Mage::getModel('sales/order_shipment_comment')->load($commentId);
                $comment = $commentModel->getComment();
        }
	
	$conNoteNumber = trim(str_replace("VVD"," ",$conNoteNum));
	
	
	$firstName = $addressLoadId['firstname'];
        $lastName = $addressLoadId['lastname'];
	$name = $firstName." ".$lastName;
	$postCode = $addressLoadId['postcode'];
        $city = $addressLoadId['city'];
        $region = $addressLoadId['region'];
        $regionId = $addressLoadId['region_id'];
        
        $regionModel = Mage::getModel('directory/region')->load($regionId);
        $regCode=$regionModel->getCode();
	$regName=$regionModel->getName();
        
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
	$totWeight = 0;
	foreach($box as $a){
		$l++;
		$arrc1 = explode("__",$a);					
		if($l > 1){
	
		$boxArr=explode("__",$a);
		
		$qtyPos =stripos($boxArr[1], ":");
		$qtyP = $qtyPos+1;
		$qty=$qty + substr($boxArr[1],$qtyP);
		
		$weiPos =stripos($boxArr[5], ":");
		$weiP = $weiPos+1;
		$totWeight = $totWeight + substr($boxArr[5],$weiP);
		
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
		<goodsDescription>Boxes</goodsDescription>
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
	
	$tntLogo = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."tnt/tnt.png";
	$storephone = Mage::getStoreConfig('general/store_information/phone');
	$shipDate = date("d/m/Y");
	
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
	
	
	$delStateCode = $array['consignment']['consignmentLabelData']['delivery']['province'];   
	
	$regionModel = Mage::getModel('directory/region')->load($delStateCode);
        
	$regName1=$regionModel->getName();
	$delState = $delStateCode; if($delState == '') $delState=$regName;
	
	$delPostcode = $array['consignment']['consignmentLabelData']['delivery']['postcode'];   if($delPostcode == '') $delPostcode=$postCode;
	$delcountryCode = $array['consignment']['consignmentLabelData']['delivery']['country'];
	
	$countryModel = Mage::getModel('directory/country')->loadByCode($delcountryCode);
	$destCountry1 = $countryModel->getName();
    	$delcountry = $destCountry1;   if($delcountry == '') $delcountry=$destCountry;
	
	
	
	require_once('tcpdf/tcpdf.php');
    	///for pdf
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetFont('helvetica', '', 8);
	$pdf->SetMargins(12,7,0,0);
	$pdf->SetPageOrientation("P",true,0);
    	$size="A4";
	
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
	
	$lenPos =stripos($boxArr[2], ":");
	$lenP = $lenPos+1;
	$length=substr($boxArr[2],$lenP);

	$heighPos =stripos($boxArr[3], ":");
	$heighP = $heighPos+1;
	$height=substr($boxArr[3],$heighP);
	
	$widPos =stripos($boxArr[4], ":");
	$widP = $widPos+1;
	$width= substr($boxArr[4],$widP);
	
	$space = "&nbsp;";
	
	$liabilityStr = "TNT'S LIABILITY FOR LOSS, DAMAGE AND DELAY IS LIMITED BY THE CMR CONVENTION OR THE
WARSAW CONVENTION WHICHEVER IS APPLICABLE. THE SENDER AGREES THAT THE GENERAL
CONDITIONS, ACCESSIBLE AT HTTP:// ICONNECTION.TNT.COM:81/TERMSANDCONDITIONS.HTML,
ARE ACCEPTABLE AND GOVERN THIS CONTRACT. IF NO SERVICES OR BILLING OPTIONS ARE
SELECTED THE FASTEST AVAILABLE SERVICE WILL BE CHARGED TO THE SENDER.
";
	//$pdf->AddPage("P",$size);
	
	if($m == 1) $code = $array['consignment']['pieceLabelData']['barcode']; // barcode, of course ;)
	
	elseif($m > 1) $code = $array['consignment']['pieceLabelData'][$n]['barcode'];
	
	$barcode = $code;						   
        $params = TCPDF_STATIC::serializeTCPDFtagParameters(array($barcode, 'C128', '', '', 70, 30, 0.4, array('position'=>'S', 'border'=>false, 'padding'=>4, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4), 'N'));
	
	$str []= <<<EOD
	<table cellpadding="0" cellspacing="0" border="1" width="420" align="center">
        <tr>
            <td colspan="2" style="width:170px; font-family: arial; padding-bottom: 15px; padding-top: 5px; padding-left: 2px; text-align: left;">
	    Account : 21664906<br/>
	    {$space}Sender<br/>
	    {$space}Vivid Ads<br/>
	    {$space}302 Bridge Street<br/>
	    {$space}Port Melbourne<br/>
	    {$space}Victoria<br/>
	    {$space}Australia<br/>
	    {$space}Contact : Vivid Ads<br/>
	    {$space}Tel : {$storephone}<br/>
	    </td>
	    
	    <td style="width:250px; font-family: arial; padding-bottom: 15px; text-align: center; padding-top: 5px; padding-left: 2px;">
	    <img width="100" height="40" border="0" src="{$tntLogo}"><br/>
	    <tcpdf method="write1DBarcode" params="{$params}" /><br/>
	    Customer Reference : {$cusref}<br/>
	    </td>
        </tr>
	<tr>
	    <td colspan="2" style="width:170px; font-family: arial; padding-bottom: 15px; padding-top: 5px; padding-left: 2px; text-align: left;">
	    Delivery Adress<br/>
	    {$space}{$delname}<br/>
	    {$space}{$delStreet}<br/>
	    {$space}{$delCity}<br/>
	    {$space}{$delState}<br/>
	    {$space}{$delPostcode} <br/>
	    {$space}{$delcountry}<br/>
	   {$space}Contact : {$delname}<br/>
	    {$space}Tel : {$telephone}<br/>
	    {$space}</td>
	    
	    <td style="width:250px; font-family: arial; padding-bottom: 15px; padding-top: 5px; padding-left: 2px; padding-right: 2px; text-align: left;">
	    {$space}Shipment Date : {$shipDate}<br/>
	    {$space}Description of Goods : <br/>
	    {$space}Box : {$m}<br/>
	    {$space}Dimension : {$length} cm x {$width} cm x {$height} cm
	    </td>
	    
	</tr>
	<tr>
	    <td colspan="3" style="width:420px; font-family: arial; padding-bottom: 15px; padding-top: 5px; padding-left: 2px; text-align: left;">
	    Special Delivery Instructions : {$comment}<br/>
	    </td>
	</tr>
	
	<tr>
	    <td style="width:170px; font-family: arial; padding-bottom: 15px; padding-top: 5px; padding-left: 2px; text-align: left;">
	    Service and Options <br/>
	    {$space}{$product}<br/>
	    {$space}{$option}
	    </td>
	    <td style="width:60px; font-family: arial; padding-bottom: 15px; padding-top: 5px; text-align: center;">
	    No Of Items:<br/>
	    {$m} of {$total}<br/><br/>
	    
	    Item<br/>
	    {$space}Weight<br/>
	    {$space}{$weight}kg<br/>
	    </td>
	    <td style="width:190px; font-family: arial; padding-bottom: 15px; padding-top: 5px; padding-left: 2px; text-align: left;">
	    {$liabilityStr}
	    
	    </td>
	</tr>
	
        </table>
EOD;
       
	$n++;
	}
	
	}
	
	//11-3-2014 S
	$chunks=array_chunk($str,1);
	
	foreach($chunks as $page)
        {
	    $pdf->AddPage("P",$size);
	    $outtable=<<<EOD
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                <tr><td>$page[0]</td><td width="70"> </td><td>$page[1]</td></tr>
                
                </table>
EOD;
	    $pdf->writeHTML($outtable, true, false, false, false, '');
	    
	}
	
	///////////////17-3-2014 connotes S
	
	$conStrCus = <<<EOD
	<table cellpadding="0" cellspacing="0" border="1" width="420" align="center">
        <tr>
            <td colspan="2" style="width:200px; font-family: arial; padding-bottom: 15px; padding-top: 5px; padding-left: 2px; text-align: left;">
	    1.From(Collection Address)
	    <hr><br/>
	    Sender's Account : 21664906<br/>
	   
	    {$space}Name : Vivid Ads<br/>
	    {$space}Address : 302 Bridge Street<br/>
	    {$space}City : Port Melbourne<br/>
	    {$space}Province : Victoria<br/>
	    {$space}Postal/ZIP Code : 3207<br/>
	    {$space}Country : Australia<br/>
	    {$space}Contact : Vivid Ads<br/>
	    {$space}Tel No: {$storephone}<br/><br/>
	    
	    2.To(Receiver Address)
	    <hr><br/>
	    {$space}Name : {$delname}<br/>
	    {$space}Address : {$delStreet}<br/>
	    {$space}City : {$delCity}<br/>
	    {$space}Province : {$delState}<br/>
	    {$space}Postal/ZIP Code :{$delPostcode} <br/>
	    {$space}Country : {$delcountry}<br/>
	    {$space}Contact : {$delname}<br/>
	    {$space}Tel No: {$telephone}<br/><br/>
	    
	    3.Goods
	    <hr><br/>
	    General description : Boxes<br/>
	    <span>Total Package : {$m}</span><br/><span>Total Weight : {$totWeight}</span><br/><span>Total Volume : 1m3</span><br/>
	    <br/><br/>
	    
	    4.Services
	    <hr><br/>
	    {$space}Service : {$product}<br/>
	    {$space}Option : {$option}<br/>
	    {$space}Insurance Currency : GBP  {$space}{$space}{$space}{$space}{$space}{$space}{$space}{$space}{$space}
	    {$space} Sender Pays : Value 150
	    <br/>
	    <hr><br/>
	    <br/>
	    Sender's Signature : _____________________<br/>
	    
	    Date : __/__/__ <br/>
	    <br/>
	    {$liabilityStr}
	    
	     
	    </td>
	    
	    <td style="width:220px; font-family: arial; padding-bottom: 15px; text-align: left; padding-top: 5px; padding-left: 2px;">
	    {$space}{$space}{$space}{$space}{$space}<img width="100" height="40" border="0" src="{$tntLogo}"><br/>
	    {$space}{$space}{$space}{$space}{$space}<tcpdf method="write1DBarcode" params="{$params}" /><br/>
	    Please quote this numbe if you have any query.<br/><br/>
	    
	    A.Delivery Address
	    <hr><br/>
	    {$space}Name : {$delname}<br/>
	    {$space}Address : {$delStreet}<br/>
	    {$space}City : {$delCity}<br/>
	    {$space}Province : {$delState}<br/>
	    {$space}Postal/ZIP Code :{$delPostcode} <br/>
	    {$space}Country : {$delcountry}<br/>
	    {$space}Contact : {$delname}<br/>
	    {$space}Tel No: {$telephone}<br/><br/>
	    
	    B.Dutiable Shipment Details <hr><br/>
	    Receiver's VAT/TVA/BTW/MWSt No. : 7668880 <br/>
	    Invoice Value of Dutiables  <br/>
	    Currency : {$currencycode} {$space}{$space}{$space}{$space}{$space}{$space}{$space}{$space}{$space}{$space}Value : 180<br/><br/>
	    
	    C.Special Delivery Instruction 
	    <hr><br/>
	    {$comment}<br/><br/>
	    
	    D.Customer Reference :
	    <hr><br/>
	    {$cusref}<br/><br/>
	    E.Invoice Receiver (Receiver's Account Number)<br/>
	    ___________________________<br/>
	    ___________________________<br/>
	    
	    <br/> <br/>
	    Received by TNT <br/>
	    By (Name) : _______________________<br/><br/>
	    Date : __/__/__ {$space}{$space}{$space}{$space}{$space}{$space} Time : __:__<br/><br/>
	    
	    <br/><br/><br/><strong>Customer's Copy</strong><br/>
	    Please Keep for reference
	    </td>
        </tr>
	</table>
EOD;
	
	
	$conStrRec = <<<EOD
	<table cellpadding="0" cellspacing="0" border="1" width="420" align="center">
        <tr>
            <td colspan="2" style="width:200px; font-family: arial; padding-bottom: 15px; padding-top: 5px; padding-left: 2px; text-align: left;">
	    1.From(Collection Address)
	    <hr><br/>
	    Sender's Account : 21664906<br/>
	   
	    {$space}Name : Vivid Ads<br/>
	    {$space}Address : 302 Bridge Street<br/>
	    {$space}City : Port Melbourne<br/>
	    {$space}Province : Victoria<br/>
	    {$space}Postal/ZIP Code : 3207<br/>
	    {$space}Country : Australia<br/>
	    {$space}Contact : Vivid Ads<br/>
	    {$space}Tel No: {$storephone}<br/><br/>
	    
	    2.To(Receiver Address)
	    <hr><br/>
	    {$space}Name : {$delname}<br/>
	    {$space}Address : {$delStreet}<br/>
	    {$space}City : {$delCity}<br/>
	    {$space}Province : {$delState}<br/>
	    {$space}Postal/ZIP Code :{$delPostcode} <br/>
	    {$space}Country : {$delcountry}<br/>
	    {$space}Contact : {$delname}<br/>
	    {$space}Tel No: {$telephone}<br/><br/>
	    
	    3.Goods
	    <hr><br/>
	    General description : Boxes<br/>
	    <span>Total Package : {$m}</span><br/><span>Total Weight : {$totWeight}</span><br/><span>Total Volume : 1 m3</span><br/>
	    <br/><br/>
	    
	    4.Services
	    <hr><br/>
	    {$space}Service : {$product}<br/>
	    {$space}Option : {$option}<br/>
	    {$space}Insurance Currency : {$currencycode}  {$space}{$space}{$space}{$space}{$space}{$space}{$space}{$space}{$space}
	    {$space} Sender Pays : Value 150
	    <br/>
	    <hr><br/>
	    <br/>
	    Sender's Signature : _____________________<br/>
	    
	    Date : __/__/__ <br/>
	    <br/>
	    {$liabilityStr}
	    
	     
	    </td>
	    
	    <td style="width:220px; font-family: arial; padding-bottom: 15px; text-align: left; padding-top: 5px; padding-left: 2px;">
	    {$space}{$space}{$space}{$space}{$space}{$space}{$space}{$space}<img width="100" height="40" border="0" src="{$tntLogo}"><br/>
	    {$space}{$space}{$space}{$space}{$space}{$space}{$space}{$space}<tcpdf method="write1DBarcode" params="{$params}" /><br/>
	    Please quote this numbe if you have any query.<br/><br/>
	    
	    A.Delivery Address
	    <hr><br/>
	    {$space}Name : {$delname}<br/>
	    {$space}Address : {$delStreet}<br/>
	    {$space}City : {$delCity}<br/>
	    {$space}Province : {$delState}<br/>
	    {$space}Postal/ZIP Code :{$delPostcode} <br/>
	    {$space}Country : {$delcountry}<br/>
	    {$space}Contact : {$delname}<br/>
	    {$space}Tel No: {$telephone}<br/><br/>
	    
	    B.Dutiable Shipment Details <hr><br/>
	    Receiver's VAT/TVA/BTW/MWSt No. : 7668880 <br/>
	    Invoice Value of Dutiables  <br/>
	    Currency : {$currencycode} {$space}{$space}{$space}{$space}{$space}{$space}{$space}{$space}{$space}{$space}Value : 180<br/><br/>
	    
	    C.Special Delivery Instruction 
	    <hr><br/>
	    {$comment}<br/><br/>
	    
	    D.Customer Reference :
	    <hr><br/>
	    {$cusref}<br/><br/>
	    E.Invoice Receiver (Receiver's Account Number)<br/>
	    ___________________________<br/>
	    ___________________________<br/>
	    
	    <br/> <br/>
	    Received by TNT <br/>
	    By (Name) : _____________________<br/><br/>
	    Date : __/__/__ {$space}{$space}{$space}{$space}{$space}{$space} Time : __:__<br/><br/>
	    
	    <br/><br/><br/><strong>Receiver's Copy</strong><br/>
	    Please Keep for reference
	    </td>
        </tr>
	</table>
EOD;
	    
	    
	    $pdf->AddPage("P",$size);
	    $outtable_conn=<<<EOD
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                <tr><td>$conStrCus</td></tr>
		
                </table>
EOD;
	    $pdf->writeHTML($outtable_conn, true, false, false, false, '');
	
	    $pdf->AddPage("P",$size);
	    $outtable_conn1=<<<EOD
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                
		<tr><td>$conStrRec</td></tr>
                
                </table>
EOD;
	    $pdf->writeHTML($outtable_conn1, true, false, false, false, '');
	
    	///////////////17-3-2014 connotes E
	
	//////////////18-3-2014 commercial invoice S
	$commInvoiceStr = <<<EOD
	<table cellpadding="0" cellspacing="0" border="1" width="500" align="center" style="padding-bottom: 5px; padding-top: 5px; margin-bottom: 5px;">
        <tr>
            <td  style="width:250px; font-family: arial; padding-bottom: 15px; padding-top: 5px; padding-left: 2px; text-align: left;">
	    
	    SENDER (Seller/Exporter)<br/>
	   
	    {$space}Vivid Ads<br/>
	    {$space}302 Bridge Street<br/>
	    {$space}Port Melbourne<br/>
	    {$space}Victoria<br/>
	    {$space}3207<br/>
	    {$space}Australia<br/>
	    {$space}Vivid Ads<br/>
	    {$space}{$storephone}<br/><br/>
	    </td>
	    <td  style="width:250px; font-family: arial; padding-bottom: 15px; padding-top: 5px; padding-left: 2px; text-align: left;">
		<table cellpadding="0" cellspacing="0" border="1" width="250" align="center">
		    <tr><td colspan="2" style="font-family: arial; font-size: 20px;text-align: center;padding: 2px">Invoice</td></tr>
		    <tr>
		    <td style="width: 130px;height:20px;font-family: arial;text-align: left;">Invoice Number : </td>
		    <td style="width: 120px;height:10px;font-family: arial;text-align: left;"></td>
		    </tr>
		    <tr>
		    <td style="width: 130px;height:20px;font-family: arial;text-align: left;">Date : </td>
		    <td style="width: 120px;height:20px;font-family: arial;text-align: left;">{$shipDate}</td>
		    </tr>
		    <tr>
		    <td style="width: 130px;height:20px;font-family: arial;text-align: left;">Consignment Note Number : </td>
		    <td style="width: 120px;height:20px;font-family: arial;text-align: left;">{$conNoteNum}</td>
		    </tr>
		    <tr>
		    <td style="width: 130px;height:20px;font-family: arial;text-align: left;">Purchase Order Number : </td>
		    <td style="width: 120px;height:20px;font-family: arial;text-align: left;"></td>
		    </tr>
		    <tr>
		    <td style="width: 130px;height:20px;font-family: arial;text-align: left;">Invoice Currency : </td>
		    <td style="width: 120px;height:20px;font-family: arial;text-align: left;">{$currencycode}</td>
		    </tr>
		    <tr>
		    <td style="width: 130px;height:20px;font-family: arial;text-align: left;">Reason For Export : </td>
		    <td style="width: 120px;height:20px;font-family: arial;text-align: left;"></td>
		    </tr>
    		</table>
	    </td>
	 </tr>   
	 
	 <tr>
	 <td style="width:250px; font-family: arial; padding-bottom: 15px; padding-top: 5px; padding-left: 2px; text-align: left;">
	    RECEIVER (Buyer/Importer)<br/>
	    {$space}{$delname}<br/>
	    {$space}{$delStreet}<br/>
	    {$space}{$delCity}<br/>
	    {$space}{$delState}<br/>
	    {$space}{$delPostcode} <br/>
	    {$space}{$delcountry}<br/>
	    {$space}{$delname}<br/>
	    {$space} {$telephone}<br/><br/>
	 </td>
	 
	 <td style="width:250px; font-family: arial; padding-bottom: 15px; padding-top: 5px; padding-left: 2px; text-align: left;">
	    Deliver To (If different from receiver)<br/>
	    {$space}{$delname}<br/>
	    {$space}{$delStreet}<br/>
	    {$space}{$delCity}<br/>
	    {$space}{$delState}<br/>
	    {$space}{$delPostcode} <br/>
	    {$space}{$delcountry}<br/>
	    {$space}{$delname}<br/>
	    {$space} {$telephone}<br/><br/>
	 </td>
	 </tr>
	</table>
	<table>
	<tr><td style="height:15px"></td></tr>
	</table>
	<table cellpadding="0" cellspacing="0" border="1" width="500" align="center" style="padding-bottom: 5px; padding-top: 5px;">
	<tr>
	    <td style="width:60px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">Quantity</td>
	    <td style="width:50px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">Units</td>
	    <td style="width:60px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">Weight</td>
	    <td style="width:100px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">Description of Goods</td>
	    <td style="width:50px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">HS Tariff Code</td>
	    <td style="width:60px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">Country of Origin</td>
	    <td style="width:60px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">Unit Value</td>
	    <td style="width:60px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">Total Value</td>
	</tr>
EOD;
	$shpItms = '';
	$toWeight=0;
	$k=$j=0;
	foreach($box as $a){
	    $k++;
	    
	    $arrc1 = explode("__",$a);					
	    if($k > 1){
		$j++;
		$boxArr=explode("__",$a);
	    
		$qtyPos =stripos($boxArr[1], ":");
		$qtyP = $qtyPos+1;
		$q = substr($boxArr[1],$qtyP);
		$qty=$qty + substr($boxArr[1],$qtyP);
		
		$weiPos =stripos($boxArr[5], ":");
		$weiP = $weiPos+1;
		$weght = substr($boxArr[5],$weiP);
		$roundwght = round($weght);
		$toWeight = $toWeight + substr($boxArr[5],$weiP);
	    
		$skuPos =stripos($boxArr[6], ":");
		$skuP = $skuPos+1;
		$sku=substr($boxArr[6],$skuP); 
		
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		$descr = $product->getName();
	    
	    
	    
	    
		$commInvoiceStr .= <<<EOD
				<tr>			
				<td style="height:20px;">{$q}</td>
				<td style="height:20px;">{$space}</td>
				<td style="height:20px;">{$weght}</td>
				<td style="height:20px;">{$descr}</td>
				<td style="height:20px;">{$roundwght}</td>
				<td style="height:20px;">AU</td>
				<td style="height:20px;">{$space}</td>
				<td style="height:20px;">2.3</td>		
				</tr>
EOD;
	    }
	}
	
	$commInvoiceStr .= <<<EOD
				<tr style="height:10px;">			
				<td>{$space}</td><td>{$space}</td><td>{$space}</td><td>{$space}</td><td>{$space}</td><td>{$space}</td><td>{$space}</td><td>{$space}</td>		
				</tr>
				<tr>
				
				<td>Total weight</td><td>{$toWeight}</td><td>Units of weight</td>
				<td style="width:40px;">Kg</td><td style="width:80px;">Total Number Of Packages</td><td>{$j}</td>
				<td style="width:90px;">Invoice Line Total<br/>Freight Charges</td><td>{$shPrice}</td>
				
				
				</tr>
				<tr>			
				<td colspan="6" style="border-color: #fff" style="height:20px;">{$space}</td><td style="height:20px;">Insurance</td><td style="height:20px;">{$space}</td>
				</tr>
				<tr>
				<td colspan="6" style="border-color: #fff" style="height:20px;">{$space}</td><td style="height:20px;">Other Charges</td><td style="height:20px;">{$space}</td>
				</tr>
				<tr>
				<td colspan="5" style="height:20px;" >INCO Terms</td><td style="height:20px;">{$space}</td><td style="height:20px;">Invoice Total</td><td style="height:20px;">{$shPrice}</td>
				</tr>
				</table>
				<table>
				<tr><td style="height:15px"></td></tr>
				</table>
				<table cellpadding="0" cellspacing="0" border="1" width="500" align="center" style="padding-bottom: 5px; padding-top: 5px; margin-bottom: 5px;">
				<tr style="height:20px;">
				    <td style="width:200px; height:20px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">Shipper Name and Job Title</td>
				    <td style="width:200px; height:20px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">Shipper Signature</td>
				    <td style="width:100px; height:20px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">Date</td>
				    
				</tr>
				<tr style="height:20px;">
				    <td style="width:200px; height:20px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">{$space}</td>
				    <td style="width:200px; height:20px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">{$space}</td>
				    <td style="width:100px; height:20px; font-family: arial; font-size: 8px; font-weight: bold;text-align: center;">{$shipDate}</td>
				    
				</tr>
				</table>
				
EOD;
	
	
	    $pdf->AddPage("P",$size);
	    $outtable_Inv=<<<EOD
                <table width="800" cellpadding="0" cellspacing="0" border="0">
                <tr><td>$commInvoiceStr</td></td></tr>
                
                </table>
EOD;
	    $pdf->writeHTML($outtable_Inv, true, false, false, false, '');
	
	//////////////18-3-2014 commercial invoice E
	//11-3-2014 E
	
	
	$tableName3 = Mage::getSingleton('core/resource')->getTableName('partialshipping_shipment_grid');
        $sel = $connectionRead->select()->from($tableName3, array('increment_id'))->where('entity_id=?',$shipId);
        $ro = $connectionRead->fetchRow($sel);
        $shipmenIncrmnttId = $ro['increment_id'];
        
        $filename=$shipmenIncrmnttId.".pdf";
        $path = Mage::getBaseDir('media') . DS ."shiplabel" . DS .$filename;
        $pdf->Output($path,'F');

	
    }
    
    
    public function getTNTet($box,$conNoteNumber,$addressLoadId,$request,$method,$shipId,$invIncrementID='',$order,$addr,$etDate)
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
        
        $date1 = $etDate; //date('dMY'); ///13-3-2014
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