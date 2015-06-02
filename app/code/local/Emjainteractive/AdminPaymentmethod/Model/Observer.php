<?php

class Emjainteractive_AdminPaymentmethod_Model_Observer
{
    public function paymentMethodIsActive($eventData)
    {
        $method = $eventData->getData('method_instance');
        if ($method) {
            $isActiveForAdmin = (bool)(int)$method->getConfigData('active', 0);
            if ($isActiveForAdmin) {
                $result = $eventData->getResult();
                $result->isAvailable = true;
            }
        }
    }
}