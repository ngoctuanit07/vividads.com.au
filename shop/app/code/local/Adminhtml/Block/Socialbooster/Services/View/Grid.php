<?php

class MageWorx_Adminhtml_Block_Socialbooster_Services_View_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('counter_id');
        $this->setUseAjax(true);
        $this->setDefaultSort('count');
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
        return 'socialbooster/counter_collection';
    }

    protected function _prepareCollection()
    {
        $id = intval($this->getRequest()->getParam('id', 0));        
        $collection = Mage::getResourceModel($this->_getCollectionClass())->addBookmarkFilter($id);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {                
        
        $this->addColumn('url', array(
            'header' => Mage::helper('socialbooster')->__('Page'),
            'index' => 'url',           
        ));                       

        
        $this->addColumn('count', array(
            'header' => Mage::helper('socialbooster')->__('Clicks'),
            'index' => 'count',
            'type'  => 'number',
            'width' => '80px',            
        ));
        
        
        $this->addColumn('last', array(
            'header' => Mage::helper('socialbooster')->__('Last Click'),
            'index' => 'last',
            'type' => 'datetime',
            'width' => '150px',
        ));
        
        
        $this->addColumn('action', array(
            'header'    => Mage::helper('socialbooster')->__('Action'),                
            'renderer'  => 'mageworx/socialbooster_grid_renderer_view',
            'index' => 'url',
            'filter'    => false,
            'sortable'  => false,
            'align'  => 'center',
            'width'     => '50px',
        ));
        

        return parent::_prepareColumns();
    }

    

    public function getRowUrl($row)
    {        
        return ''; //$row->getUrl();                
    }

    public function getGridUrl()
    {        
        return $this->getUrl('*/*/viewgrid', array('_current'=>true));
    }

}
