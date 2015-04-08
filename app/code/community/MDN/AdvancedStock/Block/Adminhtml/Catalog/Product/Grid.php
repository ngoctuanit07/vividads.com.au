<?php

class MDN_AdvancedStock_Block_Adminhtml_Catalog_Product_Grid extends 
Displaze_MyProductType_Block_Adminhtml_Catalog_Product_Grid{

    protected function _prepareColumns()
    {

        parent::_prepareColumns();

        //replace qty column
        $this->addColumn('qty', array(
            'header'=> Mage::helper('AdvancedStock')->__('Stock Summary'),
            'index' => 'stock_summary',
            'renderer'	=> 'MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_StockSummary',
            'sortable'	=> false,
            'filter'	=> false
        ));

		/*remove column websites from Adminhtml_grid*/
		unset($this->_columns['websites']);
		return $this;
    }

}
