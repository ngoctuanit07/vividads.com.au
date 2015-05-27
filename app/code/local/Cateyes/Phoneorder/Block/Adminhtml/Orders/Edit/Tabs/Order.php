<?php

class Cateyes_Phoneorder_Block_Adminhtml_Orders_Edit_Tabs_Order extends Mage_Adminhtml_Block_Widget_Form
{


	/*
	 * przygotawanie formatki przed renderowaniem widoku
	 */

	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);


		$_data = Mage::registry('data');
		

		$fieldset = $form->addFieldset("form", array(
			"legend" => Mage::helper("phoneorder")->__("Order")
		));

		$fieldset->addField("date", "text", array(
			"label" => Mage::helper("phoneorder")->__("Date"),
			"style" => 'width:130px;',
			"name" => "date",
			"disabled" => true,			
		));				
		$fieldset->addField("phone", "text", array(
			"label" => Mage::helper("phoneorder")->__("Phone Number"),
			"style" => 'width:250px;',
			"name" => "phone",
			"disabled" => true,			
		));
		$fieldset->addField("url", "text", array(
			"label" => Mage::helper("phoneorder")->__("Product Url"),
			"style" => 'width:500px;',
			"name" => "url",
			"disabled" => true,
		));		


		$fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('phoneorder')->__('Status'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'status',
			'onclick' => "",
			'onchange' => "",
			'value'  => '1',
			'values' => array(
				'0' => Mage::helper("phoneorder")->__("Not Verified"),
				'1' => Mage::helper("phoneorder")->__("Verified"),
			),
			'disabled' => false,
			'readonly' => false,
			'tabindex' => 1
        ));
		$fieldset->addField("comment", "textarea", array(
			"label" => Mage::helper("phoneorder")->__("Comment"),
			"class" => "required-entry",
			"required" => true,
			"style" => 'width:100%;',
			"name" => "comment",
			'after_element_html' => "<small>".Mage::helper("phoneorder")->__("Add comment to the order, example: Customer bought the product.")."</small>",
		));


		// pobierz dane do formularza
		if (Mage::getSingleton("adminhtml/session")->getNewsData())
		{
			$form->setValues(Mage::getSingleton("adminhtml/session")->getNewsData());
			Mage::getSingleton("adminhtml/session")->getNewsData(null);
		}
		elseif (Mage::registry("data"))
		{
			$form->setValues(Mage::registry("data")->getData());
		}

		return parent::_prepareForm();
	}


}
