<?php

class AsiaConnect_Gallery_Block_Adminhtml_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('reviewGrid');
      $this->setDefaultSort('review_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('gallery/review')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
	$this->addColumn('create_time', array(
          'header'    => Mage::helper('gallery')->__('Create on'),
		  'type'	  => 'datetime',
          'align'     =>'left',
          'index'     => 'order',
		  'width'     => '200px',
		  'index'	  => 'create_time',
      ));
      $this->addColumn('status', array(
          'header'    => Mage::helper('gallery')->__('Status'),
          'align'     => 'left',
          'width'     => '100px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => Mage::helper('gallery')->__('Pending'),
              2 => Mage::helper('gallery')->__('Approved'),
              3 => Mage::helper('gallery')->__('Not Approved'),
          ),
      ));
      $this->addColumn('name', array(
			'header'    => Mage::helper('gallery')->__('Customer Name'),
			'index'     => 'name',
		    'width'     => '150px',
      ));
      $this->addColumn('email', array(
			'header'    => Mage::helper('gallery')->__('Customer Email'),
			'index'     => 'email',
		    'width'     => '200px',
      ));
      $this->addColumn('content', array(
			'header'    => Mage::helper('gallery')->__('Review'),
			'index'     => 'content',
      )); 
        
      $this->addColumn('gallery_id', array(
          'header'    => Mage::helper('gallery')->__('Photo id'),
          'align'     =>'left',
          'index'     => 'gallery_id',
      	  'width'     => '50px',
      ));
     
      $this->addColumn('review_type', array(
          'header'    => Mage::helper('gallery')->__('Type'),
          'align'     => 'left',
          'width'     => '100px',
          'index'     => 'review_type',
          'type'      => 'options',
          'options'   => array(
              1 => Mage::helper('gallery')->__('Guest'),
              2 => Mage::helper('gallery')->__('Customer'),
              3 => Mage::helper('gallery')->__('Admin'),
          ),
      ));
	  $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('gallery')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('gallery')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
      	  		'width'     => '50px',
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
	  
		
		$this->addExportType('*/*/exportCsv', Mage::helper('gallery')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('gallery')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('review_id');
        $this->getMassactionBlock()->setFormFieldName('review');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('gallery')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('gallery')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('gallery/review_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('gallery')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('gallery')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}