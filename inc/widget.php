<?php
/*
Part of Plugin: Liz Comment Counter by Ozh
http://planetozh.com/blog/my-projects/liz-strauss-comment-count-badge-widget-wordpress/
*/

function wp_ozh_lcc_widget_init() {
	register_widget('WP_Widget_Ozh_LCC');
}
	
class WP_Widget_Ozh_LCC extends WP_Widget {

	function WP_Widget_Ozh_LCC() {
		$widget_ops = array('classname' => 'widget_ozh_lcc', 'description' => 'Show off the number of comments your blog has' );
		$this->WP_Widget('widget_ozh_lcc', 'Liz Comment Counter', $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']);
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		wp_ozh_lcc_badge();
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p>Configure the widget on its <a href="options-general.php?page=ozh_lcc">options page</p>
<?php
	}
}

?>