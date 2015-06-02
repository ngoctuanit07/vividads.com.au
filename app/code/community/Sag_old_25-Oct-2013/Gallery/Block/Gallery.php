<?php

class Sag_Gallery_Block_Gallery extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getGallery() {
        if (!$this->hasData('gallery')) {
            $current_cat = $this->getRequest()->getParam('cat');
            $this->setData('gallery', Mage::getModel('gallery/gallery')->getCollection()->addFieldToFilter("category", $current_cat));
        }
        return $this->getData('gallery');
    }

    public function getImage() {
        if (!$this->hasData('gallery')) {
            $current_img = $this->getRequest()->getParam('img');
            $this->setData('gallery', Mage::getModel('gallery/gallery')->getCollection()->addFieldToFilter("gallery_id", $current_img));
        }
        return $this->getData('gallery');
    }

    public function getCategory() {
        if (!$this->hasData('gcategory')) {
            $current_cat = $this->getRequest()->getParam('cat');
            $this->setData('gcategory', Mage::getModel('gallery/category')->getCollection()->addFieldToFilter("category_id", $current_cat));
        }
        return $this->getData('gcategory');

        //return $this->getData('cat');
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