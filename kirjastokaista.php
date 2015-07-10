<?php
/*
Plugin Name: WL Kirjastokaista
Plugin URI: http://www.kirjastokaista.fi
Description: This plugin provides a very easy and efficient way to embed videos from Kirjastokaista (Library Channel) service in your posts, pages and widgets.
Version: 1.0
Author: Buskerud fylkesbibliotek/Webløft & Jonni Tammisto
Author URI: http://webloft.no
License: GPLv2
*/




require "functions.php";

add_action( 'plugins_loaded', 'kirjastokaista_load_textdomain' );
// Load translations
function kirjastokaista_load_textdomain() {
  load_plugin_textdomain( 'kirjastokaista', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

global $kirjastokaista_url;
$kirjastokaista_url = "www.kirjastokaista.fi";


add_action( 'init', 'kirjastokaista_global_texts_init' );
function kirjastokaista_global_texts_init() {

	global $kirjastokaista_ui_list;
	$kirjastokaista_ui_list = array(
		'fi' => array('name' => __('Kirjastokaista', 'kirjastokaista'), 'url' => 'http://www.kirjastokaista.fi'),
		'sv' => array('name' => __('Bibliotekskanalen', 'kirjastokaista'), 'url' => 'http://www.kirjastokaista.fi/sv'),
		'en' => array('name' => __('Library Channel', 'kirjastokaista'), 'url' => 'http://www.kirjastokaista.fi/en'),
		'no' => array('name' => __('KanalB', 'kirjastokaista'), 'url' => 'http://www.kanalb.no')
	);

	global $kirjastokaista_medialanguages;
	$kirjastokaista_medialanguages = array('fi'=>__('finnish', 'kirjastokaista'), 'sv'=>__('swedish', 'kirjastokaista'), 'en'=>__('english', 'kirjastokaista'), 'no'=>__('norwegian', 'kirjastokaista'), 'ru'=>__('russian', 'kirjastokaista') );

	global $kirjastokaista_mediatypes;						
	$kirjastokaista_mediatypes = array('video'=>__('video', 'kirjastokaista'), 'audio'=>__('audio', 'kirjastokaista') );

	global $activate_notice;
	$activate_notice = __( 'If you want to use Kirjastokaista shortcodes in widgets, remember to allow shortcodes in widgets on WL Kirjastokaista > Settings page', 'kirjastokaista' );
}

function kirjastokaista_activate() {
	$settings['kirjastokaista_show_embed_editor']	= true;
	$settings['kirjastokaista_use_jcarousel']		= true;
	$settings['kirjastokaista_notice'] = true;
	$updated = update_option( "kirjastokaista_settings", $settings );

	if (!defined('KIRJASTOKAISTA_VERSION_KEY'))
	    define('KIRJASTOKAISTA_VERSION_KEY', 'kirjastokaista_version');

	if (!defined('KIRJASTOKAISTA_VERSION_NUM'))
	    define('KIRJASTOKAISTA_VERSION_NUM', '1.0.0');

	add_option(KIRJASTOKAISTA_VERSION_KEY, KIRJASTOKAISTA_VERSION_NUM);
}

register_activation_hook( __FILE__, 'kirjastokaista_activate' );

function kirjastokaista_notice_message(){
	$settings = get_option( "kirjastokaista_settings" );
	if (!empty($settings['kirjastokaista_notice'])) {
		global $activate_notice;
		echo '<div class="updated"><p>'.$activate_notice.'</p></div>';
		unset($settings['kirjastokaista_notice']);
		delete_option('kirjastokaista_settings');
		$updated = update_option( "kirjastokaista_settings", $settings );
	}
}

add_action('admin_notices', 'kirjastokaista_notice_message');


/* Action links for settings page */
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'kirjastokaista_add_action_links' );

function kirjastokaista_add_action_links ( $links ) {
 $mylinks = array(
 '<a href="' . admin_url( 'tools.php?page=kirjastokaista&tab=settings' ) . '">'.__('Settings', 'kirjastokaista').'</a>',
 );
return array_merge( $links, $mylinks );
}


/* If in Administrator panel load Tools */
if (is_admin()) {
	require 'kirjastokaista-tools.php';
}
require 'kirjastokaista-shortcode.php';	





?>