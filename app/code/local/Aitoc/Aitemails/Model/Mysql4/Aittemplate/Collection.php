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
class Aitoc_Aitemails_Model_Mysql4_Aittemplate_Collection extends Mage_Core_Model_Mysql4_Email_Template_Collection
{
    protected $_customFilter = array();
    
    protected $_flags = array();
    
    protected $_defaultTemplates = null;
    
    protected $_excludedTemplates = array(
    	'moneybookers_activateemail',
    	'enterprise_reminder_email_template'
    );
    
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $this->_renderFilters()
             ->_renderOrders()
             ->_renderLimit();

        $this->printLogQuery($printQuery, $logQuery);

        $defaultTemplates = $this->_getDefaultTemplates();

        /**
        * STEP1: first we load all default templates
        */
        $data = array();
        $templateCodes = array();
        foreach ($defaultTemplates as $templateCode => $aTemplate)
        {
//            $templateModel = Mage::getModel('core/email_template')->loadDefault($templateCode, Mage::registry('aitemails_email_template_scope_locale'));
            $data[] = array(
                'code'              => $templateCode,
                'file'              => Mage::helper('aitemails')->getEmailTemplateDefaultFilePath($aTemplate['file'], Mage::registry('aitemails_email_template_scope_locale')),
                'template_code'     => $aTemplate['label'],
                'custom_template'   => '',
                'scope'             => $this->getFlag('scope'),
                'scope_id'          => $this->getFlag('scope_id'),
//                'subject'           => $templateModel->getTemplateSubject(),
//                'sender'            => '',
            );
            $templateCodes[] = '"' . $templateCode . '"';
        }
        
        /**
        * STEP2: then we need to check which custom templates are used in the system config for current scope (website or/and store)
        */
        $configdataCollection = Mage::getModel('core/config_data')->getCollection();
        $configdataCollection->addScopeMultiPathFilter($this->getFlag('scope'), $this->getFlag('scope_id'), $templateCodes);
        $configTemplates = $configdataCollection->load()->toArray();
        
        foreach ($configTemplates['items'] as $configTpl)
        {
            if ($configTpl['value'] != str_replace('/', '_', $configTpl['path']) && is_numeric($configTpl['value']))
            {
                $templateModel = Mage::getModel('core/email_template')->load($configTpl['value']);
                foreach ($data as $i => $row)
                {
                    /**
                    * STEP3: and at the end we need to load custom templates that are used in config
                    */
                    if ($row['code'] == str_replace('/', '_', $configTpl['path']))
                    {
                        $data[$i]['custom_template']    = $templateModel->getTemplateCode();
                        $data[$i]['subject']            = $templateModel->getTemplateSubject();
                        $data[$i]['custom_template_id'] = $templateModel->getId();
                    }
                }
            }
        }
        
        // filtering data
        if ($this->_customFilter)
        {
            foreach ($data as $i => $row)
            {
                foreach ($this->_customFilter as $sField => $aFilter)
                {
                    $aFilter = array_values($aFilter);
                    foreach ($aFilter as $key => $sVal)
                    {
                        $aFilter[$key] = trim($sVal, '%\'');
                    }
                    
                    if (isset($row[$sField]))
                    {
                        if (false === strpos(strtolower($row[$sField]), strtolower($aFilter[0])))
                        {
                            unset($data[$i]);
                        }
                    }
                }
            }
        }

        //$data = $this->getData();
        
        $this->resetData();

        if (is_array($data)) {
            foreach ($data as $row) {
                $item = $this->getNewEmptyItem();
                if ($this->getIdFieldName()) {
                    $item->setIdFieldName($this->getIdFieldName());
                }
                $item->addData($row);
                $this->addItem($item);
            }
        }

        $this->_setIsLoaded();
        $this->_afterLoad();
        return $this;
    }
    
    public function getSize()
    {
        return count($this->_getDefaultTemplates());
    }
    
    protected function _getDefaultTemplates()
    {
    	if(null === $this->_defaultTemplates)
    	{
        	$defaultTemplates = Mage_Core_Model_Email_Template::getDefaultTemplates();
        	foreach($this->_excludedTemplates as $exT)
        	{
        		if(array_key_exists($exT, $defaultTemplates))
        		{
        			unset($defaultTemplates[$exT]);
        		}
        	}
        	$this->_defaultTemplates = $defaultTemplates;
    	}
        return $this->_defaultTemplates;
    }
    
    protected function _afterLoad()
    {
        return parent::_afterLoad();
    }
    
    public function addFieldToFilter($field, $condition=null)
    {
        parent::addFieldToFilter($field, $condition);
        $this->_customFilter[$field] = $condition;
        return $this;
    }
    
    /**
     * Retrieve Flag
     *
     * @param string $flag
     * @return mixed
     */
    public function getFlag($flag)
    {
        return isset($this->_flags[$flag]) ? $this->_flags[$flag] : null;
    }

    /**
     * Set Flag
     *
     * @param string $flag
     * @param mixed $value
     * @return Varien_Data_Collection
     */
    public function setFlag($flag, $value = null)
    {
        $this->_flags[$flag] = $value;
        return $this;
    }

    /**
     * Has Flag
     *
     * @param string $flag
     * @return bool
     */
    public function hasFlag($flag)
    {
        return array_key_exists($flag, $this->_flags);
    }
}