<?php

/**
 * Testimonial submit form block
 *
 * @category   HM
 * @package    HM_Testimonial
 * @author      Hello Magento Module Team <module@asia-connect.com.vn>
 */
class Hm_Testimonial_Block_Submit extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();

    }

    public function getAction()
    {
        return Mage::getUrl('testimonial/submit/post');
    }  

}
