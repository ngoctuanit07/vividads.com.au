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

	class Mico_Mupload_Model_System_Config_Multifile {
		const MUPLOAD_MULTIFILE_NONE = 0;
		const MUPLOAD_MULTIFILE_GLOBAL = 1;
		const MUPLOAD_MULTIFILE_OPTION = 2;

		public function toOptionArray() {
			return array( array( 'value' => self::MUPLOAD_MULTIFILE_NONE, 'label' => Mage::helper( 'mupload' )->__( 'No' ) ), array( 'value' => self::MUPLOAD_MULTIFILE_GLOBAL, 'label' => Mage::helper( 'mupload' )->__( 'Yes, All options' ) ), array( 'value' => self::MUPLOAD_MULTIFILE_OPTION, 'label' => Mage::helper( 'mupload' )->__( 'Yes, Enables per custom option' ) ) );
		}
	}

?>