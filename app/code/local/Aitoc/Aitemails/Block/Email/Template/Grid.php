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
class Aitoc_Aitemails_Block_Email_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        $this->setEmptyText(Mage::helper('adminhtml')->__('No Templates Found'));
        $this->setRowClickCallback(null);
    }
    
    protected function _afterToHtml($html)
    {
        $html = "<script>varienGrid.prototype.rowMouseOver = function(event){var element = Event.findElement(event, 'tr');Element.addClassName(element, 'on-mouse');element.style.cursor = 'default';}</script>" . $html;
        return parent::_afterToHtml($html);
    }

    protected function _prepareCollection()
    {
        //$collection = Mage::getResourceSingleton('core/email_template_collection');
        $collection = Mage::getResourceSingleton('aitemails/aittemplate_collection');
        $collection = Mage::getModel('aitemails/aitemails')->applyCollectionScope($collection);        
        $this->setCollection($collection);
        parent::_prepareCollection();
        $this->getCollection()->setPageSize(200);
        
        return $this;
    }
    
    public function getPagerVisibility()
    {
        return false;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('code',
            array(
                'header'    =>Mage::helper('aitemails')->__('Default Template Name'),
                'index'     =>'template_code',
                'sortable'  => false,
        ));
        
        $this->addColumn('file',
            array(
                'header'=>Mage::helper('aitemails')->__('Default Template File'),
                'index'=>'file',
                'sortable'  => false,
        ));
        
        $this->addColumn('custom_template',
            array(
                'header'=>Mage::helper('aitemails')->__('Custom Template Name'),
                'index'=>'custom_template',
                'renderer' => 'aitemails/email_template_grid_renderer_customtemplate',
                'sortable'  => false,
        ));

//        $this->addColumn('subject',
//            array(
//                'header'=>Mage::helper('adminhtml')->__('Subject'),
//                'index'=>'subject'
//        ));
//        
//        $this->addColumn('sender',
//            array(
//                'header'=>Mage::helper('adminhtml')->__('Sender'),
//                'index'=>'sender',
//                'renderer' => 'aitemails/email_template_grid_renderer_sender'
//        ));
        
        $this->addColumn('action',
            array(
                'header'     => Mage::helper('adminhtml')->__('Action'),
                'index'      => 'template_id',
                'sortable'   => false,
                'filter'     => false,
                'width'      => '150px',
                'renderer'   => 'aitemails/email_template_grid_renderer_action'
        ));
        

        return $this;
    }

    public function getRowUrl($row)
    {
        return '';//$this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }

}