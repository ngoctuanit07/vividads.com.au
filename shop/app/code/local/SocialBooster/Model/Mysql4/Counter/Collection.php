<?php
/**
 * MageWorx
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
 * @category   MageWorx
 * @package    MageWorx_SocialBooster
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Social Booster extension
 *
 * @category   MageWorx
 * @package    MageWorx_SocialBooster
 * @author     MageWorx Dev Team
 */

class MageWorx_SocialBooster_Model_Mysql4_Counter_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected function _construct() {        
        $this->_init('socialbooster/counter');
    }

    public function addBookmarkFilter($bookmarkId)
    {
        $this->addFieldToFilter('bookmark_id', $bookmarkId);                                    
        return $this;
    }
    
    public function addUrlFilter($url)
    {
        $this->addFieldToFilter('url', $url);                                    
        return $this;
    }
    
    public function setBookmarkTbl()
    {              
        if ($this->getSelect()!==null) {
            $this->getSelect()->joinLeft(array('bookmark_tbl'=>$this->getTable('socialbooster/bookmark')),
                    'bookmark_tbl.bookmark_id = main_table.bookmark_id',
                    array('bookmark_title', 'bookmark_code')
                );                
        }
        return $this;
    }
    
    
    public function orderByCount($dir = 'desc')
    {
        $this->getSelect()->order("count {$dir}");
        return $this;
    }
    
    
    public function groupByUrl()
    {
        $this->getSelect()->group('main_table.url');;
        return $this;
    }
    
    public function addClicksCount()
    {
        $this->getSelect()->columns(array('clicks_count' =>new Zend_Db_Expr('SUM(`count`)')));
        return $this;
    }
    
    public function addBookmarkCount()
    {
        $this->getSelect()->columns(array('bookmark_count' =>new Zend_Db_Expr('COUNT(`bookmark_id`)')));
        return $this;
    }    
    
    
    
    public function setShellRequest()
    {              
        if ($this->getSelect()!==null) {            
            $sql = $this->getSelect()->assemble();            
            $this->getSelect()->reset()->from(array('main_table' => new Zend_Db_Expr('('.$sql.')')), '*');                                    
        }                        
        return $this;
    }
    
        
    
    
}