<?php
class MageWorx_Adminhtml_Block_Socialbooster_Pages extends Mage_Adminhtml_Block_Template
{        
    
    protected function _prepareLayout() {        
        $this->setChild('grid', $this->getLayout()->createBlock('mageworx/socialbooster_pages_grid', 'socialbooster_pages.grid'));
        return parent::_prepareLayout();
    }
    
    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }    

}
