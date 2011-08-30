<?php
/*
Plugin Name: Liz Comment Counter by Ozh
Plugin URI: http://planetozh.com/blog/my-projects/liz-strauss-comment-count-badge-widget-wordpress/
Description: Liz Strauss' Comment Count Badge. Got a vibrant community of commenters? Show it off! <strong>for WordPress 2.8+ only</strong>
Version: 1.1.3
Author: Ozh
Author URI: http://planetOzh.com
*/

/* Release History :
 * 1.0       Initial release
 * 1.0.1     Fixed: options with checkbox would not be stored
             Changed: URL to plugin page also link by badge itself
 * 1.0.2     Added: ru_RU by fatcow.com
 * 1.1       Updated: fixing the fucking widget for WP 2.8. I hate those fucking widgets.
			 Removed: split logic between widget & option page
             Updated: filters for custom icon & plugin action links
 * 1.1.1     Added: be_BY by ilyuha (http://antsar.info/)
 * 1.1.2     Added: es_ES by David (Vera)
 * 1.1.3     Added: de_DE by Design Contest
*/

global $wp_ozh_lcc;

function wp_ozh_lcc_init() {
	global $wp_ozh_lcc;
	if ( !defined('WP_CONTENT_URL') )
		define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
	if ( !defined('WP_CONTENT_DIR') )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

	wp_ozh_lcc_require('core.php');
	wp_ozh_lcc_defaults(); // Populate $wp_ozh_lcc
	
	// Load translation file if any. The load_plugin_textdomain() wrapper seriously sucks in 2.6 by the way.
	$locale = get_locale();
	$mofile = wp_ozh_lcc_plugindir().'/translations/commentbadge' . '-' . $locale . '.mo';
	load_textdomain('commentbadge', $mofile);

	// Widget & option page
	wp_ozh_lcc_require('widget.php');
	add_action('widgets_init', 'wp_ozh_lcc_widget_init');
	add_action('admin_menu', 'wp_ozh_lcc_add_page', -10);
	
	// Custom icon in Ozh' Admin Drop Down Menu
	add_filter( 'ozh_adminmenu_icon_ozh_lcc', 'wp_ozh_lcc_customicon');

}

function wp_ozh_lcc_add_page() {
	add_options_page('Liz Comment Counter by Ozh', 'Liz Comment Counter', 8, 'ozh_lcc', 'wp_ozh_lcc_options_page_includes');
}

function wp_ozh_lcc_options_page_includes() {
	wp_ozh_lcc_require('optionpage.php');
	wp_ozh_lcc_options_page();
}

function wp_ozh_lcc_plugin_actions($links) {
	$links[] = "<a href='options-general.php?page=ozh_lcc'><b>".wp_ozh_lcc__('Settings').'</b></a>';
	return $links;
}

// Triggered when a visitor comments on your blog
function wp_ozh_lcc_newcomment_public($id = 0, $status = '') {
	if ($status === 1)
		wp_ozh_lcc_generate();
}


// Triggered when a privileged user manages comments from the admin area
function wp_ozh_lcc_newcomment_admin($id = 0, $status = '') {
	switch($status) {
	case 'approve':
	case 'delete':
	case 'hold':
	case 'spam':
		wp_ozh_lcc_generate();
		break;
	default:
		// in case WP implements future options?
	}
}

// Generate a new badge
function wp_ozh_lcc_generate() {
	wp_ozh_lcc_require('generate.php');
	wp_ozh_lcc_generate_badge();
}

// Include stuff from the 'inc' subdir
function wp_ozh_lcc_require($file) {
	require_once(dirname(__FILE__).'/inc/'.$file);
}

// Just activated ? Generate our first badge!
function wp_ozh_lcc_activate() {
	wp_ozh_lcc_require('core.php');
	wp_ozh_lcc_generate();
}

add_action('plugins_loaded', 'wp_ozh_lcc_init');
add_action('comment_post', 'wp_ozh_lcc_newcomment_public', 9999, 2); // someone posts a comment
add_action('wp_set_comment_status', 'wp_ozh_lcc_newcomment_admin', 9999, 2); // admin manages comments
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wp_ozh_lcc_plugin_actions', 10, 2);
add_action('activate_' . plugin_basename( __FILE__), 'wp_ozh_lcc_activate' );

?>