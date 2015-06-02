<?php

class Cateyes_Phoneorder_Block_Adminhtml_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{	
		parent::__construct();
		$this->setId("phoneorderGrid");
		$this->setDefaultSort("phoneorder_id");
		$this->setDefaultDir("DESC");
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel("phoneorder/phoneorder")->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	
	protected function _prepareColumns()
	{
		$this->addColumn("phoneorder_id", array(
					"header" => Mage::helper("phoneorder")->__("Id"),
					"align" => "center",
					"width" => "20px",
					"index" => "phoneorder_id",
				))
			->addColumn("date", array(
					"header" => Mage::helper("phoneorder")->__("Date"),
					"align" => "center",
					"width" => "50px",
					"index" => "date",
				))
			->addColumn("status", array(
					"header" => Mage::helper("phoneorder")->__("Status"),
					"align" => "center",
					"width" => "50px",
					"index" => "status",
					"type" => "options",
					"options" => array(
						1 => Mage::helper("phoneorder")->__("Verified"),	
						0 => Mage::helper("phoneorder")->__("Not Verified"),	
					),
				))			
			->addColumn("url", array(
					"header" => Mage::helper("phoneorder")->__("Product Url"),
					"align" => "center",
					"width" => "250px",
					"index" => "url",
				))	
			->addColumn("phone", array(
					"header" => Mage::helper("phoneorder")->__("Phone Number"),
					"align" => "center",
					"width" => "250px",
					"index" => "phone",
				))
			->addColumn("comment", array(
					"header" => Mage::helper("phoneorder")->__("Comment"),
					"align" => "center",
					"width" => "250px",
					"index" => "comment",
				))
	       ->addColumn('action',
	            array(
	            'header'    =>  Mage::helper('phoneorder')->__('Action'),
	            'width'     => '50',
	            'type'      => 'action',
	            'getter'    => 'getId',
	            'actions'   => array(
                    array(
                        'caption'   => Mage::helper('phoneorder')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
	            ),
	            'filter'    => false,
	            'sortable'  => false,
	            'index'     => 'stores',
	            'is_system' => true,
	   		));


		return parent::_prepareColumns();
	}


	public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}