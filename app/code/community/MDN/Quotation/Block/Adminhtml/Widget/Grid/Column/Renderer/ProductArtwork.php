<?php

class MDN_Quotation_Block_Adminhtml_Widget_Grid_Column_Renderer_ProductArtwork extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    private $_row = null;
    private $_product = null;

    /**
     * Column renderer
     *
     * @param Varien_Object $row
     * @return unknown
     */
    public function render(Varien_Object $row) {
        $html = '-';
        $this->_row = $row;

        //if product has required options..
        $productId = $row->getproduct_id();
        $quotationItemId = $row->getId();
        $product = Mage::getModel('catalog/product')->load($productId);
        $this->_product = $product;
        if ($product->getId()) {
            if ($product->gethas_options() == 1) {

        $html = '';

        foreach ($this->_row->getOptionsCollection() as $option) {
                $option1=Mage::helper('quotation/Serialization')->unserializeObject($option);
//echo $option->getId();
	        $value = $this->_row->getOptionValue($option->getId());
                $option1=Mage::helper('quotation/Serialization')->unserializeObject($value);
/*print("<pre>");
print_r($option1);
print("</pre>");
*/
do{
if(file_exists($option1['fullpath']))
	            $html .= '<div style="margin-bottom: 5px; margin-top: -5px;"><a href="'
.$option1[quote_path] . '" target="_blank" >' . $option1[title] . '</a></div>';
	$option1=$option1['next'];
}while($option1);
        }

            }
        }

        $html .= $this->getJsPriceCalculation();

        return $html;
    }

    /**
     * return html items to fill in product options
     *
     * @param unknown_type $product
     */

}
