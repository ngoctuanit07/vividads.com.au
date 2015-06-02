<?php
class AsiaConnect_Gallery_Block_Adminhtml_Album extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_album';
    $this->_blockGroup = 'gallery';
    $this->_headerText = Mage::helper('gallery')->__('Album Manager');
    $this->_addButtonLabel = Mage::helper('gallery')->__('Add Album');
	$this->_addButton('sort', array(
    	'label'     => Mage::helper('gallery')->__('Save Order'),
    	'onclick'   => 'saveSort();',
    	'class'     => 'button',
    ));
	
	$this->_addButton('update_url_rewrite', array(
    	'label'     => Mage::helper('gallery')->__('Update URL Rewrite'),
    	'onclick'   => 'window.location=\''.$this->getUrl("gallery_admin/adminhtml_album/updateUrlRewrite").'\'',
    	'class'     => 'button',
    ));
    parent::__construct();
  }
  
}