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
class Magpleasure_Ajaxcontacts_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Simpledom
     *
     * @return Magpleasure_Ajaxcontacts_Helper_Tools_Simpledom
     */
    public function getSimpledom()
    {
        return Mage::helper('ajaxcontacts/tools_simpledom');
    }

    public function extensionEnabled($extension_name)
    {
        $modules = (array) Mage::getConfig()->getNode('modules')->children();
        if (!isset($modules[$extension_name])
            || $modules[$extension_name]->descend('active')->asArray() == 'false'
            || Mage::getStoreConfig('advanced/modules_disable_output/' . $extension_name)
        ){
            return false;
        }
        return true;
    }
}


