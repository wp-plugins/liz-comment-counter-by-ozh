<?php
/*
Part of Plugin: Liz Comment Counter by Ozh
http://planetozh.com/blog/my-projects/liz-strauss-comment-count-badge-widget-wordpress/
*/

/* This file draws the option page when used as a regular plugin (vs a widget) */

global $wp_ozh_lcc;

function wp_ozh_lcc_options_page() {
	global $wp_ozh_lcc;
	
	wp_ozh_lcc_require('options.php');

	if (isset($_POST['lcc_action']) )
		wp_ozh_lcc_processform();
	
	add_action('in_admin_footer', 'wp_ozh_lcc_footer');
	
	echo '
    <div class="wrap">
    <h2>Liz Comment Counter by Ozh</h2>
    <form method="post" action="">
';
	wp_nonce_field('ozh-lcc');
	
	wp_ozh_lcc_option_table(); // The form itself, from options.php
	
	echo '
	<p class="submit">
	<input name="submit" value="'.wp_ozh_lcc__('Save Changes').'" type="submit" />
	</p>

	</form>
	</div>
	
	<div class="wrap"><h2>'.wp_ozh_lcc__('Reset Settings').'</h2>
	<form method="post" action="">
';
	wp_nonce_field('ozh-lcc');
	echo '
    <input type="hidden" name="lcc_action" value="reset_options">

	<p>'.wp_ozh_lcc__('Clicking the following button will remove all the plugin settings from your database. You might want to do so in the following cases:').'</p>
	<ul>
	<li>'.wp_ozh_lcc__('you want to uninstall the plugin and leave no unnecessary entries in your database').'</li>
	<li>'.wp_ozh_lcc__('you want all settings to be reverted to their default values').'</li>
	</ul>
	<p class="submit" style="border-top:0px;padding:0;"><input style="color:red" name="submit" value="'.wp_ozh_lcc__('Reset Settings').'" onclick="return(confirm(\''.wp_ozh_lcc__('Really do?').'\'))" type="submit" /></p>
	<p>'.wp_ozh_lcc__('There is no undo, so be very sure you want to click the button!').'</p>
	
	</form>
	</div>
';

}

function wp_ozh_lcc_footer() {
	$data = get_plugin_data(dirname(dirname(__FILE__)).'/wp_ozh_lcc.php');
	$version = $data['Version'];
	echo "<a href='http://planetozh.com/blog/my-projects/liz-strauss-comment-count-badge-widget-wordpress/'>Liz Comment Counter</a> by <a href='http://planetozh.com/blog/'>Ozh</a> &mdash; $version<br/>";
}

?>
