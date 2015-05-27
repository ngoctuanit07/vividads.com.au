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

class MageWorx_SocialBooster_Model_Bookmark extends Mage_Core_Model_Abstract {
    
    protected function _construct() {
        parent::_construct();
        $this->_init('socialbooster/bookmark');
    }
    
    public function toOptionArray() {
        $options = array();
        $bookmarks = Mage::helper('socialbooster/bookmarks')->getBookmarks();
        foreach ($bookmarks as $name => $bookmark){
            $options[] = array('value' => $name, 'label' => html_entity_decode($bookmark['title']));
        }
        return $options;
    }
}