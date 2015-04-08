<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderattr
*/
class Amasty_Orderattr_Block_Checkout_Onepage_Progress_Step extends Mage_Core_Block_Template
{
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amorderattr/progress_step.phtml');
    }
    
    public function getAttributes()
    {
    	$list    = array();
    	$session = Mage::getSingleton('checkout/type_onepage')->getCheckout();
    	$orderAttributes = $session->getAmastyOrderAttributes();
        if (is_array($orderAttributes) && !empty($orderAttributes))
        {
        	$attributes = Mage::getModel('eav/entity_attribute')->getCollection();
	        $attributes->addFieldToFilter('is_visible_on_front', 1);
            $attributes->addFieldToFilter('checkout_step', $this->getStep());
	        $attributes->addFieldToFilter('entity_type_id', Mage::getModel('eav/entity')->setType('order')->getTypeId());
	        $attributes->getSelect()->order('sorting_order');
	        foreach ($attributes as $attribute)
	        {
	        	if (in_array($attribute->getAttributeCode(), array_keys($orderAttributes)))
	        	{
	        		$value = '';
	                switch ($attribute->getFrontendInput())
	                {
	                    case 'select':
                        case 'boolean':
	                        $options = $attribute->getSource()->getAllOptions(true, true);
	                        foreach ($options as $option)
	                        {
	                            if ($option['value'] == $orderAttributes[$attribute->getAttributeCode()])
	                            {
	                                $value = $option['label'];
	                                break;
	                            }
	                        }
	                        break;
                        case 'checkboxes':
                            $temp = array();
                            $options = $attribute->getSource()->getAllOptions(true, true);
                            $checkboxValues = $orderAttributes[$attribute->getAttributeCode()];
                            foreach ($options as $option) {
                               if (($option['value'] != '') && (in_array($option['value'], $checkboxValues))) {
                                   $temp[] = $option['label'];
                               }
                            }
                            if (!empty($temp)) {
                                $value = implode(', ', $temp);
                            }
                            break;
                        case 'date':
                            $value = $orderAttributes[$attribute->getAttributeCode()];
                            if (!$value) {
                                break;
                            }
                            $format = Mage::app()->getLocale()->getDateTimeFormat(
                                Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
                            );
                            if ('time' == $attribute->getNote()) {
                                $value = Mage::app()->getLocale()->date($value, Varien_Date::DATETIME_INTERNAL_FORMAT, null, false)->toString($format);
                            } else {
                                $format = trim(str_replace(array('m', 'a', 'H', ':', 'h', 's'), '', $format));
                                $value = Mage::app()->getLocale()->date($value, Varien_Date::DATE_INTERNAL_FORMAT, null, false)->toString($format);
                            }
                            break;
                        case 'textarea':
                            $value = nl2br($orderAttributes[$attribute->getAttributeCode()]);
                            break;
	                    default:
	                        $value = $orderAttributes[$attribute->getAttributeCode()];
	                        break;
	                }
	                $list[$attribute->getFrontendLabel()] = str_replace('$', '\$', $value);
	        	}
	        }
        }
        return $list;
    }
}