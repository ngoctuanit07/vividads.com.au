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

class MageWorx_Adminhtml_Block_Socialbooster_Pages_View_Grid extends Mage_Adminhtml_Block_Widget_Grid
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

    protected function _getCollectionClass()
    {
        return 'socialbooster/counter_collection';
    }

    protected function _prepareCollection()
    {
        $id = intval($this->getRequest()->getParam('id', 0));
        $url = Mage::getModel('socialbooster/counter')->load($id)->getUrl();
        
        $collection = Mage::getResourceModel($this->_getCollectionClass())->addUrlFilter($url)->setBookmarkTbl()->setShellRequest();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {                
        
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
