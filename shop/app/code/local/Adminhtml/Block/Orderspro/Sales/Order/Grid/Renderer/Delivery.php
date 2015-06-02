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

class MageWorx_Adminhtml_Block_OrdersPro_Sales_Order_Grid_Renderer_Delivery extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $data = array();
       
        $order = Mage::getModel('sales/order')->load($row->getData('entity_id'));
	
	$items = $order->getAllItems();
	
	$flag = 0;
	
	$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	$temptablePlanning=Mage::getSingleton('core/resource')->getTableName('quote_planning');
	
	$orderAttributes = Mage::getModel('amorderattr/attribute')->load($order->getId(), 'order_id');
	
	if($connectionRead->isTableExists($temptableShipping))
	{
	    foreach($items as $item)
	    {
		$sqlShipping = $connectionRead->select()
				->from($temptablePlanning, array('*'))
				->where("quote_id = '".$order->getId()."' AND item_id ='".$item->getId()."' AND planning_type = 'order' ");
		$chkShipping = $connectionRead->fetchRow($sqlShipping);
		
		if($chkShipping['delivery_date'] > $orderAttributes->getData('delivery_date'))
		{
		    $flag = 1;
		}
	    }
	}

	if($flag == 0)
	$style = 'style="color:#04B431;"';
	else
	$style = 'style="color:red;"';
	
	
	if($orderAttributes->getData('delivery_date') != '0000-00-00' and $orderAttributes->getData('delivery_date') != '')
	$data[] = '<span '.$style.'>'.date('d/m/Y',strtotime($orderAttributes->getData('delivery_date'))).'</span>';
        
        $data = implode('<br/>', $data);        
        if (strpos(Mage::app()->getRequest()->getRequestString(), '/exportCsv/')) {
            $data = str_replace(array('&nbsp;','<br/>'), array(' ','|'), $data);
        }        
        return $data;      
    }
}
