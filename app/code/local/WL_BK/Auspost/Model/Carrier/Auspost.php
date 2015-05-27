<?php

/**
 * @category       WL
 * @package        Auspost
 * @class          WL_Auspost_Model_Carrier_Auspost
 * @description    Override the Mage_Shipping_Model_Carrier_Abstract class to add more shipping methods
 */

class WL_Auspost_Model_Carrier_Auspost extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'auspost';

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return The availave Auspost shipping methods
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::getStoreConfig('carriers/' . $this->_code . '/active')) {
            return false;
        }

        $result = Mage::getModel('shipping/rate_result');

        $_helper = Mage::helper('auspost');
        $_services = explode(',', $_helper->getEnabledServices());
        if(Mage::getStoreConfig('carriers/auspost/auspost_enable_collection_point'))
            $_services[] = WL_Auspost_Model_Config_ServiceMultiSelectionOptions::COLLECTION_POINT;

        if (!count($_services))
            return $result;

        $default_service = WL_Auspost_Model_Config_ServiceMultiSelectionOptions::getAllOptions();

        foreach ($default_service as $_code => $_value) {
            if(!in_array($_code,$_services))
                continue;
            $method = Mage::getModel('shipping/rate_result_method');

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($_helper->getTitle());
            $method->setMethod($_code);

            $method->setMethodTitle($_value . ' ');

            //$shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
            $price = $_helper->getShippingPrice($_code);
            $method->setCost($price);
            $method->setPrice($price);
            $method->setDeliveryType($_code);
            $result->append($method);
        }


        return $result;
    }

    /**
     * @return the all shipping methods, Auspost only in this case.
     */
    public function getAllowedMethods()
    {
        return array('auspost' => $this->getConfigData('auspost_method_name'));
    }
}