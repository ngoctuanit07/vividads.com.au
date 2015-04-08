<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SocialBooster
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Social Booster extension
 *
 * @category   MageWorx
 * @package    MageWorx_SocialBooster
 * @author     MageWorx Dev Team
 */

class MageWorx_Adminhtml_Block_Socialbooster_Pages_View extends Mage_Adminhtml_Block_Template
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
        
        $this->setChild('grid', $this->getLayout()->createBlock('mageworx/socialbooster_pages_view_grid', 'socialbooster_services.view.grid'));
        return parent::_prepareLayout();
    }
    
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }            
    
    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }           
    
    public function getItemUrl()
    {                        
        $id = intval($this->getRequest()->getParam('id', 0));
        return Mage::getModel('socialbooster/counter')->load($id)->getUrl();                 
    }
    
}
