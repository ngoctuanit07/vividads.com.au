<?php
/**
 * Faqs extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php

 * @category   FME
 * @package    Faqs
 * @author     Kamran Rafiq Malik <kamran.malik@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Faqs_Adminhtml_FaqsController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('faqs/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Faqs'), Mage::helper('adminhtml')->__('Manage Faqs'));
		$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('faqs/faqs')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('faqs_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('faqs/items');
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Faqs'), Mage::helper('adminhtml')->__('Manage Faqs'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			
			$this->_addContent($this->getLayout()->createBlock('faqs/adminhtml_faqs_edit'))
				->_addLeft($this->getLayout()->createBlock('faqs/adminhtml_faqs_edit_tabs'));
				
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faqs')->__('Faq does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		$session = Mage::getSingleton('adminhtml/session');
		if ($data = $this->getRequest()->getPost()) {
		
			$data['product_ids']=$data['prid'];
			$data['reply'] = 1;
			
	  		//Set the extra fields
			$data['origin'] = "General Question";
	  			
			$model = Mage::getModel('faqs/faqs');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$id  = $this->getRequest()->getParam('id');
	 			if($id !=0) {
					//Check to see if already sent reply on that Question or not
					$Faq = Mage::getModel('faqs/faqs')->getCollection()->getFaqData($id);  	
					$Faqrow = $Faq->getData();
					
					if($Faqrow[0]["reply"] == '0' && $Faqrow[0]["contact_email"] != ""){
						$storeId = 0;	
						$store = Mage::app()->getStore($storeId);
						$mailTemplate = Mage::getModel('core/email_template')
						->setDesignConfig(array('area' => 'frontend', 'store' => $store))
						->setReplyTo(Mage::getStoreConfig(FME_Faqs_Model_Source_Config_Path::EMAIL_SENDER, $storeId))
						->sendTransactional(
							Mage::getStoreConfig(FME_Faqs_Model_Source_Config_Path::EMAIL_CUSTOMER_TEMPLATE, $storeId),
							Mage::getStoreConfig(FME_Faqs_Model_Source_Config_Path::EMAIL_SENDER, $storeId),
							$Faqrow[0]["contact_email"],
							null,
							array(
								'data'          => $model,
								'question_text' => $model["title"],
								'reply_text'    => $model["faq_answar"],
								'customer_name' => $Faqrow[0]["contact_name"],
								'customer_email'=> $Faqrow[0]["contact_email"],
								'date_asked'    => Mage::app()->getLocale()->date($Faqrow[0]["created_time"],
													Varien_Date::DATETIME_INTERNAL_FORMAT)->toString(
													Mage::app()->getLocale()->getDateTimeFormat(
													Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM)),
								'product_name'  => $Faqrow[0]["product_name"],
							),
							$storeId
						);
						if (!$mailTemplate->getSentSuccess())
							throw new Exception('Message was successfull saved, but the email was not sent');
						else
							$session->addSuccess($this->__('Email was sent successfully'));
					}
				}
				
				
				$model->save();
				
				

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('faqs')->__('Faq was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faqs')->__('Unable to find Faq to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('faqs/faqs');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Faq was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $faqsIds = $this->getRequest()->getParam('faqs');
        if(!is_array($faqsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Faq(s)'));
        } else {
            try {
                foreach ($faqsIds as $faqsId) {
                    $faqs = Mage::getModel('faqs/faqs')->load($faqsId);
                    $faqs->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($faqsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $faqsIds = $this->getRequest()->getParam('faqs');
        if(!is_array($faqsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Faq(s)'));
        } else {
            try {
                foreach ($faqsIds as $faqsId) {
                    $faqs = Mage::getSingleton('faqs/faqs')
                        ->load($faqsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($faqsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'faqs.csv';
        $content    = $this->getLayout()->createBlock('faqs/adminhtml_faqs_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'faqs.xml';
        $content    = $this->getLayout()->createBlock('faqs/adminhtml_faqs_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    
    public function toOptionArray($isMultiselect)
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('core/language_collection')->loadData()->toOptionArray();
        }
        $options = $this->_options;
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=>''));
        }
        return $options;
    }
    
    /**
     * Get faqs products grid and serializer block
     */
    public function productAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Get faqs products grid
     */
    public function productGridAction()
    {
	echo 'Function ===> productgridaction';
        $this->_initProduct();
        $this->loadLayout();
		$data=$this->getRequest()->getPost();
        $this->renderLayout();
    }
     
    public function gridAction()
	{
		 
	    $this->getResponse()->setBody(
            $this->getLayout()->createBlock('faqs/adminhtml_faqs_edit_tab_product')->toHtml()
        );
	
	}

    /**
     * Get specified tab grid
     */
    public function gridOnlyAction()
    {
        echo 'Function ===> GridOnlyAction';
		$this->_initProduct();
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/faqs_edit_tab_product')
                ->toHtml()
        );
    }
    
}