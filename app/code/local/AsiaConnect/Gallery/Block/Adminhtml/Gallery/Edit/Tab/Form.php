<?php

class AsiaConnect_Gallery_Block_Adminhtml_Gallery_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('gallery_form', array('legend'=>Mage::helper('gallery')->__('Photo information')));

      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('gallery')->__('Photo title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
      
      $fieldset->addField('filename', 'image', array(
          'label'     => Mage::helper('gallery')->__('Photo image'),
          'required'  => true,
          'name'      => 'filename',
	  ));
      $fieldset->addField('url_key', 'text', array(
          'label'     => Mage::helper('gallery')->__('URL Key (suffix)'),
          'required'  => false,
          'name'      => 'url_key',
      ));
      
	  $albums = array(array('value' => '', 'label' => 'Select an Album'));
	  $albums = $this->getChildrenAlbum();
      $fieldset->addField('album_id', 'select', array(
          'label'     => Mage::helper('gallery')->__('Album'),
          'name'      => 'album_id',
          'required'  => true,
          'values'    => $albums,
      ));

      $fieldset->addField('photo_link', 'text', array(
          'label'     => Mage::helper('gallery')->__('Link to page'),
          'required'  => false,
          'name'      => 'photo_link',
      	  'note'	  => 'Enter page address',	
      ));
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('gallery')->__('Status'),
          'name'      => 'status',
      	  'required'  => true,
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('gallery')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('gallery')->__('Disabled'),
              ),
          ),
      ));
      
      $fieldset->addField('order', 'text', array(
          'label'     => Mage::helper('gallery')->__('Order'),
          'class'     => 'validate-zero-or-greater input-text validation-failed',
          'required'  => false,
          'name'      => 'order',
      ));
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('gallery')->__('Description'),
          'title'     => Mage::helper('gallery')->__('Description'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false
      ));
      $fieldset->addField('url_rewrite_id', 'hidden', array(
          'label'     => Mage::helper('gallery')->__('Url rewrite Id'),
          'required'  => false,
          'name'      => 'url_rewrite_id',
      ));
      if ( Mage::getSingleton('adminhtml/session')->getGalleryData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getGalleryData());
          Mage::getSingleton('adminhtml/session')->setGalleryData(null);
      } elseif ( Mage::registry('gallery_data') ) {
          $form->setValues(Mage::registry('gallery_data')->getData());
      }

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