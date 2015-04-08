<?php

class Sag_Gallery_CategoryController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        
        
        $params = $this->getRequest()->getParams();
        //echo "<pre>";
        //print_r($params);
        //echo "</pre>";
        
        $this->loadLayout();
        //echo "Sag_Gallery_CategoryController/indexAction";
        $gCatObject = new Sag_Gallery_Block_Gallery();
        $categoryCollection = $gCatObject->getCategory();
        $category = $categoryCollection->getData();
        $head = $this->getLayout()->getBlock('head');

        
        if (isset($category[0]['title']) & !empty($category[0]['title'])) {
            $head->setTitle($category[0]['title']);
            
            $header = $this->getLayout()->getBlock('header');
            $header->setType("category");
            $header->setH1Title($category[0]['title']);
        }
        if (isset($category[0]['meta_keyword']) & !empty($category[0]['meta_keyword'])) {
            $head->setDescription($category[0]['meta_keyword']);
        }
        if (isset($category[0]['meta_description']) & !empty($category[0]['meta_description'])) {
            $head->setKeywords($category[0]['meta_description']);
        }
        //$current_cat = $this->getRequest()->getParam('cat');
        $this->renderLayout();
    }

}