<?php

class Cateyes_Phoneorder_Block_Adminhtml_Orders_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

	public function __construct()
	{
		parent::__construct();
		$this->_objectId = "phoneorder_id";
		$this->_controller = "adminhtml_orders";				// $this->_controller = This is not the controller class name. It is actually the Block class name.
		$this->_blockGroup = "phoneorder";
		$this->_updateButton("save", "label", Mage::helper("phoneorder")->__("Save Item"));
		$this->_updateButton("delete", "label", Mage::helper("phoneorder")->__("Delete Item"));

		$this->_addButton("saveandcontinue", array(
			"label" => Mage::helper("phoneorder")->__("Save And Continue Edit"),
			"onclick" => "saveAndContinueEdit()",
			"class" => "save",
				), -100);

		$this->_formScripts[] = "
							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
	}

	/**
	 * Nagłowek formularza edycji
	 * @return text - nagłowek strony edycji
	 */
	public function getHeaderText()
	{
		if (Mage::registry("data") && Mage::registry("data")->getId())
		{
			return Mage::helper("phoneorder")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("data")->getData('phone')));
		}
		else
		{
			return Mage::helper("phoneorder")->__("Add Item");
		}
	}


	protected function _prepareLayout()
	{
		parent::_prepareLayout();
	}
}