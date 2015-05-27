<?php

/**
 * Description of View
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Block_Adminhtml_Block_Sales_Order_Invoice_View extends Mage_Adminhtml_Block_Sales_Order_Invoice_View
{
    /*
     * The constructor to get the template
     */

    public function __construct()
    {
        parent::__construct();
       
	   /*
	    $this->_addButton('printea', array(
            'label' => Mage::helper('pdfgenerator')->__('Print PDF Invoice'),
            'class' => 'saveea',
			
           // 'onclick' => $this->getThePdfSituation()
                )
        );
		*/
		$order_id = $this->getRequest()->getParam('order_id');
		//var_dump($order_id);
		$orderurl = Mage::app()->getStore()->getUrl("Quotation/Quote/printorder/order_id/".$order_id);
		
		 $this->addButton('pdf_print', array(
                'label'     => 'Print Pdf',
                'onclick'   => 'setLocation(\''.$orderurl.'\')',
                'class'     => 'saveea'
				  ));
		 
		$this->_addButtonLabel = Mage::helper('pdfgenerator')->__('Add Report');
		$this->_removeButton('print');
    }
    
    /******************** Start by dev for order status in invoice details page ********************************/
    public function getHeaderText()
    {
        if ($this->getInvoice()->getEmailSent()) {
            $emailSent = Mage::helper('sales')->__('the invoice email was sent');
        }
        else {
            $emailSent = Mage::helper('sales')->__('the invoice email is not sent');
        }
        $order = Mage::getModel('sales/order')->load($this->getInvoice()->getOrderId());
        
        //return Mage::helper('sales')->__('Invoice #%1$s | %2$s | %4$s (%3$s)', $this->getInvoice()->getIncrementId(), $this->getOrder()->getStatus(), $emailSent, $this->formatDate($this->getInvoice()->getCreatedAtDate(), 'medium', true));
        return Mage::helper('sales')->__('Invoice #%1$s | %2$s | %4$s (%3$s)', $this->getInvoice()->getIncrementId(), $order->getStatusLabel(), $emailSent, $this->formatDate($this->getInvoice()->getCreatedAtDate(), 'medium', true));
    }
    /******************** Start by dev for order status in invoice details page ********************************/
    /*
     * The url for the print template system
     */

    public function getEaPrintUrl()
    {
        return $this->getEaDesignPrintInvoiceUrl();
    }

    private function getEaDesignPrintInvoiceUrl()
    {
        /*
		return $this->getUrl('adminpdfgenerator/adminhtml_pdfgeneratorpdf/invoicepdfgenrator', array(
                    'invoice_id' => $this->getInvoice()->getId()
                ));
		*/
		$order_id = $this->getRequest()->getParam('order_id');
		return Mage::getUrl('Quotation/Quote/printorder/order_id', array(
                    'invoice_id' => $order_id
                ));
				
    }

    private function getThePdfSituation()
    {
        $templateCollection = Mage::getModel('eadesign/pdfgenerator')->getCollection();
        $templateCollection->addFieldToSelect('*')
                ->addFieldToFilter('template_store_id', $this->getCurrentInvoiceOrderStore())
                ->addFieldToFilter('pdft_is_active', 1);

        $templateId = $templateCollection->getData('pdftemplate_id');

        $checkMbstrings = extension_loaded('mbstring');

        if (!$checkMbstrings)
        {
            $messege = Mage::helper('pdfgenerator')->__('You do not have mbstrings library active. You will get the default Magento Invoice');
            return $location = "confirmSetLocation('{$messege}', '{$this->getPrintUrl()}')";
        }

        if (!empty($templateId))
        {
            return $location = 'setLocation(\'' . $this->getEaPrintUrl() . '\')';
        }
        $messege = Mage::helper('pdfgenerator')->__('You do not have a template selected for this invoice. You will get the default Magento Invoice');
        return $location = "confirmSetLocation('{$messege}', '{$this->getPrintUrl()}')";
    }

    private function getCurrentInvoiceOrderStore()
    {
        if ($storeId = $this->getInvoice()->getOrder()->getStore()->getId())
        {
            return array(0, $storeId);
        }
        return array(0);
    }

}
