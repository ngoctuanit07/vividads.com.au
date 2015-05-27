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

	class Mico_Mupload_Model_Observer extends Mage_Core_Model_Abstract {
		public function hookOrderSaveAfter($observer) {
			/*
			
			global $_MUPLOAD_HOOK_ORDER_SAVE_AFTER_PROCESS;
			global $_MUPLOAD_HOOK_ORDER_ITEMS_SAVE_AFTER_PROCESS;

			$this->_uploader = Mage::getmodel( 'mupload/uploader' );
			$order = $observer->getEvent()->getOrder();
			$this->_customerId = $order->getCustomerId();
			$this->_customerNumber = 'guest';

			if (( ( $this->_customerId && is_numeric( $this->_customerId ) ) && 0 < $this->_customerId )) {
				$this->_customerNumber = Mage::getsingleton( 'customer/session' )->getCustomer(  )->getIncrementId(  );

				if (!$this->_customerNumber) {
					$this->_customerNumber = $this->_customerId;
				}
			}

			$this->_orderId = $order->getIncrementId(  );
			$this->_itemIndex = 0;

			if (( $_MUPLOAD_HOOK_ORDER_SAVE_AFTER_PROCESS && isset( $_MUPLOAD_HOOK_ORDER_SAVE_AFTER_PROCESS[$this->_orderId] ) )) {
				return $this;
			}

			$_MUPLOAD_HOOK_ORDER_SAVE_AFTER_PROCESS[$this->_orderId] = 1;
			$_fileDatas = array(  );
			$_fileDatas['ordernumber'] = $this->_orderId;
			$_fileDatas['customernumber'] = $this->_customerNumber;
			$this->_uploadDir = trim( $this->_uploader->getModuleSetting( 'folder/orderFolder' ) );

			if (!$this->_uploadDir) {
				$this->_uploadDir = 'orders/{ordernumber}';
			}

			foreach ($_fileDatas as $from => $to) {
				$this->_uploadDir = str_replace( '{' . $from . '}', $to, $this->_uploadDir );
			}

			$this->_uploadDir = Mage::getbasedir(  ) . '/' . $this->_uploadDir . '/';

			if (!is_dir( $this->_uploadDir )) {
				mkdir( $this->_uploadDir, 493, 1 );
			}

			$reqOptions = array(  );
			$group = null;
			foreach ($order->getAllItems(  ) as $orderItem) {
				if (( $_MUPLOAD_HOOK_ORDER_ITEMS_SAVE_AFTER_PROCESS && isset( $_MUPLOAD_HOOK_ORDER_ITEMS_SAVE_AFTER_PROCESS[$orderItem->getId(  )] ) )) {
					continue;
				}

				$_MUPLOAD_HOOK_ORDER_ITEMS_SAVE_AFTER_PROCESS[$orderItem->getId(  )] = 1;
				$this->_itemIndex = $this->_itemIndex + 1;
				$options = array(  );
				OrganicInternet_SimpleConfigurableProducts_Sales_Model_Order_Item;

				if ($orderItem instanceof null) {
					$options = $orderItem->_getData( 'product_options' );
					$options = ($options ? unserialize( $options ) : array(  ));
				} else {
					$options = $orderItem->getProductOptions(  );
				}


				if (( ( !isset( $options['info_buyRequest'] ) || !isset( $options['info_buyRequest']['options'] ) ) || !$options['info_buyRequest']['options'] )) {
					continue;
				}


				if (!$group) {
					$group = new Mico_Mupload_Model_Product_Option_Type_File(  );
				}

				$reqOptions = $options['info_buyRequest']['options'];
				$_options = (isset( $options['options'] ) ? $options['options'] : array(  ));

				if (!$_options) {
					continue;
				}

				foreach ($reqOptions as $key => $val) {
					if (is_array( $val )) {
						if ($this->_isUltimate( $val )) {
							$val = $this->_moveFiles( $val, $orderItem );

							if ($_options) {
								foreach ($_options as $opt) {
									if ($opt['option_id'] == $key) {
										$opt['value'] = $group->_getOptionHtml( $val );
										$opt['option_value'] = $val;
										continue;
									}
								}
							}

							$reqOptions[$key] = $val;
							continue;
						}

						continue;
					}
				}

				$options['info_buyRequest']['options'] = $reqOptions;
				$options['options'] = $_options;
				$orderItem->setProductOptions( $options )->save(  );
			}

			return $this;
		
		*/
		}

		public function _isUltimate($datas) {
			if (isset( $datas['ultimate'] )) {
				return 1;
			}

			return 0;
		}

		public function _moveFiles($datas, $orderItem) {
			if (( !isset( $datas['next'] ) || !$datas['next'] )) {
				return $this->_moveFile( $datas, $orderItem );
			}

			$next = $datas;
			$ret = '';
			$keys = array(  );
			$list = array(  );

			while ($next) {
				$list[] = $next;
				$next = (isset( $next['next'] ) ? $next['next'] : 0);
			}

			$len = count( $list );
			$ret = array(  );
			$i = $len;

			while (0 < $i) {
				$data = $list[$i - 1];
				$data = $this->_moveFile( $data, $orderItem );
				$data['next'] = ($ret ? $ret : 0);
				$ret = $data;
				--$i;
			}

			return $ret;
		}

		public function _moveFile($data, $orderItem) {
			$_fileDatas = array(  );
			$_fileDatas['ordernumber'] = $this->_orderId;
			$_fileDatas['customernumber'] = $this->_customerNumber;
			$_fileDatas['optiontitle'] = (( isset( $data['optiontitle'] ) && $data['optiontitle'] ) ? $data['optiontitle'] : '');
			$_fileDatas['filename'] = '';
			$_fileDatas['index'] = $this->_itemIndex;
			$uploadDirectory = $this->_uploadDir;
			$_fileDatas['sku'] = $orderItem->getSku(  );
			$_fileDatas['qty'] = floor( $orderItem->getQtyOrdered(  ) );
			$pre = trim( $this->_uploader->getModuleSetting( 'folder/orderFilename' ) );

			if (!$_fileDatas['sku']) {
				$_fileDatas['sku'] = '';
			}

			$ext = pathinfo( strtolower( $data['title'] ), PATHINFO_EXTENSION );
			$filename = pathinfo( $data['title'], PATHINFO_FILENAME );
			$_fileDatas['filename'] = $filename;
			foreach ($_fileDatas as $from => $to) {
				$pre = str_replace( '{' . $from . '}', $to, $pre );
			}

			$pre = str_replace( '--', '-', $pre );
			$pre = Varien_File_Uploader::getcorrectfilename( $pre );
			$filename = $pre;
			$rand = '-r';

			while (file_exists( $uploadDirectory . $filename . '.' . $ext )) {
				$filename .= $rand . rand( 10, 99 );
				$rand = '';
			}

			$old = $data['fullpath'];
			$fileFullPath = $uploadDirectory . $filename . '.' . $ext;

			if (copy( $data['fullpath'], $fileFullPath )) {
				$oldThumbnailFullPath = $this->_uploader->getThumbnailFullpath( $old );

				if (is_file( $oldThumbnailFullPath )) {
					$thumbnailFullPath = $this->_uploader->getThumbnailFullpath( $fileFullPath );
					$path = dirname( $thumbnailFullPath );

					if (!is_dir( $path )) {
						mkdir( $path, 493, 1 );
					}


					if (strpos( $oldThumbnailFullPath, 'custom_options' ) !== false) {
						rename( $oldThumbnailFullPath, $thumbnailFullPath );
					} else {
						copy( $oldThumbnailFullPath, $thumbnailFullPath );
					}
				}

				$data['fullpath'] = $fileFullPath;
				$data['order_path'] = $this->_uploader->httpUrl( $fileFullPath );
				$data['quote_path'] = $data['order_path'];
			}

			return $data;
		}
	}

?>
