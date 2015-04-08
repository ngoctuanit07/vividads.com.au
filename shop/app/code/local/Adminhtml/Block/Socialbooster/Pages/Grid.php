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

class MageWorx_Adminhtml_Block_Socialbooster_Pages_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('counter_id');
        $this->setUseAjax(true);
        $this->setDefaultSort('clicks_count');
        $this->setDefaultDir('DESC');        
        $this->setSaveParametersInSession(true);
    }


    protected function _getCollectionClass()
    {
        return 'socialbooster/counter_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass())->groupByUrl()->addClicksCount()->addBookmarkCount()->setShellRequest();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {                
                                       
        
        $this->addColumn('url', array(
            'header' => Mage::helper('socialbooster')->__('Page'),
            'index' => 'url',           
        ));

        
        $this->addColumn('bookmark_count', array(
            'header' => Mage::helper('socialbooster')->__('Services'),
            'index' => 'bookmark_count',
            'type'  => 'number',
            'width' => '80px',            
        ));
        
        $this->addColumn('clicks_count', array(
            'header' => Mage::helper('socialbooster')->__('Clicks'),
            'index' => 'clicks_count',
            'type'  => 'number',
            'width' => '80px',            
        ));
        
        $this->addColumn('last', array(
            'header' => Mage::helper('socialbooster')->__('Last Click'),
            'index' => 'last',
            'type' => 'datetime',
            'width' => '150px',
        ));
        
        
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('socialbooster')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('socialbooster')->__('View'),
                        'url'     => array('base'=>'*/socialbooster_pages/view'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
        

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('counter_id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        
        $this->getMassactionBlock()->addItem('reset_clicks', array(
             'label'=> Mage::helper('socialbooster')->__('Reset Clicks'),
             'url'  => $this->getUrl('*/socialbooster_pages/massReset'),
        ));                

        return $this;
    }

    public function getRowUrl($row)
    {        
        return $this->getUrl('*/socialbooster_pages/view', array('id' => $row->getId()));                
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
