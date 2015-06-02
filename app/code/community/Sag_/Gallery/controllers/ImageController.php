<?php

class Sag_Gallery_ImageController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();

        $tag = $this->getRequest()->getParam('tag');
        $city = $this->getRequest()->getParam('city');
        $country = $this->getRequest()->getParam('country');
        //echo "Tag:".$tag."<br />";
        //echo "City:".$city."<br />";
        //echo "Country:".$country."<br />";

        $gObject = new Sag_Gallery_Block_Gallery();
        $galleryCollection = $gObject->getImage();
        $gallery = $galleryCollection->getData();

        $head = $this->getLayout()->getBlock('head');
        if (isset($gallery[0]['title']) & !empty($gallery[0]['title'])) {
            $head->setTitle($gallery[0]['title']);
            $header = $this->getLayout()->getBlock('header');
            $header->setType("gallery");
            $header->setH1Title($gallery[0]['title']);
        }
        if (isset($gallery[0]['meta_keyword']) & !empty($gallery[0]['meta_keyword'])) {
            $head->setDescription($gallery[0]['meta_keyword']);
        }
        if (isset($gallery[0]['meta_description']) & !empty($gallery[0]['meta_description'])) {
            $head->setKeywords($gallery[0]['meta_description']);
        }


        $this->renderLayout();
    }

}