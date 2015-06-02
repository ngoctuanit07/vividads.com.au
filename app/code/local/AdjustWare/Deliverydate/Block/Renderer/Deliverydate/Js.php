<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      1.1.7
 * @license:     5aaeipF7ooGEnIcLG9rWordPqCAsU9nCJvGBjy56tC
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * Description of Js
 *
 * @author lyskovets
 */
class AdjustWare_Deliverydate_Block_Renderer_Deliverydate_Js
    extends Mage_Core_Block_Template
{
    
    protected function _beforeToHtml() 
    {
        parent::_beforeToHtml();
        $this->_initFormFieldsIds();
        $this->_initControllerUrl();
    }
    
    private function _initControllerUrl()
    {
        $storeId = Mage::app()->getStore()->getId();
        switch(true)
        {
            //frontend context
            case($storeId>0):
                $route = 'adjdeliverydate/ajax/dateValidate';
                break;
            //adminhtml context
            case($storeId == 0):
                $route = 'adjdeliverydate/adminhtml_ajax/dateValidate';
                break;
            default:
                Mage::throwException('Invalid scope context');
        }
        $urlParams = array();
        if(Mage::app()->getStore()->isCurrentlySecure())
        {
            $urlParams['_secure'] = true;
        }
        $url = $this->getUrl($route,$urlParams);
        $this->setValidationUrl($url);
    }
    
    private function _initFormFieldsIds()
    {
        $baseName = 'delivery_date';
        $ids = array();
        if($this->getLayout()->getBlock('checkout_shipping'))
        {
            $addresses= $this->getLayout()->getBlock('checkout_shipping')->getAddresses();
            if($addresses)
            {
                foreach ($addresses as $key => $address)
                {
                    $addressId = $address->getAddressId();
                    $ids[$key] = $baseName.$addressId;
                }
                $this->setFormIds($ids);
                return;
            }
        }
        $ids[0] = $baseName;
        $this->setFormIds($ids);  
    }
        
}