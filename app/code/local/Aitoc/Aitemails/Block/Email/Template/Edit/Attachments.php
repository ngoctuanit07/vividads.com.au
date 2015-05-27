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
class Aitoc_Aitemails_Block_Email_Template_Edit_Attachments extends Mage_Adminhtml_Block_Widget
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('aitemails/edit_attachments.phtml');
    }
    
    public function getEmailTemplate()
    {
        return Mage::registry('email_template');
    }
    
    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('aitemails')->__('Add New Row'),
                'id' => 'add_aitemails_item',
                'class' => 'add',
            ));
        return $addButton->toHtml();
    }
    
    public function getUsedDefault()
    {
        return is_null($this->getProduct()->getAttributeDefaultValue('aitfiles_title'));
    }

    public function getAitfilesTitle()
    {
        return Mage::getStoreConfig(Aitoc_Aitdownloadablefiles_Model_Aitfile::XML_PATH_AITFILE_TITLE);
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'upload_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                    'id'      => '',
                    'label'   => Mage::helper('adminhtml')->__('Upload Files'),
                    'type'    => 'button',
                    'onclick' => 'Downloadable.massUploadByType(\'aitattachment\')'
                ))
        );
    }

    public function getUploadButtonHtml()
    {
        return $this->getChild('upload_button')->toHtml();
    }
    
    public function getConfigJson()
    {
        $this->getConfig()->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('aitemails/file/upload', array('type' => 'aitattachment', '_secure' => true)));
        $this->getConfig()->setParams(array('form_key' => $this->getFormKey()));
        $this->getConfig()->setFileField('aitattachment');
        $this->getConfig()->setFilters(array(
            'all'    => array(
                'label' => Mage::helper('adminhtml')->__('All Files'),
                'files' => array('*.*')
            )
        ));
        $this->getConfig()->setReplaceBrowseWithRemove(true);
        $this->getConfig()->setWidth('32');
        $this->getConfig()->setHideUploadButton(false);
        return Zend_Json::encode($this->getConfig()->getData());
    }

    /**
     * Retrive config object
     *
     * @return Varien_Config
     */
    public function getConfig()
    {
        if(is_null($this->_config)) {
            $this->_config = new Varien_Object();
        }

        return $this->_config;
    }
    
    public function getAttachmentData()
    {
        $aitfilesArr = array();

        $collection = Mage::getResourceModel('aitemails/aitattachment_collection')
        ->addTemplateToFilter($this->getEmailTemplate())
        ->addTitleToResult(0)
        ->load()
        ;

        $oAitdownloadablefiles = Mage::getModel('aitemails/aitemails');
        $oAitattachmentModel = Mage::getModel('aitemails/aitattachment');
        
        if ($collection)
        {
            foreach ($collection as $oAitattachment)
            {
                $tmpAitfileItem = array(
                    'aitattachment_id' => $oAitattachment->getId(),
                    'title' => $oAitattachment->getStoreTitle(),
                    'aitattachment_url' => $oAitattachment->getAttachmentUrl(),
                    'aitattachment_type' => $oAitattachment->getAttachmentType(),
                    'sort_order' => $oAitattachment->getSortOrder(),
                );
                $file = Mage::helper('downloadable/file')->getFilePath(
                    $oAitattachmentModel->getBasePath(), $oAitattachment->getAttachmentFile()
                );

                if ($oAitattachment->getAttachmentFile() && is_file($file)) {
                    $name = '<a href="' . $oAitattachment->getFileUrl() . '" target="_blank">' .
                            Mage::helper('downloadable/file')->getFileFromPathFile($oAitattachment->getAttachmentFile()) .
                            '</a>';
                    $tmpAitfileItem['file_save'] = array(
                        array(
                            'file' => $oAitattachment->getAttachmentFile(),
                            'name' => $name,
                            'size' => filesize($file),
                            'status' => 'old'
                        ));
                }
                if ($this->getEmailTemplate() && $oAitattachment->getStoreTitle()) {
                    $tmpAitfileItem['store_title'] = $oAitattachment->getStoreTitle();
                }
                $aitfilesArr[] = new Varien_Object($tmpAitfileItem);
            }
        }

        return $aitfilesArr;
    }
}