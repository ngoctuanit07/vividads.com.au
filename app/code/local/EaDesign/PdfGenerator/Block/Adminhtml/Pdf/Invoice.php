<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Invoice
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Pdf_Invoice extends Mage_Sales_Block_Items_Abstract
{
    //sprotected 
    public function getTheInvoice()
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        echo 'test';
        echo '<pre>';
        print_r($invoiceId);
        exit();
        //$pdf = Mage::getSingleton('eadesign/entity_invoicepdf')->getPdf($invoiceId);
    }
}

?>
