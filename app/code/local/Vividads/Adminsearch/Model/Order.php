<?php
class Vividads_Adminsearch_Model_Order extends Varien_Object
{
    /**
     * Load search results
     *
     * @return Vividads_Adminsearch_Model_Search_Order
     */
    public function load($attributes)
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }

        $query = $this->getQuery();
        //TODO: add full name logic
        list($k,$attr)=each($attributes);
        if(!empty($attr))
                $filter=array('attribute' => $attr ,'like'=>$query.'%');
	else
		 $filter= array('attribute' => 'increment_id',       'like'=>$query.'%');
	
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToSearchFilter(array(
            $filter))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();

        foreach ($collection as $order) {
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
