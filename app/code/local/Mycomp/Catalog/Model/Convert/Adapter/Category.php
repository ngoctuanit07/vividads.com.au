<?php

class Mycomp_Catalog_Model_Convert_Adapter_Category
    extends Mage_Eav_Model_Convert_Adapter_Entity
{
    protected $_categoryCache = array();

    protected $_stores;

    /**
     * Category display modes
     */
    protected $_displayModes = array( 'PRODUCTS', 'PAGE', 'PRODUCTS_AND_PAGE');

    public function parse()
    {
        $batchModel = Mage::getSingleton('dataflow/batch');
        /* @var $batchModel Mage_Dataflow_Model_Batch */

        $batchImportModel = $batchModel->getBatchImportModel();
        $importIds = $batchImportModel->getIdCollection();

        foreach ($importIds as $importId) {
            //print '<pre>'.memory_get_usage().'</pre>';
            $batchImportModel->load($importId);
            $importData = $batchImportModel->getBatchData();

            $this->saveRow($importData);
        }
    }

    /**
     * Save category (import)
     *
     * @param array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData)
    {
        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skip import row, required field "%s" not defined', 'store');
                Mage::throwException($message);
            }
        } else {
            //$store = $this->getStoreByCode($importData['store']);
            $store = $this->getStoreByCode('expo_en');
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skip import row, store "%s" field not exists', $importData['store']);
            Mage::throwException($message);
        }

        $rootId = $store->getRootCategoryId();
        if (!$rootId) {
            return array();
        }
        $rootPath = '1/'.$rootId;
        if (empty($this->_categoryCache[$store->getId()])) {
            $collection = Mage::getModel('catalog/category')->getCollection()
                ->setStore($store)
                ->addAttributeToSelect('name');
            $collection->getSelect()->where("path like '".$rootPath."/%'");

            foreach ($collection as $cat) {
                $pathArr = explode('/', $cat->getPath());
                $namePath = '';
                for ($i=2, $l=sizeof($pathArr); $i<$l; $i++) {
                    $name = $collection->getItemById($pathArr[$i])->getName();
                    $namePath .= (empty($namePath) ? '' : '/').trim($name);
                }
                $cat->setNamePath($namePath);
            }

            $cache = array();
            foreach ($collection as $cat) {
                $cache[strtolower($cat->getNamePath())] = $cat;
                $cat->unsNamePath();
            }
            $this->_categoryCache[$store->getId()] = $cache;
        }
        $cache =& $this->_categoryCache[$store->getId()];

        $importData['categories'] = preg_replace('#\s*/\s*#', '/', trim($importData['categories']));
        if (!empty($cache[$importData['categories']])) {
            return true;
        }

        $path = $rootPath;
        $namePath = '';

        $i = 1;
        $categories = explode('/', $importData['categories']);
        foreach ($categories as $catName) {
            $namePath .= (empty($namePath) ? '' : '/').strtolower($catName);
            if (empty($cache[$namePath])) {

                $dispMode = $this->_displayModes[2];
                
                /************************** Start by dev *******************************/
            if(strpos(basename($importData['thumbnail']),'.'))
            {
                $mediaPath=Mage::getBaseDir();
                $path2 = $mediaPath.'/media/catalog/category/';
                
                
                $path3 = $path2.basename($importData['thumbnail']);
                //$ch = curl_init($importData['thumbnail']);
                //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //echo $data = curl_exec($ch);exit;
                //curl_close($ch);
                
                
                $ch = curl_init();
                
                curl_setopt($ch, CURLOPT_POST, 0);
                
                curl_setopt($ch,CURLOPT_URL,$importData['thumbnail']);
                
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                
                $data=curl_exec($ch);
                
                curl_close($ch);
                
                file_put_contents($path3, $data);
                chmod($path3,0777);
            }
            
            if(strpos(basename($importData['image']),'.'))
            {
                $mediaPath=Mage::getBaseDir();
                $path2 = $mediaPath.'/media/catalog/category/';
                
                
                $path4 = $path2.basename($importData['image']);
                //$ch = curl_init($importData['thumbnail']);
                //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //echo $data = curl_exec($ch);exit;
                //curl_close($ch);
                
                
                $ch = curl_init();
                
                curl_setopt($ch, CURLOPT_POST, 0);
                
                curl_setopt($ch,CURLOPT_URL,$importData['image']);
                
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                
                $data=curl_exec($ch);
                
                curl_close($ch);
                
                file_put_contents($path4, $data);
                chmod($path4,0777);
            }
            
            //$cat->addImageToMediaGallery($path, $mediaAttribute, true, false);
            //$cat->save();
           // $parentCategory = Mage::getModel('catalog/category')->load($catId);
            //$parentCategory->addImageToMediaGallery($path, array ('thumbnail'), true, false);
            //$parentCategory->setThumbnail($path);
            //$parentCategory->save();
            
            /************************** End by dev *******************************/

                $cat = new Mage_Catalog_Model_Category();
                
                    $cat->setStoreId($store->getId())
                    ->setPath($path)
                    ->setName($catName)
                    ->setIsActive(1)
                    ->setIsAnchor(1)
                    ->setDisplayMode($dispMode)
                    ->setThumbnail(basename($path3))
                    ->setImage(basename($path4))
                    ->save();
                    
                $cache[$namePath] = $cat;
            }
            
            
            
            $catId = $cache[$namePath]->getId();
                       
            
           
            $path .= '/'.$catId;
            $i++;
        }

        return true;
    }

    /**
     * Retrieve store object by code
     *
     * @param string $store
     * @return Mage_Core_Model_Store
     */
    public function getStoreByCode($store)
    {
        $this->_initStores();
        if (isset($this->_stores[$store])) {
            return $this->_stores[$store];
        }
        return false;
    }

    /**
     *  Init stores
     *
     *  @param    none
     *  @return      void
     */
    protected function _initStores ()
    {
        if (is_null($this->_stores)) {
            $this->_stores = Mage::app()->getStores(true, true);
            foreach ($this->_stores as $code => $store) {
                $this->_storesIdCode[$store->getId()] = $code;
            }
        }
    }
}