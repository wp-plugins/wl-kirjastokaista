<?php

/*
	Shortcode handling
*/




add_action( 'init', 'kirjastokaista_shortcode_settings_init' );

global $settings;
$settings = get_option( "kirjastokaista_settings" );


/* Load Public Settings */
function kirjastokaista_shortcode_settings_init() {
	global $settings;
	if ( empty( $settings ) ) {
		$settings = array(
			'kirjastokaista_allow_text_widget' => false
		);
		add_option( "kirjastokaista_settings", $settings, '', 'yes' );
	}
	if ( isset($settings["kirjastokaista_allow_text_widget"] )) {
		add_filter( 'widget_text', 'shortcode_unautop');
		add_filter( 'widget_text', 'do_shortcode');
	}
}

add_shortcode("kirjastokaista", "kirjastokaista_shortcode_handler");


function kirjastokaista_shortcode_scripts() {
	global $settings;
	if ( $settings["kirjastokaista_use_jcarousel"] ) {
		wp_register_style ('kirjastokaista_jcarousel-css', plugins_url( 'kirjastokaista/css/jcarousel.responsive.css' ));
		wp_enqueue_style('kirjastokaista_jcarousel-css');
		wp_register_script ('kirjastokaista_jcarousel-cdn', plugins_url( 'kirjastokaista/js/jquery.jcarousel.min.js' ));
		wp_enqueue_script('kirjastokaista_jcarousel-cdn');
		wp_register_script ('kirjastokaista_jcarousel-autoscroll', plugins_url( 'kirjastokaista/js/jquery.jcarousel-autoscroll.min.js' ));
		wp_enqueue_script('kirjastokaista_jcarousel-autoscroll');
		wp_register_script ('kirjastokaista_jcarousel', plugins_url( 'kirjastokaista/js/jcarousel.responsive.js' ));
		wp_enqueue_script('kirjastokaista_jcarousel');
	}
}

add_action( 'wp_footer', 'kirjastokaista_shortcode_scripts' );

