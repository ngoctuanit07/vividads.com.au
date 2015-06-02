<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Orderattr
*/
class Amasty_Orderattr_Block_Adminhtml_Order_Grid_Renderer_Text extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
public function render(Varien_Object $row)
    {
        if ($data = $this->_getValue($row)) {
            return nl2br($data);
        }
        return $this->getColumn()->getDefault();
    }
}