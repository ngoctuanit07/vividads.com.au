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

class MageWorx_Adminhtml_Block_OrdersPro_Sales_Order_Grid_Renderer_Registry extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    
    public function render(Varien_Object $row)
    {
        $index = $this->getColumn()->getIndex();        
        switch ($index) {
            case 'method': $registry = 'payment_methods'; break;
            case 'customer_group_id': $registry = 'customer_groups'; break;
            case 'shipped': $registry = 'shipped_statuses'; break;
            case 'order_group_id': $registry = 'order_groups'; break;
            default : return '';
        }
        $id = $row->getData($index);
        $values = Mage::registry($registry);
        if (isset($values[$id])) return $this->htmlEscape($values[$id]);
        return '';                       
    }
}
