<?php

class Sag_Gallery_Block_Category extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getCategory() {
        $browseBySmall = '';
        $browseByCaps = '';
        $g = $this->getRequest()->getParam('g');
        $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        if($g == "")$g = 'a';
        if (in_array($g, $chars)) {
            $browseBySmall = $g;
            $browseByCaps = strtoupper($g);
        }
        else{
            $browseBySmall = 'a';
            $browseByCaps = 'A';
        }       
        if (!$this->hasData('category')) {
            //$this->setData('category', Mage::getModel('gallery/category')->getCollection());

            if (!Mage::app()->isSingleStoreMode()) {
                $collection = Mage::getModel('gallery/category')->getCollection();
                $collection->addStoreFilter(Mage::app()->getStore()->getId());
                $collection->getSelect()->where("title like '$browseBySmall%' or title like '$browseByCaps%'");
            } else {
                $collection = Mage::getModel('gallery/category')->getCollection();
                $collection->getSelect()->where("title like '$browseBySmall%' or title like '$browseByCaps%'");
            }
            $this->setData('category', $collection);
        }
        return $this->getData('category');


        /* if (!Mage::app()->isSingleStoreMode()) {
          $collection = Mage::getModel('testimonial/testimonial')->getCollection()
          ->addStoreFilter(Mage::app()->getStore()->getId())
          ->setOrder('created_time', 'desc');
          } else {
          $collection = Mage::getModel('testimonial/testimonial')->getCollection()->setOrder('created_time', 'desc');
          } */
    }

    /**
     * Resize Image proportionally and return the resized image url
     *
     * @param string $imageName         name of the image file
     * @param integer|null $width       resize width
     * @param integer|null $height      resize height
     * @param string|null $imagePath    directory path of the image present inside media directory
     * @return string               full url path of the image
     */
    public function resizeImage($imageName, $width = NULL, $height = NULL, $imagePath = NULL) {
        $imagePath = str_replace("/", DS, $imagePath);
        $imagePathFull = Mage::getBaseDir('media') . DS . $imagePath . DS . $imageName;

        if ($width == NULL && $height == NULL) {
            $width = 100;
            $height = 100;
        }
        $resizePath = $width . 'x' . $height;
        $resizePathFull = Mage::getBaseDir('media') . DS . $imagePath . DS . $resizePath . DS . $imageName;

        if (file_exists($imagePathFull) && !file_exists($resizePathFull)) {
            $imageObj = new Varien_Image($imagePathFull);
            $imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(TRUE);
            $imageObj->resize($width, $height);
            $imageObj->save($resizePathFull);
        }

        $imagePath = str_replace(DS, "/", $imagePath);
        $img = Mage::getBaseUrl("media") . $imagePath . "/" . $resizePath . "/" . $imageName;
        return str_replace(DS, "/", $img);
    }

}