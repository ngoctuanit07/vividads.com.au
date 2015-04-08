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

class MageWorx_Adminhtml_Block_OrdersPro_Sales_Order_Grid_Renderer_Messages extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
   /**
 *      * Render product name to add Configure link
 *           *
 *                * @param   Varien_Object $row
 *                     * @return  string
 *                          */
    public function render(Varien_Object $row)
  {
        $message_numbers  =  $row->getData($this->getColumn()->getIndex());
		$order_id = $row->getEntity_id();
		 
		 //var_dump($message_numbers);
		 $history  = Mage::getModel('sales/order_status_history')->getCollection()
		 											->addAttributeToFilter('parent_id',$order_id)
													->addAttributeToFilter('readstatus',1)		 						
							 ;										  													   ;
		// var_dump($history->getSelect()->__toString());
		//  var_dump(count($history));
		 if(count($history)>0) { 
		 		$message_numbers=count($history);
		 }else{
			 $message_numbers = 0;
			 }
		$messages = '<a target="_blank" title="click to view quote_id = '.$quote_id.'" href="'.Mage::getBaseUrl().'zulfe/sales_order/view/quote_id/'.$order_id.'/msg/1/">';
		$messages ='<span style="color:red;">('.$message_numbers.') New Messages </span>';
		$messages .='</a>';
		
		return $messages;
    }
}
