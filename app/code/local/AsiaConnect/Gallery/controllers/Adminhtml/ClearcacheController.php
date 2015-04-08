<?php

class AsiaConnect_Gallery_Adminhtml_ClearcacheController extends Mage_Adminhtml_Controller_action
{
    protected function recursiveDelete($str){
        if(is_file($str)){
            return @unlink($str);
        }
        elseif(is_dir($str)){
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path){
                $this->recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }
    
	public function indexAction() {
		$this->recursiveDelete("media/gallery/cache");
		
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('gallery')->__('Gallery Pro Images Cache was successfully cleared'));
		Mage::getSingleton('adminhtml/session')->setFormData(false);
		$this->_redirect('adminhtml/cache/index');
	}
}
