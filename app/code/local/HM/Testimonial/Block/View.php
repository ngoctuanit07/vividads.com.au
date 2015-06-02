<?php
class Hm_Testimonial_Block_View extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        //$route = Mage::helper('news')->getRoute(); 
        $isNewsPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName() == 'testimonial';
        
        // show breadcrumbs
        if ($isNewsPage && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))){
                $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('blog')->__('Home'), 'title'=>Mage::helper('blog')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));;
                $breadcrumbs->addCrumb('testimonial', array('label'=>'Testimonial', 'title'=>Mage::helper('testimonial')->__('Return to testimonial'), 'link'=>Mage::getUrl('testimonial')));
        }
                        
        return parent::_prepareLayout();   
    }
    
    public function getTestimonial()
       {
        
        $_testimonial  =   Mage::getModel('testimonial/testimonial')
                ->  setStoreId(Mage::app()->getStore()->getId())
                ->  load($this->getRequest()->getParam('id'), 'testimonial_id');
 
        /*$_news  ->  setTitle($_news->getTitle())
                ->  setContent($_news->getDescripton())
                ->  setCreatedTime($this->formatTime($_news->getCreatedTime(),'DDMMYYYY', true))
                ->  setUpdateTime($this->formatTime($_news->getUpdateTime(),'DDMMYYYY', true));*/
        
        //$this->setData('news', $_news);       
        return $_testimonial;
    }
}