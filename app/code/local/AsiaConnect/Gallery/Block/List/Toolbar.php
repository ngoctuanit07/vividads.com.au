<?php
class AsiaConnect_Gallery_Block_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    protected function _construct()
    {
        parent::_construct();
        $this->_orderField  = 'title';
        //load avaiable limit
    	$arr= explode(',',Mage::getStoreConfig('gallery/info/photo_per_page_allowed'));
		$availableLimit = array();
		foreach($arr as $value){
			$value = trim($value,' ');
			$this->_availableLimit['detail'][$value] =$value;
			$this->_availableLimit['simple'][$value] = $value;
		}
        $this->_availableOrder=array('main_table.order'=>$this->__('Default'),'main_table.title'=>$this->__('Name'),'rate'=>$this->__('Rate'));
		$this->_availableMode = array('detail' =>  $this->__('Detail'),'simple' => $this->__('Simple'));
        $this->setTemplate('gallery/list/toolbar.phtml');
    }
    public function getAvailableLimit()
    {
        $currentMode = $this->getCurrentMode();
        if (in_array($currentMode, array('detail', 'simple'))) {
            return $this->_availableLimit[$currentMode];
        } else {
            return $this->_defaultAvailableLimit;
        }
    }
	public function getSearchLimitUrl($limit)
    {
        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams    = array(
            $this->getLimitVarName() => $limit,
            $this->getPageVarName() => null,
        );
        return $this->getUrl('*/*/*', $urlParams);
    }	
    
	public function getPagerHtml()
    {
        $pagerBlock = $this->getChild('gallery_pager');
       
        if ($pagerBlock instanceof Varien_Object) {

            /* @var $pagerBlock Mage_Page_Block_Html_Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setShowPerPage(false);
            $pagerBlock->setUseContainer(false)
                ->setShowAmounts(false);
            $pagerBlock->setLimitVarName($this->getLimitVarName())
                ->setPageVarName($this->getPageVarName())
                ->setLimit($this->getLimit())
                ->setFrameLength(Mage::getStoreConfig('design/pagination/pagination_frame'))
                ->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'))
                ->setCollection($this->getCollection());

            return $pagerBlock->toHtml();
        }

        return '';
    }
}
