<?php
/**
 * Class Aptoplex_EasyUploader_Block_Adminhtml_Upload
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Block_Adminhtml_Upload extends Mage_Adminhtml_Block_Widget_Grid_Container {

    protected $helper;

    /**
     * Internal constructor
     */
    public function _construct() {
        parent::_construct();
        $this->helper = Mage::helper('aptoplex_easyuploader');
        $this->_blockGroup = 'aptoplex_easyuploader_adminhtml';
        $this->_controller = 'upload';
        $this->_headerText = Mage::helper('aptoplex_easyuploader')->__('Uploads');
    }

    public function __construct() {
        parent::__construct();
        $this->_removeButton('add');
    }

    public function _toHtml() {
        if (Aptoplex_EasyUploader_Helper_Data::RUN_IN_DEMO_MODE) {
            echo <<<HEREDOC
<div style="margin-bottom:20px;">
    <h4><strong style="color:red;">Easy Uploader is running in DEMO mode:</strong></h4>
    <p>The following restrictions apply:
    <ul style="list-style:disc; padding:0 0 10px 30px;">
        <li>Uploads cannot be deleted (they will automatically be deleted by the system from time to time).</li>
        <li>The IP Address column is intentionally hidden for security reasons.</li>
    </ul>
</div>
HEREDOC;
        }
        return parent::_toHtml();
    }
}