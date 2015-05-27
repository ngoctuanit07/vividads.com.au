<?php
class Extendware_EWCore_Block_Override_Mage_Page_Html_Head extends Extendware_EWCore_Block_Override_Mage_Page_Html_Head_Bridge
{
	private $mediaBaseUrl;
	private $mediaBasePath;
	
	public function __construct() {
		parent::__construct();
		$this->mediaBaseUrl = Mage_Core_Model_Store::URL_TYPE_MEDIA . '/extendware/ewcore/generated';
    	$this->mediaBasePath = 'extendware/ewcore/generated';
	}

	public function getCssJsHtml()
	{
		$items = array(); // this is used to keep the order the same
		if (!isset($this->_data['items']) or !is_array($this->_data['items'])) $this->_data['items'] = array();
		foreach ($this->_data['items'] as $key => $item) {
			switch ($item['type']) {
				case 'ewgenerated_js':
		            $block = $this->getLayout()->createBlock($item['name']);
		            if ($block) {
			            $newItemType = 'js';
			            $newItemName = $this->mediaBaseUrl . '/js/' . $block->getFilename();
			            $newItemKey = $newItemType. '/' . $newItemName;
			            $items[$newItemKey] = $item;
			            $items[$newItemKey]['type'] = $newItemType;
			            $items[$newItemKey]['name'] = $newItemName;
			            $items[$newItemKey]['filepath'] = $block->getCachedFilePath();
			            $items[$newItemKey]['original'] = $item;
		            }
				    break;
			    case 'ewgenerated_css':
		            $block = $this->getLayout()->createBlock($item['name']);
		            if ($block) {
			            $newItemType = 'js_css';
			            $newItemName = $this->mediaBaseUrl . '/css/' . $block->getFilename();
			            $newItemKey = $newItemType. '/' . $newItemName;
			            $items[$newItemKey] = $item;
			            $items[$newItemKey]['type'] = $newItemType;
			            $items[$newItemKey]['name'] = $newItemName;
			            $items[$newItemKey]['filepath'] = $block->getCachedFilePath();
						$items[$newItemKey]['original'] = $item;
						
			            if (!@$items[$newItemKey]['params']) {
				            $items[$newItemKey]['params'] = 'media="all"';
				        }
		            }
				    break;
			    default:
			    	$items[$key] = $item;
			    	break;
			}
			
		}

		$this->_data['items'] = $items;
		return $this->rewriteUrlsInString(parent::getCssJsHtml());
	}
	
	public function mergeJsFiles($files) 
	{
		foreach ($files as &$file) {
			if (strpos($file, $this->mediaBasePath)) {
				$file = Mage::getConfig()->getOptions()->getMediaDir() . DS . 'extendware' . DS . 'ewcore' . DS . 'generated' . DS . preg_replace('/.+?' . preg_quote($this->mediaBasePath, '/') . '/', '', $file);
			}
			unset($file); // cleanup reference
		}
		return Mage::getDesign()->getMergedJsUrl($files);
	}
	
	public function mergeCssFiles($files) 
	{
		foreach ($files as &$file) {
			if (strpos($file, $this->mediaBasePath)) {
				$file = Mage::getConfig()->getOptions()->getMediaDir() . DS . 'extendware' . DS . 'ewcore' . DS . 'generated' . DS . preg_replace('/.+?' . preg_quote($this->mediaBasePath, '/') . '/', '', $file);
			}
			unset($file); // cleanup reference
		}

		return Mage::getDesign()->getMergedCssUrl($files);
	}
	
	protected function &_prepareStaticAndSkinElements($format, array $staticItems, array $skinItems, $mergeCallback = null)
    {
    	if (strpos($format, 'type="text/css"') !== false) {
    		if (Mage::getStoreConfigFlag('dev/css/merge_css_files')) {
    			$mergeCallback = array($this, 'mergeCssFiles');
    		}
    	} elseif (strpos($format, 'type="text/javascript"') !== false) {
    		if (Mage::getStoreConfigFlag('dev/js/merge_files')) {
    			$mergeCallback = array($this, 'mergeJsFiles');
    		}
    	}
    	
    	$html = parent::_prepareStaticAndSkinElements($format, $staticItems, $skinItems, $mergeCallback);
    	$html = $this->rewriteUrlsInString($html);
        return $html;
    }
    
    protected function rewriteUrlsInString($string)
    {
    	return str_replace('js/' . $this->mediaBaseUrl, $this->mediaBaseUrl, $string);
    }
}
