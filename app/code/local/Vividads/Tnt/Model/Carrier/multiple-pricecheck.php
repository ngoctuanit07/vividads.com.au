<?php
     
    /**
    * Our test shipping method module adapter
    */
    class Vividads_Tnt_Model_Carrier_ShippingMethod extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
    {
      /**
       * unique internal shipping method identifier
       *
       * @var string [a-z0-9_]
       */
      protected $_code = 'vividadstnt';
     
      /**
       * Collect rates for this shipping method based on information in $request
       *
       * @param Mage_Shipping_Model_Rate_Request $data
       * @return Mage_Shipping_Model_Rate_Result
       */
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
		if($destCountry == "AU") 
			$response=$this->generateTntRequestAU($request);
		else
			$response=$this->generateTntRequestINT($request,$destCountry);

		echo 'response='.$response;
		exit;



			 $result = Mage::getModel('shipping/rate_result');
		if(is_array($response)){
        // $response is an array that we have
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
			$qty = $weight = $height = $width = $length = 0;
			$date = date('Y-m-d');			
			$postCode = $request->getDestPostcode();
			$city = $request->getDestCity();
			$region = $request->getDestRegion();
			$weight = $request->getPackageWeight();
			$weight = $request->getPackageVolume();
			$qty = $request->getPackageQty();
			$allItems = $request->getAllItems();		
$requestXml=
'<?xml version="1.0" encoding="UTF-8"?>
<PRICEREQUEST>
  <LOGIN>
    <COMPANY>vividintT</COMPANY>
    <PASSWORD>tnt12345</PASSWORD>
    <APPID>PC</APPID>
  </LOGIN>';

$counter=0;
			foreach($allItems as $item){
$counter++;
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
				$volume= (($length*$width*$height)/4000 );
$requestXml.=
'  <PRICECHECK>
    <RATEID>rate'.$counter.'</RATEID>
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
    <ITEMS>'.$qty.'</ITEMS>
  </PRICECHECK>';
			}

 $requestXml.=
'
</PRICEREQUEST>';
			$xml=array();
			$xml['xml_in']=$requestXml;

			$response=$this->requestTntResponseINT($xml);
			return $response;			
		}
		private function generateTntRequestAU(Mage_Shipping_Model_Rate_Request $request){
			
			$qty = $weight = $height = $width = $length = 0;
			$date = date('Y-m-d');			
			$postCode = $request->getDestPostcode();
			$city = $request->getDestCity();
			$region = $request->getDestRegion();
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
					<state>VIC</state>
				  </deliveryAddress>
				  <shippingDate>'.$date.'</shippingDate>
				  <userCurrentLocalDateTime>
					2007-11-05T10:00:00
				  </userCurrentLocalDateTime>
				  <dangerousGoods>
					<dangerous>false</dangerous>
				  </dangerousGoods>
				  <packageLines packageType="D">';
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
			Mage::getSingleton('core/session')->addSuccess($requestXml);
			$xml['Username']='CIT00000000000035655';
			$xml['Password']='toyota11';
			$xml['XMLRequest']=$requestXml;
			$response=$this->requestTntResponseAU($xml);
			return $response;			
		}
		private function requestTntResponseINT($xmlRequest){
		echo $responseXml = $this->sendTntRequestINT($xmlRequest);
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
					 $rate->setPrice($ratedProduct->RATE*1.40); //You should calculate this or obtain in a service 
					 $response[]=$rate;
				}
			 return $response;
			}
		private function requestTntResponseAU($xmlRequest){
			$responseXml = $this->sendTntRequestAU($xmlRequest);
			$xml=simplexml_load_string($responseXml);
			$ratedArray = $xml->ratedTransitTimeResponse[0]->ratedProducts[0];
				foreach($ratedArray as $ratedProduct){
					 $rate = Mage::getModel('shipping/rate_result_method');
					 $rate->setCarrier($this->_code);
					 $rate->setCarrierTitle($this->getConfigData('title'));
					 $rate->setMethod($ratedProduct->product->code); 
					 $rate->setMethodTitle($ratedProduct->product->description);
					 $rate->setCost($ratedProduct->quote->price); 
					 $rate->setPrice($ratedProduct->quote->price*1.40); //You should calculate this or obtain in a service 
					 $response[]=$rate;
				}
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
      /**
       * This method is used when viewing / listing Shipping Methods with Codes programmatically
       */
      public function getAllowedMethods() {
        return array($this->_code => $this->getConfigData('name'));
      }
    }