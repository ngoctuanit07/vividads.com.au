<?php
/**
 * Class Aptoplex_EasyUploader_Model_Observer
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Model_Observer {

    protected $_helper;
    protected $_utilityHelper;

    public function __construct() {
        $this->_helper = Mage::helper('aptoplex_easyuploader');
        $this->_utilityHelper = Mage::helper('aptoplex_easyuploader/utility');
    }

    public function addEasyUploaderLinkToTopmenu(Varien_Event_Observer $observer) {
        $name = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_MENU_LINK_TITLE, null);
        if (!isset($name)) $name = 'Easy Uploader';

        $url = Mage::getBaseUrl() . Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_UPLOADER_URL, null);
        if (!isset($url)) $url = Mage::getBaseUrl() . 'easyuploader';

        $addLinkToMenu = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_ADD_LINK_TO_MENU, null);
        if ($addLinkToMenu) {
            $menu = $observer->getMenu();
            $tree = $menu->getTree();
            $node = new Varien_Data_Tree_Node(array(
                'name'   => $name,
                'id'     => 'easyuploader',
                'url'    => $url,
            ), 'id', $tree, $menu);
            $menu->addChild($node);
        }
    }

    public function updateUploaderURL() {
        $uploaderURL = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_UPLOADER_URL, null);
        $model = Mage::getModel('core/url_rewrite')->loadByIdPath('easyuploader');
        if (!$model->getId()) {
            $model->setIsSystem(0);
            $model->setIdPath('easyuploader');
            $model->setRequestPath($uploaderURL);
            $model->setTargetPath('easyuploader');
            //$model->setOptions('RP');
            $model->save();
        }
        else {
            $model->setRequestPath($uploaderURL);
            $model->save();
        }
    }

    public function insertEasyUploaderLinkBlock(Varien_Event_Observer $observer) {
        $block = $observer->getBlock();
        if ($block->getType() != 'checkout/onepage_success') {
            return;
        }

        $isEnabled = Mage::getStoreConfig(Aptoplex_EasyUploader_Helper_Data::XML_PATH_CHECKOUT_SUCCESS_ENABLE);
        if (isset($isEnabled)) {
            $layout = Mage::app()->getLayout();
            $newBlock = $layout->createBlock(
                'Aptoplex_EasyUploader_Block_UploaderLink', //'Mage_Core_Block_Template',
                'aptoplex_easyuploader',
                array('template' => 'aptoplex_easyuploader/uploaderlink.phtml')
            );
            $block->setChild('aptoplex_easyuploader', $newBlock);
            echo $block->getChildHtml();
        }
    }
}