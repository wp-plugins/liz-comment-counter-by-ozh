<?php
/*
Part of Plugin: Liz Comment Counter by Ozh
http://planetozh.com/blog/my-projects/liz-strauss-comment-count-badge-widget-wordpress/
*/

/* This file updates the badge (ie triggers generation of a new one) */

// Generate a new badge. If $update == true then read settings
function wp_ozh_lcc_generate_badge($update = true) {
	global $wp_ozh_lcc, $wp_ozh_lcc_badge;
	
	if ($update) {
		wp_ozh_lcc_defaults();
		wp_ozh_lcc_update_count();
	}
	
	$dir = wp_ozh_lcc_uploaddir();
	$dir = $dir['path'];
	
	if (!wp_mkdir_p($dir)) {
		return false;
	}
	
	$wp_ozh_lcc_badge['filename'] = $dir.'/cmt_badge.png';
	wp_ozh_lcc_require('badge.php'); // $wp_ozh_lcc being set, wp_ozh_lcc_badge_makebadge() will save the image instead of returning it to the browser
}

// Update comment count and save it in our db entry
function wp_ozh_lcc_update_count() {
	global $wp_ozh_lcc;
	$count = wp_ozh_lcc_get_comment_count();
	$wp_ozh_lcc['count'] = $count['total'];
	if (!update_option('ozh_lcc', $wp_ozh_lcc))
		add_option('ozh_lcc', $wp_ozh_lcc);
}

?>