<?php

class MageWorx_Adminhtml_Socialbooster_ServicesController extends Mage_Adminhtml_Controller_Action {    
    
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('report/socialbooster')
            ->_addBreadcrumb($this->__('Social Booster'), $this->__('Social Booster'))
            ->_addBreadcrumb($this->__('Services'), $this->__('Services'));
        return $this;
    }
    
    public function indexAction()
    {        
        $this->_title($this->__('Social Booster'))->_title($this->__('Services'));
        $this->_initAction()->renderLayout();
    }
    
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
    
    
    public function massResetAction()
    {        
        $ids = $this->getRequest()->getPost('ids', array());
        Mage::helper('socialbooster')->resetBookmark($ids);                
        $this->_redirect('*/*/');                
    }
    
    
    public function viewAction()
    {                
        $this->_title($this->__('Social Booster'))->_title($this->__('Service View'));
        $this->_initAction()->renderLayout();                       
    }
    
    public function viewgridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
    
    
}