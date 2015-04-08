<?php
class Hm_Testimonial_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	$this->loadLayout();
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle('Testimonial');                                                 
        }
   
		$this->renderLayout();
    }
}