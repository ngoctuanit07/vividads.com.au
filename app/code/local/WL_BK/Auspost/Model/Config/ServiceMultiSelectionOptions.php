<?php
/**
  * @category       WL
  * @package        Auspost
  * @class          WL_Auspost_Model_Config_ServiceMultiSelectionOptions
  * @description    Define the options which are using in Auspost Configuration
 */

class WL_Auspost_Model_Config_ServiceMultiSelectionOptions extends Mage_Core_Model_Config_Data
{

    const AUSPOST_STANDARD = 1;
    const DATE_STANDARD = 2;
    const DATE_AND_AM_PM_STANDARD = 3;
    const DATE_AND_2_HOURS_STANDARD = 4;
    const DAY_STANDARD = 5;
    const DAY_AND_AM_PM_STANDARD = 6;
    const DAY_AND_2_HOURS_STANDARD = 7;
    const AM_PM_STANDARD = 8;
    const TWO_HOURS_STANDARD = 9;


    const AUSPOST_EXPRESS = 101;
    const DATE_EXPRESS = 102;
    const DATE_AND_AM_PM_EXPRESS = 103;
    const DATE_AND_2_HOURS_EXPRESS = 104;
    const DAY_EXPRESS = 105;
    const DAY_AND_AM_PM_EXPRESS = 106;
    const DAY_AND_2_HOURS_EXPRESS = 107;
    const AM_PM_EXPRESS = 108;
    const TWO_HOURS_EXPRESS = 109;

    const COLLECTION_POINT = 'cp';
    protected $_options;

    /**
     * @param $isMultiselect
     * @return The magento core will call this function to render select box in configuration.
     */
    public function toOptionArray($isMultiselect)
    {
        if (!$this->_options) {
            $this->_options = self::getMyOptions();
            array_pop($this->_options);
        }
        $options = $this->_options;
        return $options;
    }

    /**
     * @return The Shipping methods options and two more features: collection point selection and validate shipping address.
     */
    public static function getMyOptions()
    {
        return array(
            array('value' => self::AUSPOST_STANDARD, 'label' => Mage::helper('auspost')->__('Standard')),
            array('value' => self::DATE_STANDARD, 'label' => Mage::helper('auspost')->__('Date - Standard')),
            array('value' => self::DATE_AND_AM_PM_STANDARD, 'label' => Mage::helper('auspost')->__('Date and AM/PM - Standard')),
            array('value' => self::DATE_AND_2_HOURS_STANDARD, 'label' => Mage::helper('auspost')->__('Date and 2 hours - Standard')),
            array('value' => self::DAY_STANDARD, 'label' => Mage::helper('auspost')->__('Day - Standard')),
            array('value' => self::DAY_AND_AM_PM_STANDARD, 'label' => Mage::helper('auspost')->__('Day and AM/PM - Standard')),
            array('value' => self::DAY_AND_2_HOURS_STANDARD, 'label' => Mage::helper('auspost')->__('Day and 2 hours - Standard')),
            array('value' => self::AM_PM_STANDARD, 'label' => Mage::helper('auspost')->__('AM/PM - Standard')),
            array('value' => self::TWO_HOURS_STANDARD, 'label' => Mage::helper('auspost')->__('2 hours - Standard')),


            array('value' => self::AUSPOST_EXPRESS, 'label' => Mage::helper('auspost')->__('Express')),
            array('value' => self::DATE_EXPRESS, 'label' => Mage::helper('auspost')->__('Date - Express')),
            array('value' => self::DATE_AND_AM_PM_EXPRESS, 'label' => Mage::helper('auspost')->__('Date and AM/PM - Express')),
            array('value' => self::DATE_AND_2_HOURS_EXPRESS, 'label' => Mage::helper('auspost')->__('Date and 2 hours - Express')),
            array('value' => self::DAY_EXPRESS, 'label' => Mage::helper('auspost')->__('Day - Express')),
            array('value' => self::DAY_AND_AM_PM_EXPRESS, 'label' => Mage::helper('auspost')->__('Day and AM/PM - Express')),
            array('value' => self::DAY_AND_2_HOURS_EXPRESS, 'label' => Mage::helper('auspost')->__('Day and 2 hours - Express')),
            array('value' => self::AM_PM_EXPRESS, 'label' => Mage::helper('auspost')->__('AM/PM - Express')),
            array('value' => self::TWO_HOURS_EXPRESS, 'label' => Mage::helper('auspost')->__('2 hours - Express')),

            array('value' => self::COLLECTION_POINT, 'label' => Mage::helper('auspost')->__('Collection Point Selection'))

        );
    }

    /**
     * @return The Shipping methods with combined keys and values
     */
    public static function getAllOptions(){
        $option = array();
        $services = self::getMyOptions();
        foreach($services as $service){
            $option[$service['value']] = $service['label'];
        }
        return $option;
    }
}
