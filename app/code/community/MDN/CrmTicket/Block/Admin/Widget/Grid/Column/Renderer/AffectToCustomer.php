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
class MDN_CrmTicket_Block_Admin_Widget_Grid_Column_Renderer_AffectToCustomer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $order) {

        //$orderId = $order->getincrement_id();//display order id
        $orderId = $order->getId();//real id for db
        $customerId = $order->getcustomer_id();
        $ticketId = Mage::registry('ct_id');
		$objectId = Mage::getModel('CrmTicket/Ticket')->load($ticketId);
		
		//find either it is order or quote
		$norder = Mage::getModel('sales/order')->loadByIncrementId($order->getIncrement_id());
	
		$order_type = 'order';		
		if(count($norder->getData()) > 0){
			$order_type = 'order';
			}else{
			$order_type = 'quote';
		}
        
		if(!$ticketId){
          $ticketId = $this->getRequest()->getParam('ticket_id');
        }
		 
		
		
	$assigned_id = explode('_',$objectId->getCt_object_id());
	
	if($assigned_id[1]  != $orderId){
		$url = $this->getUrl('CrmTicket/Admin_Ticket/AffectToCustomer', array('order_id' => $orderId, 'customer_id' => $customerId, 'ticket_id' => $ticketId, 'type'=>$order_type));
		$assigned = '<a href="'.$url.'" >'.Mage::helper('CrmTicket')->__('Assign') .'</a>';
			}else{
				$url = $this->getUrl('CrmTicket/Admin_Ticket/Unaffect', array('order_id' => $orderId, 'customer_id' => $customerId, 'ticket_id' => $ticketId, 'type'=>$order_type));
		$assigned = '<a href="'.$url.'" >'.Mage::helper('CrmTicket')->__('Un Assign') .'</a>';
			}

        return  $assigned;
    }
}
