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
 * Description of ajaxController
 *
 * @author lyskovets
 */
class AdjustWare_Deliverydate_AjaxController
    extends Mage_Core_Controller_Front_Action
{
    public function dateValidateAction()
    {
        $result = $this->_getValidator();
        if (isset($result['error']))
        {
            $result['valid_date'] = $this->_getValidDate();
        }
        $this->_sendResponse($result);   
    }
    
    private function _getValidDate()
    {
        $format = Mage::getStoreConfig('checkout/adjdeliverydate/format');
        $date = Mage::getModel('adjdeliverydate/holiday')->getFirstAvailableDate('unix');
        $dateFormer = new Zend_Date($date);
        $formatedDate = $dateFormer ->toString($format);
        return $formatedDate;
    }
    
    protected function _sendResponse($data)
    {
        $ajaxResponse = Mage::helper('core')->jsonEncode($data);
        $this->getResponse()->setBody($ajaxResponse);
    }
    
    private function _getValidator()
    {
        $baseFormId = 'delivery_date';
        $formId = key($this->getRequest()->getPost('adj'));
        if(strlen($formId) > strlen($baseFormId))
        {
            return Mage::getModel('adjdeliverydate/step')->multiprocess('shippingMethod'); 
        }
        return Mage::getModel('adjdeliverydate/step')->process('shippingMethod');
    }
    
}