<?php

class AsiaConnect_Gallery_Block_Adminhtml_Gallery_Multiadd_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('gallery_form', array('legend'=>Mage::helper('gallery')->__('Photo archive information')));

      $fieldset->addField('import_image_archive', 'file', array(
          'label'     => Mage::helper('gallery')->__('Upload File'),
          //'required'  => true,
          'name'      => 'import_image_archive',
      	  'class'	  => 'validate-upload-either',
	  ));
      
      $fieldset->addField('filepath', 'text', array(
          'label'     => Mage::helper('gallery')->__('File path'),
          //'required'  => true,
          'name'      => 'filepath',
      	  'class'	  => 'validate-upload-either',
	  ));
	  
	  $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('gallery')->__('Base Title'),
          //'required'  => true,
          'name'      => 'title',
      	  'class'	  => '',
	  ));
	  
	  $albums = array(array('value' => '', 'label' => 'Select an Album'));
	  $albums = $this->getChildrenAlbum();
      $fieldset->addField('album_id', 'select', array(
          'label'     => Mage::helper('gallery')->__('Album'),
          'name'      => 'album_id',
          'required'  => true,
          'values'    => $albums,
      ));

      return parent::_prepareForm();
  }
  
 private function getChildrenAlbum($album_id = 0, $level=0,$separator="--")
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
 
}