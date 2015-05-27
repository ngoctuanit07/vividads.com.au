<?php

class AsiaConnect_Gallery_Block_Adminhtml_Album_Edit_Tab_Information extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('album_information_form', array('legend'=>Mage::helper('gallery')->__('Album information')));
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('gallery')->__('Album Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'image', array(
          'label'     => Mage::helper('gallery')->__('Album image'),
          'required'  => true,
          'name'      => 'filename',
	  ));
      $fieldset->addField('url_key', 'text', array(
          'label'     => Mage::helper('gallery')->__('URL Key (suffix)'),
          'required'  => false,
          'name'      => 'url_key',
      ));
	  if(Mage::registry('album_data')->getId() == 1) $defaultValue = 0;
	  else $defaultValue = 1;
	  
  	  $albums = array(array('value' => $defaultValue, 'label' => 'Select an Album'));
	  //a album isn't child of it self
	  $currentAlbum = Mage::registry('album_data');
  	  $collection = Mage::getModel('gallery/album')->getCollection();
  	  if($currentAlbum->getId())
  	  	$collection->addFieldToFilter('album_id',array('neq'=>$currentAlbum->getId()));
  	  //get all children of this album
  	  $children = array();
  	  if($currentAlbum->getId())
  	  	$children = Mage::getModel('gallery/album')->getCollection()
    				 ->addFieldToFilter('parent_id',array('eq'=>Mage::registry('album_data')->getId()))
    				->getAllIds();
	  foreach ($collection as $album) {
	  	 if(! in_array($album->getId(),$children))
		 	$albums[] = array('value' => $album->getId(), 'label' => $album->getTitle());
	  }
	  
	  
	  $fieldset->addField('parent_id', 'select', array(
          'label'     => Mage::helper('gallery')->__('Parent Album'),
          'required'  => true,
          'name'      => 'parent_id',
	  	  'values'	  => $albums,
      ));
      
       if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
       }
     
	$fieldset->addField('featured', 'select', array(
          'label'     => Mage::helper('gallery')->__('Feature Album On Gallery Page'),
          'name'      => 'featured',
		  'required'  => true,
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('gallery')->__('Yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('gallery')->__('No'),
              ),
          ),
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
          'label'     => Mage::helper('gallery')->__('Top Description'),
          'title'     => Mage::helper('gallery')->__('Top Description'),
          'style'     => 'width:700px; height:300px;',
          'wysiwyg'   => false,
      ));
      $fieldset->addField('bottom_description', 'editor', array(
          'name'      => 'bottom_description',
          'label'     => Mage::helper('gallery')->__('Bottom Description'),
          'title'     => Mage::helper('gallery')->__('Bottom Description'),
          'style'     => 'width:700px; height:200px;',
          'wysiwyg'   => false,
      ));

      if ( Mage::getSingleton('adminhtml/session')->getGalleryData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getGalleryData());
          Mage::getSingleton('adminhtml/session')->setGalleryData(null);
      } elseif ( Mage::registry('album_data')->getId() ) {
          $form->setValues(Mage::registry('album_data')->getData());
          if (!Mage::app()->isSingleStoreMode()) {
	          $collection =  Mage::getModel('gallery/album')->getCollection();
			  $collection->join('gallery_store', 'gallery_store.album_id = main_table.album_id AND main_table.album_id='.Mage::registry('album_data')->getId(), 'gallery_store.store_id');
			  $arrStoreId = array();
			  foreach($collection->getData() as $col){
			  	$arrStoreId[] = $col['store_id'];	
			  }
			  // set value for store view selected:
			  $form->getElement('store_id')->setValue($arrStoreId);
          }
      }
      return parent::_prepareForm();
  }
}