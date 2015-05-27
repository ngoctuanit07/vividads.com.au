<?php

class Mconnect_Brandlogo_Adminhtml_CategoryController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('brandlogo/brandlogo_category')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Category Manager'), Mage::helper('adminhtml')->__('Category Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('brandlogo/brandlogo_category')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('category_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('brandlogo/categories');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Category Manager'), Mage::helper('adminhtml')->__('Category Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Category News'), Mage::helper('adminhtml')->__('Category News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('brandlogo/adminhtml_category_edit'))
                    ->_addLeft($this->getLayout()->createBlock('brandlogo/adminhtml_category_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brandlogo')->__('Category does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        //die('category.');
        $newFileName = "";
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('brandlogo/brandlogo_category');
            $model->setData($data)->setId($this->getRequest()->getParam('id'));

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brandlogo')->__('Category was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brandlogo')->__('Unable to find Category to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('brandlogo/brandlogo_category');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Category was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $brandlogoIds = $this->getRequest()->getParam('category');
        if (!is_array($brandlogoIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Category(s)'));
        } else {
            try {
                foreach ($brandlogoIds as $brandlogoId) {
                    $brandlogo = Mage::getModel('brandlogo/brandlogo_category')->load($brandlogoId);
                    $brandlogo->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($brandlogoIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $brandlogoIds = $this->getRequest()->getParam('category');
        if (!is_array($brandlogoIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Category(s)'));
        } else {
            try {
                foreach ($brandlogoIds as $brandlogoId) {
                    $brandlogo = Mage::getSingleton('brandlogo/brandlogo_category')
                            ->load($brandlogoId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($brandlogoIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}