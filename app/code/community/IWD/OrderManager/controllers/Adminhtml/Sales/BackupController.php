<?php
class IWD_OrderManager_Adminhtml_Sales_BackupController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->salesAction();
    }


    public function commentsAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_title($this->__('IWD Order Manager - Backups'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Backup - Comments'),
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Backup - Comments')
        );

        $this->_addContent($this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_backup_comments'));
        $this->renderLayout();
    }

    public function salesAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_title($this->__('IWD Order Manager - Backups'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Backup - Sales'),
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Backup - Sales')
        );

        $this->_addContent($this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_backup_sales'));
        $this->renderLayout();
    }


    public function commentsMassDeleteAction()
    {
        $backupIds = $this->getRequest()->getParam('backup');
        if (!is_array($backupIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($backupIds as $id) {
                    $comment = Mage::getModel('iwd_ordermanager/backup_comments')->load($id);
                    $comment->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')
                    ->__('Total of %d record(s) were successfully deleted', count($backupIds)));
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function salesMassDeleteAction()
    {
        $backupIds = $this->getRequest()->getParam('backup');
        if (!is_array($backupIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($backupIds as $id) {
                    $model = Mage::getModel('iwd_ordermanager/backup_sales')->load($id);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')
                    ->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($backupIds)));
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }



    public function commentsGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_backup_comments_grid')->toHtml()
        );
    }

    public function salesGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_backup_sales_grid')->toHtml()
        );
    }

}