<?php

class GrafischDirect_Algemeen_Model_Observer
{
    /**
     * Predispath admin action controller
     *
     * @param Varien_Event_Observer $observer
     */
    public function preDispatch(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $feedModel = Mage::getModel('gdalgemeen/notification_feed');
            $feedModel->checkUpdate();
        }
    }
}
