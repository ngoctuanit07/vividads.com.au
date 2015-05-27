<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_AdminNotes
 * @copyright  Copyright (c) 2010 Iceberg Commerce
 */

class IcebergCommerce_AdminNotes_Model_Mysql4_Note_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('adminnotes/note');
    }
    
    public function setUserId( $user_id )
    {
    	if( !is_numeric( $user_id ) )
    	{
    		return $this;
    	}
    		
    	$select = $this->getSelect();
    	$select->joinLeft( array('relation'=> $this->getTable('adminnotes/note_user_relation') ), 'relation.note_id=main_table.note_id AND relation.user_id=' . $user_id, array('status') );

    	return $this;
    }
    
    public function setPathId( $path )
    {
    	$this->getSelect()->where('path_id = ?' , $path );

    	return $this;
    }
    
    public function addUsernameToSelect()
    {
    	$this->getSelect()->join( array('user' => $this->getTable('admin/user') ) , 'user.user_id=main_table.created_by' , array('username','firstname','lastname') );

    	return $this;
    }
    
    public function addNoteSearchFilter($search)
    {
    	$this->getSelect()->where('note like ?' , '%'.$search.'%' );
    	return $this;
    }
    
    public function setPage($curPage, $pageSize)
    {
    	$this->getSelect()->limitPage($curPage, $pageSize);
    	return $this;
    }
}