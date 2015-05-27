<?php
class Vividads_Adminsearch_Adminhtml_AdminsearchController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('adminsearch/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('adminsearch/adminsearch')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('adminsearch_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('adminsearch/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('adminsearch/adminhtml_adminsearch_edit'))
				->_addLeft($this->getLayout()->createBlock('adminsearch/adminhtml_adminsearch_edit_tabs'));
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminsearch')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
	  			
	  			
			$model = Mage::getModel('adminsearch/adminsearch');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminsearch')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminsearch')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('adminsearch/adminsearch');
				 
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
        $adminsearchIds = $this->getRequest()->getParam('adminsearch');
        if(!is_array($adminsearchIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($adminsearchIds as $adminsearchId) {
                    $adminsearch = Mage::getModel('adminsearch/adminsearch')->load($adminsearchId);
                    $adminsearch->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($adminsearchIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    public function searchAction(){
                
        $searchModules = Mage::getConfig()->getNode("adminhtml/global_search");
        $items = array();

        if (!Mage::getSingleton('admin/session')->isAllowed('admin/global_search')) {
            $items[] = array(
                'id' => 'error',
                'type' => Mage::helper('adminhtml')->__('Error'),
                'name' => Mage::helper('adminhtml')->__('Access Denied'),
                'description' => Mage::helper('adminhtml')->__('You have not enough permissions to use this functionality.')
            );
            $totalCount = 1;
        } else {
            if (empty($searchModules)) {
                $items[] = array(
                    'id' => 'error',
                    'type' => Mage::helper('adminhtml')->__('Error'),
                    'name' => Mage::helper('adminhtml')->__('No search modules were registered'),
                    'description' => Mage::helper('adminhtml')->__('Please make sure that all global admin search modules are installed and activated.')
                );
                $totalCount = 1;
            } else {
                $start = $this->getRequest()->getParam('start', 1);
                $limit = $this->getRequest()->getParam('limit', 10);
                $query = $this->getRequest()->getParam('query', '');
                $optionArr = $this->getRequest()->getParam('option', '');
		list($option,$attrSearch)=@explode("__",$optionArr);

                if(empty($option)){
                 foreach ($searchModules->children() as $searchConfig) {

                    if ($searchConfig->acl && !Mage::getSingleton('admin/session')->isAllowed($searchConfig->acl)){
                        continue;
                    }

                     $className = $searchConfig->getClassName();
                    $classes[]=$className;
                 }
                }else
                     $classes=array($option);

                 
                 foreach ($classes as $className) {
                    if (empty($className)) {
                        continue;
                    }
			switch($className)
			{
				case 	"Vividads_Adminsearch_Model_Customer":
					$attrName=array("firstname","lastname");
					break;
				case    "Vividads_Adminsearch_Model_Order":
                          //              $attrName=array("firstname","lastname");
                                        break;
                                case    "Vividads_Adminsearch_Model_Catalog":
                           //             $attrName=array("firstname","lastname");
					break;
				default:
                             //           $attrName=array("firstname","lastname");

			}
		if(!empty($attrSearch)) $attrName=array($attrSearch);

		
                    $searchInstance = Mage::getModel($className);
		if(!$searchInstance) exit('not valid class');
//echo get_class($searchInstance);
                    $results = $searchInstance->setStart($start)
                        ->setLimit($limit)
                        ->setQuery($query)
                        ->load($attrName)
                        ->getResults();
                    $items = array_merge_recursive($items, $results);
                }
                $totalCount = sizeof($items);
            }
        }
        $block = $this->getLayout()->createBlock('adminhtml/template')
            ->setTemplate('Adminsearch/autocomplete.phtml')
            ->assign('items', $items);

        $this->getResponse()->setBody($block->toHtml());
    }
    
                    }
