<?php
class IWD_OrderManager_Model_Payment_Authorizenet extends Mage_Paygate_Model_Authorizenet
{
    //refund the payment, or make a patial payment
    //protected $_canCapturePartial       = false;
    //protected $_canRefund               = false;
    protected $_canUseInternal = true;
    protected $_canSaveCc = true;

    /**
     * It sets card`s data into additional information of payment model
     *
     * @param Mage_Paygate_Model_Authorizenet_Result $response
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Varien_Object
     */
    protected function _registerCard(Varien_Object $response, Mage_Sales_Model_Order_Payment $payment)
    {
        $cardsStorage = $this->getCardsStorage($payment);
        $card = $cardsStorage->registerCard();
        $card
            ->setRequestedAmount($response->getRequestedAmount())
            ->setBalanceOnCard($response->getBalanceOnCard())
            ->setLastTransId($response->getTransactionId())
            ->setProcessedAmount($response->getAmount())
            ->setCcType($payment->getCcType())
            ->setCcOwner($payment->getCcOwner())
            ->setCcLast4($payment->getCcLast4())
            ->setCcExpMonth($payment->getCcExpMonth())
            ->setCcExpYear($payment->getCcExpYear())
            ->setCcSsIssue($payment->getCcSsIssue())
            ->setCcSsStartMonth($payment->getCcSsStartMonth())
            ->setCcSsStartYear($payment->getCcSsStartYear());

        $cardsStorage->updateCard($card);
        //$this->_clearAssignedData($payment);  // <-- reason to override this method
        return $card;
    }
}