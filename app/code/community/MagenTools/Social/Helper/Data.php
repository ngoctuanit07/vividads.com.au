<?php
/**
 * @author      MagenTools
 * @copyright   Copyright (c) 2012 MagenTools (www.magentools.com)
 * @license     End-User License Agreement (www.magentools.com/eula/)
 */
class MagenTools_Social_Helper_Data extends Mage_Core_Helper_Abstract 
{
    
    public function implodeThis($arr) {
        $cs = '';
        foreach ((array) $arr as $cName => $cVal) {
            $cs .= $cName . '=' . $cVal . '; ';
        } return $cs;
    }    
    
    public function randomStr($lngth) {
        $str = '';
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $size = strlen($chars);
        for ($i = 0; $i < $lngth; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        } return $str;
    }    
    
    public function httpThis($query) {
        $query_array = array();
        foreach ($query as $key => $key_value) {
            $query_array[] = $key . '=' . urlencode($key_value);
        } return implode('&', $query_array);
    }

    
    public function processString($string, $from, $to) {
        $fstart = stripos($string, $from);
        $tmp = substr($string, $fstart + strlen($from));
        $flen = stripos($tmp, $to);
        return substr($tmp, 0, $flen);
    }
}
?>
