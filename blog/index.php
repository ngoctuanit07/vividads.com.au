<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);
 
include_once($_SERVER['DOCUMENT_ROOT'].'app/Mage.php');
 $app = Mage::app();

 $cart = Mage::getModel('customer/session');
 //var_dump($cart);
 

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
