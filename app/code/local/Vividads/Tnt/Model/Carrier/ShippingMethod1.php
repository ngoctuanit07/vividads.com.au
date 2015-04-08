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
			$response=$this->generateTntRequest($request);
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
		private function generateTntRequest(Mage_Shipping_Model_Rate_Request $request){
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
					<suburb>Melbourne</suburb>
					<postCode>3000</postCode>
					<state>VIC</state>
				  </deliveryAddress>
				  <shippingDate>2007-11-05</shippingDate>
				  <userCurrentLocalDateTime>
					2007-11-05T10:00:00
				  </userCurrentLocalDateTime>
				  <dangerousGoods>
					<dangerous>false</dangerous>
				  </dangerousGoods>
				  <packageLines packageType="D">
					<packageLine>
					  <numberOfPackages>1</numberOfPackages>
					  <dimensions unit="cm">
						<length>20</length>
						<width>20</width>
						<height>20</height>
					  </dimensions>
					  <weight unit="kg">
						<weight>1</weight>
					  </weight>
					</packageLine>
				  </packageLines>
				</cutOffTimeEnquiry>
				<termsOfPayment>
				  <senderAccount>21664906</senderAccount>
				  <payer>S</payer>
				</termsOfPayment>
			  </ratedTransitTimeEnquiry>
			</enquiry>';
			$xml['Username']='CIT00000000000035655';
			$xml['Password']='toyota11';
			$xml['XMLRequest']=$requestXml;
			$response=$this->requestTntResponse($xml);
			return $response;
		}
		private function requestTntResponse($xmlRequest){
			$responseXml = $this->sendTntRequest($xmlRequest);
			Mage::getSingleton('core/session')->addSuccess($responseXml);
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
		private function sendTntRequest($xmlArray){	
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