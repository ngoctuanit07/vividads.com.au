<?php


class MDN_AdvancedStock_Block_Adminhtml_Sales_Order_Create_Search_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
{
    protected function _prepareColumns()
    {
         parent::_prepareColumns();
		 $this->addColumn('entity_id', array(
            'header'    => Mage::helper('sales')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('sales')->__('Product Name'),
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_product',
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('sales')->__('SKU'),
            'width'     => '80',
            'index'     => 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('sales')->__('Price'),
            'column_css_class' => 'price',
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index'     => 'price',
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_price',
        ));
	
		
		$this->addColumn('special_price', array(
            'header'    => Mage::helper('sales')->__('Special Price'),
            'column_css_class' => 'price',
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index'     => 'special_price',
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_price',
        ));
       
	   
	    $this->addColumn('in_products', array(
            'header'    => Mage::helper('sales')->__('Select'),
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id',
            'sortable'  => false,
        ));

        $this->addColumn('qty', array(
            'filter'    => false,
            'sortable'  => false,
            'header'    => Mage::helper('sales')->__('Qty To Add'),
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_qty',
            'name'      => 'qty',
            'inline_css'=> 'qty',
            'align'     => 'center',
            'type'      => 'input',
            'validate_class' => 'validate-number',
            'index'     => 'qty',
            'width'     => '1',
        ));
        //replace qty column
        $this->addColumn('stock', array(
            'header'=> Mage::helper('AdvancedStock')->__('Stock Summary'),
            'index' => 'stock_summary',
            'renderer'	=> 'MDN_AdvancedStock_Block_Product_Widget_Grid_Column_Renderer_StockSummary',
            'sortable'	=> false,
            'filter'	=> false
        ));
    }
}
