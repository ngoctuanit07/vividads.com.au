<?php

class MDN_quotation_Helper_ShippingRates extends Mage_Core_Helper_Abstract {

    /**
     * return shipping rates for quote
     *
     * @param unknown_type $quote
     * @param unknown_type $shippingAddress
     */
    public function collectRates($quote, $shippingAddress, $currentshipAddress) {
        //define request
        $request = Mage::getModel('shipping/rate_request');
        $request->setAllItems($quote->getItemsArray());

       /* if ($shippingAddress) {

            $request->setDestCountryId($shippingAddress->getCountryId());
            $request->setDestRegionId($shippingAddress->getRegionId());
            $request->setDestRegionCode($shippingAddress->getRegionCode());

            $request->setDestCity($shippingAddress->getCity());
            $request->setDestPostcode($shippingAddress->getPostcode());
        }*/
       
       //Start 18_02_2014
       if (!empty($currentshipAddress)) {
          
          $regionCollection = Mage::getModel('directory/region_api')->items($currentshipAddress['country_id']);
          
          foreach($regionCollection as $region) {
			    if($currentshipAddress['region_id'] == $region['code'])
			    $currentshipAddress['region_id'] = $region['region_id'];
			   			
			}

            $request->setDestCountryId($currentshipAddress['country_id']);
            $request->setDestRegionId($currentshipAddress['region_id']);
            $request->setDestRegionCode($currentshipAddress['region']);

            $request->setDestCity($currentshipAddress['city']);
            $request->setDestPostcode($currentshipAddress['postcode']);
        } 
       else {

            $request->setDestCountryId();
            $request->setDestRegionId();
            $request->setDestRegionCode();

            $request->setDestCity();
            $request->setDestPostcode();
        }
        //End 18_02_2014

        $request->setBaseCurrency(Mage::app()->getStore()->getBaseCurrency());

        $request->setPackageValue($quote->getprice_ht());
        $request->setPackageValueWithDiscount($quote->getprice_ht());
        $request->setPackageWeight($quote->getweight());
        $request->setPackageQty($quote->getItemsQty());

        $request->setStoreId($quote->getCustomer()->getStore()->getId());
        $request->setWebsiteId($quote->getCustomer()->getStore()->getWebsiteId());
        $request->setFreeShipping($quote->getfree_shipping());

        //collect rates using request
        $results = Mage::getModel('shipping/shipping')
                        ->collectRates($request)
                        ->getResult();

        return $results;
    }

    /**
     * return rate matching shipping method
     *
     * @param unknown_type $quote
     * @param unknown_type $shippingAddress
     * @param unknown_type $shippingMethod
     */
   // public function getRate($quote, $shippingAddress, $shippingMethod) {
   
   public function getRate($quote, $shippingAddress, $shippingMethod, $currentshipAddress) {//19_02_2014
        $retour = null;
        
        //$result = $this->collectRates($quote, $shippingAddress); 
        $result = $this->collectRates($quote, $shippingAddress, $currentshipAddress); //19_02_2014

        foreach ($result->getAllRates() as $rate) {

            $key = $rate['carrier'] . '_' . $rate['method'];

            if ($key == $shippingMethod)
                $retour = $rate;
        }

        return $retour;
    }

}