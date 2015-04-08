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

class MageWorx_SocialBooster_AjaxController extends Mage_Core_Controller_Front_Action
{    
    
    public function saveClickAction()
    {                
        $itemName = trim($this->getRequest()->getParam('item_name', false));
        $url = trim($this->getRequest()->getParam('url', ''));        
        if ($itemName && strlen($url)>3) {
            $sessionSbData = Mage::getSingleton('customer/session')->getSocialBoosterData();
            if (!$sessionSbData) $sessionSbData = array();                        
            if (!isset($sessionSbData[$itemName][$url])) {
                $bookmark = Mage::getModel('socialbooster/bookmark')->load($itemName, 'bookmark_code');
                if ($bookmarkId = $bookmark->getBookmarkId()) {                    
                    $counter = Mage::getModel('socialbooster/counter')->loadByBookmarkIdAndUrl($bookmarkId, $url);
                    try {                
                        $datetime = Mage::getSingleton('core/date')->gmtDate();
                        $bookmark->setClicks(intval($bookmark->getClicks())+1)->setLastClick($datetime)->save();
                        $counter->setBookmarkId($bookmarkId)->setUrl($url)->setCount(intval($counter->getCount())+1)->setLast($datetime)->save();
                        $sessionSbData[$itemName][$url] = 1;                
                        Mage::getSingleton('customer/session')->setSocialBoosterData($sessionSbData);                
                        $this->getResponse()->setBody('ok');                    
                        return true;
                    } catch (Exception $e) {}
                }    
            }
                        
        }        
        $this->getResponse()->setBody('no');
    }                          
    
}
