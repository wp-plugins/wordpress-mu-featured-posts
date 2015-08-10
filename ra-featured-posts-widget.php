<?php
/*
Plugin Name: Featured Posts Widget
Description: Adds a sidebar widget to display Featured Posts
Author: Andrea_R
Version: 1.1.1
Author URI: http://wpmututorials.com
*/

class RA_Featured_Posts_Widget extends WP_Widget {
	function __construct() {
		$widget_ops = array( 'description' => __( 'Featured Posts Widget' ) );
		$this->WP_Widget( 'ra_featured_posts_widget', __('Featured Posts Widget'), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = 'Featured Posts';
		echo $before_widget . $before_title . $title . $after_title;
		ra_featured_show( $instance['how_many'], $instance['more'], $instance['avatar'] );
		echo $after_widget;
	}

 	function update( $new_instance, $old_instance ) {
		$new_instance['how_many'] = intval( $new_instance['how_many'] );
		$new_instance['more'] = ( $new_instance['more'] ? '1' : '0' );
		$new_instance['avatar'] = ( $new_instance['avatar'] ? '1' : '0' );

		return $new_instance;
	}

	function form( $instance ) { ?>
		<p><label for="<?php echo $this->get_field_id('how_many'); ?>"><?php _e('How many posts:') ?></label>
		<input type="text" id="<?php echo $this->get_field_id('how_many'); ?>" name="<?php echo $this->get_field_name('how_many'); ?>" value="<?php echo ( $instance['how_many'] > 0 ? esc_attr( $instance['how_many'] ) : '' ); ?>" /></p>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'more' ); ?>" id="<?php echo $this->get_field_id( 'more' ); ?>" value="1" <?php checked( '1', $instance['more'] ); ?> />
			<label for="<?php echo $this->get_field_id('more'); ?>"><?php _e( 'Show More Link' ); ?></label>
		</p>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'avatar' ); ?>" id="<?php echo $this->get_field_id( 'avatar' ); ?>" value="1" <?php checked( '1', $instance['avatar'] ); ?> />
			<label for="<?php echo $this->get_field_id('avatar'); ?>"><?php _e( 'Show Avatar' ); ?></label>
		</p>
<?php	}
}

function widget_featured_post_init() {
	register_widget( 'RA_Featured_Posts_Widget' );
}
add_action('widgets_init', 'widget_featured_post_init');
