<?php

class MDN_BarcodeLabel_Block_Product_List extends Mage_Adminhtml_Block_Catalog_Product_List {

    protected function _prepareMassaction() {
       
        
        parent::_prepareMassaction();
 
        // Append new mass action option
        $this->getMassactionBlock()->addItem(
            'BarcodeLabel',
            array('label' => $this->__('Print Label'),
                  'url'   => $this->getUrl('BarcodeLabel/Admin/printSelectedProductLabel') 
            )
        );
        
    }

}

?>
