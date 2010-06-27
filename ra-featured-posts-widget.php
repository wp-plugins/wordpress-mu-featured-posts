<?php
/*
Plugin Name: Featured Posts Widget
Description: Adds a sidebar widget to display Featured Posts
Author: Andrea_R
Version: 1.0
Author URI: http://wpmututorials.com
*/

function widget_featured_post_init() {
if ( !function_exists('register_sidebar_widget') )
return;
function widget_featured_post($args) {
extract($args);

// You can change the title below to whatever you like. This shows on the Widgets page
$title ='Featured Posts';

// These lines generate our output.
echo $before_widget . $before_title . $title . $after_title;
ra_featured_show(); 
echo $after_widget;
}

// Replace the description below with your own
register_sidebar_widget('Add featured post', 'widget_featured_post');
}
// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_featured_post_init');
?>