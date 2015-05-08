<?php
   // echo 'maintenance is going on...';
   //  exit;
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
  
 ini_set('max_execution_time', 0);
ini_set('max_input_time', 0);

set_time_limit(0);

if (version_compare(phpversion(), '5.2.0', '<')===true) {
    echo  '<div style="font:12px/1.35em arial, helvetica, sans-serif;">
<div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
<h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">
Whoops, it looks like you have an invalid PHP version.</h3></div><p>Magento supports PHP 5.2.0 or newer.
<a href="http://www.magentocommerce.com/install" target="">Find out</a> how to install</a>
 Magento using PHP-CGI as a work-around.</p></div>';
    exit;
}

/**
 * Error reporting
 */
ini_set('display_errors',1);
error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(4);

/**
 * Compilation includes configuration file
 */
define('MAGENTO_ROOT', getcwd());

$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile = 'maintenance.flag';

if (!file_exists($mageFilename)) {
    if (is_dir('downloader')) {
        header("Location: downloader");
    } else {
        echo $mageFilename." was not found";
    }
    exit;
}

if (file_exists($maintenanceFile)) {
    include_once dirname(__FILE__) . '/errors/503.php';
    exit;
}

require_once $mageFilename;

#Varien_Profiler::enable();

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
}
#Mage::setIsDeveloperMode(true);
#ini_set('display_errors', 1);

umask(0);

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

 switch($_SERVER['HTTP_HOST']) {
    
		
	
	case 'tablethrows.co.nz':
    case 'www.tablethrows.co.nz':
        $mageRunCode = 'tablethrows_com_au';
        $mageRunType = 'website';
    break;


	case 'tablethrows.com.au':
    case 'www.tablethrows.com.au':
        $mageRunCode = 'tablethrows_com_au';
        $mageRunType = 'website';
    break;	
	
    case '13expo.com.au':
    case 'www.13expo.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break; 
	
	/*  case 'outdoorbannershop.com.au':
    case 'www.outdoorbannershop.com.au':
        $mageRunCode = 'outdoorbannershop_com_au';
        $mageRunType = 'website';
    break;
	*/
	case 'meshbannersprinting.com.au':
    case 'www.meshbannersprinting.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;
	
	
	case '13expo.com.au/m':
    case 'www.13expo.com.au/m':
        $mageRunCode = 'vividadscomau_mobile';
        $mageRunType = 'website';
    break; 
	
	case 'vividads.com.sg':
    case 'www.vividads.com.sg':
        $mageRunCode = 'vividads_com_au'; 
        $mageRunType = 'website'; 
    break;
	 
	 
	 
	case 'shop.vividads.com.sg':
    case 'www.shop.vividads.com.sg':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;	 
	 
	case 'shop.vividads.co.nz':
    case 'www.shop.vividads.co.nz':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;	
	
	case 'vividads.co.nz':
    case 'www.vividads.co.nz':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;	  
	
	case 'shop.vividads.com':
    case 'www.shop.vividads.com':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;	 
		
	case 'shop.vividads.com.au':
    case 'www.shop.vividads.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;	 	
	
	case 'redcarpetbackdrops.com.au':
    case 'www.redcarpetbackdrops.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;
	 
	
	
	case 'eventtablethrows.com.au':
    case 'www.eventtablethrows.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;	
	
	
	case 'outdoorbanner.com.au':
    case 'www.outdoorbanner.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;
	
	
	case 'usedbooths.com.au':
    case 'www.usedbooths.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;	

	case 'vividexhibits.com.au':
    case 'www.vividexhibits.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;		
	
	case 'custombooths.com.au':
    case 'www.custombooths.com.au':
        $mageRunCode = 'custombooths_com_au';
        $mageRunType = 'website';
    break; 
	
	
	case 'printedtablecloths.com.au':
    case 'www.printedtablecloths.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;	
	       	 
	
	case 'mesh-banner.com.au':
    case 'www.mesh-banner.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;	 
	
	case 'expobooths.com.au':
    case 'www.expobooths.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;	 
	
	
	case 'usedbooths.com.au/m':
    case 'www.usedbooths.com.au/m':
        $mageRunCode = 'vividadscomau_mobile';
        $mageRunType = 'website';
    break;	

	
	
    case 'tablethrowsdirect.com':
    case 'www.tablethrowsdirect.com':
        $mageRunCode = 'tablethrows_com_au';
        $mageRunType = 'website';
    break;	 


	case 'tablethrows.co.uk':
    case 'www.tablethrows.co.uk':
        $mageRunCode = 'tablethrows_com_au';
        $mageRunType = 'website';
    break;


	case 'tablethrows.com.sg':
    case 'www.tablethrows.com.sg':
        $mageRunCode = 'tablethrows_com_au';
        $mageRunType = 'website';
    break;
	
	
	case 'tablethrowsdirect.com.au':
    case 'www.tablethrowsdirect.com.au':
        $mageRunCode = 'tablethrows_com_au';
        $mageRunType = 'website';
    break;

   /* case 'outdoorbannershop.com':
    case 'www.outdoorbannershop.com':
        $mageRunCode = 'outdoorbannershop_com';
        $mageRunType = 'website';
    break;
*/

case 'outdoorbannershop.com.au':
    case 'www.outdoorbannershop.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;


/* redirecting the mobile sites */

    case 'm.vividads.com.au':     
        $mageRunCode = 'vividadscomau_mobile';
        $mageRunType = 'website';
    break;



	
	/*
	case 'vividads.com.au':
    case 'www.vividads.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
	
	/*
	case 'shop.usedbooths.com.au':
    case 'www.usedbooths.com.au':
        $mageRunCode = 'vividads_com_au';
        $mageRunType = 'website';
    break;
	*/
	
         	 
	 
} 

Mage::run($mageRunCode, $mageRunType);
//echo Mage::app()->getLayout()->getUpdate()->load("default")->asString();
