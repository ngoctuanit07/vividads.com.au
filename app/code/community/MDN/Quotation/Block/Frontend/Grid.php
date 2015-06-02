<?php

class MDN_Quotation_Block_Frontend_Grid extends Mage_Core_Block_Template {

    /**
     * Get customer quotations
     */
    public function getQuotations() {

        $page = $this->getRequest()->getParam('p');
        $limit = $this->getRequest()->getParam('limit');
        if($page == '')
        $page = 1;
        
        if($limit == '')
        $limit = 10;
        
        $CustomerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $model = Mage::getModel('Quotation/Quotation');
        $list = $model->loadByCustomer($CustomerId);
        $list->setOrder('quotation_id', 'desc')->setCurPage($page)->setPageSize($limit);

        return $list;
    }

    /**
     * Get view url for quote
     *
     * @param unknown_type $Quotation
     */
    public function getViewUrl($Quotation) {
        //echo "QID : ".$Quotation->getId(); exit;
        return $this->getUrl('Quotation/Quote/View', array('quote_id' => $Quotation->getId()));
    }

    /**
     * Get url for quote request
     *
     */
    public function getNewRequestPostUrl() {
        return $this->getUrl('Quotation/Quote/SendTextualRequest');
    }

}