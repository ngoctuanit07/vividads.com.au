<?php
class AsiaConnect_Gallery_Block_Upload extends Mage_Core_Block_Template
{
	public function getCurrentAlbum()
	{
		return Mage::registry('current_album');
	}
	
	public function getChildrenAlbum($album_id = 0, $level=0,$separator="--")
	 {
	 	  $albums = array();
	 	  $collection = Mage::getModel('gallery/album')->getCollection()
		  											   ->addFieldToFilter('parent_id',$album_id);
		  foreach ($collection as $album) {
		  	$label ="";
		  	for($i = 0; $i < $level; $i++) $label .= $separator." ";
		  	$label .= $album->getTitle();
			$albums[] = array('value' => $album->getId(), 'label' => $label);
			foreach ($this->getChildrenAlbum($album->getId(),$level +1) as $value)
				$albums[] = $value;
		  }
		  return $albums;
	 }
	 
	 public function getRootAlbum()
	 {
	 	return Mage::getModel("gallery/album")->load(1);
	 }
	
}