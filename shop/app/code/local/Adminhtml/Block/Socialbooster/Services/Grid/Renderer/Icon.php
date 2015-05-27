<?php
class MageWorx_Adminhtml_Block_Socialbooster_Services_Grid_Renderer_Icon extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {                        
        return '<div class="bookmark sb-'.$row->getData($this->getColumn()->getIndex()).'">&nbsp;</div>';             
    }
}
