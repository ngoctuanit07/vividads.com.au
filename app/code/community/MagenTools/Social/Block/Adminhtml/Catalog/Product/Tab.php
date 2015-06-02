<?php
/**
 * @author      MagenTools
 * @copyright   Copyright (c) 2012 MagenTools (www.magentools.com)
 * @license     End-User License Agreement (www.magentools.com/eula/)
 */ 
class MagenTools_Social_Block_Adminhtml_Catalog_Product_Tab 
	extends Mage_Adminhtml_Block_Template 
	implements Mage_Adminhtml_Block_Widget_Tab_Interface 
{
 
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('social/catalog/product/tab.phtml');
    }
 
    public function getTabLabel()
    {
        return $this->__('MagenTools');
    }
 
    public function getTabTitle()
    {
        return $this->__('Click here to view/edit MagenTools settings');
    }
 
    public function canShowTab()
    { 
        if(($this->getRequest()->getControllerName() == 'catalog_product') &&
            ($this->getRequest()->getActionName() == 'edit') || 
                ($this->getRequest()->getActionName() == 'new' && $this->getRequest()->getParam('set')) ) {

                    return true;
	}
    }
 
     public function isHidden()
    {
        return false;
    }
  
}
?>
