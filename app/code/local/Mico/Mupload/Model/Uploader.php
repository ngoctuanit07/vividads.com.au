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

	class Mico_Mupload_Model_Uploader extends Mico_Mupload_Model_Config {
		protected $_finishChunk = 0;
		protected $_fileName = '';
		protected $_targetDir = '';

		public function responeError($code, $message) {
			return array( 'file' => '', 'error' => array( 'code' => $code, 'message' => $message ) );
		}

		public function responeOk($file) {
			if ($this->_finishChunk) {
				$oName = (isset( $_REQUEST['oname'] ) ? $_REQUEST['oname'] : '');

				if ($oName) {
					$oName = $this->getTmpFilename( $oName );

					if ($oName) {
						$newFile = $this->_targetDir . DIRECTORY_SEPARATOR . $oName;

						if (rename( $file, $newFile )) {
							$file = $newFile;
						}
					}
				}
			}

			return array( 'file' => $file, 'finishChunk' => $this->_finishChunk, 'error' => 0 );
		}

		public function saveFile($targetDir, $fileName = '') {
			@set_time_limit( 10 * 60 );
			$chunk = (isset( $_REQUEST['chunk'] ) ? $_REQUEST['chunk'] : 0);
			$chunks = (isset( $_REQUEST['chunks'] ) ? $_REQUEST['chunks'] : 0);
			$this->_finishChunk = 0;

			if (( $chunks < 2 || $chunk == $chunks - 1 )) {
				$this->_finishChunk = 1;
			}


			if (!$fileName) {
				$fileName = (isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : '');
				$fileName = preg_replace( '/[^\w\._]+/', '', $fileName );
			}

			$this->_fileName = $fileName;

			if (( $chunks < 2 && file_exists( $targetDir . DIRECTORY_SEPARATOR . $fileName ) )) {
				$ext = strrpos( $fileName, '.' );
				$fileName_a = substr( $fileName, 0, $ext );
				$fileName_b = substr( $fileName, $ext );
				$count = 1;

				while (file_exists( $targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b )) {
					++$count;
				}

				$fileName = $fileName_a . '_' . $count . $fileName_b;
			}


			if (!file_exists( $targetDir )) {
				@mkdir( $targetDir, 493, 1 );
			}

			$this->_targetDir = $targetDir;

			if (isset( $_SERVER['HTTP_CONTENT_TYPE'] )) {
				$contentType = $_SERVER['HTTP_CONTENT_TYPE'];
			}


			if (isset( $_SERVER['CONTENT_TYPE'] )) {
				$contentType = $_SERVER['CONTENT_TYPE'];
			}


			if (strpos( $contentType, 'multipart' ) !== false) {
				if (( isset( $_FILES['file']['tmp_name'] ) && is_uploaded_file( $_FILES['file']['tmp_name'] ) )) {
					$out = fopen( $targetDir . DIRECTORY_SEPARATOR . $fileName, ($chunk == 0 ? 'wb' : 'ab') );

					if ($out) {
						$in = fopen( $_FILES['file']['tmp_name'], 'rb' );

						if ($in) {
							while ($buff = fread( $in, 4096 )) {
								fwrite( $out, $buff );
							}
						} else {
							return $this->responeError( 101, 'Failed to open input stream.' );
						}

						fclose( $in );
						fclose( $out );
						@unlink( $_FILES['file']['tmp_name'] );
			                        return $this->responeOk( $targetDir . DIRECTORY_SEPARATOR . $fileName );
					} else {
						return $this->responeError( 102, 'Failed to open output stream.' );
					}
				}

				return $this->responeError( 103, 'Failed to move uploaded file.' );
			}

			$out = fopen( $targetDir . DIRECTORY_SEPARATOR . $fileName, ($chunk == 0 ? 'wb' : 'ab') );

			if ($out) {
				$in = fopen( 'php://input', 'rb' );

				if ($in) {
					while ($buff = fread( $in, 4096 )) {
						fwrite( $out, $buff );
					}
				} else {
					return $this->responeError( 101, 'Failed to open input stream.' );
				}

				fclose( $in );
				fclose( $out );
			} else {
				return $this->responeError( 102, 'Failed to open output stream.' );
			}

			$this->_fileName = $fileName;
			return $this->responeOk( $targetDir . DIRECTORY_SEPARATOR . $fileName );
		}
	}

?>
