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

	class Mico_Mupload_Model_Config extends Mico_Core_Model_Config {
		protected $_config = -1;
		protected $_tmpFolder = '';

		public function __construct() {
			parent::__construct( 'muploadultimate' );
			$this->_thumbnailReplace = 0 - 1;
			$this->_convertPath = 0 - 1;
		}

		public function getName($path) {
			return pathinfo( $path, PATHINFO_BASENAME );
		}

		public function getUrl($path) {
			return preg_replace( '/^\//', Mage::getbaseurl( Mage_Core_Model_Store::URL_TYPE_WEB ), $path );
		}

		public function getThumnailUrl($path) {
			$path = $this->getUrl( $path );
			return $path;
		}

		public function getMoveTmp($fileFullPath, $toDir, $toName = '') {
			if (!file_exists( $fileFullPath )) {
				return 0;
			}


			if (!$toName) {
				$toName = $fileFullPath;
			}

			$ext = pathinfo( $toName, PATHINFO_EXTENSION );
			$fileName = pathinfo( $toName, PATHINFO_FILENAME );
			$count = 1;
			$destFile = $toDir . DIRECTORY_SEPARATOR . ( '' . $fileName . '.' . $ext );

			if (!is_dir( $toDir )) {
				mkdir( $toDir, 493, 1 );
			}


			while (file_exists( $destFile )) {
				$destFile = $toDir . DIRECTORY_SEPARATOR . ( '' . $fileName . '_' . $count . '.' . $ext );
				++$count;
			}


			if (rename( $fileFullPath, $destFile )) {
				return $destFile;
			}

			return 0 - 1;
		}

		public function getFileTmp($fileName) {
			return $this->getFolderTmp(  ) . DIRECTORY_SEPARATOR . $fileName;
		}

		public function getFolderTmp() {
			return Mage::getbasedir(  ) . DIRECTORY_SEPARATOR . $this->getTmpPath(  );
		}

		public function getTmpPath() {
			if ($this->_tmpFolder) {
				return $this->_tmpFolder;
			}

			$ret = trim( $this->getModuleSetting( 'folder/tmpFolder' ) );

			if (!$ret) {
				return 'media/tmp';
			}

			$datas = array(  );
			$website = Mage::app(  )->getStore(  )->getWebsite(  );
			$datas['site_id'] = $website->getId(  );
			$datas['site_code'] = $this->strToPath( $website->getCode(  ) );
			$datas['site_name'] = $this->strToPath( $website->getName(  ) );
			$datas['site_domain'] = $this->strToPath( $_SERVER['HTTP_HOST'] );
			foreach ($datas as $from => $to) {
				$ret = str_replace( '{' . $from . '}', $to, $ret );
			}

			$this->_tmpFolder = $ret;
			return $this->_tmpFolder;
		}

		public function strToPath($val) {
			$val = Varien_File_Uploader::getcorrectfilename( strtolower( trim( $val ) ) );
			return $val;
		}

		public function getTmpFilename($fileName) {
			$fileName = $this->strToPath( $fileName );
			$tmpFilename = trim( $this->getModuleSetting( 'folder/tmpFilename' ) );

			if (!$tmpFilename) {
				return '';
			}

			$info = pathinfo( $fileName );
			$datas = array(  );
			$datas['hex'] = sprintf( '%02x%08x', rand( 0, 255 ), time(  ) + rand( 0, 99999999 ) );
			$website = Mage::app(  )->getStore(  )->getWebsite(  );
			$datas['filename'] = $this->strToPath( $info['filename'] );
			$datas['site_id'] = $website->getId(  );
			$datas['site_code'] = $this->strToPath( $website->getCode(  ) );
			$datas['site_name'] = $this->strToPath( $website->getName(  ) );
			$datas['site_domain'] = $this->strToPath( $_SERVER['HTTP_HOST'] );
			foreach ($datas as $from => $to) {
				$tmpFilename = str_replace( '{' . $from . '}', $to, $tmpFilename );
			}

			$fileName = '' . $tmpFilename . '.' . $info['extension'];
			return $fileName;
		}

		public function getFolderUpload($incrementId) {
			$ret = Mage::getbasedir(  ) . DIRECTORY_SEPARATOR . trim( $this->getModuleSetting( 'folder/upload' ) );
			return str_replace( '{ordernumber}', $incrementId, $ret );
		}

		public function httpUrl($path, $_wwwRoot = 0) {
			$old = $path;
			$path = str_replace( Mage::getbasedir( Mage_Core_Model_Store::URL_TYPE_MEDIA ), '/', $path );
			$root = '/';

			if ($old != $path) {
				$root = Mage::getbaseurl( Mage_Core_Model_Store::URL_TYPE_MEDIA );
			} else {
				$path = str_replace( Mage::getbasedir(  ), '', $path );
				$root = Mage::getbaseurl( Mage_Core_Model_Store::URL_TYPE_WEB );
			}

			$path = str_replace( '\\', '/', $path );
			$path = str_replace( '//', '/', $path );

			if ($_wwwRoot) {
				$path = preg_replace( '/^\//', $root, $path );
			}

			return $path;
		}

		public function getConfig($product) {
			if ($this->_config !== 0 - 1) {
				return $this->_config;
			}
			$this->_config = array(  );
			$ret = array(  );
			$active = $this->getModuleSetting( 'config/active' );

			if (!$active) {
				return array(  );
			}

			$ret['_sizeMin'] = array(  );
			$ret['_sizeMax'] = array(  );
			$ret['_fileExt'] = array(  );
			$ret['baseUrl'] = Mage::geturl( '/' );
			$ret['downloadUrl'] = Mage::geturl( 'mupload/uploader/preview' );
			$ret['uploadedFileUrl'] = Mage::getbaseurl( Mage_Core_Model_Store::URL_TYPE_WEB ) . $this->getTmpPath(  ) . '/';
			$ret['folder']['multiFile'] = $this->getModuleSetting( 'folder/multiFile' ) * 1;
			$ret['folder']['orderFolder'] = trim( $this->getModuleSetting( 'folder/orderFolder' ) );
			$ret['folder']['orderFilename'] = trim( $this->getModuleSetting( 'folder/orderFilename' ) );
			$ret['folder']['fileExt'] = trim( $this->getModuleSetting( 'folder/fileExt' ) );
			$ret['folder']['fileMin'] = trim( $this->getModuleSetting( 'folder/fileMin' ) );
			$ret['folder']['fileMax'] = trim( $this->getModuleSetting( 'folder/fileMax' ) );
			$ret['folder']['fileChunk'] = trim( $this->getModuleSetting( 'folder/fileChunk' ) );
			$ret['folder']['fileExt'] = trim( $this->configToUploaderExt( $ret['folder']['fileExt'] ) );
			$ret['folder']['fileFilter'] = trim( $this->getModuleSetting( 'folder/fileFilter' ) );

			if (!$ret['folder']['fileFilter']) {
				$ret['folder']['fileFilter'] = 'Attach files';
			}


			if (!$ret['folder']['fileMin']) {
				$ret['folder']['fileMin'] = 0;
			}


			if (!$ret['folder']['fileMax']) {
				$ret['folder']['fileMax'] = '512mb';
			}


			if (!$ret['folder']['fileChunk']) {
				$ret['folder']['fileChunk'] = '512kb';
			}

			$ret['uploader']['auto'] = $this->getModuleSetting( 'uploader/auto' ) * 1;
			$ret['uploader']['runtimes'] = trim( $this->getModuleSetting( 'uploader/runtimes' ) );

			if (!$ret['uploader']['runtimes']) {
				$ret['uploader']['runtimes'] = 'silverlight,flash,html5,html4';
			}

			$ret['uploader']['selectText'] = trim( $this->getModuleSetting( 'uploader/selectText' ) );
			$ret['uploader']['selectImage'] = $this->getUploaderImg( 'uploader/selectImage', false );
			$ret['uploader']['uploadText'] = trim( $this->getModuleSetting( 'uploader/uploadText' ) );
			$ret['uploader']['uploadImage'] = $this->getUploaderImg( 'uploader/uploadImage', false );
			$ret['uploader']['thumbnailEngine'] = $this->getModuleSetting( 'uploader/thumbnailEngine' ) * 1;
			$ret['uploader']['thumbnailExt'] = trim( $this->getModuleSetting( 'uploader/thumbnailExt' ) );
			$ret['uploader']['thumbnailWidth'] = $this->getModuleSetting( 'uploader/thumbnailWidth' ) * 1;
			$ret['uploader']['multiFile'] = $this->getModuleSetting( 'uploader/multiFile' ) * 1;
			$ret['uploader']['multiOptions'] = $this->_configToList( trim( $this->getModuleSetting( 'uploader/multiOptions' ) ), ',' );
			$ret['uploader']['multiFileGlobal'] = 0;
			$ret['uploader']['multiFileOptions'] = array(  );
			$_mapOptions = array(  );
			$options = $product->getOptions(  );

			if ($options) {
				foreach ($options as $option) {
					if ($option->getGroupByType(  ) == Mage_Catalog_Model_Product_Option::OPTION_GROUP_FILE) {
						$code = trim( strtolower( $option->getTitle(  ) ) );
						$_mapOptions[$code] = $option;
						$ext = ($option->getFileExtension(  ) ? $this->configToUploaderExt( $option->getFileExtension(  ) ) : '');

						if ($ext) {
							$ret['_fileExt'][$option->getId(  )] = $ext;
							continue;
						}

						continue;
					}
				}
			}


			if ($ret['uploader']['multiFile']) {
				if ($ret['uploader']['multiFile'] == Mico_Mupload_Model_System_Config_Multifile::MUPLOAD_MULTIFILE_GLOBAL) {
					if ($_mapOptions) {
						foreach ($_mapOptions as $code => $option) {
							if ($this->_checkOption( $ret['uploader']['multiOptions'], $code )) {
								continue;
							}

							$ret['uploader']['multiFileOptions'][$option->getId(  )] = 1;
						}
					}
				} else {
					if ($_mapOptions) {
						foreach ($_mapOptions as $code => $option) {
							if ($this->_checkOption( $ret['uploader']['multiOptions'], $code )) {
								$ret['uploader']['multiFileOptions'][$option->getId(  )] = 1;
								continue;
							}
						}
					}
				}
			}

			$ret['resolution']['quality'] = $this->_configToArray( $this->getModuleSetting( 'resolution/quality' ), ',', '=' );
			$ret['resolution']['template'] = trim( $this->getModuleSetting( 'resolution/template' ) );
			$this->_config = $ret;
			return $ret;
		}

		public function resolutionToQualityType($resolution) {
			$config = (isset( $this->_config['resolution']['quality'] ) ? $this->_config['resolution']['quality'] : $this->_configToArray( $this->getModuleSetting( 'resolution/quality' ), ',', '=' ));
			$max = 0;
			$ret = '';
			foreach ($config as $type => $_resolution) {
				if (( $_resolution <= $resolution && $max <= $_resolution )) {
					$max = $_resolution;
					$ret = $type;
					continue;
				}
			}

			return $ret;
		}

		public function fileToQuality($file) {
			$size = 0;
			$ret = array( 'quality' => 0 );
			$_width = 0;
			$_height = 0;

			if (is_file( $file )) {
				$_imageSize = getimagesize( $file );

				if ($_imageSize) {
					$_width = $_imageSize[0];
					$_height = $_imageSize[1];
				} else {
					return $ret;
				}
			}


			if (( !$_width || !$_height )) {
				return $ret;
			}

			$resolution = ceil( $_width * $_height / 100000 ) / 10;
			$ret['quality'] = $resolution;
			$ret['qualityType'] = $this->resolutionToQualityType( $resolution );
			return $ret;
		}

		public function _checkOption($val, $arr) {
			if (( !$val || !$arr )) {
				return 0;
			}

			return in_array( $arr, $val );
		}

		public function getUploaderImg($name, $setDef = true) {
			$val = trim( $this->getModuleSetting( $name ) );

			if (( !$val && !$setDef )) {
				return '';
			}

			$val = ($val ? $val : 'skin/frontend/base/default/mico/images/' . $name . '.png');
			return Mage::getbaseurl( Mage_Core_Model_Store::URL_TYPE_WEB ) . $val;
		}

		public function formKey($number, $numberTwo = '') {
			if ($numberTwo) {
				$number = '' . $number . '-' . $numberTwo;
			}

			return md5( $number . 'mupload' );
		}

		public function configToUploaderExt($ext) {
			if (!$ext) {
				return $this->configToUpcaseUploaderExt( '' );
			}

			$ext = preg_replace( '/\s+/i ', '', $ext );
			$ext = str_replace( '*', '', $ext );
			$ext = str_replace( '.', '', $ext );
			$ext = str_replace( ';', ',', $ext );
			return $this->configToUpcaseUploaderExt( $ext );
		}

		public function configToUpcaseUploaderExt($ext) {
			if (!$ext) {
				$ext = 'jpg,gif,png,jpeg,bmp,psd,ai,eps,pdf,tif';
			}

			return $ext . ',' . strtoupper( $ext );
		}

		public function getTranslate() {
			$ret['Processing'] = Mage::helper( 'mupload' )->__( 'Processing' );
			$ret['File Min Size {fileMin}'] = Mage::helper( 'mupload' )->__( 'File Min Size {fileMin}' );
			$ret['File Max Size {fileMax}'] = Mage::helper( 'mupload' )->__( 'File Max Size {fileMax}' );
			foreach ($ret as $key => $val) {
				if ($key == $val) {
					unset( $ret[$key] );
					continue;
				}
			}

			return $ret;
		}

		public function _formatSize($bytes) {
			$i = 0 - 1;
			$mapping = array( 'kB', 'MB', 'GB', 'TB', 'PB', 'EB' );
			do {
				$bytes = $bytes / 1024;
				++$i;
			}while (!( 99 < $bytes));

			$bytes = (0.100000000000000005551115 < $bytes ? ceil( $bytes * 100 ) / 100 : 0.100000000000000005551115);
			return $bytes . $mapping[$i];
		}

		public function getThumbnailFullpath($file) {
			$path_parts = pathinfo( $file );

			if (!$path_parts) {
				return '';
			}

			$dirname = $path_parts['dirname'] . DIRECTORY_SEPARATOR;
			$filename = $path_parts['filename'];
			$ext = strtolower( $path_parts['extension'] );
			$toExt = $ext;

			if (( $ext != 'gif' && $ext != 'png' )) {
				$toExt = 'jpg';
			}

			return $this->replaceThumbnailFullpath( '' . $dirname . $filename . '_' . $ext . '_thumb.' . $toExt );
		}

		public function replaceThumbnailFullpath($file) {
			if ($this->_thumbnailReplace == 0 - 1) {
				$this->_thumbnailReplace = $this->_configToArray( $this->getModuleSetting( 'uploader/thumbnailReplace' ), ',', '=>' );
			}


			if (!$this->_thumbnailReplace) {
				return $file;
			}

			foreach ($this->_thumbnailReplace as $from => $to) {
				$file = str_replace( $from, $to, $file );
			}

			return $file;
		}

		public function saveThumbnail($file) {
			if (!is_file( $file )) {
				return '';
			}

			$thumbnailFullPath = $this->getThumbnailFullpath( $file );
			$toFile = pathinfo( $thumbnailFullPath, PATHINFO_BASENAME );
			$ext = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
			$config = array(  );

			if ($this->_config !== 0 - 1) {
				$config = $this->_config;
			} else {
				$config['uploader']['thumbnailEngine'] = $this->getModuleSetting( 'uploader/thumbnailEngine' ) * 1;
				$config['uploader']['thumbnailExt'] = trim( $this->getModuleSetting( 'uploader/thumbnailExt' ) );
				$config['uploader']['thumbnailWidth'] = $this->getModuleSetting( 'uploader/thumbnailWidth' ) * 1;
			}


			if ($config['uploader']['thumbnailEngine'] == Mico_Mupload_Model_System_Config_Engine::MUPLOAD_THUMBNAIL_ENGINE_NONE) {
				return '';
			}


			if (strpos( $config['uploader']['thumbnailExt'], $ext ) === false) {
				return '';
			}

			$ret = 0;

			if (!$config['uploader']['thumbnailWidth']) {
				$config['uploader']['thumbnailWidth'] = 150;
			}


			if ($config['uploader']['thumbnailEngine'] == Mico_Mupload_Model_System_Config_Engine::MUPLOAD_THUMBNAIL_ENGINE_MAGENTO) {
				$ret = $this->saveThumbnailMagento( $file, $thumbnailFullPath, $config['uploader']['thumbnailWidth'] );
			} else {
				if ($config['uploader']['thumbnailEngine'] == Mico_Mupload_Model_System_Config_Engine::MUPLOAD_THUMBNAIL_ENGINE_MICO_PHP) {
					$ret = $this->saveThumbnailPhpImagick( $file, $thumbnailFullPath, $config['uploader']['thumbnailWidth'] );
				} else {
					$ret = $this->saveThumbnailMico( $file, $thumbnailFullPath, $config['uploader']['thumbnailWidth'] );
				}
			}


			if ($ret) {
				return array( 'fullpath' => $thumbnailFullPath, 'name' => $toFile );
			}

			return '';
		}

		public function saveThumbnailMagento($file, $output, $width = 100) {
			$size = filesize( $file );
			$maxFileSize = $this->getModuleSetting( 'uploader/thumbnailSize' ) * 1;

			if ($maxFileSize < 1000) {
				$maxFileSize = 5000000;
			}


			if ($maxFileSize < $size) {
				return 0;
			}

			$adapter = new Varien_Image( $file );
			$info = getimagesize( $file );
			$w = 0;
			$h = 0;

			if (( $info && isset( $info[0] ) )) {
				$w = $info[0];
				$h = $info[1];
			}


			if (( !$w || !$h )) {
				return 0;
			}

			$adapter->constrainOnly( TRUE );
			$adapter->keepAspectRatio( TRUE );
			$adapter->keepFrame( false );
			$adapter->keepTransparency( True );
			$adapter->setImageBackgroundColor( false );
			$adapter->backgroundColor( false );

			if ($width <= 0) {
				$width = $w;
				$height = $h;
			} else {
				$height = $h * $width / $w;
			}

			$adapter->resize( $width, $height );
			$path = dirname( $output );

			if (!is_dir( $path )) {
				mkdir( $path, 493, 1 );
			}

			$adapter->save( $path, basename( $output ) );
			return 1;
		}

		public function saveThumbnailPhpImagick($file, $output, $width) {
			if (!is_file( $file )) {
				return 0;
			}


			if ($width < 5) {
				$width = 100;
			}


			if (!class_exists( 'Imagick' )) {
				return $this->saveThumbnailMagento( $file, $output, $width );
			}

			try {
				$thumb = new Imagick(  );
				$thumb->readImage( $file );
				$thumb->thumbnailImage( $width, 0, false );
				$thumb->writeImage( $output );
				chmod( $output, 493 );
			} catch (Exception $e) {
				return 0;
			}
			return 1;
		}

		public function saveThumbnailMico($file, $output, $width) {
			if (!is_file( $file )) {
				return 0;
			}

			$size = '';

			if ($width < 5) {
				$width = 100;
			}

			$size = sprintf( '%s', $width );
			$needRemove = 0;

			if ($this->_convertPath == 0 - 1) {
				$this->_convertPath = trim( $this->getModuleSetting( 'uploader/convertPath' ) );

				if (!$this->_convertPath) {
					$this->_convertPath = 'convert';
				}
			}


			if ($this->imageFile( $file )) {
				$command = '' . $this->_convertPath . ' -resize ' . $size . ' ' . $file . ' ' . $output;
			} else {
				if ($this->typePsd( $file )) {
					$command = '' . $this->_convertPath . ' ' . $file . ' -flatten ' . $output;
					exec( $command );
					$command = '' . $this->_convertPath . ' -resize ' . $size . ' ' . $output . ' ' . $output;
				} else {
					if ($this->firstPageFile( $file )) {
						$command = '' . $this->_convertPath . ' -thumbnail  ' . $size . ' ' . $file . '[0] ' . $output;
					} else {
						if ($this->multiPageFile( $file )) {
							$command = '' . $this->_convertPath . ' -thumbnail  ' . $size . ' ' . $file . '[1] ' . $output;
						} else {
							$needRemove = 1;
							$command = '' . $this->_convertPath . ' -thumbnail ' . $size . ' ' . $file . ' ' . $output;
						}
					}
				}
			}

			exec( $command );

			if ($needRemove) {
				$this->removeThumbnail( $output );
			}

			return 1;
		}

		public function firstPageFile($file) {
			$ext = pathinfo( $file, PATHINFO_EXTENSION );

			if (( ( $ext == 'pdf' || $ext == 'ai' ) || $ext == 'tif' )) {
				return 1;
			}

			return 0;
		}

		public function multiPageFile($file) {
			$ext = pathinfo( $file, PATHINFO_EXTENSION );

			if (( $ext == 'psd' || $ext == 'eps' )) {
				return 1;
			}

			return 0;
		}

		public function typePsd($file) {
			$ext = pathinfo( $file, PATHINFO_EXTENSION );

			if ($ext == 'psd') {
				return 1;
			}

			return 0;
		}

		public function multiPage($file) {
			$ext = pathinfo( $file, PATHINFO_EXTENSION );

			if (( ( $ext == 'tif' || $ext == 'eps' ) || $ext == 'pdf' )) {
				return 0;
			}

			return 1;
		}

		public function imageFile($file) {
			$ext = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );

			if (( ( ( ( $ext == 'jpg' || $ext == 'gif' ) || $ext == 'png' ) || $ext == 'jpeg' ) || $ext == 'bmp' )) {
				return 1;
			}

			return 0;
		}

		public function removeThumbnail($file) {
			if (is_file( $file )) {
				return 0;
			}

			$pathInfo = pathinfo( $file );
			$filename = $pathInfo['filename'];
			$dirname = $pathInfo['dirname'];
			$extension = $pathInfo['extension'];
			$list = glob( '' . $dirname . '/' . $filename . '-*.' . $extension );

			if ($list) {
				foreach ($list as $name) {
					if (!is_file( $file )) {
						rename( $name, $file );
						continue;
					}

					unlink( $name );
				}
			}

		}

		public function getCropFullpath($file) {
			return $this->getFullpathOf( $file, 'crop', '' );
		}

		public function getFullpathOf($file, $type, $toExt = 'jpg') {
			$path_parts = pathinfo( $file );

			if (!$path_parts) {
				return '';
			}

			$dirname = $path_parts['dirname'] . DIRECTORY_SEPARATOR;
			$filename = $path_parts['filename'];
			$ext = strtolower( $path_parts['extension'] );

			if (!$toExt) {
				$toExt = $ext;

				if ($ext == 'jpeg') {
					$toExt = 'jpg';
				} else {
					if (( $type == 'crop' && ( $ext == 'tif' || $ext == 'ttif' ) )) {
						$toExt = 'png';
					} else {
						if (( $type == 'thumb' && ( $ext == 'tif' || $ext == 'ttif' ) )) {
							$toExt = 'png';
						}
					}
				}
			} else {
				if ($toExt == 'jpg') {
					if (( $ext == 'gif' || $ext == 'png' )) {
						$toExt = $ext;
					} else {
						if (( $type == 'thumb' && ( $ext == 'tif' || $ext == 'ttif' ) )) {
							$toExt = 'png';
						}
					}
				} else {
					if (( $type == 'crop' && ( $toExt == 'tif' || $toExt == 'ttif' ) )) {
						$toExt = 'png';
					}
				}
			}

			return $this->replaceThumbnailFullpath( '' . $dirname . $type . '_' . $filename . '_' . $ext . '_' . $type . '.' . $toExt );
		}

		public function getFormattedOptionValue($value, $optionId = 0) {
			if (!$value) {
				return '';
			}


			if (!is_array( $value )) {
				if (is_string( $value )) {
					$value = $this->_unserializeValue( $optionValue );
				} else {
					$value = $value->getData(  );
				}
			}

			$this->_isMulti = 0;

			if (!isset( $value['next'] )) {
				return $this->_customOptionHtml( $value, $optionId );
			}

			$next = $value;
			$ret = '';
			$this->_isMulti = 1;

			while ($next) {
				$ret .= '<div class="mico-mupload-item-option">' . $this->_customOptionHtml( $next, $optionId ) . '</div>';
				$next = (isset( $next['next'] ) ? $next['next'] : null);
			}

			$this->_isMulti = 0;
			return sprintf( '<div class="mico-mupload-preview mico-mupload-uploaded-old-list " id="mico-mupload-uploaded-old-list-' . $optionId . '" >%s</div><div class="clear-both"></div>', $ret );
		}

		public function _customOptionHtml($value, $optionId) {
			if (( !isset( $value['fullpath'] ) || !$value['fullpath'] )) {
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
					$optionLink = $this->httpUrl( $fileFullPath, 1 );
				} else {
					$optionLink = $this->httpUrl( $fileFullPath, 1 );
				}

				$title = (!empty( $value['title'] ) ? $value['title'] : '');
				$optionImg = '';
				$thumbnailLink = '';
				$thumbnailFullPath = $this->getThumbnailFullpath( $fileFullPath );

				if (is_file( $thumbnailFullPath )) {
					$thumbnailLink = $this->httpUrl( $thumbnailFullPath, 1 );
				}


				if ($thumbnailLink) {
					$optionImg = '' . '<img src="' . $thumbnailLink . '?cart=1" class="mico-mupload-thumbnail"/><br/>';
				}


				if (( ( !$sizes && isset( $value['size'] ) ) && $value['size'] )) {
					$sizes = $this->_formatSize( $value['size'] );
				}

				$mapping['sizePX'] = $sizes;
				$mapping['quality'] = $resolution;
				$mapping['qualityType'] = $this->resolutionToQualityType( $resolution );
				$mapping['img'] = $optionImg;
				$mapping['link'] = $optionLink;
				$mapping['title'] = Mage::helper( 'core' )->htmlEscape( $title );

				if (!$optionId) {
					$templateCart = trim( $this->getModuleSetting( 'resolution/templateCart' ) );

					if (!$templateCart) {
						$templateCart = '{img}<a href="{link}" target="_blank">{title}</a> {sizePX}';
					}

					$ret = $templateCart;
					foreach ($mapping as $key => $val) {
						$ret = str_replace( '{' . $key . '}', $val, $ret );
					}

					return $ret;
				}

				$templateResolution = trim( $this->getModuleSetting( 'resolution/template' ) );

				if ($templateResolution) {
					foreach ($mapping as $key => $val) {
						$templateResolution = str_replace( '{' . $key . '}', $val, $templateResolution );
					}
				}

				$templatePreview = '<div class="mico-mupload-uploaded-preview">%s</div>';
				$templatePreviewNoThumnail = '{optionSelect} <a href="{optionLink}" target=\"_blank"\>{optionName}</a>';
				$templatePreviewThumbnail = '{img}<div class="clear-both"></div>{optionResolution}' . $templatePreviewNoThumnail;
				$templatePreviewNoThumnail = sprintf( $templatePreview, $templatePreviewNoThumnail );
				$templatePreviewThumbnail = sprintf( $templatePreview, $templatePreviewThumbnail );
				$mapping['optionLink'] = $mapping['link'];
				$mapping['optionName'] = $mapping['title'];
				$mapping['optionResolution'] = $templateResolution;
				$secretKey = (isset( $value['secret_key'] ) ? $value['secret_key'] : '');

				if ($secretKey) {
					$onclick = '' . 'micoUploadUltimate.onSelectUploadedOldItem(this,\'' . $optionId . '\',\'' . $secretKey . '\');';
					$mapping['optionSelect'] = '<input class="mico_custom_file_uploaded mico_custom_file_uploaded_none mico_custom_file_uploaded_' . $optionId . '"  ';
					$mapping->optionSelect .= ' id = "mico_custom_file_uploaded_' . $optionId . '_' . $secretKey . '"';
					$mapping->optionSelect .= ' type="checkbox" checked=false ';
					$mapping->optionSelect .= ' name="mico_custom_file_uploaded[' . $optionId . '][' . $secretKey . ']" ';
					$mapping->optionSelect .= ' value="1" onclick= "' . $onclick . '"/>';
				} else {
					$mapping['optionSelect'] = '';
				}


				if ($optionImg) {
					$ret = $templatePreviewThumbnail;
				} else {
					$ret = $templatePreviewNoThumnail;
				}

				foreach ($mapping as $key => $val) {
					$ret = str_replace( '{' . $key . '}', $val, $ret );
				}

				return $ret;
			} catch (Exception $e) {
				Mage::throwexception( Mage::helper( 'catalog' )->__( 'File options format is not valid.' ) );
			}
		}

		public function _unserializeValue($value) {
			if (is_array( $value )) {
				return $value;
			}


			if (( is_string( $value ) && !empty( $value ) )) {
				return unserialize( $value );
			}

			return array(  );
		}
	}

?>