<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order view
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class MDN_ProductReturn_Block_Admin_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View
{

    public function __construct()
    {

        parent::__construct();

        if ($this->getOrder()->getId())
        {
	        $this->_addButton('order_new_product_return', array(
	            'label'     => Mage::helper('ProductReturn')->__('New Product Return'),
	            'onclick'   => 'setLocation(\'' . $this->getNewProductReturnUrl() . '\')',
	        ));
        }

        
        
        /**************** Start by dev ***********************/
        
        $all_permission = Mage::getSingleton('core/session')->getAllpermission();
        $order = $this->getOrder();
        
        if($order->getTotalPaid() != 0  || $order->getTotalPaid() !=''){
        
            
            if ($this->_isAllowedAction('edit') && $order->canEdit()) {
                $onclickJs = 'deleteConfirm(\''
                    . Mage::helper('sales')->__('Are you sure? This order will be canceled and a new one will be created instead')
                    . '\', \'' . $this->getEditUrl() . '\');';
                $this->_addButton('order_edit', array(
                    'label'    => Mage::helper('sales')->__('Edit'),
                    'onclick'  => $onclickJs,
                ));
                // see if order has non-editable products as items
                $nonEditableTypes = array_keys($this->getOrder()->getResource()->aggregateProductsByTypes(
                    $order->getId(),
                    array_keys(Mage::getConfig()
                        ->getNode('adminhtml/sales/order/create/available_product_types')
                        ->asArray()
                    ),
                    false
                ));
                if ($nonEditableTypes) {
                    $this->_updateButton('order_edit', 'onclick',
                        'if (!confirm(\'' .
                        Mage::helper('sales')->__('This order contains (%s) items and therefore cannot be edited through the admin interface at this time, if you wish to continue editing the (%s) items will be removed, the order will be canceled and a new order will be placed.', implode(', ', $nonEditableTypes), implode(', ', $nonEditableTypes)) . '\')) return false;' . $onclickJs
                    );
                }
            }
        }

        if(in_array(38,$all_permission))
        {
            if ($this->_isAllowedAction('cancel') && $order->canCancel()) {
                $message = Mage::helper('sales')->__('Are you sure you want to cancel this order?');
                $this->_addButton('order_cancel', array(
                    'label'     => Mage::helper('sales')->__('Cancel'),
                    'onclick'   => 'deleteConfirm(\''.$message.'\', \'' . $this->getCancelUrl() . '\')',
                ));
            }
        }
        
        if(in_array(39,$all_permission))
        {    
            if ($this->_isAllowedAction('hold') && $order->canHold()) {
                $this->_addButton('order_hold', array(
                    'label'     => Mage::helper('sales')->__('Hold'),
                    'onclick'   => 'setLocation(\'' . $this->getHoldUrl() . '\')',
                ));
            }
        }
	
	$this->_removeButton('order_invoice');
	$this->_removeButton('order_ship');
        
    /**************** End by dev ***********************/
        
      
        
    }

    public function getNewProductReturnUrl()
    {
    	return $this->getUrl('ProductReturn/Admin/Edit', array('order_id' => mage::registry('sales_order')->getId()));
    }

}
