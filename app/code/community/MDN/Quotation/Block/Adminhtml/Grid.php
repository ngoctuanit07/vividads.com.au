<?php

class MDN_Quotation_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
       // $this->setId('QuotationCompleteListGrid');
	    $this->setId('quotation_id');
		$this->setOrder('quotation_id','DESC');		
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(Mage::helper('quotation')->__('No items'));
		
    }

    /**
     * Load collection
     *
     * @return unknown
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('Quotation/Quotation')->getCollection();		 
	    $collection->setOrder('quotation_id');
		$this->setDefaultSort('created_time', 'Asc');       
		//  echo $collection->getSelect()->__toString();		 
        $this->setCollection($collection);	
		
        return parent::_prepareCollection();
    }

    /**
     * set columns
     *
     * @return unknown
     */
    protected function _prepareColumns() {


        $this->addColumn('is_invoice', array(
            'header' => Mage::helper('quotation')->__('Type'),
            'index' => 'is_invoice',
        'type'  => 'options',
        'options' => array(
            0 => 'Quote',
            1 => 'Invoice',
        )

        ));
        
        $this->addColumn('created_time', array(
            'header' => Mage::helper('quotation')->__('Date'),
            'index' => 'created_time',
            'type' => 'date'
        ));

        $this->addColumn('increment_id', array(
            'header' => Mage::helper('quotation')->__('Ref'),
            'index' => 'increment_id',
        ));

        $this->addColumn('customer_name', array(
            'header' => Mage::helper('quotation')->__('Customer'),
            'index' => 'customer_name'
        ));

        $this->addColumn('caption', array(
            'header' => Mage::helper('quotation')->__('Caption'),
            'index' => 'caption',
        ));
		
		$this->addColumn('store', array(
            'header' => Mage::helper('quotation')->__('Store'),			
            'store_name' => 'store',
			'index'=>'store_name',
			'align'=>'left',  
        ));
		
		/*
		$this->addColumn('update_status', array(
            'header' => Mage::helper('quotation')->__('Updated Status'),
            'index' => 'message',
			'align'=>'left',
        ));
		*/

        $this->addColumn('status', array(
            'header' => Mage::helper('quotation')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::helper('quotation')->getStatusesAsArray(),
            'align' => 'center'
        ));

        /*$this->addColumn('Bought', array(
            'header' => Mage::helper('quotation')->__('Commercial status'),
            'index' => 'bought',
            'type' => 'options',
            'options' => Mage::getModel('Quotation/Quotation')->getBoughtStatusValues(),
            'align' => 'center'
        ));*/

        $this->addColumn('manager', array(
            'header' => Mage::helper('quotation')->__('Manager'),
            'index' => 'manager',
            'type' => 'options',
            'options' => Mage::helper('quotation')->getUsers(),
            'align' => 'center'
        ));
		
		 $this->addColumn('manager', array(
            'header' => Mage::helper('quotation')->__('Messages'),
            'index' => 'readstatus',
			'type'=>'options',
			'options'=> array( 1=>'New Messages'),
			'width'=>'200',
            'align' => 'center',
			'renderer'  => 'MDN_Quotation_Block_Adminhtml_Renderer_Messages'
        ));

        return parent::_prepareColumns();
    }


    public function getGridParentHtml() {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));
        return $this->fetchView($templateName);
    }

    /**
     * Set row url
     */
    public function getRowUrl($row) {
        return $this->getUrl('Quotation/Admin/edit', array('quote_id' => $row->getId()));
    }

}
