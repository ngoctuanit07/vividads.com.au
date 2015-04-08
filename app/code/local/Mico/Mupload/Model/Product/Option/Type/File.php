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

	class Mico_Mupload_Model_Product_Option_Type_File extends Mico_Mupload_Model_Product_Option_Type_Filecore {
		protected $_newCurrentConfig = null;

		protected function _validateUploadedFile() {
			$this->_uploader = Mage::getmodel( 'mupload/uploader' );
			$option = $this->getOption(  );
			$optionId = $option->getId(  );
			$customUploadedValues = $this->_customUploadedValues( $optionId );

			if ($customUploadedValues) {
				$this->setIsValid( true );
				$this->setUserValue( $customUploadedValues );
				return $this;
			}

			return parent::_validateuploadedfile(  );
		}

		protected function _customUploadedValues($optionId) {
			$defaultRet = $ajaxField = '' . 'options_' . $optionId . '_file';
			$ajaxData = (isset( $_POST[$ajaxField] ) ? $_POST[$ajaxField] . '' : '');

			if (!$ajaxData) {
				return null;
			}


			if ($ajaxData === '1') {
			} else {
				$_ajaxData = json_decode( $ajaxData, 1 );
			}

			$_ajaxUploadedValues = ($this->_newCurrentConfig ? $this->_newCurrentConfig : array(  ));

			if ($_ajaxData) {
				foreach ($_ajaxData as $key => $item) {
					$value = $this->_getUserValue( $key, $item );

					if ($value) {
						$_ajaxUploadedValues[] = $value;
						continue;
					}
				}
			}


			if ($_ajaxUploadedValues) {
				$len = count( $_ajaxUploadedValues );
				$ret = array(  );
				$i = $len;

				while (0 < $i) {
					$item = $_ajaxUploadedValues[$i - 1];
					$item['next'] = ($ret ? $ret : 0);
					$ret = $item;
					--$i;
				}

				return $ret;
			}

			return null;
		}

		protected function _getUserValue($key, $uploadedItem) {
			$tmpName = $uploadedItem['name'];
			$oName = $uploadedItem['oname'];
			$check = $this->_uploader->formKey( $tmpName );

			if ($check != $key) {
				return null;
			}

			$tmpFile = $this->_uploader->getFileTmp( $tmpName );

			if (!is_file( $tmpFile )) {
				return null;
			}

			$extension = pathinfo( strtolower( $oName ), PATHINFO_EXTENSION );
			$fileName = Varien_File_Uploader::getcorrectfilename( strtolower( $oName ) );
			$dispersion = Varien_File_Uploader::getdispretionpath( $fileName );
			$filePath = $dispersion;
			$fileHash = md5( file_get_contents( $tmpFile ) );
			$hex = sprintf( '%02x%08x', rand( 0, 255 ), time(  ) + rand( 0, 99999999 ) );
			$filePath .= DS . ( '' . 'm' . $hex ) . '.' . $extension;
			$fileFullPath = $this->getQuoteTargetDir(  ) . $filePath;
			$_width = 0;
			$_height = 0;
			$_imageSize = getimagesize( $tmpFile );

			if ($_imageSize) {
				$_width = $_imageSize[0];
				$_height = $_imageSize[1];
			}

			$size = filesize( $tmpFile );
			$toDir = dirname( $fileFullPath );

			if (!is_dir( $toDir )) {
				mkdir( $toDir, 493, 1 );
			}


			if (!rename( $tmpFile, $fileFullPath )) {
				$this->setIsValid( false );
				Mage::throwexception( Mage::helper( 'mupload' )->__( 'Can\'t move file to quote folder.' ) );
				return null;
			}

			$oldThumbnailFullPath = $this->_uploader->getThumbnailFullpath( $tmpFile );

			if (is_file( $oldThumbnailFullPath )) {
				$thumbnailFullPath = $this->_uploader->getThumbnailFullpath( $fileFullPath );
				$path = dirname( $thumbnailFullPath );

				if (!is_dir( $path )) {
					mkdir( $path, 493, 1 );
				}

				rename( $oldThumbnailFullPath, $thumbnailFullPath );
			}

			Mage::dispatchevent( 'mupload_add_to_cart', array( 'fullpath' => $fileFullPath, 'tmp_file' => $tmpFile, 'uploaded_item' => $uploadedItem ) );
			return array( 'type' => 'application/octet-stream', 'title' => $oName, 'optiontitle' => $this->getOption(  )->getTitle(  ), 'quote_path' => $this->getQuoteTargetDir( true ) . $filePath, 'order_path' => $this->getOrderTargetDir( true ) . $filePath, 'fullpath' => $fileFullPath, 'size' => $size, 'width' => $_width, 'height' => $_height, 'ultimate' => 1, 'secret_key' => substr( $fileHash, 0, 20 ) );
		}

		public function _getOptionHtml($optionValue) {
			$this->_uploader = Mage::getmodel( 'mupload/uploader' );
			$value = $this->_unserializeValue( $optionValue );
			$this->_isMulti = 0;

			if (!isset( $value['next'] )) {
				return $this->_customOptionHtml( $value );
			}

			$next = $value;
			$ret = '';
			$this->_isMulti = 1;

			while ($next) {
				$ret .= '<div class="mico-mupload-item-option">' . $this->_customOptionHtml( $next ) . '</div>';
				$next = (isset( $next['next'] ) ? $next['next'] : null);
			}

			$this->_isMulti = 0;
			return $ret;
		}

		protected function _customOptionHtml($value) {
			if (!$value['fullpath']) {
				return '';
			}

			try {
				$resolution = 0;

				if (( ( ( ( isset( $value ) && isset( $value['width'] ) ) && isset( $value['height'] ) ) && 0 < $value['width'] ) && 0 < $value['height'] )) {
					$sizes = $value['width'] . ' x ' . $value['height'] . ' ' . Mage::helper( 'catalog' )->__( 'px.' );
					$resolution = ceil( $value['width'] * $value['height'] / 100000 ) / 10;
				} else {
					$sizes = '';
				}

				$fileFullPath = $value['fullpath'];
				$optionLink = '';

				if (( !isset( $value['url'] ) || !$value['url'] )) {
					$optionLink = $this->_uploader->httpUrl( $fileFullPath, 1 );
				} else {
					$optionLink = $this->_uploader->httpUrl( $fileFullPath, 1 );
				}

				$title = (!empty( $value['title'] ) ? $value['title'] : '');
				$optionImg = '';
				$thumbnailLink = '';
				$thumbnailFullPath = $this->_uploader->getThumbnailFullpath( $fileFullPath );

				if (is_file( $thumbnailFullPath )) {
					$thumbnailLink = $this->_uploader->httpUrl( $thumbnailFullPath, 1 );
				}


				if ($thumbnailLink) {
					$optionImg = '' . '<img src="' . $thumbnailLink . '?cart=1" class="mico-mupload-thumbnail"/><br/>';
				}


				if (( ( !$sizes && isset( $value['size'] ) ) && $value['size'] )) {
					$sizes = $this->_uploader->_formatSize( $value['size'] );
				}

				$mapping['sizePX'] = $sizes;
				$mapping['quality'] = $resolution;
				$mapping['qualityType'] = $this->_uploader->resolutionToQualityType( $resolution );
				$mapping['img'] = $optionImg;
				$mapping['link'] = $optionLink;
				$mapping['title'] = Mage::helper( 'core' )->htmlEscape( $title );
				$templateCart = trim( $this->_uploader->getModuleSetting( 'resolution/templateCart' ) );

				if (!$templateCart) {
					$templateCart = '{img}<a href="{link}" target="_blank">{title}</a> {sizePX}';
				}

				$ret = $templateCart;
				foreach ($mapping as $key => $val) {
					$ret = str_replace( '{' . $key . '}', $val, $ret );
				}

				return $ret;
			} catch (Exception $e) {
				Mage::throwexception( Mage::helper( 'catalog' )->__( 'File options format is not valid.' ) );
			}
		}

		protected function _getCurrentConfigFileInfo() {
			$this->_newCurrentConfig = null;
			$ret = parent::_getcurrentconfigfileinfo(  );

			if (!$ret) {
				$this->_setNewCurrentConfigFileInfo(  );
			}

			return $ret;
		}

		protected function _setNewCurrentConfigFileInfo() {
			$this->_newCurrentConfig = null;
			$option = $this->getOption(  );
			$optionId = $option->getId(  );
			$mico_custom_file_uploaded_name = 'mico_custom_file_uploaded';
			$micoUploadedSecretKeys = (( isset( $_REQUEST[$mico_custom_file_uploaded_name] ) && isset( $_REQUEST[$mico_custom_file_uploaded_name][$optionId] ) ) ? $_REQUEST[$mico_custom_file_uploaded_name][$optionId] : array(  ));

			if (!$micoUploadedSecretKeys) {
				return 0;
			}

			$processingParams = $this->_getProcessingParams(  );
			$buyRequest = $this->getRequest(  );
			$optionActionKey = 'options_' . $optionId . '_file_action';
			$fileInfo = array(  );
			$currentConfig = $processingParams->getCurrentConfig(  );

			if ($currentConfig) {
				$fileInfo = $currentConfig->getData( 'options/' . $optionId );
			}


			if (!$fileInfo) {
				return 0;
			}

			$retInfo = array(  );
			$next = $fileInfo;

			while ($next) {
				$obj = $next;
				$next = (( isset( $next['next'] ) && $next['next'] ) ? $next['next'] : 0);
				$obj['next'] = 0;
				$secretKey = (isset( $obj['secret_key'] ) ? $obj['secret_key'] : '');

				if (( $secretKey && isset( $micoUploadedSecretKeys[$secretKey] ) )) {
					$retInfo[] = $obj;
					continue;
				}
			}

			$this->_newCurrentConfig = $retInfo;
			return 1;
		}
	}

?>