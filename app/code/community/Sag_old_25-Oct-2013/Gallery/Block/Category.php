<?php

class Sag_Gallery_Block_Category extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getCategory() {
        if (!$this->hasData('category')) {
            //$this->setData('category', Mage::getModel('gallery/category')->getCollection());

            if (!Mage::app()->isSingleStoreMode()) {
                $collection = Mage::getModel('gallery/category')->getCollection()
                        ->addStoreFilter(Mage::app()->getStore()->getId());
            } else {
                $collection = Mage::getModel('gallery/category')->getCollection();
            }
            $this->setData('category', $collection);
        }
        return $this->getData('category');


        /*if (!Mage::app()->isSingleStoreMode()) {
            $collection = Mage::getModel('testimonial/testimonial')->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->setOrder('created_time', 'desc');
        } else {
            $collection = Mage::getModel('testimonial/testimonial')->getCollection()->setOrder('created_time', 'desc');
        }*/
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