<?php
class MDN_Quotation_Model_Statushistory extends Mage_Core_Model_Abstract 
{
    const CUSTOMER_NOTIFICATION_NOT_APPLICABLE = 2;

    /**
     * Quote instance
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * Whether setting quote again is required (for example when setting non-saved yet quote)
     * @deprecated after 1.4, wrong logic of setting quote id
     * @var bool
     */
    private $_shouldSetQuoteBeforeSave = false;

    protected $_eventPrefix = 'quotation_Statushistory';
    protected $_eventObject = 'Statushistory';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Quotation/statushistory');
    }

    /**
     * Set quote object and grab some metadata from it
     *
     * @param   Mage_Sales_Model_Quote $quote
     * @return  Mage_Sales_Model_Quote_Status_History
     */
    public function setQuote(MDN_Quotation_Model_Quotation $quote)
    {
        $this->_quote = $quote;
        $this->setStoreId($quote->getStoreId());
        return $this;
    }

    /**
     * Notification flag
     *
     * @param  mixed $flag OPTIONAL (notification is not applicable by default)
     * @return Mage_Sales_Model_Quote_Status_History
     */
    public function setIsCustomerNotified($flag = null)
    {
        if (is_null($flag)) {
            $flag = self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
        }

        return $this->setData('is_customer_notified', $flag);
    }

    /**
     * Customer Notification Applicable check method
     *
     * @return boolean
     */
    public function isCustomerNotificationNotApplicable()
    {
        return $this->getIsCustomerNotified() == self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
    }

    /**
     * Retrieve quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Retrieve status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        if($this->getQuote()) {
            return $this->getQuote()->getConfig()->getStatusLabel($this->getStatus());
        }
    }

    /**
     * Get store object
     *
     * @return unknown
     */
    public function getStore()
    {
        if ($this->getQuote()) {
            return $this->getQuote()->getStore();
        }
        return Mage::app()->getStore();
    }

    /**
     * Set quote again if required
     *
     * @return Mage_Sales_Model_Quote_Status_History
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getQuote()) {
            $this->setParentId($this->getQuote()->getId());
        }

        return $this;
    }
}
