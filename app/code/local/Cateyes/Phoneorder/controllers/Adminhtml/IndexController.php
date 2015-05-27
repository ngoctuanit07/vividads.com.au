<?php

class Cateyes_Phoneorder_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
	/*
	 * index - wysawietlanie grida 
	 */

	public function indexAction()
	{
		$this->_init();
		$this->renderLayout();
	}


	/**
	 * init
	 */
	private function _init()
	{
		$this->loadLayout()
				->_setActiveMenu("sales/phoneorder_menu")
				->_addBreadcrumb(Mage::helper("phoneorder")->__("Phones Orders"), Mage::helper("phoneorder")->__("Phones Orders")
		);
		
		return $this;
	}

	/**
	 * edycja
	 */
	public function editAction()
	{
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('phoneorder/phoneorder')->load($id);

		if ($model->getId() || $id == 0) 
		{
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			
			if (!empty($data)) 
			{
				$model->setData($data);
			}

			Mage::register('data', $model);

			$this->_init();

			$this->_addBreadcrumb(Mage::helper('phoneorder')->__('Edit'), Mage::helper('phoneorder')->__('Edit'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('phoneorder/adminhtml_orders_edit'));
			$this->_addLeft($this->getLayout()->createBlock("phoneorder/adminhtml_orders_edit_tabs"));
			$this->renderLayout();
		} 
		else 
		{
			Mage::getSingleton('adminhtml/session')
				->addError(Mage::helper('phoneorder')->__('Item does not exist'));
			$this->_redirect('*/*/');
 		}		
	}


	/**
	 * Zapis rekordu
	 */
	public function saveAction()
	{
		$post_data = $this->getRequest()->getPost();

		// zapis danych do bazy
		if (!empty($post_data))
		{
			
			try
			{		
				$data = Mage::getModel("phoneorder/phoneorder")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("phoneorder")->__("Item was successfully saved"));
				Mage::getSingleton("adminhtml/session")->setData(false);

				if ($this->getRequest()->getParam("back"))
				{
					$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
				}

				$this->_redirect("*/*/");
				return;
			}
			catch (Exception $e)
			{
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
				Mage::getSingleton("adminhtml/session")->setData($this->getRequest()->getPost());
				$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
				return;
			}
		}
		$this->_redirect("*/*/");

	}

	/**
	 * Usuwanie rekordu
	 */
	public function deleteAction()
	{
		if ($this->getRequest()->getParam("id") > 0)
		{
			try
			{
				$model = Mage::getModel("phoneorder/phoneorder");
				$model->setId($this->getRequest()->getParam("id"))->delete();
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("phoneorder")->__("Item was successfully deleted"));
				$this->_redirect("*/*/");
			} 
			catch (Exception $e)
			{
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
				$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
			}
		}
		$this->_redirect("*/*/");
	}	
	
}