<?php

class Sag_Gallery_Adminhtml_GalleryController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('gallery/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
       // echo $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('gallery/gallery')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
                if (isset($data['category']) && count($data['category']) > 0) {
                    $condition = array($connection->quoteInto('imagecategories_id=?', $id));
                    $connection->delete('image_categories', $condition);
                }
            }


            Mage::register('gallery_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('gallery/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('gallery/adminhtml_gallery_edit'))
                    ->_addLeft($this->getLayout()->createBlock('gallery/adminhtml_gallery_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        $newFileName = "";
        if ($data = $this->getRequest()->getPost()) {

            if (isset($data['imagefilename']) && !empty($data['imagefilename'])) {
                $newFileName = strtolower($data['imagefilename']);
            } else {
                $newFileName = strtolower($data['title']);
            }
            $newFileName = str_replace(" ", "-", $newFileName);

            if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('filename');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(true);

                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS . 'gallery' . DS . 'images' . DS;
                    $uploader->save($path, $newFileName); //str_replace(' ', '_', $_FILES['filename']['name'])
                } catch (Exception $e) {
                    
                }

                //this way the name is saved in DB
                $data['filename'] = 'gallery' . DS . 'images' . DS . $newFileName; //str_replace(' ', '_', $_FILES['filename']['name']);
            } else {
                unset($data['filename']);  // Unset filename part when image upload field is empty       
            }


            $model = Mage::getModel('gallery/gallery');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $blkObj = new Sag_Gallery_Block_Gallery();
                $categoryData = $blkObj->getCategoryByCID($data['category']);
                $category = $categoryData->getData();
                $imageSef = preg_replace(array('/[^a-z0-9-]/i', '/[ ]{2,}/', '/[ ]/'), array(' ', ' ', '-'), strtolower($data['title']));
                $categorySef = preg_replace(array('/[^a-z0-9-]/i', '/[ ]{2,}/', '/[ ]/'), array(' ', ' ', '-'), strtolower($category[0]['title']));

                $model->setUrl($categorySef . "/" . $imageSef);

                if ($model->save()) {
                    $write = Mage::getSingleton('core/resource')->getConnection('core_write');

                    $image_id = $model->getId();
                   
				  /*image id*/ 
				$resource = Mage::getSingleton('core/resource')->getConnection('core_read');
    			$query = 'SELECT * FROM ' . $resource->getTableName('image_categories').' WHERE image_id='.$image_id.'';
    			$results = $resource->fetchAll($query);
				/**
				 * Print out the results
				 */
					if(count($results) ==0){
					
					if (!empty($image_id)) {
                        if (count($data['category']) > 0) {
                            foreach ($data['category'] as $category) {
                                $write->query("insert into image_categories (category_id, image_id, created_time) values('$category', '$image_id', 'NOW()')");
                            }
                        }
                    }
					
					}
                   
				   
				    $rwObj = new Sag_Gallery_Helper_Data();

                    //gallery_category_id
                    $id_path = "gallery_image_" . $image_id;
                    //category-name.html                    
                    $request_path = $categorySef . "/" . $imageSef . ".html";
                    //gallery/category/index/cat/id
                    $target_path = "gallery/image/index/img/" . $image_id;
                    $rwObj->CustomReriteUrl($id_path, $request_path, $target_path);
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('gallery')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('gallery')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('gallery/gallery');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $galleryIds = $this->getRequest()->getParam('gallery');
        if (!is_array($galleryIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($galleryIds as $galleryId) {
                    $gallery = Mage::getModel('gallery/gallery')->load($galleryId);
                    $gallery->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($galleryIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $galleryIds = $this->getRequest()->getParam('gallery');
        if (!is_array($galleryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($galleryIds as $galleryId) {
                    $gallery = Mage::getSingleton('gallery/gallery')
                            ->load($galleryId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($galleryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}