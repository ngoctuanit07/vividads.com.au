<?php



/*

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */



/**

 * Description of PdfgeneratorPdfConstroller

 *

 * @author Ea Design

 */

class EaDesign_PdfGenerator_Adminhtml_PdfgeneratorpdfController extends Mage_Adminhtml_Controller_Action

{
   public function invoicepdfgenratorAction()
    {
        if(!$invoiceId = $this->getRequest()->getParam('invoice_id'))
        {
            return false;
        }
        try{
			$pdfFile = Mage::getSingleton('eadesign/entity_invoicepdf')->getThePdf((int) $invoiceId);
            $this->_prepareDownloadResponse($pdfFile->getData('filename') .
	          '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
	        } catch (Exception $e)
        {Mage::log($e->getMessage());return null;} }
		
//Order Controller
   public function orderpdfgenratorAction()
    {
        if(!$orderId = $this->getRequest()->getParam('order_id'))
        {
            return false;
        }
        try{
			$pdfFile = Mage::getSingleton('eadesign/entity_invoicepdf')->getThePdf((int) $orderId);
            $this->_prepareDownloadResponse($pdfFile->getData('filename') .
	          '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
	        } catch (Exception $e)
        {Mage::log($e->getMessage());return null;} }
}

