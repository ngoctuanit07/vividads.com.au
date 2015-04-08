<?php
/**
 * Email Template Manager
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitemails
 * @version      2.0.13
 * @license:     POwWoRanFs2vVHb6wy050abrHO3ftlyCeMSf01dXU3
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 *
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 * @package    Aitoc_Aitemails
 * @author lyskovets
 */
class Aitoc_Aitemails_Model_Observer_Save
    extends Aitoc_Aitemails_Model_Observer_Abstract  
{ 
    private $_header = 'Location';
    
    public function process(Varien_Event_Observer $event)
    {                
        $this->_init($event);
        $action = $this->getController()->getFullActionName();
        if(!$this->_isActionRequired($action))
        {
            return;
        }
        
        $comparingPath = '*/*';
        $requiredPath = 'aitemails/index/index';
        
        $comparingUrl = $this->_getUrl($comparingPath);
        $currentUrl = $this->_getResponseUrl();
        if($comparingUrl == $currentUrl)
        {
            $curWebsite = Mage::app()->getRequest()->getParam('website');
            $curStore   = Mage::app()->getRequest()->getParam('store');
            $aParams    = array();
            if ($curWebsite) $aParams['website'] = $curWebsite;
            if ($curStore) $aParams['store'] = $curStore;
            $requiredUrl = $this->_getUrl($requiredPath, $aParams);
            $this->_replaceUrl($requiredUrl);
        } 
    }
    
    private function _init($event)
    {
        $controller = $event->getControllerAction();
        /*@var $controller Mage_Adminhtml_System_Email_TemplateController */
        $this->setController($controller);
    }
    /**
     * 
     * @return boolean
     */
    private function _isActionRequired($action)
    {
        if($action == 'adminhtml_system_email_template_save')
        {
            return true;
        }
        return false;
    }
    
    private function _getResponseUrl()
    {
        $headers = $this->getController()->getResponse()->getHeaders();
        foreach($headers as $header)
        {
            if($header['name'] == $this->_header)
            {
                return $header['value'];
            }
        }
        return false;
    }
    
    private function _getUrl($path, $params = array())
    {
        $url = $this->getController()->getUrl($path, $params);
        return $url;
    }
    
    private function _replaceUrl($url)
    {
        return $this->getController()
            ->getResponse()
            ->setHeader($this->_header, $url, true);  
    }
        
}