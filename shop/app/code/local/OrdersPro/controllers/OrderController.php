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
 * @package    MageWorx_OrdersPro
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Orders Pro extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersPro
 * @author     MageWorx Dev Team
 */

include_once('Mage/Sales/controllers/OrderController.php');

class MageWorx_OrdersPro_OrderController extends Mage_Sales_OrderController
{  

    // Check order view availability
    protected function _canViewOrder($order) {                
        if (Mage::helper('orderspro')->isHideDeletedOrdersForCustomers()) {
            $orderItemGroup = Mage::getModel('orderspro/order_item_group')->load($order->getId(), 'order_id');
            if ($orderItemGroup->getOrderGroupId()==2) return false;
        }        
        
        return parent::_canViewOrder($order);
    }   
   
}
