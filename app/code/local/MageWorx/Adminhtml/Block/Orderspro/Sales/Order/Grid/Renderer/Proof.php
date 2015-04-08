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

class MageWorx_Adminhtml_Block_OrdersPro_Sales_Order_Grid_Renderer_Proof extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $data = array();
       
         $order = Mage::getModel('sales/order')->load($row->getData('entity_id'));
	
	 $items = $order->getAllItems();
	
	 
	foreach($items as $item)
	{
        
	    if($item->getProductType() != 'bundle')
	    {
		//Start 03_03_2014
		
		$_Product = Mage::getModel('catalog/product')->load($item->getProductId());
		
		$connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		$temptablePlanning=Mage::getSingleton('core/resource')->getTableName('quote_planning');
		if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptablePlanning))
		{
			//$sqlProof="SELECT * FROM  ".$temptableProof." WHERE  order_id ='".$orderid."' AND item_id ='".$itemid."' AND status = 'Approved' ";
			//$chkProof = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlProof);
			
			$select = $connectionRead->select()
				->from($temptablePlanning, array('*,DATE_FORMAT(proof_date,"%d/%m/%y") AS PROOFDATE'))
				->where("quote_id ='".$row->getData('entity_id')."' AND item_id = '".$item->getId()."' AND planning_type = 'order' ");
			
			$chkPlanning = $connectionRead->fetchRow($select);
			
		    if($_Product->getIsPrintable() == 164 or $_Product->getIsPlanningEnable() == 0)
		    {
		       
		    }
		    else{
			 if ($chkPlanning['proof_date'] != '0000-00-00') 
			 $data[] = $item->getSku().'&nbsp;:&nbsp;'.$chkPlanning['PROOFDATE'];
		    }
		}
		//End 03_03_2014
	    }
	//    else{
	//	$data[] = $item->getSku().'--';
	//    }
	}
         
        $data = implode('<br/>', $data);        
       
	    if (strpos(Mage::app()->getRequest()->getRequestString(), '/exportCsv/')) {
            $data = str_replace(array('&nbsp;','<br/>'), array(' ','|'), $data);
        }        
        return $data; 
		    
    }
}
