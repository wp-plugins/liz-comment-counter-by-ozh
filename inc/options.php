<?php
/*
Part of Plugin: Liz Comment Counter by Ozh
http://planetozh.com/blog/my-projects/liz-strauss-comment-count-badge-widget-wordpress/
*/

/* This file draws the option form, included either in the option page or the widget control */


// Process POSTed data and save in DB
function wp_ozh_lcc_processform() {

	global $wp_ozh_lcc;
	
	check_admin_referer('ozh-lcc');
	
	/* Debug: *
	echo "<pre>POST: ";echo htmlentities(print_r($_POST,true));echo "</pre>";	
	/**/
	
	switch ($_POST['lcc_action']) {
	case 'update_options':
	
		$options['fg'] = str_replace('#', '', $_POST['lcc_input_fg']);
		$options['bg'] = str_replace('#', '', $_POST['lcc_input_bg']);
		$options['label'] = $_POST['lcc_input_label'];
		$options['fontname'] = $_POST['lcc_fontname'];
		$options['linkback'] = ($_POST['lcc_linkback'])? '1' : '0';
		$options['fontspacing'] = ($_POST['lcc_fontspacing'])? '1' : '0';
		$options['title'] = $_POST['lcc_input_title'];
		$options['count'] = intval($_POST['lcc_commentcount']);
		
		if (!update_option('ozh_lcc', $options))
			add_option('ozh_lcc', $options);
			
		$wp_ozh_lcc = array_merge( (array)$wp_ozh_lcc, $options );
		wp_ozh_lcc_generate(false);
		
		$msg = wp_ozh_lcc__('updated');
		break;

	case 'reset_options':
		delete_option('ozh_lcc');
		wp_ozh_lcc_generate(true);	
		$msg = wp_ozh_lcc__('deleted');
		break;
	}

	echo '<div id="message" class="updated fade">';
	echo '<p>'.sprintf(wp_ozh_lcc__('Settings <strong>%s</strong>'),$msg).'</p>';
	echo "</div>\n";

}

// Return a dropdown list of fonts from subdir, with $selected highlighted
function wp_ozh_lcc_get_fonts($selected='') {
	global $wp_ozh_lcc;
	$fontlist = '<option value="builtin" '.($selected == 'builtin' ? 'selected="selected"' : '').'>'.wp_ozh_lcc__('Built in').'</option>';
	$fontdir = wp_ozh_lcc_plugindir().'/inc/fonts';
	foreach (glob("$fontdir/*.ttf") as $font) {
		$font = basename($font);
		$fontlist .= '<option value="'.$font.'" '.($selected == $font ? 'selected="selected"' : '').'">'.ucfirst(strtolower(reset(explode('.',$font))))."</option>\n";
	}
	return $fontlist;
}

// Draws the option table
function wp_ozh_lcc_option_table() {
	global $wp_ozh_lcc;
	wp_ozh_lcc_defaults();
	wp_ozh_lcc_css();
	wp_ozh_lcc_js();
	
	extract($wp_ozh_lcc); // $fg, $bg, $label, $linkback, $count, $fontname, $fontspacing, $title
	$label = stripslashes(attribute_escape($label));
	$count = mt_rand(999, 99999);
	$comments = wp_ozh_lcc_get_comment_count();
	$totalcomments = $comments['total'];
	$comment_details = $comments['details'];
	$totalposts = wp_ozh_lcc_get_post_count();
	$ratio = number_format($totalcomments / $totalposts, 2, '.', '');
	
	$checked_linkback = ($linkback == 1) ? 'checked="checked"' : '' ;
	$checked_fontspacing = ($fontspacing == 1) ? 'checked="checked"' : '' ;
	$fontlist = wp_ozh_lcc_get_fonts($fontname);
	
	$plugin_url = wp_ozh_lcc_pluginurl().'/inc';
	
	$lcc_howto = $lcc_widget_title = '';
	
		$lcc_howto = '
	<tr><th scope="row"><label for="lcc-input-title">'.wp_ozh_lcc__('Usage').'</label></th>
	<td colspan="3">'.wp_ozh_lcc__('Place the following template tag in your template file, for instance sidebar.php').':<br/>
	<code>&lt;?php if (function_exists(\'wp_ozh_lcc_badge\')) wp_ozh_lcc_badge(); ?></code><br/>'.
	wp_ozh_lcc__('If your theme is widget enabled, simply go the Widgets management page').'
	</td></tr>
';



	?>

	<table class="form-table" border="0"><tbody>
    <input type="hidden" name="lcc_action" value="update_options">
	
	<?php echo $lcc_howto; ?>
		
    <tr><th scope="row"><label for="lcc-input-fg"><?php echo wp_ozh_lcc__('Text Color');?></label></th>
	<td class="lcc_pickercell">
	<input type="text" size="7" id="lcc-input-fg" name="lcc_input_fg" value="#<?php echo $fg; ?>" />&nbsp;<span class='lcc_swatch' id='lcc-swatch-fg' title='<?php echo wp_ozh_lcc__('Pick a color');?>'>&nbsp;</span>
	<div class='lcc_picker_wrap' id='lcc-picker-wrap-fg'>
		<div class='lcc_picker' id='lcc-picker-fg'></div>
	</div>
	</td>
	<td rowspan="2" id="lcc_presets">
	</td>
	<td rowspan="3" width="150" id="lcc_preview" valign="center"><strong><?php echo wp_ozh_lcc__('Badge Preview');?>:</strong><br/>
	<img id="lcc_badge" src="<?php echo "$plugin_url/badge.php?fg=$fg&bg=$bg&label=$label&count=$count&spacing=$fontspacing&font=$fontname&ozh=cool"; ?>" />
	<span id="lcc_update_badge" title="<?php echo wp_ozh_lcc__('Update preview');?>">&nbsp;</span>
	</td>
	</tr>

    <tr><th scope="row"><label for="lcc-input-bg"><?php echo wp_ozh_lcc__('Background Color');?></label></th>
	<td class="lcc_pickercell">
	<input type='text' size='7' id='lcc-input-bg' name='lcc_input_bg' value='#<?php echo $bg; ?>' />&nbsp;<span class='lcc_swatch' id='lcc-swatch-bg' title='<?php echo wp_ozh_lcc__('Pick a color');?>'>&nbsp;</span>
	<div class='lcc_picker_wrap' id='lcc-picker-wrap-bg'>
		<div class='lcc_picker' id='lcc-picker-bg'></div>
	</div>
	</td></tr>

    <tr><th scope="row"><label for="lcc-input-label"><?php echo wp_ozh_lcc__('Label');?></label></th>
	<td colspan="2"><input type="text" value="<?php echo $label ?>" id="lcc-input-label" size="10" name="lcc_input_label"/>
	<label for="lcc-input-fontname"><?php echo wp_ozh_lcc__('Font');?> :</label><select name="lcc_fontname" id="lcc-input-fontname"><?php echo $fontlist; ?></select>
	<span id="lcc-groupspacing"><input type="checkbox" name="lcc_fontspacing" id="lcc-input-fontspacing" <?php echo $checked_fontspacing; ?> /><label for="lcc-input-fontspacing"><?php echo wp_ozh_lcc__('Extra&nbsp;spacing');?></label></span>
	</td></tr>

    <tr><th scope="row"><?php echo wp_ozh_lcc__('Credit &amp; Love');?></th>
	<td colspan="3"><label><input type="checkbox" <?php echo $checked_linkback; ?> id="lcc-input-linkback" name="lcc_linkback"/> <?php echo wp_ozh_lcc__('Add a link back to the plugin page');?></label><br/>
	<?php echo wp_ozh_lcc__('If you think this is a cool plugin, please leave this option enabled so your loyal readers &amp; commenters can find about this plugin too!');?>
	</td></tr>

    <tr><th scope="row"><?php echo wp_ozh_lcc__('Conversations');?></th>
	<td colspan="3"><?php echo sprintf(wp_ozh_lcc__('As of today, there have been here on this blog <b>%s</b>. Given that you currently have <b>%s</b> posts (including pages), this makes a conversation ratio of <b>%s</b> comments per post.'), $comment_details, $totalposts, $ratio); ?>
	</td></tr>

    <tr><th scope="row"><?php echo wp_ozh_lcc__('About Liz');?></th>
	<td colspan="3"><?php echo wp_ozh_lcc__('Liz Strauss of <a href="http://www.successful-blog.com/" title="Successful-Blog">Successful-Blog</a> is known as a relationship blogger, with 70,000 and counting comments on her blog. As a professional Social Web Strategist, if you are looking to build connections and conversation or to grow a community of fiercely loyal fans, Liz is the person to see.');?><br/>
	<?php echo wp_ozh_lcc__('This plugin was made in celebration of her dedication to blog community building so you, too, can show the world how social your blog is by showing off your comment count.');?>
	</td></tr>
	
	<input type="hidden" value="<?php echo $totalcomments ?>" name="lcc_commentcount" />

	</tbody></table>
<?php
}


function wp_ozh_lcc_css() {
	$plugin_url = wp_ozh_lcc_pluginurl().'/inc/colorpicker';
	echo "<style type='text/css'>
	@import url($plugin_url/farbtastic.css) screen;
	</style>
	";

	echo <<<HTML
<style type="text/css">
div.wrap {
	margin-bottom:2em;
}
#lcc_preview {
	text-align:left;
	width:200px;
	height:100%;
}
#lcc_preview p.submit {
	border-top:0px solid white;
	padding:inherit;
	margin:inherit;
}
#lcc_update_badge {
	border:1px solid inherit;
}
#lcc_badge {
	cursor:pointer;
}
#lcc_presets {
	width:150px;
}
.lcc_preset {float:left;margin:2px 4px;-moz-border-radius:3px;padding:0px;width:0;height:0;line-height:0;cursor:pointer;}
#lcc-reset {
	margin-right:1em;
	padding:0px 4px;
	-moz-border-radius:3px;
	cursor:pointer;
}
</style>


HTML;
}

function wp_ozh_lcc_js() {
	global $wp_ozh_lcc;
	
	$plugin_url = wp_ozh_lcc_pluginurl().'/inc/colorpicker';
	echo "<script src='$plugin_url/farbtastic.js' type='text/javascript'></script>\n";
	
	$presets = wp_ozh_lcc__('Presets');

	echo <<<JS
<script type="text/javascript">

jQuery(document).ready(function(){
	// add a color picker to every div.lcc_picker
	jQuery('.lcc_picker').each(function(){
		var id = jQuery(this).attr('id');
		var target = id.replace(/picker/, 'input');
		jQuery(this).farbtastic('#'+target);
	});

	// add the toggling behavior to .lcc_swatch
	jQuery('.lcc_swatch').click(function(){
		var id = jQuery(this).attr('id');
		var target = id.replace(/swatch/, 'picker-wrap');
		lcc_hideothercolorpickers(target);
		var display = jQuery('#'+target).css('display');
		(display == 'block') ? jQuery('#'+target).fadeOut(100) : jQuery('#'+target).fadeIn(100);
		var bg = (display == 'block') ? '0px 0px' : '0px -24px';
		jQuery(this).css('background-position', bg);
	});
	
	// Close color pickers when click on the document. This function is hijacked by farbtastic's event when a color picker is open
	jQuery(document).mousedown(function(){
		lcc_hideothercolorpickers();
	});

	// Close color pickers except "what"
	function lcc_hideothercolorpickers(what) {
		var update = false;
		jQuery('.lcc_picker_wrap').each(function(){
			var id = jQuery(this).attr('id');
			if (id == what) {
				return;
			}
			var display = jQuery(this).css('display');
			if (display == 'block') {
				jQuery(this).fadeOut(2);
				var swatch = id.replace(/picker-wrap/, 'swatch');
				jQuery('#'+swatch).css('background-position', '0px 0px');
				update = true;
			}
		});
		//if (update) lcc_update_badge();
	}
	
	// Presets
	lcc_make_presets();
	jQuery('.lcc_preset').click(function(){
		lcc_update_badge(ozh_cl_RGBtoHex(jQuery(this).css('color')), ozh_cl_RGBtoHex(jQuery(this).css('backgroundColor')));
	});
	
	jQuery('#lcc-input-fontname').change(function(){
	lcc_hide_spacingtoggle();}).change();
	
	jQuery('#lcc-reset').click(function(){lcc_reset();});
	
	jQuery('#lcc_update_badge').click(function(){
		lcc_update_badge();
	})
	jQuery('#lcc_update_badge');
	jQuery('#lcc_badge').click(function(){
		lcc_update_badge();
	});
});

function lcc_hide_spacingtoggle() {
	var display = (jQuery('#lcc-input-fontname').val() == 'builtin') ? 'none' : 'inline';
	jQuery('#lcc-groupspacing').css('display', display);
}

function lcc_update_badge(fgcol, bgcol) {
	if (fgcol != undefined) {jQuery('#lcc-input-fg').val(fgcol).keyup();}
	if (bgcol != undefined) {jQuery('#lcc-input-bg').val(bgcol).keyup();}
	var src = jQuery('#lcc_badge').attr('src');
	var fg = jQuery('#lcc-input-fg').val().replace('#','');
	var bg = jQuery('#lcc-input-bg').val().replace('#','');
	var label = jQuery('#lcc-input-label').val();
	var count = parseInt(Math.random()*99000)+999;
	var spacing = (jQuery('#lcc-input-fontspacing').attr('checked') == true ? 1 : 0);
	var fontname = jQuery('#lcc-input-fontname').val();
		
	src = src.replace(/fg=[^&]+/, 'fg='+fg);
	src = src.replace(/bg=[^&]+/, 'bg='+bg);
	src = src.replace(/label=[^&]+/, 'label='+label);
	src = src.replace(/count=[^&]+/, 'count='+count);
	src = src.replace(/spacing=[^&]+/, 'spacing='+spacing);
	src = src.replace(/font=[^&]+/, 'font='+fontname);

	jQuery('#lcc_badge').attr('src', src);
}

// rgb(1, 2, 3) -> #010203
function ozh_cl_RGBtoHex(color) {
	color = color.replace(/rgb\(|\)| /g,'').split(','); // ["1","2","3"]
	return '#' + ozh_cl_array_RGBtoHex(color[0],color[1],color[2]);
}

// From: http://www.linuxtopia.org/online_books/javascript_guides/javascript_faq/RGBtoHex.htm
function ozh_cl_array_RGBtoHex(R,G,B) {return ozh_cl_toHex(R)+ozh_cl_toHex(G)+ozh_cl_toHex(B)}
function ozh_cl_toHex(N) {
 if (N==null) return "00";
 N=parseInt(N); if (N==0 || isNaN(N)) return "00";
 N=Math.max(0,N); N=Math.min(N,255); N=Math.round(N);
 return "0123456789ABCDEF".charAt((N-N%16)/16)
      + "0123456789ABCDEF".charAt(N%16);
}

// Reset to defaults
function lcc_reset() {
	jQuery('#lcc-input-title').val('');
	jQuery('#lcc-input-fg').val('#444444');
	jQuery('#lcc-input-fg').keyup(); // trigger the color change
	jQuery('#lcc-input-bg').val('#99CCFF');
	jQuery('#lcc-input-bg').keyup();
	jQuery('#lcc-input-label').val('comments');
	jQuery('#lcc-input-fontname').val('trebuchet.ttf');
	jQuery('#lcc-input-fontspacing').attr('checked', true);
	jQuery('#lcc-input-linkback').attr('checked', true);
	lcc_update_badge();
}

// Make presets
/*#lcc_preset_x {background:transparent}*/
function lcc_make_presets() {
	if (jQuery.browser.msie) return;
	var presets = {
	'Default': ['#444444', '#99CCFF'],
	'Sandy': ['#6e5e30', '#f7cd50'],
	'Contrast': ['#ffffff', '#000000'],
	'Chlorophile': ['#bdfbbc', '#346e30'],
	'Strawberry': ['#d52a30', '#f6bcbd'],
	'Panic': ['#f4fe85', '#f24121'],
	'Lime': ['#526e30', '#b2f750'],
	'Nightly': ['#d7d5fb', '#100884'],
	'Rosy': ['#8f0569', '#faa3e6'],
	'Icy': ['#b3ad8f', '#52faf2']	
	};
	jQuery('#lcc_presets').html('$presets:<br/>');
	for (var i in presets) {
		var fg = presets[i][0];
		var bg = presets[i][1];
		//
		jQuery('#lcc_presets')
			.append('<div class="lcc_preset" style="color:'+fg+';background:'+bg+';border-top:10px solid '+fg+';border-right:10px solid '+bg+';border-bottom:10px solid '+bg+';border-left:10px solid '+fg+';" title="'+i+'"></div> ');
	}
}

</script>
	
JS;


}

	
?>