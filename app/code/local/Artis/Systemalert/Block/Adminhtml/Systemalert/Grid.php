<?php

class Artis_Systemalert_Block_Adminhtml_Systemalert_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('systemalertGrid');
      $this->setDefaultSort('systemalert_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
      
      $this->setTemplate('systemalert/systemalert.phtml');
      return;
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('systemalert/systemalert')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('systemalert_id', array(
          'header'    => Mage::helper('systemalert')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'systemalert_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('systemalert')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('systemalert')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('systemalert')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('systemalert')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('systemalert')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('systemalert')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('systemalert')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('systemalert_id');
        $this->getMassactionBlock()->setFormFieldName('systemalert');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('systemalert')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('systemalert')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('systemalert/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('systemalert')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('systemalert')->__('Status'),
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