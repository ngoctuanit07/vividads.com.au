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

	class Mico_Mupload_Model_System_Config_Engine {
		const MUPLOAD_THUMBNAIL_ENGINE_NONE = 0;
		const MUPLOAD_THUMBNAIL_ENGINE_MAGENTO = 1;
		const MUPLOAD_THUMBNAIL_ENGINE_MICO = 2;
		const MUPLOAD_THUMBNAIL_ENGINE_MICO_PHP = 3;

		public function toOptionArray() {
			return array( array( 'value' => self::MUPLOAD_THUMBNAIL_ENGINE_NONE, 'label' => Mage::helper( 'mupload' )->__( 'No' ) ), array( 'value' => self::MUPLOAD_THUMBNAIL_ENGINE_MAGENTO, 'label' => Mage::helper( 'mupload' )->__( 'Magento Core' ) ), array( 'value' => self::MUPLOAD_THUMBNAIL_ENGINE_MICO, 'label' => Mage::helper( 'mupload' )->__( 'ImageMagick' ) ), array( 'value' => self::MUPLOAD_THUMBNAIL_ENGINE_MICO_PHP, 'label' => Mage::helper( 'mupload' )->__( 'Imagick (php)' ) ) );
		}
	}

?>