<?php
class Hm_Testimonial_Model_Configuration_Templateoption
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	$array = array();
    	$collections = Mage::getResourceSingleton('core/email_template_collection');
    	foreach ($collections as $collection){
    		$array[$collection->getTemplateId()]= $collection->getTemplateCode();
    	}
        return $array;
    }

}