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

	class Mico_Mupload_UploaderController extends Mage_Core_Controller_Front_Action {
		public function responseJson($data) {
			$ret = $data;
			$ret['jsonrpc'] = '2.0';
			$ret['id'] = 'id';
			print json_encode( $ret );
			exit(  );
		}

		public function responeError($message, $code = 205) {
			$ret = array( 'error' => array( 'code' => $code, 'message' => Mage::helper( 'mupload' )->__( $message ) ) );
			return $this->responseJson( $ret );
		}

		public function saveAction() {
			$this->_uploader = Mage::getmodel( 'mupload/uploader' );
			$this->_request = $this->getRequest(  );
			$productId = $this->_request->getParam( 'productId' );
			$key = $this->_request->getParam( 'key' );
			$okey = $this->_request->getParam( 'okey' );
			$validate = $this->_uploader->formKey( $productId );
			$optionId = $this->_request->getParam( 'optionId' );
			$sizeMin = $this->_request->getParam( 'sizeMin' );
			$sizeMax = $this->_request->getParam( 'sizeMax' );
			if ($okey != $validate) {
				return $this->responeError( 'Invalid data' );
			}

			$_fileName = (isset( $_REQUEST['name'] ) ? trim( $_REQUEST['name'] ) : '');

			if (!$_fileName) {
				return $this->responeError( 'Invalid filename' );
			}

			$ext = strtolower( pathinfo( $_fileName, PATHINFO_EXTENSION ) );
			$validateExt = $this->_isSecureFileExt( $ext );

			if (!$validateExt) {
				return $this->responeError( 'Invalid file extension' );
			}

			$fileExt = trim( $this->_uploader->getModuleSetting( 'folder/fileExt' ) );

			if (( $fileExt && strpos( $fileExt, $ext ) === false )) {
				return $this->responeError( 'Invalid file extension' );
			}

			$destPath = $this->_uploader->getFolderTmp(  );
			$destFileName = '';

			if (!is_dir( $destPath )) {
				mkdir( $destPath, 493, 1 );
			}
			$uploadedResult = $this->_uploader->saveFile( $destPath, $destFileName );

			if (( isset( $uploadedResult['error'] ) && $uploadedResult['error'] )) {
				return $this->responseJson( $uploadedResult );
			}

			$uploadedResult['value'] = pathinfo( $uploadedResult['file'], PATHINFO_BASENAME );
			$uploadedResult['hash'] = $this->_uploader->formKey( $uploadedResult['value'] );

			if (( isset( $uploadedResult['finishChunk'] ) && $uploadedResult['finishChunk'] )) {
				$uploadedSize = filesize( $uploadedResult['file'] );
				$errMsg = '';

				if (( $sizeMax && $sizeMax < $uploadedSize )) {
					$errMsg = Mage::helper( 'mupload' )->__( 'File Max Size {fileMax}' );
					$errMsg = str_replace( '{fileMax}', $this->_uploader->_formatSize( $sizeMax ), $errMsg );
				} else {
					if (( $sizeMin && $uploadedSize < $sizeMin )) {
						$errMsg = Mage::helper( 'mupload' )->__( 'File Min Size {fileMin}' );
						$errMsg = str_replace( '{fileMin}', $this->_uploader->_formatSize( $sizeMin ), $errMsg );
					}
				}


				if ($errMsg) {
					unlink( $uploadedResult['file'] );
					return $this->responeError( $errMsg );
				}

				$thumbnail = $this->_uploader->saveThumbnail( $uploadedResult['file'] );
				$uploadedResult['thumbnail'] = ($thumbnail ? $thumbnail['name'] : '');
				$_imageSize = getimagesize( $uploadedResult['file'] );

				if ($_imageSize) {
					$uploadedResult['imageWidth'] = $_imageSize[0] * 1;
					$uploadedResult['imageHeight'] = $_imageSize[1] * 1;
				} else {
					$uploadedResult['imageWidth'] = 0;
					$uploadedResult['imageHeight'] = 0;
				}
			} else {
				$uploadedResult['thumbnail'] = '';
			}

			$uploadedResult['resolution'] = $this->_uploader->fileToQuality( $uploadedResult['file'] );
			unset( $uploadedResult[file] );
			return $this->responseJson( $uploadedResult );
		}

		public function _isSecureFileExt($ext) {
			$backList = '*.php;*.php3;*.php4;*.phtml;*.py;*.pl;*.sh;*.dll;*.asp;*.shtml;*.htm;*.cgi;*.html;*.css;*.js;*.exe;*.com;*.bat;*.vb;*.vbs;scr;*.inf;*.reg;*.lnk;*.pif;*.ade;*.adp;*.app;*.bas;*.chm;*.cmd;*.cpl;*.crt;*.csh;*.fxp;*.hlp;*.hta;*.ins;*.isp;*.jse;*.ksh;*.Lnk;*.mda;*.mdb;*.mde;*.mdt;*.mdw;*.mdz;*.msc;*.msi;*.msp;*.mst;*.ops;*.pcd;*.prf;*.prg;*.pst;*.scf;*.scr;*.sct;*.shb;*.shs;*.url;*.vbe;*.wsc;*.wsf;*.wsh;';

			if (!$ext) {
				return 0;
			}

			$ext = trim( strtolower( $ext ) );
			return (strpos( $backList, $ext . ';' ) === false ? 1 : 0);
		}
	}

?>
