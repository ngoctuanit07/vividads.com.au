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
 * @package    Magpleasure_Common
 * @version    0.6.12
 * @copyright  Copyright (c) 2012-2013 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Common_Model_Form_Element_Customer extends Varien_Data_Form_Element_Text
{
    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _getCommonHelper()
    {
        return Mage::helper('magpleasure');
    }

    protected function _getCustomerName($customerId)
    {
        /** @var Mage_Customer_Model_Customer $cusotmer  */
        $customer = Mage::getModel('customer/customer')->load($customerId);
        return $customer->getName();
    }

    /**
     * Retrives element html
     * @return string
     */
    public function getElementHtml()
    {
        $customerId = $this->getValue();
        $html = "";
        if ($customerId){
            $url = Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $customerId));
            $html .= "<a href=\"{$url}\" target=\"_blank\">".$this->_getCustomerName($customerId)."</a>";
        } else {
            $html .= $this->_getCommonHelper()->__('Guest');
        }
        return $html;
    }
}