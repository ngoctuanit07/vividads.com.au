<?php
class Excellence_Ship_Model_Carrier_Excellence extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface {
    protected $_code = 'excellence';
 
   public function collectRates(Mage_Shipping_Model_Rate_Request $request)
      {
        // skip if not enabled
        if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
            return false;
        }
     
        /**
         * here we are retrieving shipping rates from external service
         * or using internal logic to calculate the rate from $request
         * you can see an example in Mage_Usa_Model_Shipping_Carrier_Ups::setRequest()
         */
     
        // get necessary configuration values
        $handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
     
        // this object will be returned as result of this method
        // containing all the shipping rates of this method
		$destCountry=$request->getDestCountryId();
    //echo $destCountry.'**'; die;
		if($destCountry == "AU"){
      //echo 'au'; 
			$response=$this->generateTntRequestAU($request);
    }
		else{
			$response=$this->generateTntRequestINT($request,$destCountry);
    }
			 $result = Mage::getModel('shipping/rate_result');
       //echo 'res'; die;
		if(is_array($response)){
        // $response is an array that we have
      //echo 'array'; die;
       foreach ($response as $rMethod) {
          // create new instance of method rate
          $method = Mage::getModel('shipping/rate_result_method');
     
          // record carrier information
          $method->setCarrier($rMethod->getCarrier());
          $method->setCarrierTitle($rMethod->getCarrierTitle());
     
          // record method information
          $method->setMethod($rMethod->getMethod());
          $method->setMethodTitle($rMethod->getMethodTitle());
     
          // rate cost is optional property to record how much it costs to vendor to ship
          $method->setCost($rMethod->getCost());
     
          // in our example handling is fixed amount that is added to cost
          // to receive price the customer will pay for shipping method.
          // it could be as well percentage:
          /// $method->setPrice($rMethod['amount']*$handling/100);
          $method->setPrice($rMethod->getPrice()+$handling);
     
          // add this rate to the result
          $result->append($method);
        }
			}
        return $result;
      }
private function generateTntRequestINT(Mage_Shipping_Model_Rate_Request $request,$countrycode){
      //echo 'xyz'; die;
			$qty = $weight = $height = $width = $length = 0;
			$date = date('Y-m-d');			
			$postCode = $request->getDestPostcode();
			$city = $request->getDestCity();
			$region = $request->getDestRegion();
			$weight = $request->getPackageWeight();
			$weight = $request->getPackageVolume();
			$qty = $request->getPackageQty();
			$allItems = $request->getAllItems();		
			foreach($allItems as $item){
				$itemId = $item->getId();									
				$quoteId = $item->getQuoteId();				
				$productId = $item->getProductId();				
				$quoteModel = Mage::getModel('sales/quote')->load($quoteId);				
				$addresses = $quoteModel->getAllShippingAddresses();
				$product = Mage::getModel('catalog/product')->load($productId);				
				$qty = $item->getQty();		
				$weight = $product->getWeight();
				$length = $product->getLength();
				$width = $product->getWidth();
				$height = $product->getHeight();
				$volume+= (($length*$width*$height)/4000 )*$qty;
			}
$requestXml=
'<?xml version="1.0" encoding="UTF-8"?>
<PRICEREQUEST>
  <LOGIN>
    <COMPANY>vividintT</COMPANY>
    <PASSWORD>tnt12345</PASSWORD>
    <APPID>PC</APPID>
  </LOGIN>
  <PRICECHECK>
    <RATEID>rate1</RATEID>
    <ORIGINCOUNTRY>AU</ORIGINCOUNTRY>
    <ORIGINTOWNNAME>Sydney</ORIGINTOWNNAME>
    <ORIGINPOSTCODE>2000</ORIGINPOSTCODE>
    <ORIGINTOWNGROUP/>
    <DESTCOUNTRY>'.$countrycode.'</DESTCOUNTRY>
    <DESTTOWNNAME>'.$city.'</DESTTOWNNAME>
    <DESTPOSTCODE>'.$postCode.'</DESTPOSTCODE>
    <DESTTOWNGROUP/>
    <CONTYPE>N</CONTYPE>
    <CURRENCY>AUD</CURRENCY>
    <WEIGHT>'.$weight.'</WEIGHT>
    <VOLUME>'.$volume.'</VOLUME>
	<ACCOUNT accountcountry="AU">021664906</ACCOUNT>
    <ITEMS>1</ITEMS>
  </PRICECHECK>
</PRICEREQUEST>';
//echo $requestXml; die;
			$xml=array();
			$xml['xml_in']=$requestXml;

		  $response=$this->requestTntResponseINT($xml);
			return $response;			
}
private function generateTntRequestAU(Mage_Shipping_Model_Rate_Request $request){
        //echo "From AU"; die;
			
			$qty = $weight = $height = $width = $length = 0;
			$date = date('Y-m-d');			
			$postCode = $request->getDestPostcode();
			$city = $request->getDestCity();
			
			
			//start 21_02_2014
			if($request->getDestRegion() != '')
			$region = $request->getDestRegion();
			else
			$region = $request->getDestRegionId();
			//End 21_02_2014
			
			$regionCollection = Mage::getModel('directory/region_api')->items($request->getDestCountryId());
          
			foreach($regionCollection as $region1) {
					  if($region == $region1['name'])
					  $region = $region1['code'];
							      
				      }
			
			$weight = $request->getPackageWeight();
			$qty = $request->getPackageQty();
			$allItems = $request->getAllItems();		
			$requestXml='<?xml version="1.0"?>
			<enquiry xmlns="http://www.tntexpress.com.au">
			  <ratedTransitTimeEnquiry>
				<cutOffTimeEnquiry>
				  <collectionAddress>
					<suburb>Sydney</suburb>
					<postCode>2000</postCode>
					<state>NSW</state>
				  </collectionAddress>
				  <deliveryAddress>
					<suburb>'.$city.'</suburb>
					<postCode>'.$postCode.'</postCode>
					<state>'.$region.'</state>
				  </deliveryAddress>
				  <shippingDate>'.$date.'</shippingDate>
				  <userCurrentLocalDateTime>
					2007-11-05T10:00:00
				  </userCurrentLocalDateTime>
				  <dangerousGoods>
					<dangerous>false</dangerous>
				  </dangerousGoods>
				  <packageLines packageType="D">';
          //echo "Req XML:".$requestXml; die;
			foreach($allItems as $item){
			   
				$itemId = $item->getId();
				$quoteId = $item->getQuoteId();				
				$productId = $item->getProductId();				
				$quoteModel = Mage::getModel('sales/quote')->load($quoteId);				
				$addresses = $quoteModel->getAllShippingAddresses();
				$product = Mage::getModel('catalog/product')->load($productId);				
				$qty = $item->getQty();
        //echo '**'.$qty; die;
			    if($product->getTypeId() == 'simple')//14_03_2014
			    {
				if($product->getWeight()!=""){
						$weight = $product->getWeight();
				}
				else{
						$weight = 10;
				}
				if($product->getLength()!=""){
						$length = $product->getLength();
				}
				
				else{
							$length = 10;
						
				}
				
			  if($product->getWidth()!=""){
						$width = $product->getWidth();
						
				}
				else{
					$width =10;	
				}
				if($product->getHeight()!=""){
						$height = $product->getHeight();
				}
				else{
						$height =10;
				}
				//$volume+= (($length*$width*$height)/4000 )*$qty;
				  $requestXml.=
					'
					<packageLine>
					  <numberOfPackages>'.$qty.'</numberOfPackages>
					  <dimensions unit="cm">
						<length>'.$length.'</length>
						<width>'.$width.'</width>
						<height>'.$height.'</height>
					  </dimensions>
					  <weight unit="kg">
						<weight>'.$weight.'</weight>
					  </weight>
					</packageLine>';
			    }
			    else{
				//start 14_03_2014
				if($item->getQuotationId() != '')
				{
				    $temptableItem=Mage::getSingleton('core/resource')->getTableName('quotation_bundle_item');
				    $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
				    
				    $sqlItem = $connectionRead->select()
						    ->from($temptableItem, array('*'))
						    ->where("parent_item_id = '".$itemId."' AND quotation_id = '".$item->getQuotationId()."' ");
				    $chkItem = $connectionRead->fetchAll($sqlItem);
				    foreach($chkItem as $bundle_option)
				    {
					$qty = $bundle_option['qty'];
					$product = Mage::getModel('catalog/product')->load($bundle_option['product_id']);		
					    if($product->getWeight()!=""){
							$weight = $product->getWeight();
					}
					else{
							$weight = 10;
					}
					if($product->getLength()!=""){
							$length = $product->getLength();
					}
					
					else{
								$length = 10;
							
					}
					
					if($product->getWidth()!=""){
							$width = $product->getWidth();
							
					}
					else{
						$width =10;	
					}
					if($product->getHeight()!=""){
							$height = $product->getHeight();
					}
					else{
							$height =10;
					}
					//$volume+= (($length*$width*$height)/4000 )*$qty;
					  $requestXml.=
						'
						<packageLine>
						  <numberOfPackages>'.$qty.'</numberOfPackages>
						  <dimensions unit="cm">
							<length>'.$length.'</length>
							<width>'.$width.'</width>
							<height>'.$height.'</height>
						  </dimensions>
						  <weight unit="kg">
							<weight>'.$weight.'</weight>
						  </weight>
						</packageLine>';
				    }
					
				}
				//End 14_03_2014
				
			    }
			}

			$requestXml.='
			</packageLines>
				</cutOffTimeEnquiry>
				<termsOfPayment>
				  <senderAccount>21664906</senderAccount>
				  <payer>S</payer>
				</termsOfPayment>
			  </ratedTransitTimeEnquiry>
			</enquiry>
';

//			Mage::getSingleton('core/session')->addSuccess($requestXml);
			$xml['Username']='CIT00000000000035655';
			$xml['Password']='toyota11';
			$xml['XMLRequest']=$requestXml;
			$response=$this->requestTntResponseAU($xml);
			//echo $response; die;
			return $response;			
}
private function requestTntResponseINT($xmlRequest){
        
    $responseXml = $this->sendTntRequestINT($xmlRequest);

//			Mage::getSingleton('core/session')->addSuccess($responseXml);
	   $xml=simplexml_load_string($responseXml);
	   $ratedArray = $xml->PRICE;
		   foreach($ratedArray as $ratedProduct){
			    $rate = Mage::getModel('shipping/rate_result_method');
			    $rate->setCarrier($this->_code);
			    $rate->setCarrierTitle($this->getConfigData('title'));
			    $option="";
			    if($ratedProduct->OPTION <> 'NONE') $option=$ratedProduct->OPTION;

			    $rate->setMethod($ratedProduct->SERVICE."-".$option); 
			    $optiondesc="";
			    if($ratedProduct->OPTIONDESC <> 'NONE') $optiondesc=$ratedProduct->OPTIONDESC;
			    $rate->setMethodTitle($ratedProduct->SERVICEDESC."-".$optiondesc);
			    $rate->setCost($ratedProduct->RATE);
			    
			    ///17-2-2014  GC S
			    $charge = $ratedProduct->RATE*1.40;
			    $margin = $this->getConfigData('profit_margin')/100;
			    $charge = $charge + ($charge*$margin);
			    
			    $rate->setPrice($charge);
			    //$rate->setPrice($ratedProduct->RATE*1.40); //You should calculate this or obtain in a service 
			    ///17-2-2014  GC E
			    
			    
			    $response[]=$rate;
		   }
	    return $response;
}
private function requestTntResponseAU($xmlRequest){
//echo '<pre>';print_r($xmlRequest) ; die;
	 $responseXml = $this->sendTntRequestAU($xmlRequest);
	//echo $responseXml; 
	$xml=simplexml_load_string($responseXml);
	if($xml->error[0]->description != ""){
//echo 'error'; die;
			return $xml->error->description;
		}
//echo 'aaaa'; die;
	$ratedArray = $xml->ratedTransitTimeResponse[0]->ratedProducts[0];
		foreach($ratedArray as $ratedProduct){
			 $rate = Mage::getModel('shipping/rate_result_method');
			 $rate->setCarrier($this->_code);
			 $rate->setCarrierTitle($this->getConfigData('title'));
			 $rate->setMethod($ratedProduct->product->code); 
			 $rate->setMethodTitle($ratedProduct->product->description);
			 $rate->setCost($ratedProduct->quote->price);
			 
			 ///17-2-2014  GC S
			 $charge = $ratedProduct->quote->price*1.40;
			 $margin = $this->getConfigData('profit_margin')/100;
                         $charge = $charge + ($charge*$margin);
			 
			 $rate->setPrice($charge);
			 //$rate->setPrice($ratedProduct->quote->price*1.40); //You should calculate this or obtain in a service 
			 ///17-2-2014  GC E
			 
			 
			 
			 $response[]=$rate;
		}
// echo $response; die;
	 return $response;
}
private function sendTntRequestINT($xmlArray){	
	$iClient = new Varien_Http_Client();
$iClient->setUri('https://express.tnt.com/expressconnect/pricing/getprice')
    ->setMethod('POST')
	->setConfig(array(
'maxredirects'=>0,
'timeout'=>30,
));
	$iClient->setParameterPost($xmlArray);    
	$response = $iClient->request();

    if ($response->isSuccessful()) 
	 $results = $response->getBody();
	return $results;
}     
private function sendTntRequestAU($xmlArray){	
	$iClient = new Varien_Http_Client();
$iClient->setUri('https://www.tntexpress.com.au/Rtt/inputRequest.asp')
    ->setMethod('POST')
	->setConfig(array(
'maxredirects'=>0,
'timeout'=>30,
));
	$iClient->setParameterPost($xmlArray);    
	$response = $iClient->request();
    if ($response->isSuccessful()) 
	 $results = $response->getBody();
	return $results;
}
    public function getAllowedMethods()
    {
        return array('excellence'=>$this->getConfigData('name'));
    }
}
