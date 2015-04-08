<?php

/**
 * @author      MagenTools
 * @copyright   Copyright (c) 2012 MagenTools (www.magentools.com)
 * @license     End-User License Agreement (www.magentools.com/eula/)
 */
class MagenTools_Social_Model_Autopin extends Mage_Core_Model_Abstract {
    
    public function logMe($str) {        
        Mage::log($str);
    }


    public function chkSSLCurl($url) {
        $ch = curl_init($url);
        $headers = array();
        $headers[] = 'Accept: text/html, application/xhtml+xml, */*';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Accept-Language: en-us';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)");
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        if ($err != 0)
            return array('errNo' => $err, 'errMsg' => $errmsg);
        else
            return false;
    }

    public function buildRequest($ch, $ref = '', $ctOnly = false, $fields = '', $dbg = false, $advSettings = '') {
        $helper = Mage::helper('social');
        $ccURL = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        if ($dbg)
            echo '<br/><b style="font-size:16px;color:green;">#### START CURL:' . $ccURL . '</b><br/>';
        static $curl_loops = 0;
        static $curl_max_loops = 20;
        global $mtCookiesArr, $mtCookiesArrBD;
        $cookies = $helper->implodeThis($mtCookiesArr);
        if ($dbg) {
            echo '<br/><b style="color:#005800;">## Request Cookies:</b><br/>';
            $this->logMe($cookies);
        }
        if ($curl_loops++ >= $curl_max_loops) {
            $curl_loops = 0;
            return false;
        }
        $headers = array();
        $headers[] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Accept-Language: en-US,en;q=0.8'; //$headers[] = 'Accept-Encoding: gzip, deflate';   
        if ($fields != '') {
            if ((stripos($ccURL, 'http://www.blogger.com/blogger_rpc') !== false))
                $headers[] = 'Content-Type: application/javascript; charset=UTF-8';
            else
                $headers[] = 'Content-Type: application/x-www-form-urlencoded;charset=utf-8';
        }
        if (stripos($ccURL, 'http://www.blogger.com/blogger_rpc') !== false) {
            $headers[] = 'X-GWT-Permutation: 767A98E1C0A5F1D22D727BB9E37360B2';
            $headers[] = 'X-GWT-Module-Base: http://www.blogger.com/static/v1/gwt/';
        }
        if (isset($advSettings['liXMLHttpRequest'])) {
            $headers[] = 'X-Requested-With: XMLHttpRequest';
        }
        if (isset($advSettings['Origin'])) {
            $headers[] = 'Origin: ' . $advSettings['Origin'];
        }

        if (stripos($ccURL, 'blogger.com') !== false && (isset($advSettings['cdomain']) && $advSettings['cdomain'] == 'google.com'))
            $advSettings['cdomain'] = 'blogger.com';
        if (isset($advSettings['noSSLSec'])) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }
        if (isset($advSettings['proxy']) && $advSettings['proxy']['host'] != '' && $advSettings['proxy']['port'] !== '') {
            if ($dbg) {
                echo '<br/><b style="color:#005800;">## Using Proxy:</b><br/>'; /* $this->logMe($advSettings); */
            }
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, $advSettings['proxy']['host']);
            curl_setopt($ch, CURLOPT_PROXYPORT, $advSettings['proxy']['port']);
            if (isset($advSettings['proxy']['up']) && $advSettings['proxy']['up'] != '') {
                curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $advSettings['proxy']['up']);
            }
        }
        if (isset($advSettings['headers'])) {
            $headers = array_merge($headers, $advSettings['headers']);
        }
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // $this->logMe($headers);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        if (is_string($ref) && $ref != '')
            curl_setopt($ch, CURLOPT_REFERER, $ref);
        curl_setopt($ch, CURLOPT_USERAGENT, (( isset($advSettings['UA']) && $advSettings['UA'] != '') ? $advSettings['UA'] : "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.44 Safari/537.36"));
        if ($fields != '') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        } else {
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        $content = curl_exec($ch); //$this->logMe($content);  
        $errmsg = curl_error($ch);
        if (isset($errmsg) && stripos($errmsg, 'SSL') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $content = curl_exec($ch);
        }
        if (strpos($content, "\n\n") != false && strpos($content, "\n\n") < 100)
            $content = substr_replace($content, "\n", strpos($content, "\n\n"), strlen("\n\n"));
        if (strpos($content, "\r\n\r\n") != false && strpos($content, "\r\n\r\n") < 100)
            $content = substr_replace($content, "\r\n", strpos($content, "\r\n\r\n"), strlen("\r\n\r\n"));
        $ndel = strpos($content, "\n\n");
        $rndel = strpos($content, "\r\n\r\n");
        if ($ndel == false)
            $ndel = 1000000; if ($rndel == false)
            $rndel = 1000000; $rrDel = $rndel < $ndel ? "\r\n\r\n" : "\n\n";
        list($header, $content) = explode($rrDel, $content, 2);
        if ($ctOnly !== true) {
            $nsheader = curl_getinfo($ch);
            $err = curl_errno($ch);
            $errmsg = curl_error($ch);
            $nsheader['errno'] = $err;
            $nsheader['errmsg'] = $errmsg;
            $nsheader['headers'] = $header;
            $nsheader['content'] = $content;
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headers = curl_getinfo($ch);
        if ($dbg) {
            echo '<br/><b style="color:#005800;">## Headers:</b><br/>';
            $this->logMe($headers);
            $this->logMe($header);
        }

        $results = array();
        preg_match_all('|Host: (.*)\n|U', $headers['request_header'], $results);
        $ckDomain = str_replace('.', '_', $results[1][0]);
        $ckDomain = str_replace("\r", "", $ckDomain);
        $ckDomain = str_replace("\n", "", $ckDomain);
        if ($dbg) {
            echo '<br/><b style="color:#005800;">## Domain:</b><br/>';
            $this->logMe($ckDomain);
        }

        $results = array();
        $cookies = '';
        preg_match_all('|Set-Cookie: (.*);|U', $header, $results);
        $carTmp = $results[1]; //$mtCookiesArr = array_merge($mtCookiesArr, $ret['cookies']); 
        preg_match_all('/Set-Cookie: (.*)\b/', $header, $xck);
        $xck = $xck[1];
        if ($dbg) {
            echo "Full Resp Cookies";
            $this->logMe($xck);
            echo "Plain Resp Cookies";
            $this->logMe($carTmp);
        }
        //$clCook = array();
        if (isset($advSettings['cdomain']) && $advSettings['cdomain'] != '') {
            foreach ($carTmp as $iii => $cTmp)
                if (stripos($xck[$iii], 'Domain=') === false || stripos($xck[$iii], 'Domain=.' . $advSettings['cdomain'] . ';') !== false) {
                    $ttt = explode('=', $cTmp, 2);
                    $mtCookiesArr[$ttt[0]] = $ttt[1];
                }
        } else {
            foreach ($carTmp as $cTmp) {
                $ttt = explode('=', $cTmp, 2);
                $mtCookiesArr[$ttt[0]] = $ttt[1];
            }
        }
        foreach ($carTmp as $cTmp) {
            $ttt = explode('=', $cTmp, 2);
            $mtCookiesArrBD[$ckDomain][$ttt[0]] = $ttt[1];
        }
        if ($dbg) {
            echo '<br/><b style="color:#005800;">## Common/Response Cookies:</b><br/>';
            $this->logMe($mtCookiesArr);
            echo "\r\n\r\n<br/>" . $ckDomain . "\r\n\r\n";
            $this->logMe($mtCookiesArrBD);
        }
        if ($dbg && $http_code == 200) {
            $contentH = htmlentities($content);
            $this->logMe($contentH);
        } $rURL = '';

        if ($http_code == 200 && stripos($content, 'http-equiv="refresh" content="0; url=&#39;') !== false) {
            $http_code = 301;
            $rURL = $helper->processString($content, 'http-equiv="refresh" content="0; url=&#39;', '&#39;"');
            $mtCookiesArr = array();
        } elseif ($http_code == 200 && stripos($content, 'location.replace') !== false) {
            $http_code = 301;
            $rURL = $helper->processString($content, 'location.replace("', '"');
        }// echo "~~~~~~~~~~~~~~~~~~~~~~".$rURL."|".$http_code;
        if ($http_code == 301 || $http_code == 302 || $http_code == 303) {
            if ($rURL != '') {
                $rURL = str_replace('\x3d', '=', $rURL);
                $rURL = str_replace('\x26', '&', $rURL);
                $url = @parse_url($rURL);
            } else {
                $matches = array();
                preg_match('/Location:(.*?)\n/', $header, $matches);
                $url = @parse_url(trim(array_pop($matches)));
            } $rURL = ''; //echo "#######"; $this->logMe($url);
            if (!$url) {
                $curl_loops = 0;
                return ($ctOnly === true) ? $content : $nsheader;
            }
            $last_urlX = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            $last_url = @parse_url($last_urlX);
            if (!$url['scheme'])
                $url['scheme'] = $last_url['scheme']; if (!$url['host'])
                $url['host'] = $last_url['host']; if (!$url['path'])
                $url['path'] = $last_url['path']; if (!isset($url['query']))
                $url['query'] = '';
            $new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query'] ? '?' . $url['query'] : '');
            curl_setopt($ch, CURLOPT_URL, $new_url);
            if ($dbg)
                echo '<br/><b style="color:#005800;">Redirecting to:</b>' . $new_url . "<br/>"; return $this->buildRequest($ch, $last_urlX, $ctOnly, '', $dbg, $advSettings);
        } else {
            $curl_loops = 0;
            return ($ctOnly === true) ? $content : $nsheader;
        }
    }


    public function queryPinterest($url, $ref = '', $ctOnly = false, $fields = '', $dbg = false, $advSettings = '') {
        if ($dbg)
            echo '<br/><b style="font-size:16px;color:green;">#### GSTART URL:' . $url . '</b><br/>';
        $ch = curl_init($url);
        $contents = $this->buildRequest($ch, $ref, $ctOnly, $fields, $dbg, $advSettings);
        curl_close($ch);
        return $contents;
    }


    public function nxs_clFN($fn) {
        $sch = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
        return trim(preg_replace('/[\s-]+/', '-', str_replace($sch, '', $fn)), '.-_');
    }


    public function nxs_mkImgNm($fn, $cType) {
        $iex = array(".png", ".jpg", ".gif", ".jpeg");
        $map = array('image/gif' => '.gif', 'image/jpeg' => '.jpg', 'image/png' => '.png');
        $fn = str_replace($iex, '', $fn);
        if (isset($map[$cType])) {
            return $fn . $map[$cType];
        }
        else
            return $fn . ".jpg";
    }


    public function chkLogin() {
        global $mtCookiesArr, $mtTkn, $mtPNBoards;
        $helper = Mage::helper('social');
        $advSettings = array();
        $mtCookiesArr['csrftoken'] = '';
        $contents = $this->queryPinterest('http://www.pinterest.com/', 'http://www.pinterest.com', true, '', false, $advSettings); //  echo "===END CHECK:"; $this->logMe($mtCookiesArr);  
        if (stripos($contents, 'UserNav') !== false || stripos($contents, 'class="profileName"') !== false) { /* echo "You are IN"; */
            if (stripos($contents, "name='csrfmiddlewaretoken' value='") !== false)
                $mtTkn = trim($helper->processString($contents, "name='csrfmiddlewaretoken' value='", "'"));
            else
                $mtTkn = $mtCookiesArr['csrftoken']; return false;
        } else { /* echo "NOOO=="; $this->logMe($contents); */
            return 'No Saved Login';
        }
        return false;
    }


    public function loginToPinterest($email, $pass) {
        global $mtCookiesArr, $mtTkn, $mtPNBoards;
        $mtCookiesArr = array();
        $advSettings = array();
        $boards = array();
        $helper = Mage::helper('social');
        $err = $this->chkSSLCurl('https://www.pinterest.com');
        if ($err !== false && $err['errNo'] == '60')
            $advSettings['noSSLSec'] = true;
        if ($err !== false && stripos($err['errMsg'], 'Protocol https not supported') !== false)
            return 'Protocol https not supported or disabled in libcurl. Please install or enable OpenSSL. ';
        $contents = $this->queryPinterest('https://www.pinterest.com/login/?next=%2F', 'https://www.pinterest.com', true, '', false, $advSettings);
        //## GET HIDDEN FIELDS
        $md = array();
        $apVer = trim($helper->processString($contents, '"app_version": "', '"'));
        $fldsTxt = 'data=%7B%22options%22%3A%7B%22username_or_email%22%3A%22' . urlencode($email) . '%22%2C%22password%22%3A%22' . urlencode($pass) . '%22%7D%2C%22context%22%3A%7B%22app_version%22%3A%22' . $apVer .
                '%22%7D%7D&source_url=%2Flogin%2F&module_path=App()%3ELoginPage()%3ELogin()%3EButton(class_name%3Dprimary%2C+text%3DLog+in%2C+type%3Dsubmit%2C+tagName%3Dbutton%2C+size%3Dlarge)';
        $advSettings['liXMLHttpRequest'] = 1;
        if (trim($mtTkn) == '' && trim($mtCookiesArr['csrftoken']) != '')
            $mtTkn = $mtCookiesArr['csrftoken']; $advSettings['headers'] = array('X-CSRFToken: ' . $mtTkn);
        //## ACTUAL LOGIN
        $contents = $this->queryPinterest('https://www.pinterest.com/resource/UserSessionResource/create/', 'https://www.pinterest.com/login/', true, $fldsTxt, false, $advSettings);
        $resp = json_decode($contents, true); //  $this->logMe($resp);   $this->logMe($fldsTxt);  $this->logMe($contents);    
        if ((isset($resp['http_status']) && $resp['http_status'] == '200') || (isset($resp['resource_response']['data']['username']) && $resp['resource_response']['data']['username'] != '')) { // echo "You are IN"; 
            $contents = $this->queryPinterest('http://www.pinterest.com/resource/NoopResource/get/?data={%22module%22:{%22name%22:%22PinCreate%22}}', 'https://www.pinterest.com', true, '', false, $advSettings);
            $k = json_decode($contents, true);
            $khtml = $helper->processString($k['module']['html'], "boardPickerInnerWrapper", "</ul>");
            $khA = explode('<li', $khtml);
            foreach ($khA as $kh)
                if (stripos($kh, 'data-id') !== false) {
                    $bid = $helper->processString($kh, 'data-id="', '"');
                    $bname = trim($helper->processString($kh, '</div>', '</li>'));
                    if (isset($bid))                        
                        $boards[] = array('value' => $bid, 'name' => trim($bname));
                } $mtPNBoards = $boards;
                $session = Mage::getSingleton('core/session');
                $session->setPinterestBoards($mtPNBoards);
            return false;
        } elseif (is_array($resp) && isset($resp['resource_response']['error']))
            return "ERROR: " . $resp['resource_response']['error']['http_status'] . " | " . $resp['resource_response']['error']['message'];
        elseif (stripos($contents, 'CSRF verification failed') !== false) {
            $retText = trim(str_replace(array("\r\n", "\r", "\n"), " | ", strip_tags($helper->processString($contents, '</head>', '</body>'))));
            return "CSRF verification failed - Please contact NextScripts Support | Pinterest Message:" . $retText;
        } elseif (stripos($contents, 'IP because of suspicious activity') !== false)
            return 'Pinterest blocked logins from this IP because of suspicious activity';
        elseif (stripos($contents, 've detected a bot!') !== false)
            return 'Pinterest has your IP (' . $helper->processString($contents, 'ess: <b>', '<') . ') blocked. Please <a target="_blank" class="link" href="//help.pinterest.com/entries/22914692">Contact Pinterest</a> and ask them to unblock your IP. ';
        else
            return 'Pinterest login failed. Unknown Error. Please contact support.';
        return false;
    }


    public function getBoards() {
        global $mtPNBoards;
        //return $mtPNBoards;
        $session = Mage::getSingleton('core/session');
        return $session->getPinterestBoards();
    }


    public function doPostToPinterest($msg, $imgURL, $lnk, $boardID, $title = '', $price = '', $via = '') {
        global $mtTkn, $mtCookiesArr;
        $msg = strip_tags($msg);
        $msg = substr($msg, 0, 480);
        $tgs = '';
        $helper = Mage::helper('social');
        // if (stripos($imgURL, 'youtube.com')!==false || stripos($imgURL, 'youtu.be')!==false) { $tgs = 'http://img.youtube.com/vi/'.str_ireplace('http://youtu.be/','',$imgURL).'/0.jpg'; }
        if ($msg == '')
            $msg = '&nbsp;'; if (trim($boardID) == '')
            return "Board is not Set"; if (trim($imgURL) == '')
            return "Image is not Set"; $msg = str_ireplace(array("\r\n", "\n", "\r"), " ", $msg);
        $msg = strip_tags($msg);
        if (function_exists('nxs_decodeEntitiesFull'))
            $msg = nxs_decodeEntitiesFull($msg, ENT_QUOTES);
        $mgsOut = urlencode($msg);
        $mgsOut = str_ireplace(array('%28', '%29', '%27', '%21', '%22', '%09'), array("(", ")", "'", "!", "%5C%22", '%5Ct'), $mgsOut);
        $fldsTxt = 'source_url=%2Fpin%2Ffind%2F%3Furl%3D' . urlencode(urlencode($lnk)) . '&data=%7B%22options%22%3A%7B%22board_id%22%3A%22' . $boardID . '%22%2C%22description%22%3A%22' . $mgsOut . '%22%2C%22link%22%3A%22' . urlencode($lnk) . '%22%2C%22share_facebook%22%3Afalse%2C%22image_url%22%3A%22' . urlencode($imgURL) . '%22%2C%22method%22%3A%22scraped%22%7D%2C%22context%22%3A%7B%22app_version%22%3A%2250c6169%22%7D%7D';
        $advSettings = array();
        $advSettings['liXMLHttpRequest'] = 1; // $this->logMe($mtTkn); $this->logMe($mtCookiesArr); //die();
        if (trim($mtTkn) == '' && trim($mtCookiesArr['csrftoken']) != '')
            $mtTkn = $mtCookiesArr['csrftoken'];
        $advSettings['headers'] = array('X-CSRFToken: ' . $mtTkn);
        $advSettings['headers'][] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
        $contents = $this->queryPinterest('http://www.pinterest.com/resource/PinResource/create/', '', true, $fldsTxt, false, $advSettings);
        $resp = json_decode($contents, true); // $this->logMe($advSettings);  $this->logMe($resp);   $this->logMe($fldsTxt);  $this->logMe($contents);    
        if (is_array($resp)) {
            if (isset($resp['resource_response']) && isset($resp['resource_response']['error']) && $resp['resource_response']['error'] != '')
                return print_r($resp['resource_response']['error'], true);
            elseif (isset($resp['resource_response']) && isset($resp['resource_response']['data']) && $resp['resource_response']['data']['id'] != '') { // gor JSON
                if (isset($resp['resource_response']) && isset($resp['resource_response']['error']) && $resp['resource_response']['error'] != '')
                    return print_r($resp['resource_response']['error'], true);
                else
                    return array("code" => "OK", "post_id" => "/pin/" . $resp['resource_response']['data']['id']);
            }
        }elseif (stripos($contents, 'blocked this') !== false) {
            $retText = trim(str_replace(array("\r\n", "\r", "\n"), " | ", strip_tags($helper->processString($contents, '</head>', '</body>'))));
            return "Pinterest ERROR: 'The Source is blocked'. Please see https://support.pinterest.com/entries/21436306-why-is-my-pin-or-site-blocked-for-spam-or-inappropriate-content/ for more info | Pinterest Message:" . $retText;
        } elseif (stripos($contents, 'image you tried to pin is too small') !== false) {
            $retText = trim(str_replace(array("\r\n", "\r", "\n"), " | ", strip_tags($helper->processString($contents, '</head>', '</body>'))));
            return "Image you tried to pin is too small | Pinterest Message:" . $retText;
        } elseif (stripos($contents, 'CSRF verification failed') !== false) {
            $retText = trim(str_replace(array("\r\n", "\r", "\n"), " | ", strip_tags($helper->processString($contents, '</head>', '</body>'))));
            return "CSRF verification failed - Please contact NextScripts Support | Pinterest Message:" . $retText;
        } elseif (stripos($contents, 'Oops') !== false && stripos($contents, '<body>') !== false)
            return 'Pinterest ERROR MESSAGE : ' . trim(str_replace(array("\r\n", "\r", "\n"), " | ", strip_tags($helper->processString($contents, '</head>', '</body>'))));
        else
            return "Somethig is Wrong - Pinterest Returned Error 502";
    }

    
    public function pinThis($p, $pintoboard) {
        if ($p == '' || !is_numeric($p)) {
            return 'Invalid Product Id';
        }$product = Mage::getModel('catalog/product')->load($p);
        if (!$product->getId())
            return 'Product not available';$prodName = $product->getName();
        $pin_msg = $prodName;
        $pinSku = Mage::getStoreConfig("social/pinterest/include_sku");
        $pinPrice = Mage::getStoreConfig("social/pinterest/include_price");
	if ($pinSku) {
            $sku = $product->getSku();
            $pin_msg .= " | " . $sku;
        }if ($pinPrice) {
            $price = $product->getPrice();
            $currSym = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
            $specialprice = $product->getSpecialPrice();
            $specialPriceFromDate = $product->getSpecialFromDate();
            $specialPriceToDate = $product->getSpecialToDate();
            $today = time();
            if ($specialprice && ($today >= strtotime($specialPriceFromDate) && $today <= strtotime($specialPriceToDate) || $today >= strtotime($specialPriceFromDate) && is_null($specialPriceToDate))): $price = "RP: " . $currSym . number_format($price, 2) . ", SP: " . $currSym . number_format($specialprice, 2);
            else: $price = $currSym . number_format($price, 2);
            endif;
            $pin_msg .= " | " . $price;
        } 
	//$imgURL = "http://cdn.sailusfood.com/wp-content/uploads/mexican_tacos3.JPG";
	$imgURL = Mage::helper('catalog/image')->init($product, 'image');
        $link = Mage::getBaseUrl() . $product->getUrlPath();
        Mage::log($pin_msg . '==' . $imgURL . '==' . $link . '==' . $pintoboard);
        $res = $this->doPostToPinterest($pin_msg, $imgURL, $link, $pintoboard);
        return $res;
    }
    
}
?>
