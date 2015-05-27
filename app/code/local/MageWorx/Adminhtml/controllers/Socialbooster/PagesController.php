<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SocialBooster
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Social Booster extension
 *
 * @category   MageWorx
 * @package    MageWorx_SocialBooster
 * @author     MageWorx Dev Team
 */

class MageWorx_Adminhtml_Socialbooster_PagesController extends Mage_Adminhtml_Controller_Action {    
    
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('report/socialbooster')
            ->_addBreadcrumb($this->__('Social Booster'), $this->__('Social Booster'))
            ->_addBreadcrumb($this->__('Pages'), $this->__('Pages'));
        return $this;
    }
    
    public function indexAction()
    {        
        $this->_title($this->__('Social Booster'))->_title($this->__('Pages'));
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
        Mage::helper('socialbooster')->resetPages($ids);                
        $this->_redirect('*/*/');                
    }
    
    
    public function viewAction()
    {                
        $this->_title($this->__('Social Booster'))->_title($this->__('Page View'));
        $this->_initAction()->renderLayout();                       
    }
    
    public function viewgridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
    
    
}