<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_AdminNotes
 * @copyright  Copyright (c) 2010 Iceberg Commerce
 */

class IcebergCommerce_AdminNotes_Model_Mysql4_Note extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('adminnotes/note', 'note_id');
    }
    
	/**
     * Retrieve select object for load object data
     *
     * @param   string $field
     * @param   mixed $value
     * @return  Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('main_table' => $this->getMainTable() ))
            ->join( array('user' => $this->getTable('admin/user') ) , 'user.user_id=main_table.created_by' , array('username','firstname','lastname') )
            ->where('main_table.'.$field.'=?', $value);
            
        if( $object->hasUserId() )
        {
        	$select->joinLeft( array('relation'=> $this->getTable('adminnotes/note_user_relation') ), 'relation.note_id=main_table.note_id AND relation.user_id=' . $object->getUserId() , array('status') );
        }
        
        return $select;
    }
    
    public function loadStatus( $model )
    {
    	$select = $this->_getReadAdapter()->select()
            ->from(
                array('main'=>$this->getTable('adminnotes/note_user_relation')) , 'status'
            )
            ->where('main.user_id = ?', $model->getUserId() )
            ->where('main.note_id = ?', $model->getId() );
            
        $result = $this->_getReadAdapter()->fetchOne($select);
        
        if( $result !== false )
        {
	        $model->setStatus( $result );
        }
        
        return $this;
    }
    
    public function updateStatus( $model , $status )
    {
	   	$data = array('status' => $status , 'user_id' => $model->getUserId() );

    	if( $model->hasStatus() )
    	{
    		$this->_getWriteAdapter()->update($this->getTable('adminnotes/note_user_relation'),
                $data ,
                array(
                    $this->_getWriteAdapter()->quoteInto('note_id=?', $model->getId() ),
                    $this->_getWriteAdapter()->quoteInto('user_id=?', $model->getUserId() ),
                )
            );
    	}else{
    		$data['note_id'] = $model->getId();
    		$this->_getWriteAdapter()->insert($this->getTable('adminnotes/note_user_relation'), $data);
    	}
    }
    
	public function deleteRelations( $model )
    {
    	$this->_getWriteAdapter()->delete($this->getTable('adminnotes/note_user_relation'),
            array(
                $this->_getWriteAdapter()->quoteInto('note_id=?', $model->getId() ),
            )
        );
        
        return $this;
    }
}