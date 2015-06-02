<?php

/**
 * Search Invoice Model
 *
 * @category    Mage
 * @package     Vividads_Adminsearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Vividads_Adminsearch_Model_Invoice extends Varien_Object
{
    /**
     * Load search results
     *
     * @return Vividads_Adminsearch_Model_Search_Invoice
     */
    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }

        $query = $this->getQuery();
        //TODO: add full name logic
        $collection = Mage::getResourceModel('sales/order_invoice_collection')
            ->addAttributeToSelect('*');
	$collection->getSelect()->where('increment_id like "'.$query.
'%"');
        foreach ($collection as $invoice) {
            $arr[] = array(
                'id'                => 'invoice/1/'.$invoice->getId(),
                'type'              => Mage::helper('adminhtml')->__('Invoice'),
                'name'              => Mage::helper('adminhtml')->__('Invoice #%s', $invoice->getIncrementId()),
                'description'       => $invoice->getBillingFirstname().' '.$invoice->getBillingLastname(),
                'form_panel_title'  => Mage::helper('adminhtml')->__('Invoice #%s (%s)', $invoice->getIncrementId(), $invoice->getBillingFirstname().' '.$invoice->getBillingLastname()),
                'url' => Mage::helper('adminhtml')->getUrl('adminhtml/sales_invoice/view', array('invoice_id'=>$invoice->getId())),
            );
        }

        $this->setResults($arr);

        return $this;
    }
}
