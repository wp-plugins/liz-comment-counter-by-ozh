<?php
/*
Part of Plugin: Liz Comment Counter by Ozh
http://planetozh.com/blog/my-projects/liz-strauss-comment-count-badge-widget-wordpress/
*/

/* This file generates the badge image, either for display or for writing on the disk */

global $wp_ozh_lcc_badge, $wp_ozh_lcc;

if (!class_exists('CSS_Color'))
	include_once(dirname(__FILE__).'/csscolor.php');

if(!function_exists('attribute_escape')) {
function attribute_escape( $text, $quotes = 0 ) {
	// This is wp_specialchars()
	$text = str_replace('&&', '&#038;&', $text);
	$text = str_replace('&&', '&#038;&', $text);
	$text = preg_replace('/&(?:$|([^#])(?![a-z1-4]{1,8};))/', '&#038;$1', $text);
	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('>', '&gt;', $text);
	if ( 'double' === $quotes ) {
	    $text = str_replace('"', '&quot;', $text);
	} elseif ( 'single' === $quotes ) {
	    $text = str_replace("'", '&#039;', $text);
	} elseif ( $quotes ) {
	    $text = str_replace('"', '&quot;', $text);
	    $text = str_replace("'", '&#039;', $text);
	}
	return $text;
}
}

if ($wp_ozh_lcc) {
	$wp_ozh_lcc_badge = array_merge($wp_ozh_lcc_badge, $wp_ozh_lcc); // merge not to overwrite $wp_ozh_lcc_badge['filename'] defined in generate.php
	$wp_ozh_lcc_badge['standalone'] = false;
} else {
	$fg = wp_ozh_lcc_sanitize($_GET['fg']);
	$bg = wp_ozh_lcc_sanitize($_GET['bg']);
	$label = (attribute_escape($_GET['label']));
	$fontname = (attribute_escape($_GET['font']));
	$wp_ozh_lcc_badge['bg'] = $bg ? $bg : '99CCFF';
	$wp_ozh_lcc_badge['fg'] = $fg ? $fg : '444444';
	$wp_ozh_lcc_badge['label'] = $label ? $label : 'comments';
	$wp_ozh_lcc_badge['fontname'] = $fontname ? $fontname : 'trebuchet.ttf';
	$wp_ozh_lcc_badge['count'] = isset($_GET['count']) ? intval($_GET['count']) : '12345';
	$wp_ozh_lcc_badge['fontspacing'] = ($_GET['spacing'] == 1) ? 1 : 0;
	$wp_ozh_lcc_badge['standalone'] = true;
}

// Returns $hex or empty
function wp_ozh_lcc_sanitize($hex) {
	return wp_ozh_lcc_ishex($hex) ? str_replace('#','',$hex) : '';
}

// Returns true if $hex is a valid CSS hex color.
function wp_ozh_lcc_ishex($hex) {
	// The "#" character at the start is optional.

	// Regexp for a valid hex digit
	$d = '[a-fA-F0-9]';

	// Make sure $hex is valid
	if (preg_match("/^#?$d$d$d$d$d$d\$/", $hex) ||
	preg_match("/^#?$d$d$d\$/", $hex)) {
	  return true;
	}
	return false;
}

function wp_ozh_lcc_badge_init() {
	global $wp_ozh_lcc_badge;

	$back = new CSS_Color($wp_ozh_lcc_badge['bg']);
	$fore = new CSS_Color($wp_ozh_lcc_badge['fg']);

	// original
	$wp_ozh_lcc_badge['fg'] = $fore->bg['0'];
	$wp_ozh_lcc_badge['bg'] = $back->bg['0'];
	// darker
	$wp_ozh_lcc_badge['d1'] = $back->bg['-2']; // bottom & right outer border
	$wp_ozh_lcc_badge['d2'] = $back->bg['+1']; // top & left inner border
	// lighter
	$wp_ozh_lcc_badge['l1'] = $back->bg['+2']; // top & left outer border
	$wp_ozh_lcc_badge['l2'] = $back->bg['+3']; // inner background
	$wp_ozh_lcc_badge['l3'] = $back->bg['+5']; // right & bottom inner border
}

// Output a CSS badge. Just for fun, not used.
function wp_ozh_lcc_badge_css() {
	global $wp_ozh_lcc_badge;
	wp_ozh_lcc_badge_init();
	$bg = $wp_ozh_lcc_badge['bg'];
	$fg = $wp_ozh_lcc_badge['fg'];
	$d1 = $wp_ozh_lcc_badge['d1'];
	$d2 = $wp_ozh_lcc_badge['d2'];
	$l1 = $wp_ozh_lcc_badge['l1'];
	$l2 = $wp_ozh_lcc_badge['l2'];
	$l3 = $wp_ozh_lcc_badge['l3'];
	$label = stripslashes($wp_ozh_lcc_badge['label']);
	$count = $wp_ozh_lcc_badge['count'];

	echo <<<HTML
	<style>
	#lcc_div {
		background:#$bg !important;
		color:#$fg !important;
		width:86px !important;
		height:18px !important;
		font-size:10px !important;
		font-family:Arial !important;
		border:1px solid #$d1 !important;
		border-left:1px solid #$l1 !important;
		border-top:1px solid #$l1 !important;
	}
	#lcc_div2 {
		float:left !important;
		margin:1px !important;
		background:#$l2 !important;
		border:1px solid #$d2 !important;
		border-right:1px solid #$l3 !important;
		border-bottom:1px solid #$l3 !important;
		width:30px !important;
		text-align:right !important;
	}
	#lcc_div3 {
		padding-top:2px !important;
		margin-right:2px !important;
		float:right !important;
	}
	</style>

	<div id="lcc_div">
		<div id="lcc_div2">$count</div> <div id="lcc_div3">$label</div>
	</div>

HTML;

}

// Display or save the image
function wp_ozh_lcc_badge_makeimg($im) {
	global $wp_ozh_lcc_badge;
	
	if ($wp_ozh_lcc_badge['standalone']) {
		// Display
		@header("Content-type: image/png");
		@imagepng($im);
	} else {
		// Write image to disk
		@imagepng($im, $wp_ozh_lcc_badge['filename']);
	}
}


function wp_ozh_lcc_badge_makebadge() {
	global $wp_ozh_lcc_badge;
	wp_ozh_lcc_badge_init();
	
	// Badge dimensions
	$w = 88;
	$h = 19;
	
	// Inner area width & position
	$iw = 32; // pixels wide
	$ix = 3; // pixels from the top
	$iy = 2; // pixels from the left
	
	$im = imagecreatetruecolor ($w, $h); // empty badge

	$bg = wp_ozh_lcc_badge_colorallocate($im, $wp_ozh_lcc_badge['bg']);
	$fg = wp_ozh_lcc_badge_colorallocate($im, $wp_ozh_lcc_badge['fg']);
	$d1 = wp_ozh_lcc_badge_colorallocate($im, $wp_ozh_lcc_badge['d1']); // bottom & right outer border
	$d2 = wp_ozh_lcc_badge_colorallocate($im, $wp_ozh_lcc_badge['d2']); // top & left inner border
	$l1 = wp_ozh_lcc_badge_colorallocate($im, $wp_ozh_lcc_badge['l1']); // top & left outer border
	$l2 = wp_ozh_lcc_badge_colorallocate($im, $wp_ozh_lcc_badge['l2']); // inner background
	$l3 = wp_ozh_lcc_badge_colorallocate($im, $wp_ozh_lcc_badge['l3']);	// right & bottom inner border

	imagefill($im, 0, 0, $bg);
	
	// Outer borders
	imageline($im, 0, 0, $w, 0, $l1); // top
	imageline($im, 0, 0, 0, $h, $l1); // left
	imageline($im, 0, $h-1, $w-1, $h-1, $d1); // bottom
	imageline($im, $w-1, 0, $w-1, $h-1, $d1); // right
	
	// Inner borders
	imageline($im, $ix, $iy, ($iw+$ix), $iy, $d2); // top
	imageline($im, $ix, $iy, $ix, ($h - $iy -2), $d2); // left
	imageline($im, $ix, ($h - $iy -2), ($iw+$ix), ($h - $iy -2), $l3); // bottom
	imageline($im, ($iw+$ix), $iy, ($iw+$ix), ($h - $iy -2), $l3); // right
	
	// Inner area
	imagefill($im, $ix+1, $iy+1, $l2);

	// Syntax:
	// imagettftext ($image, $size, $angle, $x, $y, $color, $fontfile, $text )
	// imagestring ($image, $font, $x, $y, $text, $color )


	// 'Comments'
	$text = stripslashes($wp_ozh_lcc_badge['label']);
	if ($wp_ozh_lcc_badge['fontname'] == 'builtin') {
		// "Safe" mode using built-in font instead of a TTF
		imagestring($im, 2, $iw+$ix+4, 2, $text, $fg);	
	} else {
		$font = '/fonts/'.stripslashes($wp_ozh_lcc_badge['fontname']);
		$spacing = $wp_ozh_lcc_badge['fontspacing'];;
		$x = $iw+$ix+3; // 3 pixels margin from the inner area
		for ($i = 0; $i <strlen($text); $i++) {
			$arr = imagettftext ($im, 7 ,0, $x, 12, -$fg, dirname(__FILE__).$font, $text{$i});
			$x = $arr[4]+$spacing;
		}
	}

	// Count
	$text = $wp_ozh_lcc_badge['count'];
	if ($text > 99999)
		$text = number_format(intval($text/1000)).'K'; // just in case someone gets more than 1 million comments: "5350987" -> "5,350K"
	$text = sprintf("% 5s",  $text); // add leading spaces
	imagestring($im, 2, $ix+2, 2, $text, $fg);
	
	// Display
	wp_ozh_lcc_badge_makeimg($im);
	imagedestroy($im);
}

function wp_ozh_lcc_badge_colorallocate($im, $hex) {
	$colors = CSS_Color::hex2rgb($hex);
	return imagecolorallocate($im, $colors[0], $colors[1], $colors[2]);
}



wp_ozh_lcc_badge_makebadge();
//wp_ozh_lcc_badge_css(); // Just for fun when used in standalone mode

?>