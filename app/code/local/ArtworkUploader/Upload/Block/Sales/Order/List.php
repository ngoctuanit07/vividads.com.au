<?php
/**
 * Artwork Upload
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   ArtworkUploader
 * @package    MageWorx_OrdersPro
 * @copyright  Copyright (c) 2012 Vivid Advertising
 * @license    http://www.vividads.com.au
 */

/**
 * Orders Pro extension
 *
 * @category   Artwork
 * @package    ArtworkUpload
 * @author     Ashfaq Ahmed
 */
class ArtworkUploader_Upload_Block_Sales_Order_List extends Mage_Core_Block_Template
{

    public function __construct()
    {        
       $this->_controller = 'upload';
    $this->_blockGroup = 'upload';
   // $this->_headerText = Mage::helper('upload')->__('Item Manager');
   // $this->_addButtonLabel = Mage::helper('upload')->__('Add Item');
    parent::__construct();
       
       
    }        
    
}
