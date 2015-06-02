<?php
/**
 * Altima Lookbook Free Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Altima
 * @package    Altima_LookbookFree
 * @author     Altima Web Systems http://altimawebsystems.com/
 * @email      support@altima.net.au
 * @copyright  Copyright (c) 2012 Altima Web Systems (http://altimawebsystems.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Altima_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      /********************** Start by dev **************************/
      $products = Mage::getModel('catalog/product')->getCollection();
     // $products->addAttributeToFilter('status', 1);//enabled
      //$products->addAttributeToFilter('visibility', 4);//catalog, search
       $products->addAttributeToSelect('*');
      $prodIds=$products->getAllIds();
      
      //echo "<pre>";
      //print_r($prodIds);
      //echo "</pre>";
      
      
      for($i=0;$i<count($prodIds);$i++)
      {
	      $temptableLookbook=Mage::getSingleton('core/resource')->getTableName('lookbook');
	      
	      $sqlLookbook="select *  from ".$temptableLookbook." where product_id='".$prodIds[$i]."'";
	      try {
		      $chkLookbook = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sqlLookbook);
		      
		      if(count($chkLookbook)>0)
		      {
			      foreach($chkLookbook as $res_Lookbook) 
			      {
				      
				      $_previewWidth   = intval(Mage::getStoreConfig('amzoom/size/preview_width'));
				      $_previewHeight  = intval(Mage::getStoreConfig('amzoom/size/preview_height'));
				      
				      $_product=Mage::getModel('catalog/product')->load($prodIds[$i]);
				      // get  Product's name
				      //echo $_product->getName();
				      ////get product's short description
				      //echo $_product->getShortDescription();
				      ////get Product's Long Description
				      //echo $_product->getDescription();
				      ////get  Product's Regular Price
				      //echo $_product->getPrice();
				      ////get  Product's Special price
				      //echo $_product->getSpecialPrice();
				      ////get Product's Url
				      //echo $_product->getProductUrl();
				      ////get Product's image Url
				      //echo $_product->getImageUrl();
				      $search_txt="/media/";
				      $start_pos=strpos($_product->getSmallImageUrl($_previewWidth,$_previewHeight),$search_txt);
				      
				      $sub=substr($_product->getSmallImageUrl($_previewWidth,$_previewHeight),($start_pos+7));
				      
				      
				      
				      
				      
				      $sqlLookbook="update ".$temptableLookbook." set image='".$sub."' where product_id='".$prodIds[$i]."'";
				      $chkLookbook = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlLookbook);
				      
				      
				      
				      
				      
				      
				      
				      
			      }	
		      }
		      else
		      {
			      $_previewWidth   = intval(Mage::getStoreConfig('amzoom/size/preview_width'));
			      $_previewHeight  = intval(Mage::getStoreConfig('amzoom/size/preview_height'));
			      $_product=Mage::getModel('catalog/product')->load($prodIds[$i]);
			      
			      $search_txt="/media/";
			      $start_pos=strpos($_product->getSmallImageUrl($_previewWidth,$_previewHeight),$search_txt);
			      
			      $sub=substr($_product->getSmallImageUrl($_previewWidth,$_previewHeight),($start_pos+7));
			      
			      
			      
			      $sqlLookbook="insert into ".$temptableLookbook." set lookbook_id='',name='".addslashes($_product->getName())."',image='".$sub."',hotspots='[]',position='0',status='1',product_id='".$prodIds[$i]."'";
			      $chkLookbook = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sqlLookbook);
			      
		      }
		  } catch (Exception $e){
		      //echo $e>getMessage();
	      }
      }
      
      $product_id = $this->getRequest()->getParam('id');
      
      $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
	
      $temptableHotspot = Mage::getSingleton('core/resource')->getTableName('lookbook');
      
      $select = $connectionRead->select()
      ->from($temptableHotspot, array('*'))
      ->where('product_id=?',$product_id);
      
      $result = $connectionRead->fetchRow($select);
      
      /********************** Start by dev **************************/
      $form = new Varien_Data_Form();
      

      $this->setForm($form);

      $fieldset = $form->addFieldset('lookbook_form', array('legend'=>Mage::helper('lookbook')->__('Hot Spot information')));
     
      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('lookbook')->__('Name'),
         // 'required'  => true,
          'name'      => 'name',
      ));
 
      $fieldset->addField('position', 'text', array(
          'label'     => Mage::helper('lookbook')->__('Order'),
          'required'  => false,
          'name'      => 'position',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('lookbook')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('lookbook')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('lookbook')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addType('lookbookimage','Altima_Lookbook_Block_Adminhtml_Lookbook_Edit_Form_Element_Lookbookimage');
      $fieldset->addField('image', 'lookbookimage', array(
          'label'     => Mage::helper('lookbook')->__('Image'),
          'name'      => 'image',
          //'required'  => true,       
      ));
      
      $fieldset->addType('hotspots','Altima_Lookbook_Block_Adminhtml_Lookbook_Edit_Form_Element_Hotspots');
      $fieldset->addField('hotspots', 'hotspots', array(
          'name'      => 'hotspots',        
      ));
      
      if ( Mage::getSingleton('adminhtml/session')->getLookbookData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getLookbookData());
          Mage::getSingleton('adminhtml/session')->setLookbookData(null);
      } elseif ( Mage::registry('lookbook_data') ) {
          $form->setValues(Mage::registry('lookbook_data')->getData());
      }
      elseif(!empty($result))
      {
        $form->setValues($result);
      }
      return parent::_prepareForm();
  }
}