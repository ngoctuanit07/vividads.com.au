<?php

/**
  * @category       WL
  * @package        Auspost
  * @class          WL_Auspost_DataController
  * @description    Extended from the Mage_Core_Controller_Front_Action class, dispatch the action  requests which are used in Auspost extension
 */

class WL_Auspost_DataController extends Mage_Core_Controller_Front_Action
{ 

/**
 *
 * Handle Ajax action to get shipping duration
 * 
 */
        
    public function getShippingDurationAjaxAction()
    {
        $result = array('code'=>-1, 'message'=>"Error");
        try {
            $this->loadLayout();
            $result = array('code'=>0, 'message'=>"");

            $result['shipping_duration']['count'] = 1;
            $result['shipping_duration']['html'] = $this->getLayout()->getBlock('page_ajax_duration')->toHtml();
        }  catch (Exception $e) 
        {
            Mage::logException($e);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

/**
 *
 * Handle Ajax action to get collection points
 * 
 */
        
    public function getCollectionPointsAjaxAction ()
    {
        $this->loadLayout();
        $body = $this->getLayout()->getBlock('page_ajax_collectionpoint')->toHtml();
        $this->getResponse()->setBody($body);
    }

/**
 *
 * Handle Ajax action to get collection points by identifier
 * 
 */
    
    public function getCollectionPointByIdAjaxAction ()
    {
        $this->loadLayout();
        $body = $this->getLayout()->getBlock('page_ajax_collectionpointbyid')->toHtml();
        $this->getResponse()->setBody($body);
    }

/**
 *
 * Handle Ajax action to get delivery table
 * 
 */    
    public function getBusinessDaysAjaxAction ()
    {
        $this->loadLayout();
        $body = $this->getLayout()->getBlock('page_ajax_getbusinessdays')->toHtml();
        $this->getResponse()->setBody($body);
    } 

/**
 *
 * Handle Ajax action to get article traking
 * 
 */
    
    public function getTrackingAjaxAction ()     
    {
        $this->loadLayout();
        $body = $this->getLayout()->getBlock('page_ajax_gettrackingajax')->toHtml();
        $this->getResponse()->setBody($body);
    }

/**
 *
 * Handle Ajax action to get validated address
 * 
 */
    
    public function getAddressValidateAction() 
    {
        $this->loadLayout();
        $body = $this->getLayout()->getBlock('page_ajax_addressvalidate')->toHtml();
        $this->getResponse()->setBody($body);
    }

/**
 *
 * Handle Ajax action to get customer address
 * 
 */
    
    public function getCustomerAddressAction() {
        $this->loadLayout();
        $body = $this->getLayout()->getBlock('page_ajax_customeraddress')->toHtml();
        $this->getResponse()->setBody($body);
    }
/**
 *
 * Handle action to get capability of delivery by postcode
 * 
 */    
    public function getPostcodeCapabilityAjaxAction(){
        $this->loadLayout();
        $body = $this->getLayout()->getBlock('page_ajax_postcodecapability')->toHtml();
        $this->getResponse()->setBody($body);
    }
}