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

class MageWorx_SocialBooster_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnabled(){
        return Mage::getStoreConfigFlag('mageworx_social_tools/socialbooster/enabled');
    }

    public function cmsPagesEnabled(){
        return Mage::getStoreConfigFlag('mageworx_social_tools/socialbooster/cms_pages_enabled');
    }

    public function categoryPagesEnabled(){
        return Mage::getStoreConfigFlag('mageworx_social_tools/socialbooster/category_pages_enabled');
    }

    public function getBookmarks(){
        return explode(',', Mage::getStoreConfig('mageworx_social_tools/socialbooster/bookmarks'));
    }

    public function getFeaturedBookmarks(){
        return explode(',', Mage::getStoreConfig('mageworx_social_tools/socialbooster/featured_bookmarks'));
    }

    public function getAdditionalButtons(){
        return explode(',', Mage::getStoreConfig('mageworx_social_tools/socialbooster/additional_buttons'));
    }

    public function getDefaultPosition(){
        return Mage::getStoreConfig('mageworx_social_tools/socialbooster/default_position');
    }

    public function getProductPosition(){
        return Mage::getStoreConfig('mageworx_social_tools/socialbooster/product_position');
    }

    public function getIgnoredCmsPages(){
    	return explode(',', Mage::getStoreConfig('mageworx_social_tools/socialbooster/ignore_cms_pages'));
    }
    
    public function resetBookmark($ids) {
        foreach($ids as $id) {
            Mage::getModel('socialbooster/bookmark')->load($id)->setClicks(0)->setLastClick(null)->save();
            Mage::getModel('socialbooster/counter')->getResource()->deleteByBookmarkId($id);
        }    
    }
    
    public function resetPages($ids) {
        foreach($ids as $id) {
            $url = Mage::getModel('socialbooster/counter')->load($id)->getUrl();            
            Mage::getModel('socialbooster/counter')->getResource()->deleteByUrl($url);
        }    
    }    
    
    
    
}