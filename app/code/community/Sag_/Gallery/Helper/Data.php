<?php

class Sag_Gallery_Helper_Data extends Mage_Core_Helper_Abstract {

    function CustomReriteUrl($id_path, $request_path, $target_path, $store_id = "0", $is_system = false) {

       // Zend_Debug::dump($id_path);
		
		//exit;
		
		$isrewrite = Mage::getModel('core/url_rewrite')->load($id_path, id_path);
		
		if($isrewrite->url_rewrite_id == ""){
			
		$rewrite = Mage::getModel('core/url_rewrite');
        $rewrite->setStoreId($store_id)
                ->setIdPath($id_path)
                ->setRequestPath($request_path)
                ->setTargetPath($target_path)
                ->setIsSystem($is_system)
                ->save();
		}
	
        
        return;
    }

}

?>