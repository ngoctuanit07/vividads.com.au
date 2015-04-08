<?php
class AsiaConnect_Gallery_Block_Adminhtml_Gallery extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_gallery';
    $this->_blockGroup = 'gallery';
    $this->_headerText = Mage::helper('gallery')->__('Photo Manager');
    $this->_addButtonLabel = Mage::helper('gallery')->__('Add Photo');
	$this->_addButton('sort', array(
    	'label'     => Mage::helper('gallery')->__('Save Order'),
    	'onclick'   => 'saveSort();',
    	'class'     => 'button',
    ));
	$this->_addButton('update_url_rewrite', array(
    	'label'     => Mage::helper('gallery')->__('Update URL Rewrite'),
    	'onclick'   => 'window.location=\''.$this->getUrl("gallery_admin/adminhtml_gallery/updateUrlRewrite").'\'',
    	'class'     => 'button',
    ));
    
	$this->_addButton('multiadd', array(
        'label'     => Mage::helper('gallery')->__('Upload Multiple Photos'),
        'onclick'   => 'setLocation(\'' . $this->getUrl('*/adminhtml_multiadd/multiadd') .'\')',
		'id'		=> 'multiadd',
		'class'     => 'add',
	));
			
    parent::__construct();
  }
}