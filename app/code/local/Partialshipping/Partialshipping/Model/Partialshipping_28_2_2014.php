<?php

class Partialshipping_Partialshipping_Model_Partialshipping extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('partialshipping/partialshipping');
    }
    
    public function getTNTlebal($box,$conNoteNumber,$addressLoadId,$request,$method,$shipId,$invIncrementID='',$order)
    {
        extract($request);
       
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
                        $barcode = "6104".$conNote.(str_pad($counter,3,"0",STR_PAD_LEFT))."0".(str_pad($postCode,5,"0",STR_PAD_RIGHT));						   
                        $params = TCPDF_STATIC::serializeTCPDFtagParameters(array($barcode, 'C128', '', '', 80, 30, 0.4, array('position'=>'S', 'border'=>false, 'padding'=>4, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>true, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4), 'N'));
                        $itemno = (str_pad($q,3,"0",STR_PAD_LEFT)) . $conNote . (str_pad($j,3,"0",STR_PAD_LEFT));
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
        <td style="font-size:13"><strong>{$method[0]}</strong></td>
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
                <tr><td>$page[0]</td><td width="120"> </td><td>$page[1]</td></tr>
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
        $f=1;
        $date1 = date('dMY');
        do{
	    $file = Mage::getBaseDir('media') . DS ."shiplabel" . DS ."ET-".$date1.$f.'.txt';
	    $remote_file = '/outbox/'."ET-".$date1.$f.'.txt';
	    $f++;
        }while(file_exists($file));

        $handle = fopen($file, 'w') or die('Cannot open file:  '.$file);
        $manifat_number = 1297;
        
        $shMethod=$method[0];
        $shPrice=$method[1];
        
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
        $data = "A".(str_pad($manifat_number,20,"0",STR_PAD_LEFT))."                                          TNT".date("YmdHi")."12";
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
        
        $nameT = str_pad($nameT, strlen($nameT)+(30-strlen($nameT)), $pad_string, STR_PAD_RIGHT);
        $street = str_pad($street, strlen($street)+(60-strlen($street)), $pad_string, STR_PAD_RIGHT);
        $city = str_pad($city, strlen($city)+(20-strlen($city)), $pad_string, STR_PAD_RIGHT);
        $regCode = str_pad($regCode, strlen($regCode)+(3-strlen($regCode)), $pad_string, STR_PAD_RIGHT);
        $postcode = str_pad($postcode, strlen($postcode)+(4-strlen($postcode)), $pad_string, STR_PAD_RIGHT);
        $cname = $firstName." ".$lastName;
        $cname = str_pad($cname, strlen($cname)+(20-strlen($cname)), $pad_string, STR_PAD_RIGHT);
        $telephone = str_pad($telephone, strlen($telephone)+(13-strlen($telephone)), $pad_string, STR_PAD_RIGHT);
        $date = str_pad(date("dmY"), strlen(date("dmY"))+(8-strlen(date("dmY"))), $pad_string, STR_PAD_RIGHT);
        
        
        
        
        $data .="\n"."C".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad($j,3,"0",STR_PAD_LEFT))."                         ".$nameT.$street.$city.$regCode.$postcode.$cname.$telephone.$date.$tnt_methos[$method[0]]."  0S0000000000 ";
        
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
                
                //for($j ; $j<=$boxdata_item['box'] ; $j++){
                
                        
                        //$conNoteNumber="VVD000055714";
                        $conNote=str_replace("VVD","00313113",$conNoteNumber);// It wil be dynamic
                        $barcode = "6104".$conNote.(str_pad($counter,3,"0",STR_PAD_LEFT))."0".(str_pad($postCode,5,"0",STR_PAD_RIGHT));						   
                        
                        $itemno = (str_pad($q,3,"0",STR_PAD_LEFT)) . $conNote . (str_pad($j,3,"0",STR_PAD_LEFT));
                                        



                        if($j%7 == 0)
                        {
                        $allitem .= $conNote . (str_pad($j,3,"0",STR_PAD_LEFT));
                        $data .="\n"."H".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad($q,3,"0",STR_PAD_LEFT)).$allitem;
                        $l++;
                        $allitem = '';
                        
                        }
                        elseif($j == $boxdata_item['box'])
                        {
                        $allitem .= $conNote . (str_pad($j,3,"0",STR_PAD_LEFT));
                        $data .="\n"."H".(str_pad($manifat_number,10,"0",STR_PAD_LEFT)).$conNoteNumber."   ".(str_pad(($q+1),3,"0",STR_PAD_LEFT)).$allitem;
                        $l++;
                        $allitem = '';
                        }
                        else
                        $allitem .= $conNote . (str_pad($j,3,"0",STR_PAD_LEFT));
                        
                        
                        $counter++;
                        
                                        //}
                                        $j++;
                }
        }
				
				
		
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