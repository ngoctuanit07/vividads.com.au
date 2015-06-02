<?php

require_once WPNGG_NIVOSLIDER_PLUGIN_DIR . '/includes/ngg-nivoslider-widget.php';
require_once WPNGG_NIVOSLIDER_PLUGIN_DIR . '/includes/ngg-nivoslider-admin.php';

class NGG_NivoSlider {
	public static $pluginPath;
	public static $pluginUrl;

	public function __construct() {
		self::$pluginPath = dirname(__FILE__);
		self::$pluginUrl = plugins_url( '', WPNGG_NIVOSLIDER_PLUGIN_BASENAME );

		add_action( 'widgets_init', create_function('', 'return register_widget("NGG_NivoSlider_Widget");') );
		add_shortcode( 'ngg-nivoslider', array($this, 'shortcode_handler'));

		if ( !is_admin() ) {
			add_action( 'init', array($this, 'register_scripts'));
			add_action( 'init', array($this, 'enqueue_styles'));
		}
	}

	public function register_scripts() {
		if( !is_admin() ) {
			wp_register_script( 'jquery-nivo-slider', self::$pluginUrl . '/script/jquery.nivo.slider.js', array('jquery'), '2.4', false );
			wp_register_script( 'jquery-shuffle', self::$pluginUrl .'/script/jquery.jj_ngg_shuffle.js', array('jquery'), '', false );
		}
	}

	public function enqueue_styles() {
		if( !is_admin() ) {
			wp_enqueue_style( 'jquery-plugins-slider-style', self::$pluginUrl . '/stylesheets/nivo-slider.css', array(), '', 'all' );

			$theme = get_option('ngg_nivoslider_theme', 'default');
			wp_enqueue_style( 'ngg-nivoslider-theme', self::$pluginUrl . '/themes/'. $theme . '/' . $theme . '.css', array(), '', 'all' );
		}
	}

	private function use_default($instance, $key) {
		return !array_key_exists($key, $instance) || trim($instance[$key]) == '';
	}

	public function shortcode_handler($atts) {
		// Enqueue script within shortcode handler for conditional loading
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-nivo-slider' );
		wp_enqueue_script( 'jquery-shuffle' );

		$instance = array();
		foreach($atts as $att => $val) {
			$instance[$att] = $val;
		}

		// Set defaults
		if($this->use_default($instance, 'html_id')) { $instance['html_id'] = 'slider'; }
		if($this->use_default($instance, 'order')) { $instance['order'] = 'random'; }
		if($this->use_default($instance, 'center')) { $instance['center'] = '1'; }
		$instance['shortcode'] = '1';

  		ob_start();
 		the_widget("NGG_NivoSlider_Widget", $instance, array());
 		$output = ob_get_contents();
 		ob_end_clean();
 		return $output;
	}
}