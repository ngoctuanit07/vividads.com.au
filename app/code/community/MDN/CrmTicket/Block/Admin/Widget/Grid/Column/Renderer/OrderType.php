<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @package MDN_CrmTicket
 * @version 1.2
 */
class MDN_CrmTicket_Block_Admin_Widget_Grid_Column_Renderer_OrderType extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $order) {

        //$orderId = $order->getincrement_id();//display order id
        $orderId = $order->getIncrement_id();//real id for db
		$norder = Mage::getModel('sales/order')->loadByIncrementId($orderId);
		
		$order_type = 'Order';
		
		if(count($norder->getData()) > 0){
			$order_type = 'Order';
			}else{
			$order_type = 'Quote';
		}
		
        return $order_type;
		
    }
}
