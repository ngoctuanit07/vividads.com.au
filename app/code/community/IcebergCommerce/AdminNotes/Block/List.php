<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_AdminNotes
 * @copyright  Copyright (c) 2010 Iceberg Commerce
 */

class IcebergCommerce_AdminNotes_Block_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
    {
        $this->_controller = 'list';
        $this->_blockGroup = 'adminnotes';
        $this->_headerText = Mage::helper('adminnotes')->__('Page Notes');
        parent::__construct();
        
        $this->_removeButton('add');
    }
   
}