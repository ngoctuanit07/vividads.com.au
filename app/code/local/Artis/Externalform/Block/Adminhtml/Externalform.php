<?php
class Artis_Externalform_Block_Adminhtml_Externalform extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_externalform';
    $this->_blockGroup = 'externalform';
    $this->_headerText = Mage::helper('externalform')->__('External Form Iframe Creator');
    $this->_addButtonLabel = Mage::helper('externalform')->__('Create Iframe');
    parent::__construct();
  }
}