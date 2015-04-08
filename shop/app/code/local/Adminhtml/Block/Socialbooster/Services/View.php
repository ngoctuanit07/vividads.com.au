<?php
class MageWorx_Adminhtml_Block_Socialbooster_Services_View extends Mage_Adminhtml_Block_Template
{        
    
    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('socialbooster')->__('Back'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/index/').'\')',
                    'class' => 'back'
                ))
        );
        
        $this->setChild('grid', $this->getLayout()->createBlock('mageworx/socialbooster_services_view_grid', 'socialbooster_services.view.grid'));
        return parent::_prepareLayout();
    }
    
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }            
    
    public function getGridHtml() {
        return $this->getChildHtml('grid');
    } 
    
       
    public function getBookmarkName()
    {                
        $id = intval($this->getRequest()->getParam('id', 0));
        $bookmark = Mage::getModel('socialbooster/bookmark')->load($id);
        return $bookmark->getBookmarkTitle();
    }
    
}
