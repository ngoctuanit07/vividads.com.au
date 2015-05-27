<?php

class MageWorx_Adminhtml_Block_Socialbooster_Services_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('bookmark_id');
        $this->setUseAjax(true);
        $this->setDefaultSort('clicks');
        $this->setDefaultDir('DESC');        
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'socialbooster/bookmark_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass())->setCounterTbl()->setShellRequest();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {                
        
        $this->addColumn('bookmark_id', array(
            'header'=> Mage::helper('socialbooster')->__('Bookmark #'),
            'width' => '50px',
            'type'  => 'text',
            'index' => 'bookmark_id',
            'align' => 'center',
        ));
       
        
        $this->addColumn('icon', array(
            'header' => Mage::helper('socialbooster')->__('Icon'),            
            'index' => 'bookmark_code',
            'renderer'  => 'mageworx/socialbooster_grid_renderer_icon',
            'filter'    => false,
            'sortable'  => false,            
            'width' => '40px',
            'align' => 'center',
        ));
        
        $this->addColumn('bookmark_title', array(
            'header' => Mage::helper('socialbooster')->__('Service Name'),
            'index' => 'bookmark_title',           
        ));

        
        $this->addColumn('pages_count', array(
            'header' => Mage::helper('socialbooster')->__('Pages'),
            'index' => 'pages_count',
            'type'  => 'number',
            'width' => '80px',            
        ));
        
        $this->addColumn('clicks', array(
            'header' => Mage::helper('socialbooster')->__('Clicks'),
            'index' => 'clicks',
            'type'  => 'number',
            'width' => '80px',            
        ));
        
        $this->addColumn('last_click', array(
            'header' => Mage::helper('socialbooster')->__('Last Click'),
            'index' => 'last_click',
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
                        'url'     => array('base'=>'*/socialbooster_services/view'),
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
        $this->setMassactionIdField('bookmark_id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        
        $this->getMassactionBlock()->addItem('reset_clicks', array(
             'label'=> Mage::helper('socialbooster')->__('Reset Clicks'),
             'url'  => $this->getUrl('*/socialbooster_services/massReset'),
        ));
                

        return $this;
    }

    public function getRowUrl($row)
    {        
        return $this->getUrl('*/socialbooster_services/view', array('id' => $row->getId()));                
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
