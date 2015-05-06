<?php
/**
 * Class Aptoplex_EasyUploader_Model_Adminhtml_System_Config_Source_OrderStatuses
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Model_Adminhtml_System_Config_Source_Orderstatuses extends Mage_Core_Model_Abstract {

    /**
     * Returns array of order statuses
     *
     * @return array
     */
    public function toOptionArray() {
        $statuses = Mage::getModel('sales/order_status')->getResourceCollection()->getData();

        $return = array();
        for ($i = 0; $i < count($statuses); ++$i) {
            $element = array(
                'value' => $statuses[$i]['status'],
                'label' => Mage::helper('adminhtml')->__($statuses[$i]['label'])
            );
            array_push($return, $element);
        }

        return $return;
    }
}