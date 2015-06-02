<?php
class AsiaConnect_Gallery_ViewController extends AsiaConnect_Gallery_Controller_Action
{
    public function indexAction()
    {
		if (!Mage::getStoreConfig('gallery/info/enabled')) $this->_redirect('no-route');
		$this->_redirect('gallery');
    }

	public function albumAction() {
		if (!Mage::getStoreConfig('gallery/info/enabled')) $this->_redirect('no-route');
		$album_id = $this->getRequest()->getParam('id');

		if($album_id != null && $album_id != '')	{
			$abm = Mage::getModel('gallery/album')->getCollection()
												  ->addStoreFilter(Mage::helper("core")->getStoreId())
												  ->addFieldToFilter('main_table.album_id',$album_id);
			if(sizeof($abm))
				$album = Mage::getModel('gallery/album')->load($album_id);
			else $album = false;
		} else {
			$album = false;
		}
		if ($album) {
			Mage::register('current_album', $album);
			$this->loadLayout();
			$breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs');
			$album_arr = array();
			$_album = $album;
			while($p_album = $_album->getParentAlbum())
			{
				$album_arr[] = $p_album;
				$_album = $p_album;
			}
			$rewrite = Mage::getModel('core/url_rewrite');
			$this->_addCrumb($breadcrumbBlock, Mage::helper('gallery')->__('Home'), Mage::helper('gallery')->__('home'),Mage::getBaseUrl(),true);
			for($i = sizeof($album_arr)-1; $i >= 0; $i --)
			{
				$rewrite->load($album_arr[$i]->getUrlRewriteId());
				$this->_addCrumb($breadcrumbBlock, $album_arr[$i]->getTitle(), $album_arr[$i]->getTitle(),trim(Mage::getBaseUrl(),'/')."/".$rewrite->getRequestPath(),false,false);
			}
			$this->_addCrumb($breadcrumbBlock, $album->getTitle(), $album->getTitle(),null,false,true);
			$this->renderLayout();
		} else {
			$this->_forward('noRoute');
		}
	}
	
	
	
	public function photoAction() {
		
		if (!Mage::getStoreConfig('gallery/info/enabled')) $this->_redirect('no-route');
			$photo_id = $this->getRequest()->getParam('id');
			$album_id = $this->getRequest()->getParam('album');
			//check store of current album
			$abm = Mage::getModel('gallery/album')->getCollection()
												  ->addStoreFilter(Mage::helper("core")->getStoreId())
												  ->addFieldToFilter('main_table.album_id',$album_id);
			if(sizeof($abm)){
				if($photo_id != null && $photo_id != '')	{
					$photo = Mage::getModel('gallery/gallery')->load($photo_id);
				} else {
					$photo = false;
				}	
				
				if ($photo) {
					if($album_id != null && $album_id != '')	{
						$album = Mage::getModel('gallery/album')->load($album_id);
					} else {
						$album = Mage::getModel('gallery/album')->load($photo->getAlbumId());
					}
					$collection = Mage::getModel('gallery/gallery')
									->getCollection()
									->addFieldToFilter('album_id',array('eq'=>$album->getId()));
					$nextPhoto ="";
					$previousPhoto ="";
					$index = 0;
					$i = 0;
					$_collection = array();
					foreach($collection as $key=>$_photo){
						$_collection[$i] = $_photo;
						if($photo->getId() == $_photo->getId())  $index = $i;
						$i++;
					}
					if(isset($_collection[$index - 1])) $previousPhoto = $_collection[$index - 1];
						else $previousPhoto = $_collection[sizeof($collection)-1];
					if(isset($_collection[$index + 1])) $nextPhoto = $_collection[$index + 1];
						else $nextPhoto = $_collection[0];
					
					
					Mage::register('current_photo', $photo);
					Mage::register('location_of_current_photo',$index);
					Mage::register('current_album', $album);
					Mage::register('next_photo',$nextPhoto);
					Mage::register('previous_photo',$previousPhoto);
					
					$block = $this->getLayout()->createBlock('gallery/photo','gallery_photo',array('template'=>'gallery/photoajax.phtml'));
					$this->getResponse()->setBody($block->toHtml());
					//echo $block->toHtml();
				} else {
					$this->_forward('noRoute');
				}
			}else{
				$this->_forward('noRoute');
			}
	}
}