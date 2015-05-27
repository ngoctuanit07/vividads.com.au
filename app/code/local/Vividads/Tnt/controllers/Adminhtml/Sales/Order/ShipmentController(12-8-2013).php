<?php
 require_once 'Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php';
class Vividads_Tnt_Adminhtml_Sales_Order_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController
{    
    protected function _saveShipment($shipment)
    {																									   											
		$shipment->getOrder()->setIsInProcess(true);
		$transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($shipment->getOrder())
            ->save();
        $saveid = $this->getRequest()->getPost('saveid');
		$savedshipmentModel=Mage::getModel('multishipping/shipment')->load($saveid);
		$savedshipmentModel->setShipmentStatus(9);	
        $addressid=$savedshipmentModel->getShippingAddressId();				
		$shipments = Mage::getModel('sales/order_shipment')->getCollection()
			->setOrder('entity_id','DESC')
			->setPageSize(1)
			->setCurPage(1);					
	  	$shippingId = $shipments->getLastItem()->getId();
		$savedshipmentModel->setShipmentId($shippingId);	

		$shipments->getLastItem()->setShippingAddressId($addressid)->save();	

		Mage::getModel('sales/order_shipment')->getCollection()
			->setOrder('entity_id','DESC')
			->getLastItem()
			->setShippingAddressId($addressid)
			->save();
		$address_Model = Mage::getModel('sales/order_address')->load($addressid);
		$countryid=$address_Model->getCountryId();					
		if($countryid=='AU'){									
			   $conNoteNumber = $this->shipTNT();					   
			   $savedshipmentModel->setConsignmentNoteNumber($conNoteNumber);						
		}else{		
			$this->shipInternationalTNT($shipmentid);			  									
		}		
       	$savedshipmentModel->save();
		return $this;
    }
	public function responseOfTNT($shippingId,$orderId){
		$xml='<?xml version="1.0" standalone="yes"?>
<DOCUMENT>
<GROUPCODE>12345678</GROUPCODE>
<CREATE>
	<CONREF>ref 1</CONREF>
	<CONNUMBER>GE09889787GB</CONNUMBER>
	<SUCCESS>Y</SUCCESS>
	<CONREF>ref 2</CONREF>
	<CONNUMBER>GE098645789GB</CONNUMBER>
	<SUCCESS>Y</SUCCESS>
</CREATE>
<RATE>
	<PRICE>
		<RATEID>ref 1</RATEID>
		<SERVICE>productcode</SERVICE>
		<SERVICEDESC>servicedesc</SERVICEDESC>
		<OPTION>optioncode</OPTION>
		<OPTIONDESC>optiondesc</OPTIONDESC>
		<RATE>70 90</RATE>
		<RESULT>Y </RESULT>
	</PRICE>
	<PRICE>
		<RATEID>ref 2</RATEID>
		<SERVICE>productcode</SERVICE>
		<SERVICEDESC>servicedesc</SERVICEDESC>
		<OPTION>optioncode</OPTION>
		<OPTIONDESC>optiondesc</OPTIONDESC>
		<CURRENCY>GBP</CURRENCY>
		<RATE>34 50</RATE>
		<RESULT>Y </RESULT>
	</PRICE>
</RATE>
	<BOOK>
			<CONSIGNMENT>
			<CONREF>ref 1</CONREF>
			<CONNUMBER>GE09889787GB</CONNUMBER>
			<SUCCESS>Y</SUCCESS>
			<FIRSTTIMETRADER>N</FIRSTTIMETRADER>
			<BOOKINGREF>CVT 876543</BOOKINGREF>
			</CONSIGNMENT>
	</BOOK>
	<PRINT>
			<CONNOTE>CREATED</CONNOTE>
			<LABEL>CREATED</LABEL>
			<MANIFEST>CREATED</MANIFEST>
			<INVOICE>CREATED</INVOICE>
	</PRINT>
</DOCUMENT>';	
		$obj = simplexml_load_string($xml);	
		$conNumber = $obj->CREATE->CONNUMBER;	
		$model=Mage::getModel('sales/order_shipment_track')		
		->setParentId($shippingId)
		->setWeight('100')
		->setQty(45)
		->setOrderId($orderId)
		->setTrackNumber($conNumber)
		->setDescription()
		->setTitle()
		->setCarrierCode()
		->save();    
  		}
	public function shipInternationalTNT($shipmentid){								
			$shipModel=Mage::getModel('sales/order_shipment')->load($shipmentid);
			$totalQty=$shipmentModel['total_qty'];		
			$addressid=$shipmentModel['shipping_address_id'];				
			$address_Model = Mage::getModel('sales/order_address')->load($addressid);
				$company=$address_Model->getCompany();
				$pCode=$address_Model->getPostcode();
				$street=$address_Model->getStreet();
				$telephone=$address_Model->getTelephone();
				$countryid=$address_Model->getCountryId();
				$name=$address_Model->getName();
				$city=$address_Model->getCity();
				$email=$address_Model->getEmail();
				$shipAddress=$street[0];										
				$address=array();
				$addresslength=strlen($shipAddress);
								if($addresslength <= 30){ 
										 $address[0]=$shipAddress;
										 $address[1]="";
								}
								else{
										$addressString = wordwrap($shipAddress, 30, "\n", false);
										$address = explode("\n", $addressString);
									}																
									    $postData = Mage::app()->getRequest()->getPost();		
	$itemsModel = Mage::getModel('sales/order_shipment_item')->getCollection()->addFilter('parent_id',$shippingId);
		$data=$itemsModel->getData();					
		$volume=0;$length=0;$height=0;$width=0;$weight=0;
		$row=array();
		$row['width']=0;
		$row['height']=0;
		$row['length']=0;
		$row['weight']=0;
		$row['boxes']=0;		
		$row['price']=0;
			foreach($itemsModel as $item){
				$id=$item->getId();
				$modelData=Mage::getModel('sales/order_shipment_item')->load($id);
				$productQty=$modelData->getQty();					
				$productId=$modelData->getProductId();					
				$productModel=Mage::getModel('catalog/product')->load($productId);											
				$dimension=$productModel->getDimension();
				$width = $productModel->getWidth();
				$row['width']+=$width*$productQty;
				$height = $productModel->getHeight();
				$row['height']+=$height*$productQty;
				$length = $productModel->getLength();
				$row['length']+=$length*$productQty;
				$weight=$productModel->getWeight();
				$row['weight']+=$weight*$productQty;
				$price = $productModel->getPrice();
				$row['price']+=$price*$productQty;															
				$row['boxes']+=$productQty;	
				$pkg='<PACKAGE>
						<ITEMS>'.$productQty.'</ITEMS>
						<DESCRIPTION>box</DESCRIPTION>
						<LENGTH>'.trim($length).'</LENGTH>
						<HEIGHT>'.trim($height).'</HEIGHT>
						<WIDTH>'.trim($width).'</WIDTH>
						<WEIGHT>'.intval($weight).'</WEIGHT>
					</PACKAGE>';
				$row['pkg'].=$pkg;				
			}
			$volume+= (($row['length']*$row['height']*$row['width'])/4000 )*$row['boxes'];					
			$totalWeight=$row['weight'];
			$totalPrice=$row['price'];						
			$tntcode=$postData['tnt_service_code'];
			$packages = $row['pkg'];		
			$xml='<?xml version="1.0" standalone="no"?><!DOCTYPE ESHIPPER SYSTEM "http://164.39.41.88:81/ShipperDTD2.0/EShipperIN2.dtd">
<ESHIPPER>
<LOGIN>
	<COMPANY>vividintT</COMPANY>
	<PASSWORD>tnt12345</PASSWORD>
	<APPID>IN</APPID>
	<APPVERSION>2</APPVERSION>
</LOGIN>
<CONSIGNMENTBATCH>
<SENDER>
	<COMPANYNAME>VIVID ADS</COMPANYNAME>
	<STREETADDRESS1>302 BRIDGE STREET</STREETADDRESS1>
	<STREETADDRESS2>Sender Addr 2</STREETADDRESS2>
	<STREETADDRESS3>Sender Addr 3</STREETADDRESS3>
	<CITY>PORT MELBOURNE</CITY>
	<PROVINCE>Warwickshire</PROVINCE>
	<STATE>VIC</STATE>
	<POSTCODE>3207</POSTCODE>
	<COUNTRY>AU</COUNTRY>
	<ACCOUNT>021664906</ACCOUNT>
	<VAT></VAT>
	<CONTACTNAME>Mr Yasir Ahmad</CONTACTNAME>
	<CONTACTDIALCODE>1300</CONTACTDIALCODE>
	<CONTACTTELEPHONE>721614</CONTACTTELEPHONE>
	<CONTACTEMAIL>yasir.ahmad@gmail.com</CONTACTEMAIL>	
</SENDER>	
<CONSIGNMENT>
	<CONREF>ref2</CONREF>
	<DETAILS>
		<DELIVERY>
			<COMPANYNAME>'.trim($company).'</COMPANYNAME>
			<STREETADDRESS1>'.trim($address[0]).'</STREETADDRESS1>
			<STREETADDRESS2>'.trim($address[1]).'</STREETADDRESS2>  		
			<STREETADDRESS3>Del Addr 3</STREETADDRESS3>
			<CITY>'.trim($city).'</CITY>
			<PROVINCE></PROVINCE>
			<POSTCODE>'.trim($pCode).'</POSTCODE>
			<CITY>'.trim($city).'</CITY>
			<COUNTRY>'.trim($countryid).'</COUNTRY>
			<VAT>7668880</VAT>
			<CONTACTNAME>'.trim($name).'</CONTACTNAME>
			<CONTACTDIALCODE>1672</CONTACTDIALCODE>
			<CONTACTTELEPHONE>'.trim($telephone).'</CONTACTTELEPHONE>
			<CONTACTEMAIL>'.trim($email).'</CONTACTEMAIL>
		</DELIVERY>
		<CUSTOMERREF>DISKS</CUSTOMERREF>
		<CONTYPE>N</CONTYPE>
		<PAYMENTIND>S</PAYMENTIND>
		<ITEMS>'.trim($totalQty).'</ITEMS>
		<TOTALWEIGHT>'.trim($totalWeight).'</TOTALWEIGHT>
		<TOTALVOLUME>'.trim($volume).'</TOTALVOLUME>
		<CURRENCY>GBP</CURRENCY>
		<GOODSVALUE>'.trim($totalPrice).'</GOODSVALUE>
		<INSURANCEVALUE>0</INSURANCEVALUE>
		<INSURANCECURRENCY>AUD</INSURANCECURRENCY>
		<SERVICE>'.trim($tntcode).'</SERVICE>
		<OPTION>PR</OPTION>
		<DESCRIPTION>assorted office accessories</DESCRIPTION>
		<DELIVERYINST>ggg</DELIVERYINST>
		'.$packages.'
	</DETAILS>
</CONSIGNMENT>
</CONSIGNMENTBATCH>
<ACTIVITY>
<CREATE>
	<CONREF>ref2</CONREF>
</CREATE>
<RATE>
	<CONREF>ref2</CONREF>
</RATE>
<BOOK>
	<CONREF>ref2</CONREF>
</BOOK>
<PRINT>
	<CONNOTE>
		<CONREF>ref2</CONREF>
	</CONNOTE>
	<LABEL>
		<CONREF>ref2</CONREF>
	</LABEL>
	<MANIFEST>
		<CONREF>ref2</CONREF>
	</MANIFEST>
	<INVOICE>
		<CONREF>ref2</CONREF>
	</INVOICE>
</PRINT>
</ACTIVITY>
</ESHIPPER>';
} 
	public function shipTNT(){
		$phone="1300-72-16-14";
		$dat=date('Y-m-d');
		$time = date('H-i-s');
		$date=date("Y-m-d");		
		 $sql="SELECT TransmissionIdentifier FROM tnt_typeA WHERE FileGenerationDate = '".$dat."';";		
		$read= Mage::getSingleton('core/resource')->getConnection('core_read'); 
		$value=$read->query($sql);
		$row = $value->fetch(); 	
		$manifest_id=$row['TransmissionIdentifier'];
		if($row==NULL)
		{						
			   $sql="INSERT INTO tnt_typeA
(`SenderInterchangeAddress`,
`ReceiverInterchangeAddress`,
`TradingPartnerIdentifier`,
`Carrier`,
`FileGenerationDate`,
`FileGenerationTime`,
`FileVersionNumber`,
`RoutingAffectiveDate`,
`RoutingVersionNumber`,
`uploaded`)
VALUES('','','','TNT','".$dat."','".$time."','12','','',0);";
		try {
			$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
			$conn->query($sql);
			$manifest_id=$lastInsertId = $conn->lastInsertId();
			} catch (Exception $e){
				echo $e->getMessage();
				} 				
				$sql="insert into tnt_typeB values (".$lastInsertId.",'VIVID','21664906','VIVID ADS','302 BRIDGE STREET','', 'PORT MELBOURNE', 'VIC','3207','DESPATCH','".$phone."');";					
					try {
					$chkSystem = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);
					} catch (Exception $e){
					echo $e->getMessage();
										}			
		}//end of if($row==NULL) statement
		else{
					$sql="SELECT ConsignmentNoteNumber From tnt_typeC order by ConsignmentNoteNumber desc limit 1;";
					$read= Mage::getSingleton('core/resource')->getConnection('core_read'); 
					$value=$read->query($sql);
					$row = $value->fetch(); 
					$consignmentNoteNumber=$row['ConsignmentNoteNumber'];
					if($consignmentNoteNumber=="") $conNoteNumber=0;	
					 else $conNoteNumber=$consignmentNoteNumber;
					 $conNoteNumber=str_replace("VVD","",$conNoteNumber)+1;
					 if($conNoteNumber == 1 ) $conNoteNumber=50000;
					 $conNoteNumber=(string)str_pad($conNoteNumber,9,"0",STR_PAD_LEFT);
					 $conNote="VVD".str_pad($conNoteNumber,9,"0",STR_PAD_LEFT);				 
					 $lines='001' ; // number of items iww think												
		$shipModel = Mage::getModel('sales/order_shipment')->getCollection()
			->setOrder('entity_id','DESC')
			->setPageSize(1)
			->setCurPage(1);				
		$shipment_id = $shipModel->getLastItem()->getId();
		$shipmentModel=Mage::getModel('sales/order_shipment')->load($shipment_id);
		$totalQty=$shipmentModel['total_qty'];	
		$addressid=$shipmentModel['shipping_address_id'];
		$commentsCollection=Mage::getModel('sales/order_shipment_comment')->getCollection()->addFilter('parent_id',$shipment_id);
		$comment_id = $commentsCollection->getLastItem()->getId();
		$comment_Model=Mage::getModel('sales/order_shipment_comment')->load($comment_id);
		$comment=$comment_Model['comment'];		
										$address_Model = Mage::getModel('sales/order_address')->load($addressid);
										$company=$address_Model->getCompany();
										$pCode=$address_Model->getPostcode();
										$street=$address_Model->getStreet();
										$telephone=$address_Model->getTelephone();
										$name=$address_Model->getName();
										$city=$address_Model->getCity();
										$shipAddress=$street[0];										
										$address=array();
										$addresslength=strlen($shipAddress);
								if($addresslength <= 30){ 
										 $address[0]=$shipAddress;
										 $address[1]="";
								}
								else{
										$addressString = wordwrap($shipAddress, 30, "\n", false);
										$address = explode("\n", $addressString);
									}																
									    $postData = Mage::app()->getRequest()->getPost();																		
										
									  $tntcode=$postData['tnt_service_code']; 																				 		 								  																
					 $sql="insert into tnt_typeC values (".$manifest_id.",'".$conNote."','".$lines."','','','".$company."','".$address[0]."','".$address[1]."','".$city."','','".$pCode."','".$name."','".$telephone."','".$dat."','".$tntcode."',0,'S','0','0','','','','','','','','','','')";
						try {
								$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
								$conn->query($sql);								
							}  catch (Exception $e){
									echo $e->getMessage();
								}					
									$sql="insert into tnt_typeE values (".$manifest_id.",'".$conNote."',0,'".$comment."')";							
											try {
												$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
												$conn->query($sql);								
												}  catch (Exception $e){
													echo $e->getMessage();
													}
				// count product, weight and volume  against one order
				
				$orderId=$this->getRequest()->getParam('order_id');
				$orderModel=Mage::getModel('sales/order')->load($orderId);
				$increment_Id=$orderModel['increment_id'];	
				$invoicecode=$increment_Id;
				$weightunit="KG";
				$dimensionunit="CM";					
		$itemsModel = Mage::getModel('sales/order_shipment_item')->getCollection()->addFilter('parent_id',$shipment_id);
		$data=$itemsModel->getData();					
		$volume=$length=$height=$width=$weight=0;
		$row=array();
		$row['width']=$row['height']=$row['length']=$row['weight']=$row['boxes']=0;		
			foreach($itemsModel as $item){
				$id=$item->getId();
				$modelData=Mage::getModel('sales/order_shipment_item')->load($id);
				$productQty=$modelData->getQty();					
				$productId=$modelData->getProductId();					
				$productModel=Mage::getModel('catalog/product')->load($productId);											
				$dimension=$productModel->getDimension();
				$width = $productModel->getWidth();
				$row['width']+=$width*$productQty;
				$height = $productModel->getHeight();
				$row['height']+=$height*$productQty;
				$length = $productModel->getLength();
				$row['length']+=$length*$productQty;
				$weight=$productModel->getWeight();
				$row['weight']+=$weight*$productQty;															
				$row['boxes']+=$productQty;		
			}					
			$volume= (($row['length']*$row['height']*$row['width'])/4000 )*$row['boxes'];										
			$sql="insert into tnt_typeF values(".$manifest_id.",'".$conNote."',1,'".$invoicecode."','BOX','',".$row['boxes'].",'".$row['weight']."','KG','".$row['length']."','".$row['width']."','".$row['height']."','".$dimensionunit."','".$volume."','CC','','')";							
							try {
								$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
								$conn->query($sql);								
								}  catch (Exception $e){
									echo $e->getMessage();
									}									
								$s=1;
								$sumbox=$row['boxes'];								
								$xyz=$sumbox % 8;
					if($xyz > 0) $loop = $sumbox + (8-$xyz);
					else $loop = $sumbox;
						for($b=1;$b<=$loop;$b++){
							if($s == 8) $s=1;
								if($b<=$sumbox)
									$vals1[]='00313113'.($conNoteNumber).(str_pad($b,3,"0",STR_PAD_LEFT));
								else
									$vals1[]="";
							$s++;
						}						
					$chunks=array_chunk($vals1,8);
					$b=1;
					foreach($chunks as $vals){
						$putvals="'".implode("','",$vals)."'";
						$sql="insert into tnt_typeH values (".$manifest_id.",'".$conNote."','".$b."',".$putvals.")";	
						try {
								$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
								$conn->query($sql);								
								}  catch (Exception $e){
									echo $e->getMessage();
									}					
					$b++;
					
					}		 
					
		 Mage::getSingleton('core/session')->addError('Successfully Booked...'.$conNote.' ');
		return $conNote; 								
		}//end of else
	}

	
}


?>