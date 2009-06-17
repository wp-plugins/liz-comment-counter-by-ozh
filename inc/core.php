<?php
/*
Part of Plugin: Liz Comment Counter by Ozh
http://planetozh.com/blog/my-projects/liz-strauss-comment-count-badge-widget-wordpress/
*/

/* This file contains all the general functions */

global $wp_ozh_lcc;

// Main function that actually displays the badge. Called either by the widget or manually in your PHP files (eg sidebar.php)
function wp_ozh_lcc_badge() {
	global $wp_ozh_lcc;
	
	extract($wp_ozh_lcc); // $fg, $bg, $count, $label, $linkback, $fontname, $fontspacing, $title
	extract(wp_ozh_lcc_uploaddir()); // $path, $url
	
	$src = $path.'/cmt_badge.png';
	if (file_exists($src)) {
		$url = $url . "/cmt_badge.png?c=$count";
	} else {
		$url = wp_ozh_lcc_pluginurl() . "/inc/badge.php?fg=$fg&amp;bg=$bg&amp;label=$label&amp;count=$count&amp;font=$fontname&amp;spacing=$fontspacing";
	}

	$badge = "<img id=\"comment_count_badge\" border=\"0\" width=\"88\" height=\"19\" src=\"$url\" alt=\"Comment Count Badge\" />";
	if ($linkback) {
		printf ("<a href=\"http://planetozh.com/blog/my-projects/liz-strauss-comment-count-badge-widget-wordpress/\">%s</a>", $badge);
		echo "<span style='display:block;font-family:sans-serif;font-size:9px'>Get your <a title='Liz&rsquo;s Comment Count Badge By Ozh' href='http://planetozh.com/blog/my-projects/liz-strauss-comment-count-badge-widget-wordpress/'>Badge</a>!</span>";
	} else {
		echo $badge;
	}
}

// Read plugin settings or set default values
function wp_ozh_lcc_defaults() {
	global $wp_ozh_lcc;
	$defaults = array(
		'fg' => '444444',
		'bg' => '99CCFF',
		'label' => 'comments',
		'linkback' => 1,
		'count' => 0,
		'title' => '',
		'fontspacing' => 1,
		'fontname' => 'trebuchet.ttf'
	);
		
	if (!count($wp_ozh_lcc))
		$wp_ozh_lcc = array_map('stripslashes',(array)get_option('ozh_lcc'));
	
	unset($wp_ozh_lcc[0]); // produced by the (array) casting
	
	foreach ($defaults as $k=>$v) {
		if (!isset($wp_ozh_lcc[$k]))
			$wp_ozh_lcc[$k] = $v;
	}
	
}

// Translation wrapper: alias for __($string, 'commentbadge')
function wp_ozh_lcc__($string) {
	return __($string, 'commentbadge');
}

// Return URL of plugin. Const WP_CONTENT_URL defined (if needed) in main plugin file on 'plugins_loaded' hook 
function wp_ozh_lcc_pluginurl() {
	return WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(dirname(__FILE__)));
}

// Return physical path of plugin. Const WP_CONTENT_DIR defined (if needed) in main plugin file on 'plugins_loaded' hook 
function wp_ozh_lcc_plugindir() {
	return WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(dirname(__FILE__)));
}

// Return physical path of the upload directory in which we'll save the badge image
function wp_ozh_lcc_uploaddir() {
	extract(wp_upload_dir()); // $path, $url & $subdir
	
	// Replace the /year/month part of the location by our directory
	$path = str_replace($subdir, '/cmt_badge', $path);
	$url = str_replace($subdir, '/cmt_badge', $url);
	return array('path'=>$path, 'url'=>$url);
}

// Count the number and types of comments
// Returns array('total'=>5 , 'details'=>'3 comments, 1 trackback & 1 pingback')
function wp_ozh_lcc_get_comment_count() {
	global $wpdb;

	$totals = (array) $wpdb->get_results("
		SELECT comment_type, COUNT( * ) AS total
		FROM {$wpdb->comments}
		WHERE comment_approved = 1
		GROUP BY comment_type
	", ARRAY_A);
	
	$sum = 0;
	foreach($totals as $what) {
		$type = $what['comment_type'];
		if (!$type) $type = 'comment';
		$count = $what['total'];
		if ($count > 1) $type.='s';
		$result .= "$count $type, ";
		$sum += $count;
	}
	
	// Replace the last ", " by " & "
	$result = preg_replace('/, ([^,]*?)$/e',"' &amp; '.'\\1'", trim($result, ', '));
	
	return array('total'=>$sum, 'details'=>$result);
}

// Return the total number of posts & pages published
function wp_ozh_lcc_get_post_count() {
	$num_posts = wp_count_posts( 'post' ); // $num_posts->publish
	$num_pages = wp_count_posts( 'page' ); // $num_pages->publish

	return intval($num_posts->publish) + intval($num_pages->publish);
}

function wp_ozh_lcc_customicon() {
	return wp_ozh_lcc_pluginurl().'/inc/icon.png';
}

?>