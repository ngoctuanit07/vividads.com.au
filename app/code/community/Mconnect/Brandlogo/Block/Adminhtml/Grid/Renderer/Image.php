<?php
/**
 * M-Connect Solutions.
 * 
 *
 * NOTICE OF LICENSE
 *
 *
 * It is also available through the world-wide-web at this URL:
 * http://www.mconnectsolutions.com/lab/
 *
 * @category   M-Connect
 * @package    M-Connect
 * @copyright  Copyright (c) 2009-2010 M-Connect Solutions. (http://www.mconnectsolutions.com)
 */
?>
<?php

 class Mconnect_Brandlogo_Block_Adminhtml_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
            $html .= 'src="' . Mage::getBaseUrl("media") ."brandlogo".$row->getData($this->getColumn()->getIndex()) . '"';
            $html .= 'class="grid-image' . $this->getColumn()->getInlineCss() . '"/>';
            
            return $html;
         }
     }
     
     
 }
?>