<?php

class MDN_Quotation_Block_Ticket_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	private $_guid = null;
	
	static $_guidSequence = 1;
	
    private $_EntityId;
	private $_EntityType;
	private $_Mode;
	private $_EnableAdd = 1;
	private $_EnableSortFilter = 1;
	
	private $_ShowTarget = true;
	private $_ShowEntity = true;
	
	private $_Title = 'Tickets';
	
	public function __construct() {
        
		parent::__construct();
        
		$this->setId('TicketsGrid'.$this->getGuid());
       // $this->setDefaultSort('ot_id');
		$this->setDefaultSort('ct_created_at');
        $this->setDefaultDir('DESC');
        $this->_parentTemplate = $this->getTemplate();
        //$this->setTemplate('Quotation/Ticket/List.phtml');	
        $this->setEmptyText($this->__('No items'));
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    /**
     * Gestion des entit�s (type & id)
     *
     * @param unknown_type $EntityId
     * @return unknown
     */
    public function setEntityId($EntityId)
    {
    	$this->_EntityId = $EntityId;
    	return $this;
    }
    public function getEntityId()
    {
    	return $this->_EntityId;
    }
    public function setEntityType($EntityType)
    {
    	$this->_EntityType = $EntityType;
    	return $this;
    }
    public function getEntityType()
    {
    	return $this->_EntityType;
    }
    public function setMode($mode)
    {
    	$this->_Mode = $mode;
    }
    public function getMode()
    {
    	return $this->_Mode;
    }
    
    public function setEnableAdd($enableAdd)
    {
    	$this->_EnableAdd = $enableAdd;
    }
    public function getEnableAdd()
    {
    	return $this->_EnableAdd;
    }
    
    public function setEnableSortFilter($enableSortFilter)
    {
    	$this->_EnableSortFilter = $enableSortFilter;
    	$this->setFilterVisibility($enableSortFilter);
    }
    public function getEnableSortFilter()
    {
    	return $this->_EnableSortFilter;
    }
    
    /**
     * Affichage de colonnes
     *
     * @param unknown_type $value
     */
    public function setShowTarget($value)
    {
    	$this->_ShowTarget = $value;
    	return $this;
    }
    public function setShowEntity($value)
    {
    	$this->_ShowEntity = $value;
    	return $this;
    }
    
    public function setTitle($title)
    {
    	$this->_Title = $title;
    }
    public function getTitle()
    {
    	return $this->_Title;
    }
    
   
    /**
     * Charge la collection
     *
     * @return unknown
     */
    protected function _prepareCollection() {
		$quote_id = $this->getRequest()->getParam('quote_id');		
        $collection = Mage::getModel('CrmTicket/Ticket')
                ->getCollection()
                ->addFieldToFilter('ct_object_id', 'quote_' . $quote_id)
                ->setOrder('ct_created_at', 'desc');        
		$this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
   /**
     * D�fini les colonnes du grid
     *
     * @return unknown
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('CrmTicket');

        $this->addColumn('ct_id', array(
            'header' => $helper->__('Id'),
            'index' => 'ct_id',
            'renderer' => 'MDN_CrmTicket_Block_Admin_Widget_Grid_Column_Renderer_IdLink',
            'align' => 'center',
            'width' => '20px'
        ));

        $this->addColumn('ct_created_at', array(
            'header' => $helper->__('Created at'),
            'index' => 'ct_created_at',
            'type' => 'datetime'
        ));

        $this->addColumn('ct_updated_at', array(
            'header' => $helper->__('Updated at'),
            'index' => 'ct_updated_at',
            'type' => 'datetime'
        ));

        $this->addColumn('ct_subject', array(
            'header' => $helper->__('subject'),
            'index' => 'ct_subject',
            'type' => 'varchar'
        ));

        $this->addColumn('ct_status', array(
            'header' => $helper->__('status'),
            'index' => 'ct_status',
            'type' => 'options',
            'options' => $this->getStatus(),
            'renderer' => 'MDN_CrmTicket_Block_Admin_Widget_Grid_Column_Renderer_Status',
            'filter' => 'MDN_CrmTicket_Block_Admin_Widget_Grid_Column_Filter_MultiSelect'
        ));

        $this->addColumn('ct_manager', array(
            'header' => $helper->__('manager'),
            'index' => 'ct_manager',
            'type' => 'options',
            'options' => $this->getManagers()
        ));

        $this->addColumn('ct_message', array(
            'header' => $helper->__('Messages'),
            'index' => 'ct_id',
            'renderer' => 'MDN_CrmTicket_Block_Admin_Widget_Grid_Column_Renderer_Messages',
            'align' => 'center',
            'filter' => 'MDN_CrmTicket_Block_Admin_Widget_Grid_Column_Filter_Search'
        ));

        return parent::_prepareColumns();
    }

     public function getGridUrl()
    {
        return $this->getUrl('Organizer/Task/EntityList', 
        					array(
        						'entity_type'=>$this->_EntityType, 
        						'entity_id' => $this->_EntityId,
        						'show_target' => $this->_ShowTarget,
        						'show_entity' => $this->_ShowEntity,
        						'guid'	=> $this->_guid,
        						'mode'	=> $this->_Mode,
        						'enable_sort_filter' => $this->_EnableSortFilter
        						)
        					);
    }

    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }
    
    /**
     * Retourne la liste des utilisateurs sous la forme d'un array
     *
     */
    public function getUsersAsArray()
    {
    	//recupere la liste des utilisateurs
		$collection = mage::getModel('admin/user')
			->getCollection()
			->addFieldToFilter('is_active', 1);
		
		$retour = array();
		foreach ($collection as $item)
		{
			$retour[$item->getuser_id()] = $item->getusername();
		}
		
		
		return $retour;
    }

    /**
     * Retourne la liste des cat�gories sous la forme d'un array
     *
     */
    
    
    public function getGuid()
    {
    	if ($this->_guid == null)
    	{
    		if ($this->getRequest()->getParam('guid') != '')
    		{
    			$this->_guid = $this->getRequest()->getParam('guid');
    		}
    		else 
    		{
	    		$this->_guid = MDN_Organizer_Block_Task_Grid::$_guidSequence;
	    		MDN_Organizer_Block_Task_Grid::$_guidSequence += 1;
    		}
    	}
    	return $this->_guid;
    }
}
