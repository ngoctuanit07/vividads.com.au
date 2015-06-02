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
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitemails_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $model = Mage::getModel('aitemails/aitemails');
        /* @var $model Aitoc_Aitemails_Model_Aitemails */
        $this->loadLayout();
        $this->_setActiveMenu('system/email_template/aitemails');
        $this->_addBreadcrumb(Mage::helper('aitemails')->__('Store Transactional Emails'), Mage::helper('aitemails')->__('Store Transactional Emails'));
        
        $this->_addContent($this->getLayout()->createBlock('aitemails/email_template', 'template'));
        $this->renderLayout();
    }
    
    public function massNewAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/email_template/aitemails');

        $this->_addContent($this->getLayout()->createBlock('aitemails/email_template_massnew', 'template.massnew'));
        $this->renderLayout();
    }
    
    public function massReplaceAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/email_template/aitemails');

        $this->_addContent($this->getLayout()->createBlock('aitemails/email_template_massreplace', 'template.massreplace'));
        $this->renderLayout();
    }

    public function massNewGenerateAction()
    {
        $aReplacement     = Mage::app()->getRequest()->getPost('aitemails_massnew');
        $bReplaceExisting = (bool) Mage::app()->getRequest()->getPost('aitemails_massnew_replace');
        
        $aitemailsModel = Mage::getModel('aitemails/aitemails');
        
        $aitemailsModel->processMassGenerate($aReplacement, $bReplaceExisting);
        
        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('aitemails')->__('Total %s templates have been generated. %s templates have been overwritten. In %s templates text replacing have been made.', 
                $aitemailsModel->getTotalGenerated(), 
                $aitemailsModel->getTotalReGenerated(), 
                $aitemailsModel->getTotalReplaced())
        );
        $this->getResponse()->setRedirect($this->getUrl('*/*', array('website' => Mage::app()->getRequest()->getParam('website'), 'store' => Mage::app()->getRequest()->getParam('store'))));
    }
    
    public function massReplaceProcessAction()
    {
        $aReplacement     = Mage::app()->getRequest()->getPost('aitemails_massreplace');
        
        $aitemailsModel = Mage::getModel('aitemails/aitemails');
        
        $aitemailsModel->processMassReplace($aReplacement);
        
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('aitemails')->__('Replace complete. Total %s entries replaced.', $aitemailsModel->getTotalReplaced()));
        $this->getResponse()->setRedirect($this->getUrl('*/*', array('website' => Mage::app()->getRequest()->getParam('website'), 'store' => Mage::app()->getRequest()->getParam('store'))));
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/email_template');
    }
}