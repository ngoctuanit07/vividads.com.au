<?php


class BS_Carpet_Model_Entity_Attribute_Source_Boolean_Config extends Mage_Eav_Model_Entity_Attribute_Source_Boolean
{

    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('carpet')->__('Yes'),
                    'value' =>  1
                ),
                array(
                    'label' => Mage::helper('carpet')->__('No'),
                    'value' =>  0
                ),
               
            );
        }
        return $this->_options;
    }

}
