<?php
class AsiaConnect_Gallery_PController extends AsiaConnect_Gallery_Controller_Action
{
    public function savereviewAction()
    {
    	if (!Mage::getStoreConfig('gallery/info/enabled')) $this->_redirect('no-route');
    	$data = $this->getRequest()->getPost();
    	$data['create_time'] = now();
    	$data['status'] = AsiaConnect_Gallery_Model_Review_Status::STATUS_PENDING;
    	$model = Mage::getModel('gallery/review')->setData($data)->save();
    	
    	if($data['status']== AsiaConnect_Gallery_Model_Review_Status::STATUS_APPROVED)
    	{
    		$gallery = Mage::getModel('gallery/gallery')->load($data['gallery_id']);
    		$rate = AsiaConnect_Gallery_Model_Review::_getRate($gallery);
    		$gallery->setRate($rate);
    		$gallery->save();
    	}
		Mage::getSingleton('gallery/session')->setSuccess(Mage::helper('gallery')->__('Review was successfully sended and waiting admin approve'));
		Mage::getSingleton('gallery/session')->setFormData(false);
    	$this->_redirectSuccess($data['url']);
    }
    
    public function indexAction()
    {
    	if (!Mage::getStoreConfig('gallery/info/enabled')) $this->_redirect('no-route');
    	$this->loadLayout();
    	$head = $this->getLayout()->getBlock('head');
		$Block = $this->getLayout()->createBlock('core/template','ie6.fix',array('template'=>'gallery/ie6_fix.phtml'));
		$head->setChild('gallery.ie6fix',$Block);
		
		$breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs');
		$this->_addCrumb($breadcrumbBlock, Mage::helper('gallery')->__('Home'), Mage::helper('gallery')->__('home'),Mage::getBaseUrl(),true);
		$this->_addCrumb($breadcrumbBlock, Mage::helper('gallery')->__('Gallery'), Mage::helper('gallery')->__('gallery'),Mage::getUrl('gallery'),false,false);
    	$this->renderLayout();
    }
    public function getBreadcrumbAction()
    {
    	if (!Mage::getStoreConfig('gallery/info/enabled')) $this->_redirect('no-route');
		$photo_id = $this->getRequest()->getParam('id');
		$album_id = $this->getRequest()->getParam('album');
    	if($photo_id != null && $photo_id != '')	{
				$photo = Mage::getModel('gallery/gallery')->load($photo_id);
			} else {
				$photo = false;
			}	
			
			if ($photo) 
				if($album_id != null && $album_id != '')	{
					$album = Mage::getModel('gallery/album')->load($album_id);
				} else {
					$album = Mage::getModel('gallery/album')->load($photo->getAlbumId());
				}
    	$_album = $album;
    	$album_arr = array();
		while($p_album = $_album->getParentAlbum())
		{
			$album_arr[] = $p_album;
			$_album = $p_album;
		}
		$rewrite = Mage::getModel('core/url_rewrite');
    	$breadcrumbBlock = $this->getLayout()->createBlock('page/html_breadcrumbs','breadcrumbs',array('template'=>'gallery/page/html/breadcrumbs.phtml'));
    	$rewrite = Mage::getModel('core/url_rewrite');
		$this->_addCrumb($breadcrumbBlock, Mage::helper('gallery')->__('Home'), Mage::helper('gallery')->__('home'),Mage::getBaseUrl(),true);
		for($i = sizeof($album_arr)-1; $i >= 0; $i --)
		{
			$rewrite->load($album_arr[$i]->getUrlRewriteId());
			$this->_addCrumb($breadcrumbBlock, $album_arr[$i]->getTitle(), $album_arr[$i]->getTitle(),trim(Mage::getBaseUrl(),'/')."/".$rewrite->getRequestPath(),false,false);
		}
		$rewrite->load($album->getUrlRewriteId());
		$this->_addCrumb($breadcrumbBlock, $album->getTitle(), $album->getTitle(),trim(Mage::getBaseUrl(),'/')."/".$rewrite->getRequestPath(),false,false);
		$this->_addCrumb($breadcrumbBlock, $photo->getTitle(), $photo->getTitle(),null,true,true);
		echo $breadcrumbBlock->toHtml();
    }
}