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
class MageWorx_SocialBooster_Model_Position_Product
{
    public function toOptionArray() {
        return array(
            array(
                'value' => '',
                'label' => Mage::helper('socialbooster')->__('Use Default')
            ),
            array(
                'value' => 'short_description',
                'label' => Mage::helper('socialbooster')->__('Above Short Description')
            ),
            array(
                'value' => 'description',
                'label' => Mage::helper('socialbooster')->__('Below Full Description')
            ),
        );
    }
}