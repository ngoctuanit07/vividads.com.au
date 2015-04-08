<?php
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
error_reporting(E_ALL | E_STRICT);

/**
 * Compilation includes configuration file
 */
define('MAGENTO_ROOT', getcwd());

$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}

//$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$mageFilename =  '../app/Mage.php';
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

#ini_set('display_errors', 1);

umask(0);

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';
/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

$_host = $_SERVER['HTTP_HOST'];


 //Zend_debug::dump($_host);
 //exit;
	
 switch($_host) {
    
	case 'vividads.com.au':
            $mageRunCode = 'vividadscomau_mobile';
        	$mageRunType = 'website';
    break;	
	case '13expo.com.au':
            $mageRunCode = 'expo_mobile';
        	$mageRunType = 'website';
    break;	
	case 'tablethrows.com.au':
            $mageRunCode = 'tablethrows_mobile';
        	$mageRunType = 'website';
    break;	
		
	case 'meshbannersprinting.com.au':
            $mageRunCode = 'meshbannersprinting_com_au';
        	$mageRunType = 'website';
    break;
	
	case 'tablthrows.co.nz':
            $mageRunCode = 'tablethrows_co_nz';
        	$mageRunType = 'website';
    break;
	case 'outdoorbannershop.com.au':
            $mageRunCode = 'outdoorbannershop_com_au';
        	$mageRunType = 'website';
    break;
	
	case 'redcarpetbackdrops.com.au':
            $mageRunCode = 'redcarpetbackdrops_mobile';
        	$mageRunType = 'website';
    break;
	
	case 'media-wall.com.au':
            $mageRunCode = 'media_wall_mobile';
        	$mageRunType = 'website';
    break;	

	case 'crowdcontrol-barriers.com.au':
            $mageRunCode = 'crowdcontrol_barriers_mobile';
        	$mageRunType = 'website';
    break;		

	case 'constructionsitebanners.com.au':
            $mageRunCode = 'constructionsitebanners_mobile';
        	$mageRunType = 'website';
    break;		

	case 'fence-wraps.com.au':
            $mageRunCode = 'fence_wraps_mobile';
        	$mageRunType = 'website';
    break;		

	case 'banner-factory.com.au':
            $mageRunCode = 'banner_factory_mobile';
        	$mageRunType = 'website';
    break;		

	case 'banners-direct.com.au':
            $mageRunCode = 'banners_direct_mobile';
        	$mageRunType = 'website';
    break;		

	case 'bannerscheap.com.au':
            $mageRunCode = 'bannerscheap_mobile';
        	$mageRunType = 'website';
    break;	
	
	
				
	
  }
 //exit;




Mage::run($mageRunCode, $mageRunType);
