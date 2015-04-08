<?php
class Hm_Testimonial_ViewController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
            $this->_redirect('/');
    }
    public function detailsAction()
    {
        $this->loadLayout();
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle('Testimonial');
        }
        $this->renderLayout(); 
    }
}