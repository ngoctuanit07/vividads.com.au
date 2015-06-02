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

class MageWorx_Adminhtml_Block_OrdersPro_Sales_Order_Grid_Renderer_Qnty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $data = array();
        $data[] = Mage::helper('orderspro')->__('Ordered').'&nbsp;'.intval($row->getData('total_qty_ordered'));
        
        $total = intval($row->getData('total_qty_invoiced'));
        if ($total>0) $data[] = Mage::helper('orderspro')->__('Invoiced').'&nbsp;'.$total;
        
        //$total = intval($row->getData('total_qty_shipped'));
        //if ($total>0) $data[] = Mage::helper('orderspro')->__('Shipped').'&nbsp;'.$total;
        
        $total = intval($row->getData('total_qty_refunded'));
        if ($total>0) $data[] = Mage::helper('orderspro')->__('Refunded').'&nbsp;'.$total;
        
        //Start 03_03_2014
        $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $temptableProof=Mage::getSingleton('core/resource')->getTableName('proofs');
	if(Mage::getSingleton('core/resource')->getConnection('core_write')->isTableExists($temptableProof))
        {
		//$sqlProof="SELECT * FROM  ".$temptableProof." WHERE  order_id ='".$orderid."' AND item_id ='".$itemid."' AND status = 'Approved' ";
		//$chkProof = Mage::getSingleton('core/resource')->getConnection('core_write')->fetchAll($sqlProof);
		
		$select = $connectionRead->select()
			->from($temptableProof, array('*'))
			->where("order_id ='".$row->getData('entity_id')."' AND status = 'Approved' ")
                        ->group('item_id')
			->order('entity_id DESC');
		
		$chkProof = $connectionRead->fetchAll($select);
                
            
            if (count($chkProof)>0) $data[] = '<span style="color:#04B431;">'.Mage::helper('orderspro')->__('Approved').'&nbsp;'.count($chkProof).'</span>';
	}
        //End 03_03_2014
        
        $data = implode('<br/>', $data);        
        if (strpos(Mage::app()->getRequest()->getRequestString(), '/exportCsv/')) {
            $data = str_replace(array('&nbsp;','<br/>'), array(' ','|'), $data);
        }        
        return $data;      
    }
}
