<?php

class Cateyes_Phoneorder_Block_Adminhtml_Orders extends Mage_Adminhtml_Block_Widget_Grid_Container
{

	public function __construct()
	{
		$this->_controller = "adminhtml_orders";				// $this->_controller = This is not the controller class name. It is actually the Block class name.
		$this->_blockGroup = "phoneorder";
		$this->_headerText = Mage::helper("phoneorder")->__("Phone Orders");
		parent::__construct();
		
		$this->removeButton('add');
	}
	
	

}
