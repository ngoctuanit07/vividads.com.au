<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      1.1.7
 * @license:     5aaeipF7ooGEnIcLG9rWordPqCAsU9nCJvGBjy56tC
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Deliverydate_Block_Renderer_Default extends Mage_Core_Block_Template 
implements Varien_Data_Form_Element_Renderer_Interface
{    
    public function render(Varien_Data_Form_Element_Abstract $element){
        return $element->getLabelHtml() . '<br />' . $element->getElementHtml();
    }
}