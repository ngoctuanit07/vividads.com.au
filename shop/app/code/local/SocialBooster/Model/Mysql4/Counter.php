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

class MageWorx_SocialBooster_Model_Mysql4_Counter extends Mage_Core_Model_Mysql4_Abstract {
    
    protected function _construct() 
    {        
        $this->_init('socialbooster/counter', 'counter_id');
    }           
    
    
    public function loadByBookmarkIdAndUrl($object, $bookmarkId, $url)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('bookmark_id=?', $bookmarkId)
            ->where('url=?', $url);                
        
        $data = $adapter->fetchRow($select);
                
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);        
        
    }
    
    public function deleteByBookmarkId($bookmarkId)
    {        
        $this->_getWriteAdapter()->delete($this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('bookmark_id=?', $bookmarkId)
        );
        return $this;
    }
    
    
    public function deleteByUrl($url)
    {        
        $this->_getWriteAdapter()->delete($this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('url=?', $url)
        );
        return $this;
    }
        
    
}