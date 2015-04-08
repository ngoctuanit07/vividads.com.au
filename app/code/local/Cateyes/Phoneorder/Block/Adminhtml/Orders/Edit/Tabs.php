<?php

class Cateyes_Phoneorder_Block_Adminhtml_Orders_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

	public function __construct()
	{
		parent::__construct();
		$this->setId("order_tabs");
		$this->setDestElementId("edit_form");
		$this->setTitle(Mage::helper("phoneorder")->__("Item Information"));
	}


	/**
	 * zakładka zamówienia
	 */
	protected function _beforeToHtml()
	{
		$this->addTab("order_tabs", array(
			"label" => Mage::helper("phoneorder")->__("Order"),
			"title" => Mage::helper("phoneorder")->__("Order"),
			"content" => $this->getLayout()->createBlock("phoneorder/adminhtml_orders_edit_tabs_order")->toHtml(),
		));
		return parent::_beforeToHtml();
	}

}
