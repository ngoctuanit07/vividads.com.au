<?php
/**
*
* @ This file is created by deZender.Net
* @ deZender (PHP5 Decoder for ionCube Encoder)
*
* @	Version			:	1.1.6.0
* @	Author			:	DeZender
* @	Release on		:	02.06.2013
* @	Official site	:	http://DeZender.Net
*
*/

	class Mico_Mupload_Block_Product extends Mage_Catalog_Block_Product_View_Abstract {
		protected $_config = -1;
		protected $_uploader = 0;

		public function getConfig() {
			if ($this->_config === 0 - 1) {
				$this->_config = $this->getUploader(  )->getConfig( $this->getProduct(  ) );
			}

			return $this->_config;
		}

		public function getUploader() {
			if (!$this->_uploader) {
				$this->_uploader = Mage::getmodel( 'mupload/uploader' );
			}

			return $this->_uploader;
		}

		public function formToken() {
			return $this->getUploader(  )->formKey( $this->getProduct(  )->getId(  ) );
		}
	}

?>