<?php
/**
 * MD_Vividslider.
 * Company: Vivid Ads Inc Australia
 * Author: AShfaq Ahmed
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.vividads.com.au
 *
 * @category   MD
 * @package    Vividslider
 * @copyright  Copyright (c) 2014 Vivid Ads Ashfaq Ahmed (http://www.vividads.com.au)
 */
?>
<?php

 class MD_Vividslider_Block_Adminhtml_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
 {
     
     public function render(Varien_Object $row)             
     {
         
         if ($row->getData($this->getColumn()->getIndex())=="")
         {
             return "";
         }
         else
         {
            $html = '<img ';
            $html .= 'id="' . $this->getColumn()->getId() . '" ';                        
            $html .= 'src="' . Mage::getBaseUrl("media") ."vividslider".$row->getData($this->getColumn()->getIndex()) . '"';
            $html .= 'class="grid-image' . $this->getColumn()->getInlineCss() . '"/>';
            
            return $html;
         }
     }
     
     
 }
?>