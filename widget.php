<?php
/*
Part of Plugin: Liz Comment Counter by Ozh
http://planetozh.com/blog/my-projects/liz-strauss-comment-count-badge-widget-wordpress/
*/

/* This file handles the widget functions when used as a widget (vs a regular plugin) */

global $wp_ozh_lcc;

// Display the badge in sidebar
function widget_ozh_lcc($args) {
	global $wp_ozh_lcc;
	extract($args);
	$title = $wp_ozh_lcc['title'];

	echo $before_widget . $before_title . $title . $after_title;
	wp_ozh_lcc_badge();
	echo $after_widget;
}

// Control stuff for the Widgets admin page
function widget_ozh_lcc_control() {
	global $wp_ozh_lcc;
	
	wp_ozh_lcc_require('options.php');

	if (isset($_POST['lcc_action']))
		wp_ozh_lcc_processform();

	echo "<style type='text/css'>
	.widget-control .form-table th, .widget-control .form-table td {border-bottom-style: hidden;	}
	.lcc_pickercell {width: 100px;}
</style>
";

	wp_ozh_lcc_option_table(); // The form itself, from options.php
}

?>