<?php
class MDN_Quotation_Model_Mysql4_Statushistory_Collection
    extends Mage_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'quotation_statushistory_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_statushistory_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Quotation/statushistory');
    }

    /**
     * Get history object collection for specified instance (order, shipment, invoice or credit memo)
     * Parameter instance may be one of the following types: Mage_Sales_Model_Order,
     * Mage_Sales_Model_Order_Creditmemo, Mage_Sales_Model_Order_Invoice, Mage_Sales_Model_Order_Shipment
     *
     * @param mixed $instance
     * @param string $historyEntityName
     *
     * @return Mage_Sales_Model_Order_Status_History|null
     */
    public function getUnnotifiedForInstance($instance, $historyEntityName=Mage_Sales_Model_Order::HISTORY_ENTITY_NAME)
    {
exit(__FUNCTION__.__FILE__. ' Manually disabled ');
        if(!$instance instanceof Mage_Sales_Model_Order) {
            $instance = $instance->getOrder();
        }
        $this->setOrderFilter($instance)->setOrder('created_at', 'desc')
            ->addFieldToFilter('entity_name', $historyEntityName)
            ->addFieldToFilter('is_customer_notified', 0)->setPageSize(1);
        foreach($this as $historyItem) {
            return $historyItem;
        }
        return null;
    }

}
