<?php

/**
 * Search Shipment Model
 *
 * @category    Mage
 * @package     Vividads_Adminsearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Vividads_Adminsearch_Model_Shipment extends Varien_Object
{
    /**
     * Load search results
     *
     * @return Vividads_Adminsearch_Model_Search_Shipment
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
        $collection = Mage::getResourceModel('sales/order_shipment_track_collection')
            ->addAttributeToSelect('*')
            ->joinAttribute('order_id', 'shipment/order_id', 'parent_id', null, 'left');
		if(!$collection) exit('invalid collection');
	$collection->getSelect()->where('track_number like "'.$query.
'%"');

        foreach ($collection as $shipment) {
	$orderid=$shipment->getOrderId();
	$order= Mage::getModel('sales/order')->load($orderid);
            $arr[] = array(
                'id'                => 'order/1/'.$order->getId(),
                'type'              => Mage::helper('adminhtml')->__('Order'),
                'name'              => Mage::helper('adminhtml')->__('Order #%s', $order->getIncrementId()),
                'description'       => $order->getBillingFirstname().' '.$order->getBillingLastname(),
                'form_panel_title'  => Mage::helper('adminhtml')->__('Order #%s (%s)', $order->getIncrementId(), $order->getBillingFirstname().' '.$order->getBillingLastname()),
                'url' => Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('order_id'=>$order->getId())),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
