<?php
/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Magpleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Magpleasure
 * @package    Magpleasure_Ajaxcontacts
 * @version    1.0
 * @copyright  Copyright (c) 2011 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */
class Magpleasure_Ajaxcontacts_Block_Wrapper extends Mage_Core_Block_Template
{
    public function getEnabled()
    {
        return Mage::getStoreConfig('ajaxcontacts/general/enabled');
    }

    public function getTop()
    {
        return Mage::getStoreConfig('ajaxcontacts/general/top') ? Mage::getStoreConfig('ajaxcontacts/general/top') : 180;
    }

    public function getWidth()
    {
        return Mage::getStoreConfig('ajaxcontacts/window/width');
    }

    public function getHeight()
    {
        return Mage::getStoreConfig('ajaxcontacts/window/height');
    }

}