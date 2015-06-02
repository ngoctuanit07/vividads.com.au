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

class Magpleasure_Ajaxcontacts_Helper_Tools_Helpdesk extends Mage_Core_Helper_Abstract
{
    /**
     * Helper
     *
     * @return Magpleasure_Ajaxcontacts_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('ajaxcontacts');
    }

    /**
     * Checks and creates AW_Helpdesk proto from contact form
     * @return null
     */
    public function contactForm()
    {
        if (class_exists('AW_Helpdeskultimate_Model_Observer') && Mage::getStoreConfig(AW_Helpdeskultimate_Helper_Config::XML_PATH_USE_CONTACTFORM)){
            /** @var AW_Helpdeskultimate_Model_Observer $observer  */
            $observer = Mage::getModel('helpdeskultimate/observer');
            $observer->saveContactFormToTicket();
            if(Mage::getStoreConfig(AW_Helpdeskultimate_Helper_Config::XML_PATH_CONTACTFORM_DISABLE_STANDART_EMAIL)){
                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded as soon as possible. Thank you for contacting us.'));
            }
        }
    }

}