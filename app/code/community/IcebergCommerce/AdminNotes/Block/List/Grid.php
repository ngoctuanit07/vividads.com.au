<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_AdminNotes
 * @copyright  Copyright (c) 2010 Iceberg Commerce
 */

class IcebergCommerce_Adminnotes_Block_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
       parent::__construct();
       $this->setId('id');
       $this->setDefaultSort('created_at');
       $this->setDefaultDir('DESC');
       //$this->setDefaultFilter(array('status'=> 0 ));
       $this->setSaveParametersInSession(true);
       $this->setUseAjax(true);
	} 
  
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('adminnotes/note_collection');
			
		$collection->setUserId( Mage::getSingleton('admin/session')->getUser()->getId() )
					->addUsernameToSelect();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
  
        $this->addColumn('path_id', array(
            'header'    => Mage::helper('adminnotes')->__('Path ID'),
            'width'     => '150px',
            'index'     => 'path_id',
            'type'  => 'text',
        ));
        
        $this->addColumn('type',
            array(
                'header'=> Mage::helper('adminnotes')->__('Type'),
                'width' => '70px',
                'index' => 'type',
                'type'  => 'options',
                'options' => Mage::getSingleton('adminnotes/note')->getTypes(),
        ));

        /*$this->addColumn('title', array(
            'header'    => $this->__('Title'),
            'width'     => '150px',
            'index'     => 'title',
            'type'      => 'text',
        ));*/
        
        $this->addColumn('note', array(
            'header'    => Mage::helper('adminnotes')->__('Note'),
            'width'     => '400px',
            'index'     => 'note',
            'type'  => 'text',
        ));
        $this->addColumn('status',
            array(
                'header'=> Mage::helper('adminnotes')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => array(0 => 'Visible' , 1 => 'Hidden'),
        ));
        $this->addColumn('username', array(
            'header'    => Mage::helper('adminnotes')->__('Created By'),
            'width'     => '100px',
            'index'     => 'username',
            'type'  => 'text',
        ));
        $this->addColumn('created_at',
            array(
                'header'=> Mage::helper('adminnotes')->__('Date Created'),
                'width' => '160px',
                'index' => 'created_at',
                'type'  => 'datetime',
        ));
        
        return parent::_prepareColumns();
    }
    
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('note_id');
        $this->getMassactionBlock()->setFormFieldName('note_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        //if (Mage::getSingleton('admin/session')->isAllowed('admin/adminnotes/edit')) {
        //    $this->getMassactionBlock()->addItem('note_type', array(
        //         'label'=> Mage::helper('adminnotes')->__('Set Notes Type'),
        //         'url'  => $this->getUrl('*/*/massType'),
	    //        'additional' => array(
	    //                'visibility' => array(
	    //                     'name' => 'type',
	    //                     'type' => 'select',
	    //                     'class' => 'required-entry',
	    //                     'label' => Mage::helper('adminnotes')->__('Type'),
	    //                     'values' => Mage::getSingleton('adminnotes/note')->getTypes()
	    //                 )
	    //         )
        //    ));
        //}
        
    	if (Mage::getSingleton('admin/session')->isAllowed('admin/adminnotes/delete')) {
            $this->getMassactionBlock()->addItem('delete_note', array(
                 'label'=> Mage::helper('adminnotes')->__('Delete Notes'),
                 'url'  => $this->getUrl('*/*/massDelete'),
            ));
        }
        
        $this->getMassactionBlock()->addItem('hide_note', array(
             'label'=> Mage::helper('adminnotes')->__('Hide Notes'),
             'url'  => $this->getUrl('*/*/massHide'),
        ));
        $this->getMassactionBlock()->addItem('unhide_note', array(
             'label'=> Mage::helper('adminnotes')->__('Unhide Notes'),
             'url'  => $this->getUrl('*/*/massUnhide'),
        ));

        return $this;
    }


    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=> true));
    }

    public function getRowUrl($row)
    {
        return Mage::helper('adminhtml')->getUrl($row->getPath());//$row->getPath();
    }
}
