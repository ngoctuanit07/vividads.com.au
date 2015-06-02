<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      1.1.7
 * @license:     5aaeipF7ooGEnIcLG9rWordPqCAsU9nCJvGBjy56tC
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @author Adjustware
 */ 
class AdjustWare_Deliverydate_Block_Rewrite_AdminhtmlSalesOrderGrid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 
     *
     * @param Mage_Sales_Model_Mysql4_Order_Grid_Collection $collection
    */

    protected function _prepareColumns()
    {
        $res = parent::_prepareColumns();

        $action = $this->_columns['action'];
        unset($this->_columns['action']);
        
        $this->addColumn('delivery_date', array(
            'header' => Mage::helper('adjdeliverydate')->__('Delivery Date'),
            //'type'   => 'text',
            'index' =>'delivery_date',
            'renderer' => 'adminhtml/widget_grid_column_renderer_date',
            'filter' => 'adjdeliverydate/adminhtml_filter_delivery', //AdjustWare_Deliverydate_Block_Adminhtml_Filter_Delivery
            'width'  => '100px', 
        ));
        
        $this->_columns['action'] = $action;
        $this->_columns['action']->setId('action');
        $this->_lastColumnId = 'action';
 
        
        return $res;
    }

}