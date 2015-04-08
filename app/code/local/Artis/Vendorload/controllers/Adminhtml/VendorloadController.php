<?php

class Artis_Vendorload_Adminhtml_VendorLoadController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('vendorload/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
//exit('hello');
		$this->_initAction()
			->renderLayout();
	}
	public function addAction(){
		$attribute_id=intval($this->getRequest()->getParam('attribute_id'));
		$load=intval($this->getRequest()->getParam('load'));
		$admin_id=intval($this->getRequest()->getParam('admin_id'));
                $model = Mage::getModel('vendorload/vendorload');
		$model->setLoad($load);
                $model->setAdminId($admin_id);
                $model->setAttributeId($attribute_id);
		$model->save();
		echo "Added";
	} 
    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('vendorload/vendorload')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('vendorload_data', $model);

            $this->loadLayout();


            $this->_addContent($this->getLayout()->createBlock('vendorload/adminhtml_vendorload_edit'))
                ->_addLeft($this->getLayout()->createBlock('vendorload/adminhtml_vendorload_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorload')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
 
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('vendorload/vendorload');       
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vendorload')->__('Facotry Load was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vendorload')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }
    
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('vendorload/vendorload');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				//$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $vendorloadIds = $this->getRequest()->getParam('vendorload');
        if(!is_array($vendorloadIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($vendorloadIds as $vendorloadId) {
                    $vendorload = Mage::getModel('vendorload/vendorload')->load($vendorloadId);
                    $vendorload->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($vendorloadIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	

}
