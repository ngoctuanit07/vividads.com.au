<?php
/**
 * Email Template Manager
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitemails
 * @version      2.0.13
 * @license:     POwWoRanFs2vVHb6wy050abrHO3ftlyCeMSf01dXU3
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/
class Aitoc_Aitemails_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getEmailTemplateDefaultFilePath($file, $localeCode = null)
    {
        $type = 'email';
        if (is_null($localeCode) || preg_match('/[^a-zA-Z_]/', $localeCode)) {
            $localeCode = Mage::app()->getLocale()->getLocaleCode();
        }
        $filePath = Mage::getBaseDir('locale')  . DS
                  . $localeCode . DS . 'template' . DS . $type . DS . $file;
        if (!file_exists($filePath)) { // If no template specified for this locale, use store default
            $filePath = Mage::getBaseDir('locale') . DS
                      . Mage::app()->getLocale()->getDefaultLocale()
                      . DS . 'template' . DS . $type . DS . $file;
        }
        if (!file_exists($filePath)) {  // If no template specified as  store default locale, use en_US
            $filePath = Mage::getBaseDir('locale') . DS
                      . Mage_Core_Model_Locale::DEFAULT_LOCALE
                      . DS . 'template' . DS . $type . DS . $file;
        }
        $filePath = str_replace(Mage::getBaseDir(), '', $filePath);
        return (string) $filePath;
    }
    
    public function replaceCharsAtCnt($sString, $cCharFrom, $cCharTo, $aCnt)
    {
        if (!is_array($aCnt))
        {
            $aCnt = array($aCnt);
        }
        
        $iCharFoundCnt = 0;
        for ($i = 0; $i < strlen($sString); $i++)
        {
            if ($sString{$i} == $cCharFrom)
            {
                $iCharFoundCnt++;
                if (in_array($iCharFoundCnt, $aCnt))
                {
                    $sString{$i} = $cCharTo;
                }
            }
        }
        
        return $sString;
    }
    
    public function getPathByEmailTemplateCode($templateCode)
    {
        $sections       = Mage::getSingleton('adminhtml/config')->getSections()->asArray();

        $iUnderscoreCnt = substr_count($templateCode, '_');
        
        // checking sections -> groups -> fields
        for ($i = 1; $i < $iUnderscoreCnt; $i++)
        {
            for ($j = $i + 1; $j <= $iUnderscoreCnt; $j++)
            {
                $sPathString = $this->replaceCharsAtCnt($templateCode, '_', '/', array($i, $j));
                $aPath = explode('/', $sPathString);
                if (isset($sections[$aPath[0]]['groups'][$aPath[1]]['fields'][$aPath[2]]))
                {
                    return $sPathString;
                }
            }
        }
        
        // checking sections -> groups if nothing found in fields
        for ($i = 1; $i <= $iUnderscoreCnt; $i++)
        {
            $sPathString = $this->replaceCharsAtCnt($templateCode, '_', '/', $i);
            $aPath = explode('/', $sPathString);
            if (isset($sections[$aPath[0]]['groups'][$aPath[1]]))
            {
                return $sPathString;
            }
        }
        
        return $templateCode;
    }
}