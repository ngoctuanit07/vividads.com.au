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

class MageWorx_SocialBooster_Block_Wrapper extends Mage_Core_Block_Abstract
{
    protected function _prepareLayout()
    {
        $helper = Mage::helper('socialbooster');
        if (!$helper->isEnabled() || Mage::registry('socialbooster')){
            return $this;
        }

        $product = Mage::registry('current_product');
        $category = Mage::registry('current_category');
        $page = Mage::getSingleton('cms/page')->getIdentifier();

        if (!$product && !$category && !$page){
            return $this;
        }

        if ($page && (in_array($page, $helper->getIgnoredCmsPages()) || !$helper->cmsPagesEnabled())){
            return $this;
        }

        if ($category && !$product && !$helper->categoryPagesEnabled()){
            return $this;
        }

        $bookmarksBlock = $this->getLayout()->createBlock('socialbooster/bookmarks', 'socialbooster');
        if (($product && !$helper->getProductPosition()) || !$product){
            $position = $helper->getDefaultPosition();
            switch ($position){
                case 'left':
                    if ($block = $this->getLayout()->getBlock('left')){
                        $block->insert($bookmarksBlock, '', false, 'socialbooster');
                    }
                    break;
                case 'right':
                    if ($block = $this->getLayout()->getBlock('right')){
                        $block->insert($bookmarksBlock, '', false, 'socialbooster');
                    }
                    break;
                case 'content':
                    if ($block = $this->getLayout()->getBlock('content')){
                        $block->insert($bookmarksBlock, '', false, 'socialbooster');
                    }
                    break;
            }
        } elseif ($product){
            $position = $helper->getProductPosition();
            if ($position == 'short_description'){
                if ($block = $this->getLayout()->getBlock('product.info.addto')){
                    $block->setTemplate('socialbooster/catalog-product-view-addto.phtml');
                    $block->insert($bookmarksBlock, '', false, 'socialbooster');
                }
            } elseif ($position == 'description'){
                if ($block = $this->getLayout()->getBlock('product.info.additional')){
                    $block->insert($bookmarksBlock, '', false, 'socialbooster');
                }
            }
        }
        Mage::register('socialbooster', true);

        return $this;
    }
}