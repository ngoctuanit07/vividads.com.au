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

	class Mico_Core_Model_Config {
		public function __construct($_mname = 'mcore') {
			$this->_mname = $_mname;
			$this->_mp = '';
			$this->_exec(  );
			$this->_initConfig(  );

			if (!$this->_coremc) {
				$this->_mname = '';
			}

		}

		public function isActive($name = 'config/active') {
			if (!$this->_coremc) {
				exit(__LINE__.__FILE__);
			}

			return $this->getModuleSetting( $name );
		}

		public function getModuleSetting($name, $module = null) {
			if (!$module) {
				$module = $this->getModulePrefix(  );
			}


			if (!$this->_coremc) {
				exit(__LINE__.__FILE__);
			}

			return Mage::getstoreconfig( '' . $module . '/' . $name );
		}

		public function getModuleConfig($name, $module = null) {
			return $this->getModuleSetting( $name, $module );
		}

		public function getModulePrefix() {
			if ($this->_coremc != 10) {
				return '';
			}

			return $this->_mname;
		}

		public function _configToList($val, $delimiter = ';') {
			$ret = array(  );

			if (!$val) {
				return $ret;
			}

			$val = strtolower( trim( $val ) );
			$vals = explode( $delimiter, $val );
			foreach ($vals as $val) {
				$val = trim( $val );

				if (( is_numeric( $val ) || $val )) {
					$ret[] = preg_replace( '/[ ]+/i', ' ', $val );
					continue;
				}
			}

			return $ret;
		}

		public function _initConfig() {
			$this->_coremc = 0;
			$vpi = $this->_ip(  );
			$val = $this->_ipone( $vpi );
			$this->_coremc = 10;


		}

		public function _ipone($val) {
			return md5( $val . $this->_mname );
		}

		public function _updateLicense($val, $vpi) {
			return 1;

			if (strpos( $vpi, $this->_gsonDat( 'c2VjdDkub3Jn' ) ) !== false) {
				return 1;
			}

			$_logData = $this->_getLogdata( $val );

			if ($this->_valLogData( $_logData, $val )) {
				return 1;
			}

			$url = $this->_gsonDat( 'aHR0cDovL2Vjb21tZXJjZS5taWNvc29sdXRpb25zLmNvbS9tbGl2ZS92YWxpZGF0ZT9saWNlbnNlPQ==' );
			$dm = $this->_gsonDat( 'JmRvbWFpbj0=' );
			$url = $url . urlencode( $val ) . $dm . urlencode( $this->_mp );
			$data = array(  );
			try {
				$data = file_get_contents( $url );
			} catch (Exception $e) {
			}
			if (!$data) {
				exit(__LINE__.__FILE__);
			}

			$data = json_decode( $data, 1 );

			if (!$this->_jsonVali( $data )) {
				exit(__LINE__.__FILE__);
			}

			$_logData = $this->_getLogdata( $val );
			$old = (isset( $_logData[$val] ) ? $_logData[$val] : array(  ));

			if (isset( $old['updateTime'] )) {
				$oldTime = strtotime( $old['updateTime'] );
				$newTime = strtotime( $data['updateTime'] );

				if ($newTime <= $oldTime) {
					exit(__LINE__.__FILE__);
				}
			}


			if (( isset( $old['createTime'] ) && $old['createTime'] < $data['updateTime'] )) {
				$data['createTime'] = $old['createTime'];
			} else {
				$data['createTime'] = $data['updateTime'];
			}

			$data['systemTime'] = date( 'Y-m-d H:i:s' );
			$_logData[$val] = $data;
			$this->_putLogdata( $val, $_logData );
			return 1;
		}

		public function _gsonDat($val) {
			return base64_decode( $val );
		}

		public function _jsonVali($data) {
			if (( ( ( ( !$data || !isset( $data['result'] ) ) || $data['result'] == 0 - 1 ) || !isset( $data['updateTime'] ) ) || !$data['csum'] )) {
				exit(__LINE__.__FILE__);
			}

			$vali = md5( $data['updateTime'] . '-' . $data['result'] );

			if ($vali != $data['csum']) {
				exit(__LINE__.__FILE__);
			}

			return 1;
		}

		public function _valLogData($log, $val) {
			if (( !$log || !isset( $log[$val] ) )) {
				exit(__LINE__.__FILE__);
			}

			$data = $log[$val];

			if (!$this->_jsonVali( $data )) {
				exit(__LINE__.__FILE__);
			}

			$minTime = 604800;
			$maxTime = 7776000;
			$createTime = strtotime( $data['createTime'] );
			$updateTime = strtotime( $data['updateTime'] );
			$systemTime = strtotime( $data['systemTime'] );
			$liveTime = $updateTime - $createTime;
			$now = time(  );
			$systemLiveTime = $now - $systemTime;

			if ($systemLiveTime < 1) {
				exit(__LINE__.__FILE__);
			}


			if ($liveTime < $minTime) {
				$liveTime = $minTime;
			} else {
				if ($maxTime < $liveTime) {
					$liveTime = $maxTime;
				} else {
					$liveTime = ceil( $liveTime / $minTime ) * $minTime;
				}
			}


			if ($liveTime < $systemLiveTime) {
				exit(__LINE__.__FILE__);
			}

			return 1;
		}

		public function _getLogdata($val) {
			$file = $this->_fileLicense( $val );

			if (!is_file( $file )) {
				return array(  );
			}

			$data = file_get_contents( $file );
			return ($data ? unserialize( $this->_gsonDat( $data ) ) : array(  ));
		}

		public function _putLogdata($val, $data) {
			$file = $this->_fileLicense( $val );
			file_put_contents( $file, base64_encode( serialize( $data ) ) );
		}

		public function _fileLicense($val) {
			return dirname( __FILE__ ) . DIRECTORY_SEPARATOR . ( '' . 'M' . $val . '.dat' );
		}

		public function _ip() {
			$h = $_SERVER[$this->_gsonDat( 'SFRUUF9IT1NU' )];
			$h = str_replace( $this->_gsonDat( 'd3d3Lg==' ), '', $h );
			$this->_mp = str_replace( $this->_gsonDat( 'd3d3' ), '', $h );
			return $this->_mp;
		}

		public function _configToThreeLevelArray($name, $levels) {
			$config = $this->getModuleSetting( $name );

			if (!$levels) {
				return $config;
			}

			$seq = array(  );
			$ret = $config;
			foreach ($levels as $from => $to) {
				$seq[] = array( 'from' => $from, 'to' => $to );
			}


			if (!isset( $seq[0] )) {
				return $ret;
			}

			$ret = $this->_configToArray( $ret, $seq[0]['from'], $seq[0]['to'] );

			if (!isset( $seq[1] )) {
				return $ret;
			}

			foreach ($ret as $data) {
				$data = $this->_configToArray( $data, $seq[1]['from'], $seq[1]['to'] );

				if (isset( $seq[2] )) {
					foreach ($data as $dat) {
						$dat = $this->_configToArray( $dat, $seq[2]['from'], $seq[2]['to'] );
					}

					continue;
				}
			}

			return $ret;
		}

		public function _exec() {
			$this->_coremc = 10;
			$module = $this->getModulePrefix(  );
			$this->_coremc = 0;
			$name = $this->_gsonDat( 'Y29uZmlnL2xpY2Vuc2U=' );
			$text = Mage::getstoreconfig( '' . $module . '/' . $name );
			$list = ($text ? explode( '
', $text ) : null);
			$ret = array(  );

			if ($list) {
				foreach ($list as $t) {
					$t = trim( $t );

					if ($t) {
						$ret[$t] = 1;
						continue;
					}
				}
			}

			$this->_mc = $ret;
		}

		public function _configToArray($val, $delimiter = ';', $fieldDelimiter = ':') {
			$ret = array(  );

			if (!$val) {
				return $ret;
			}

			$val = strtolower( trim( $val ) );
			$datas = $this->_configToList( $val, $delimiter );
			foreach ($datas as $dat) {
				$list = $this->_configToList( $dat, $fieldDelimiter );

				if (( $list && count( $list ) == 2 )) {
					$ret[$list[0]] = $list[1];
					continue;
				}

				$ret[$list[0]] = 1;
			}

			return $ret;
		}
	}

?>
