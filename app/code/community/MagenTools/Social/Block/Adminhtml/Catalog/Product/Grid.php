<?php
/**
 * @author      MagenTools
 * @copyright   Copyright (c) 2012 MagenTools (www.magentools.com)
 * @license     End-User License Agreement (www.magentools.com/eula/)
 */
class MagenTools_Social_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
 
        $session = Mage::getSingleton('core/session');
        $pinterestBoardOptions = $session->getPinterestBoardOptions();
        if(!(is_array($pinterestBoardOptions) && isset($pinterestBoardOptions[0]['value']))) {
            Mage::getSingleton('social/boards')->setOptionArray();
        }
        
        $this->getMassactionBlock()->addItem('pinterest', array(
                'label' => 'Pinterest AutoPin',
                'url' => Mage::app()->getStore()->getUrl('social/adminhtml_index/massPin'),
                'additional' => array(
                      'visibility' => array(
                         'name' => 'pintoboard',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Pin to Board'),
                         'values' => $pinterestBoardOptions
                       )
                  )
            ));
        $this->getMassactionBlock()->addItem('facebook', array(
                'label' => 'Facebook AutoPost',
                'url' => Mage::app()->getStore()->getUrl('social/adminhtml_index/massPost')
            ));
        $this->getMassactionBlock()->addItem('twitter', array(
                'label' => 'Twitter AutoTweet',
                'url' => Mage::app()->getStore()->getUrl('social/adminhtml_index/massTweet')
            ));
    }
}
?>
