<?php
/**
 * Faqs extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php

 * @category   FME
 * @package    Faqs
 * @author     Kamran Rafiq Malik <kamran.malik@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Faqs_Helper_Data extends Mage_Core_Helper_Abstract
{


	const XML_PATH_ENABLED						=	'faqs/general/enabled';
	const XML_PATH_LIST_PAGE_TITLE				=	'faqs/list/page_title';
	const XML_PATH_LIST_IDENTIFIER				=	'faqs/list/identifier';
	const XML_PATH_LIST_META_DESCRIPTION		=	'faqs/list/meta_description';
	const XML_PATH_LIST_META_KEYWORDS			=	'faqs/list/meta_keywords';
	
	const XML_PATH_DETAIL_TITLE_PREFIX				=	'faqs/detail/title_prefix';
	const XML_PATH_DETAIL_DEFAULT_META_DESCRIPTION	=	'faqs/detail/default_meta_description';
	const XML_PATH_DETAIL_DEFAULT_META_KEYWORDS		=	'faqs/detail/default_meta_keywords';
	const XML_PATH_SEO_URL_SUFFIX					=	'faqs/seo/url_suffix';

	
	public function getListPageTitle()
	{
		return Mage::getStoreConfig(self::XML_PATH_LIST_PAGE_TITLE);
	}
	
	public function getListIdentifier()
	{
		$identifier = Mage::getStoreConfig(self::XML_PATH_LIST_IDENTIFIER);
		if ( !$identifier ) {
			$identifier = 'faqs';
		}
		return $identifier;
	}
	
	public function getUrl($identifier = null)
	{
		
		if (is_null($identifier)) {
			$url = Mage::getUrl('') . self::getListIdentifier() . self::getSeoUrlSuffix();
		} else {
			$url = Mage::getUrl(self::getListIdentifier()) . $identifier . self::getSeoUrlSuffix();
		}
		return $url;
		
	}
		
	public function getListMetaDescription()
	{
		return Mage::getStoreConfig(self::XML_PATH_LIST_META_DESCRIPTION);
	}
	
	public function getListMetaKeywords()
	{
		return Mage::getStoreConfig(self::XML_PATH_LIST_META_KEYWORDS);
	}
	
	public function getSeoUrlSuffix()
	{
		return Mage::getStoreConfig(self::XML_PATH_SEO_URL_SUFFIX);
	}
	
	public function getDetailDefaultMetaDescription()
	{
		return Mage::getStoreConfig(self::XML_PATH_DETAIL_DEFAULT_META_DESCRIPTION);
	}
	
	public function getDetailDefaultMetaKeywords()
	{
		return Mage::getStoreConfig(self::XML_PATH_DETAIL_DEFAULT_META_KEYWORDS);
	}
	
	public function getDetailTitlePrefix()
	{
		return Mage::getStoreConfig(self::XML_PATH_DETAIL_TITLE_PREFIX);
	}
	
	public function recursiveReplace($search, $replace, $subject)
    {
        if(!is_array($subject))
            return $subject;

        foreach($subject as $key => $value)
            if(is_string($value))
                $subject[$key] = str_replace($search, $replace, $value);
            elseif(is_array($value))
                $subject[$key] = self::recursiveReplace($search, $replace, $value);

        return $subject;
    }

    public function extensionEnabled($extension_name)
	{
		$modules = (array)Mage::getConfig()->getNode('modules')->children();
		if (!isset($modules[$extension_name])
			|| $modules[$extension_name]->descend('active')->asArray()=='false'
			|| Mage::getStoreConfig('advanced/modules_disable_output/'.$extension_name)
		) return false;
		return true;
	}
	
	public function strip_only($str, $tags, $stripContent = false) {
		$content = '';
		if(!is_array($tags)) {
			$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
			if(end($tags) == '') array_pop($tags);
		}
		foreach($tags as $tag) {
			if ($stripContent)
				 $content = '(.+</'.$tag.'[^>]*>|)';
			 $str = preg_replace('#</?'.$tag.'[^>]*>'.$content.'#is', '', $str);
		}
		return $str;
	} 
	
	/***************************************************************
     this function draws the path of FME_Faqs folder on local
     and returns the path to the frontend from where it is called
    ***************************************************************/
      public function getSecureImageUrl() {
		  
		$path = Mage::getBaseUrl('media');
		$pos =strripos($path,'media');
		$apppath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS) . 'faqs/FME_FAQS_CAPTCHA' . DS . 'captcha/';
		return $apppath;
       
    }
    /***************************************************************
     this function gets a new unique value by sending request to the
     assign_rand_value() function which returns a character and it
     adds the character in its variable and returns to the form at
     frontend
    ***************************************************************/
    
    function getNewrandCode($length) {
		
		if($length>0) { 
			$rand_id="";
			for($i=1; $i<=$length; $i++) {
				mt_srand((double)microtime() * 1000000);
				$num = mt_rand(1,36);
				$rand_id .= $this->assign_rand_value($num);
			}
		}
		return $rand_id;
	}
	
	function assign_rand_value($num)
	{
		//accepts 1 - 36
		switch($num) {
			case "1":
			 $rand_value = "a";
			break;
			case "2":
			 $rand_value = "b";
			break;
			case "3":
			 $rand_value = "c";
			break;
			case "4":
			 $rand_value = "d";
			break;
			case "5":
			 $rand_value = "e";
			break;
			case "6":
			 $rand_value = "f";
			break;
			case "7":
			 $rand_value = "g";
			break;
			case "8":
			 $rand_value = "h";
			break;
			case "9":
			 $rand_value = "i";
			break;
			case "10":
			 $rand_value = "j";
			break;
			case "11":
			 $rand_value = "k";
			break;
			case "12":
			 $rand_value = "z";
			break;
			case "13":
			 $rand_value = "m";
			break;
			case "14":
			 $rand_value = "n";
			break;
			case "15":
			 $rand_value = "o";
			break;
			case "16":
			 $rand_value = "p";
			break;
			case "17":
			 $rand_value = "q";
			break;
			case "18":
			 $rand_value = "r";
			break;
			case "19":
			 $rand_value = "s";
			break;
			case "20":
			 $rand_value = "t";
			break;
			case "21":
			 $rand_value = "u";
			break;
			case "22":
			 $rand_value = "v";
			break;
			case "23":
			 $rand_value = "w";
			break;
			case "24":
			 $rand_value = "x";
			break;
			case "25":
			 $rand_value = "y";
			break;
			case "26":
			 $rand_value = "z";
			break;
			case "27":
			 $rand_value = "0";
			break;
			case "28":
			 $rand_value = "1";
			break;
			case "29":
			 $rand_value = "2";
			break;
			case "30":
			 $rand_value = "3";
			break;
			case "31":
			 $rand_value = "4";
			break;
			case "32":
			 $rand_value = "5";
			break;
			case "33":
			 $rand_value = "6";
			break;
			case "34":
			 $rand_value = "7";
			break;
			case "35":
			 $rand_value = "8";
			break;
			case "36":
			 $rand_value = "9";
			break;
		}
		return $rand_value;
	}
	
	public function getUserName()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return trim($customer->getName());
    }

    public function getUserEmail()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->getEmail();
    }
	
	/**
	 * Splits images Path and Name
	 *
	 * Path=custom/module/images/
	 * Name=example.jpg
	 *
	 * @param string $imageValue
	 * @param string $attr
	 * @return string
	 */
	public function splitImageValue($imageValue,$attr="name"){
		$imArray=explode("/",$imageValue);

		$name=$imArray[count($imArray)-1];
		$path=implode("/",array_diff($imArray,array($name)));
		if($attr=="path"){
			return $path;
		}
		else
			return $name;

	}
	
	public function getWysiwygFilter($data)
	{
		$helper = Mage::helper('cms');
		$processor = $helper->getPageTemplateProcessor();
		return $processor->filter($data);
	}

}