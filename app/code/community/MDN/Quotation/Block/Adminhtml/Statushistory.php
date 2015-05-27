<?php
class MDN_Quotation_Block_Adminhtml_Statushistory extends Mage_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        $onclick = "submitHistoryAndReload($('order_history_block'), '".$this->getSubmitUrl()."')";

        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'   => Mage::helper('sales')->__('Submit Comment'),
                'class'   => 'save',
                'onclick' => $onclick
            ));
        $this->setChild('submit_button', $button);
        return $this;
    }

    public function getStatuses()
    {
        $state = $this->getQuote()->getState();
//        $statuses = $this->getQuote()->getConfig()->getStateStatuses($state);
        return $statuses;
    }

    public function canSendCommentEmail()
    {
	return true;
//        return Mage::helper('sales')->canSendOrderCommentEmail($this->getQuote()->getStore()->getId());
    }

    /**
     * Retrieve order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getQuote()
    {
        return Mage::registry('current_quote');
    }

    public function canAddComment()
    {
	return true;
//        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/comment') &&              $this->getQuote()->canComment();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment', array('quote_id'=>$this->getQuote()->getId()));
    }

    /**
     * Customer Notification Applicable check method
     *
     * @param  Mage_Sales_Model_Order_Status_History $history
     * @return boolean
     */
    public function isCustomerNotificationNotApplicable(MDN_Quotation_Model_Statushistory $history)
    {
        return $history->isCustomerNotificationNotApplicable();
    }
}
