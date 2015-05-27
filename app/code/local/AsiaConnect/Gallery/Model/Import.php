<?php
class AsiaConnect_Gallery_Model_Import extends Mage_ImportExport_Model_Import
{
    /**
     * Key in config with entities.
     */
    const CONFIG_KEY_ENTITIES  = 'global/importexport/import_entities';

    /**
     * Import behavior.
     */
    const BEHAVIOR_APPEND  = 'append';
    const BEHAVIOR_REPLACE = 'replace';
    const BEHAVIOR_DELETE  = 'delete';

    /**
     * Form field names (and IDs)
     */
    const FIELD_NAME_SOURCE_FILE = 'import_file';
    const FIELD_NAME_IMG_ARCHIVE_FILE = 'import_image_archive';

    /**
     * Import constants
     *
     */
    const DEFAULT_SIZE      = 50;
    const MAX_IMPORT_CHUNKS = 4;

    /**
     * Entity adapter.
     *
     * @var Mage_ImportExport_Model_Import_Entity_Abstract
     */
    protected $_entityAdapter;

    /**
     * Entity invalidated indexes.
     *
     * @var Mage_ImportExport_Model_Import_Entity_Abstract
     */
     protected static $_entityInvalidatedIndexes = array (
        'catalog_product' => array (
            'catalog_product_price',
            'catalog_category_product',
            'catalogsearch_fulltext'
        )
    );

    protected function _getEntityAdapter()
    {
        if (!$this->_entityAdapter) {
            $validTypes = Mage_ImportExport_Model_Config::getModels(self::CONFIG_KEY_ENTITIES);

            if (isset($validTypes[$this->getEntity()])) {
                try {
                    $this->_entityAdapter = Mage::getModel($validTypes[$this->getEntity()]['model']);
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::throwException(
                        Mage::helper('importexport')->__('Invalid entity model')
                    );
                }
                if (!($this->_entityAdapter instanceof Mage_ImportExport_Model_Import_Entity_Abstract)) {
                    Mage::throwException(
                        Mage::helper('importexport')->__('Entity adapter object must be an instance of Mage_ImportExport_Model_Import_Entity_Abstract')
                    );
                }
            } else {
                Mage::throwException(Mage::helper('importexport')->__('Invalid entity'));
            }
            // check for entity codes integrity
            if ($this->getEntity() != $this->_entityAdapter->getEntityTypeCode()) {
                Mage::throwException(
                    Mage::helper('importexport')->__('Input entity code is not equal to entity adapter code')
                );
            }
            $this->_entityAdapter->setParameters($this->getData());
        }
        return $this->_entityAdapter;
    }

    protected function _getSourceAdapter($sourceFile)
    {
        return Mage_ImportExport_Model_Import_Adapter::findAdapterFor($sourceFile);
    }

    public function getOperationResultMessages($validationResult)
    {
        $messages = array();
        if ($this->getProcessedRowsCount()) {
            if (!$validationResult) {
                if ($this->getProcessedRowsCount() == $this->getInvalidRowsCount()) {
                    $messages[] = Mage::helper('importexport')->__('File is totally invalid. Please fix errors and re-upload file');
                } elseif ($this->getErrorsCount() >= $this->getErrorsLimit()) {
                    $messages[] = Mage::helper('importexport')->__('Errors limit (%d) reached. Please fix errors and re-upload file',
                        $this->getErrorsLimit()
                    );
                } else {
                    if ($this->isImportAllowed()) {
                        $messages[] = Mage::helper('importexport')->__('Please fix errors and re-upload file');
                    } else {
                        $messages[] = Mage::helper('importexport')->__('File is partially valid, but import is not possible');
                    }
                }
                // errors info
                foreach ($this->getErrors() as $errorCode => $rows) {
                    $error = $errorCode . ' '
                        . Mage::helper('importexport')->__('in rows') . ': '
                        . implode(', ', $rows);
                    $messages[] = $error;
                }
            } else {
                if ($this->isImportAllowed()) {
                    $messages[] = Mage::helper('importexport')->__('Validation finished successfully');
                } else {
                    $messages[] = Mage::helper('importexport')->__('File is valid, but import is not possible');
                }
            }
            $notices = $this->getNotices();
            if (is_array($notices)) {
                $messages = array_merge($messages, $notices);
            }
            $messages[] = Mage::helper('importexport')->__('Checked rows: %d, checked entities: %d, invalid rows: %d, total errors: %d',
                $this->getProcessedRowsCount(), $this->getProcessedEntitiesCount(),
                $this->getInvalidRowsCount(), $this->getErrorsCount()
            );
        } else {
            $messages[] = Mage::helper('importexport')->__('File does not contain data.');
        }
        return $messages;
    }
    public static function getAttributeType(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if ($attribute->usesSource()) {
            return $attribute->getFrontendInput() == 'multiselect' ?
                'multiselect' : 'select';
        } elseif ($attribute->isStatic()) {
            return $attribute->getFrontendInput() == 'date' ? 'datetime' : 'varchar';
        } else {
            return $attribute->getBackendType();
        }
    }

    public static function getDataSourceModel()
    {
        return Mage::getResourceSingleton('importexport/import_data');
    }

    public static function getDefaultBehavior()
    {
        return self::BEHAVIOR_APPEND;
    }

    public function getEntity()
    {
        if (empty($this->_data['entity'])) {
            Mage::throwException(Mage::helper('importexport')->__('Entity is unknown'));
        }
        return $this->_data['entity'];
    }

    public function getErrors()
    {
        return $this->_getEntityAdapter()->getErrorMessages();
    }

    public function getErrorsCount()
    {
        return $this->_getEntityAdapter()->getErrorsCount();
    }

    public function getErrorsLimit()
    {
        return $this->_getEntityAdapter()->getErrorsLimit();
    }

    public function getInvalidRowsCount()
    {
        return $this->_getEntityAdapter()->getInvalidRowsCount();
    }

    public function getNotices()
    {
        return $this->_getEntityAdapter()->getNotices();
    }

    public function getProcessedEntitiesCount()
    {
        return $this->_getEntityAdapter()->getProcessedEntitiesCount();
    }

    public function getProcessedRowsCount()
    {
        return $this->_getEntityAdapter()->getProcessedRowsCount();
    }

    public function importSource()
    {
        $this->setData(array(
            'entity'   => self::getDataSourceModel()->getEntityTypeCode(),
            'behavior' => self::getDataSourceModel()->getBehavior()
        ));
        $this->addLogComment(Mage::helper('importexport')->__('Begin import of "%s" with "%s" behavior', $this->getEntity(), $this->getBehavior()));
        $result = $this->_getEntityAdapter()->importData();
        $this->addLogComment(array(
            Mage::helper('importexport')->__('Checked rows: %d, checked entities: %d, invalid rows: %d, total errors: %d',
                $this->getProcessedRowsCount(), $this->getProcessedEntitiesCount(),
                $this->getInvalidRowsCount(), $this->getErrorsCount()
            ),
            Mage::helper('importexport')->__('Import has been done successfuly.')
        ));
        return $result;
    }

    public function isImportAllowed()
    {
        return $this->_getEntityAdapter()->isImportAllowed();
    }

    public function expandSource()
    {
        $writer  = Mage::getModel('importexport/export_adapter_csv', self::getWorkingDir() . "big0.csv");
        $regExps = array('last' => '/(.*?)(\d+)$/', 'middle' => '/(.*?)(\d+)(.*)$/');
        $colReg  = array(
            'sku' => 'last', 'name' => 'last', 'description' => 'last', 'short_description' => 'last',
            'url_key' => 'middle', 'meta_title' => 'last', 'meta_keyword' => 'last', 'meta_description' => 'last',
            '_links_related_sku' => 'last', '_links_crosssell_sku' => 'last', '_links_upsell_sku' => 'last',
            '_custom_option_sku' => 'middle', '_custom_option_row_sku' => 'middle', '_super_products_sku' => 'last',
            '_associated_sku' => 'last'
        );
        $size = self::DEFAULT_SIZE;

        $filename = 'catalog_product.csv';
        $filenameFormat = 'big%s.csv';
        foreach ($this->_getSourceAdapter(self::getWorkingDir() . $filename) as $row) {
            $writer->writeRow($row);
        }
        $count = self::MAX_IMPORT_CHUNKS;
        for ($i = 1; $i < $count; $i++) {
            $writer = Mage::getModel(
                'importexport/export_adapter_csv',
                self::getWorkingDir() . sprintf($filenameFormat, $i)
            );

            $adapter = $this->_getSourceAdapter(self::getWorkingDir() . sprintf($filenameFormat, $i - 1));
            foreach ($adapter as $row) {
                $writer->writeRow($row);
            }
            $adapter = $this->_getSourceAdapter(self::getWorkingDir() . sprintf($filenameFormat, $i - 1));
            foreach ($adapter as $row) {
                foreach ($colReg as $colName => $regExpType) {
                    if (!empty($row[$colName])) {
                        preg_match($regExps[$regExpType], $row[$colName], $m);

                        $row[$colName] = $m[1] . ($m[2] + $size) . ('middle' == $regExpType ? $m[3] : '');
                    }
                }
                $writer->writeRow($row);
            }
            $size *= 2;
        }
    }
    public function uploadSource()
    {
        //$entity    = $this->getEntity();
        $uploader  = Mage::getModel('core/file_uploader', self::FIELD_NAME_IMG_ARCHIVE_FILE);
        $uploader->setAllowedExtensions(array('rar','zip'));
		$uploader->setAllowRenameFiles(false);
        //$uploader->skipDbProcessing(true);
        $result    = $uploader->save(self::getWorkingDir(),$this->_data['title']);
        $extension = pathinfo($result['file'], PATHINFO_EXTENSION);

        $uploadedFile = $result['path'] . $result['file'];
        if (!$extension) {
            unlink($uploadedFile);
            Mage::throwException(Mage::helper('importexport')->__('Uploaded file has no extension'));
        }
        return $result;
    }
    public function validateSource($sourceFile)
    {
        $this->addLogComment(Mage::helper('importexport')->__('Begin data validation'));
        $result = $this->_getEntityAdapter()
            ->setSource($this->_getSourceAdapter($sourceFile))
            ->isDataValid();

        $messages = $this->getOperationResultMessages($result);
        $this->addLogComment($messages);
        if ($result) {
            $this->addLogComment(Mage::helper('importexport')->__('Done import data validation'));
        }
        return $result;
    }

    public function invalidateIndex()
    {
        if (!isset(self::$_entityInvalidatedIndexes[$this->getEntity()])) {
            return $this;
        }

        $indexers = self::$_entityInvalidatedIndexes[$this->getEntity()];
        foreach ($indexers as $indexer) {
            $indexProcess = Mage::getSingleton('index/indexer')->getProcessByCode($indexer);
            if ($indexProcess) {
                $indexProcess->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
            }
        }

        return $this;
    }
}

