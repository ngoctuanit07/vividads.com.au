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
class Aitoc_Aitemails_Model_Aitemails extends Mage_Eav_Model_Entity_Attribute
{
    
    public function getAitfileUrl($iAitfileId)
    {
        return $this->getUrl('downloadable/download/sample', array('sample_id' => $iAitfileId));
    }
    
    /**
     * 
     * @param $exc
     * @param string $format model|url 
     * @return unknown_type
     */
    public function getAvalibleScope( Exception $exc = null , $format = 'model' )
    {
        $scope = 'stores';
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitemails')->getLicense()->getPerformer();
        $ruler = $performer->getRuler();
        $stores = $ruler->getAvailableStores();
        if (!$stores)
        {
            if ($exc)
            {
                throw $exc;
            }
            else
            {
                $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitemails')->getLicense()->getPerformer();
                $ruler = $performer->getRuler();
                $ruler->throwException();
            }
        }
        $stores = Mage::app()->getGroup(array_shift($stores))->getStores();
        $store = array_shift($stores);
        if ('url' == $format)
        {
            return array(
                'scope' => $scope,
                'scopeid' => $store->getCode()
            );
        }
        $scopeId = $store->getId();
        return array($scope,$scopeId);
    }
    
    public function applyCollectionScope($collection,$throwException = false)
    {
        list ($scope, $scopeId) = $this->_getCurrentScope();
        $collection->setFlag('scope', $scope);
        $collection->setFlag('scope_id', $scopeId);
        return $collection;
    }
    
    public function getCurrentScope()
    {
        return $this->_getCurrentScope();
    }
    
    public function getFreeScope()
    {
    	/**
        * Detecting current scope
        */
        if (Mage::app()->getRequest()->getParam('website') && Mage::app()->getRequest()->getParam('store'))
        {
            $scope = 'stores';
            $storeModel = Mage::getModel('core/store')->load(Mage::app()->getRequest()->getParam('store'), 'code');
            $scopeId = $storeModel->getId();
        } elseif (Mage::app()->getRequest()->getParam('website'))
        {
            $scope = 'websites';
            $websiteModel = Mage::getModel('core/website')->load(Mage::app()->getRequest()->getParam('website'), 'code');
            $scopeId = $websiteModel->getId();
        }
        else 
        {
            $scope = 'default';
            $scopeId = 0;
        }
        return array(Mage::app()->getRequest()->getParam('scope', $scope), Mage::app()->getRequest()->getParam('scopeid', $scopeId));
    }
    
    protected function _getCurrentScope()
    {
        list($scope,$scopeId) = $this->getFreeScope();
        return array(Mage::app()->getRequest()->getParam('scope', $scope), Mage::app()->getRequest()->getParam('scopeid', $scopeId));
    }
    
    public function addTemplateToConfig(Mage_Core_Model_Email_Template $template, $templateCode = '', $scope = '', $scopeId = null)
    {
        if (!$templateCode) 
        {
            $templateCode = Mage::app()->getRequest()->getParam('templatecode');
        }
        try
        {
        	if (is_null($scopeId))
        	{
            	list ($scope, $scopeId) = $this->_getCurrentScope();
        	}
	        
	        $oReq = Mage::app()->getFrontController()->getRequest();
	        if ($scope)
	        {
	            $templatePath = Mage::helper('aitemails')->getPathByEmailTemplateCode($templateCode);
	            if ($templatePath)
	            {
	                Mage::getConfig()->saveConfig($templatePath,$template->getId(),$scope,$scopeId);
	                Mage::getConfig()->reinit();
	                Mage::app()->reinitStores();
	                //$this->_getRulerResource()->markConfig($configDataModel);
	            }
	        }
	    }
        catch(Exception $exc)
        {
            
        }
    }
    
    /**
     * 
     * @return Aitoc_Aitemails_Model_Mysql4_Ruler
     */
    protected function _getRulerResource()
    {
        return Mage::getResourceModel('aitemails/ruler');
    }
    
    public function detectScopeLocale()
    {
        $localeData['totalRecords'] = 0;
        $configdataCollection = Mage::getModel('core/config_data')->getCollection();
        if (Mage::app()->getRequest()->getParam('website') && Mage::app()->getRequest()->getParam('store'))
        {
            $storeModel = Mage::getModel('core/store')->load(Mage::app()->getRequest()->getParam('store'), 'code');
            $configdataCollection->addScopePathFilter('stores', $storeModel->getId(), Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE);
            $localeData = $configdataCollection->load()->toArray();
        }
        $configdataCollection = Mage::getModel('core/config_data')->getCollection();
        if (0 == $localeData['totalRecords'] && Mage::app()->getRequest()->getParam('website'))
        {
            $websiteModel = Mage::getModel('core/website')->load(Mage::app()->getRequest()->getParam('website'), 'code');
            $configdataCollection->addScopePathFilter('websites', $websiteModel->getId(), Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE);
            $localeData = $configdataCollection->load()->toArray();
        }
        if (0 == $localeData['totalRecords'])
        {
            $localeCode = Mage::app()->getLocale()->getDefaultLocale();
        } else 
        {
            $localeCode = $localeData['items'][0]['value'];
        }
        return $localeCode;
    }
    
    public function processMassGenerate($aReplacement, $bReplaceExisting = true)
    {
        $this->setTotalReplaced(0);
        $this->setTotalGenerated(0);
        $this->setTotalReGenerated(0);
        $entries     = Mage::getConfig()->getNode('global/aitemails/email/template/default')->asArray();
        $collection  = Mage::getResourceSingleton('aitemails/aittemplate_collection');
        $collection  = $this->applyCollectionScope($collection);
        $aCollection = $collection->load()->toArray();
        $aCollection = $aCollection['items'];
        
        $aSearchAndReplace  = array(); // array of ('search' => 'replace')
        foreach ($aReplacement as $sEntryId => $sReplaceValue)
        {
            if ($sReplaceValue && isset($entries[$sEntryId]))
            {
                $aSearchAndReplace[$entries[$sEntryId]['value']] = $sReplaceValue;
            }
        }

        Mage::register('aitemails_template_save_noredirect', true);
        Mage::register('aitemails_template_save_noaddconfig', true);

        foreach ($aCollection as $aTemplate)
        {
            if ($aTemplate['custom_template'] && !$bReplaceExisting)
            {
                continue;
            }
            elseif($aTemplate['custom_template'] && isset($aTemplate['custom_template_id']))
            {
                $this->setTotalReGenerated(1 + $this->getTotalReGenerated());
                $attachments = Mage::getResourceSingleton('aitemails/aitattachment_collection')
                        ->addTemplateToFilter($aTemplate['custom_template_id']);
                foreach ($attachments as $attachment)
                {
                    $attachment->delete();
                }
            }  
            
            $templateModel = Mage::getModel('core/email_template')->loadDefault($aTemplate['code'], $this->detectScopeLocale());
            if ($templateModel->getTemplateText())
            {
                $template = Mage::getModel('core/email_template');
                if (isset($aTemplate['custom_template_id']) && $aTemplate['custom_template_id'])
                {
                    $template->load($aTemplate['custom_template_id']);
                }
                if ($aSearchAndReplace)
                {
                    // replacing in template
                    $sCustomTemplateText = $this->replaceInTemplate($templateModel->getTemplateText(), $aSearchAndReplace);
                    $sCustomTemplateSubject = $this->replaceInTemplate($templateModel->getTemplateSubject(), $aSearchAndReplace);
                }
                else
                {
                    $sCustomTemplateText = $templateModel->getTemplateText();
                    $sCustomTemplateSubject = $templateModel->getTemplateSubject();
                }
                $template->setTemplateSubject($sCustomTemplateSubject)
                         ->setTemplateCode($this->generateTemplatePrefixName() . $aTemplate['template_code'])
                         ->setTemplateText($sCustomTemplateText)
                         ->setTemplateStyles($templateModel->getTemplateStyles())
                         ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate());
                $type = constant(Mage::getConfig()->getModelClassName('core/email_template') . "::TYPE_HTML");
                $template->setTemplateType($type);
                $template->save();
                $this->addTemplateToConfig($template, $aTemplate['code']);
            }
        }        
        $this->setTotalGenerated(count($aCollection));
        Mage::unregister('aitemails_template_save_noredirect');
        Mage::unregister('aitemails_template_save_noaddconfig');
    }
    
    /*
     * Generate prefix for new template, which contains Store and Store view names
     * 
     * @return string
     */
    
    public function generateTemplatePrefixName ()
    {
        $storeModel = Mage::getModel('core/store')->load(Mage::app()->getRequest()->getParam('store'), 'code');
        // and saving template as custom
        if ($storeModel->getCode())
        {
            $sNewTemplateCode = $storeModel->getName().': '.$storeModel->getCode().' - ';
        }
        elseif (Mage::app()->getRequest()->getParam('website'))
        {
            $storeGroup = '';
            $websiteModel = Mage::getModel('core/website')->load(Mage::app()->getRequest()->getParam('website'), 'code');
            if ($websiteModel->getName())
            {
                $sNewTemplateCode = $websiteModel->getName().': '.$websiteModel->getCode().' - ';
            }
        }
        else 
        {
            $sNewTemplateCode = '';
        }        
        
        $templatePrefix = $sNewTemplateCode ? $sNewTemplateCode : '';
        
        return $templatePrefix;
        
    }
    
    public function processMassReplace($aReplacement)
    {
        $this->setTotalReplaced(0);
        $aSearchAndReplace = array();
        if (isset($aReplacement['search']) && isset($aReplacement['replace']))
        {
            foreach ($aReplacement['search'] as $i => $sSearch)
            {
                if ($sSearch and isset($aReplacement['replace'][$i]))
                {
                    $aSearchAndReplace[$sSearch] = $aReplacement['replace'][$i];
                }
            }
        }
        Mage::register('aitemails_template_save_noredirect', true);
        Mage::register('aitemails_template_save_noaddconfig', true);
        if ($aSearchAndReplace)
        {
            $collection  = Mage::getResourceSingleton('aitemails/aittemplate_collection');
            $collection  = $this->applyCollectionScope($collection);
            $aCollection = $collection->load()->toArray();
            $aCollection = $aCollection['items'];
            foreach ($aCollection as $aTemplate)
            {
                if (isset($aTemplate['custom_template_id']) && $aTemplate['custom_template_id'])
                {
                    $template = Mage::getModel('core/email_template');
                    $template->load($aTemplate['custom_template_id']);
                    
                    $sReplacedTemplateText = $this->replaceInTemplate($template->getTemplateText(), $aSearchAndReplace);
                    $sReplacedTemplateSubject = $this->replaceInTemplate($template->getTemplateSubject(), $aSearchAndReplace);
                    
                    $template->setTemplateSubject($sReplacedTemplateSubject)
                             ->setTemplateText($sReplacedTemplateText)
                             ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate());
                    $template->save();
                }
            }
        }
        Mage::unregister('aitemails_template_save_noredirect');
        Mage::unregister('aitemails_template_save_noaddconfig');
    }
    
    public function replaceInTemplate($sSourceText, $aSearchAndReplace)
    {
        $iCount = 0;
        $sResultText = str_replace(array_keys($aSearchAndReplace), array_values($aSearchAndReplace), $sSourceText, $iCount);
        $this->setTotalReplaced($iCount + $this->getTotalReplaced());
        return $sResultText;
    }
}