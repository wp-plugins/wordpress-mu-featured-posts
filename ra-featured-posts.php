<?php
/*
Plugin Name: WordPress Featured Posts
Plugin URI: http://wpmututorials.com/plugins/featured-posts-plugin-and-widget/
Description: Adds a featured post management page for site admins who can input a sitewide feed address, then select posts via a checkbox. Also includes a widget to disply posts on the main (or any) blog.
Version: 2.8.2
Author: Ron Rennick, Andrea Rennick
Author URI: http://ronandandrea.com

Copyright 2008  Ron Rennick  (email : ron@ronandandrea.com)
Released under the GPLv2.
*/

### Move this file to the mu-plugins folder

### Define featured posts table
global $wpdb, $wpmu_version;
if( function_exists( 'is_multisite' ) && is_multisite() && empty( $wpmu_version ) ) {
	$ra_parent = 'ms-admin.php';
	$wpdb->featuredposts = $wpdb->base_prefix .'featuredposts';
} elseif( !empty( $wpmu_version ) ) {
	$ra_parent = 'wpmu-admin.php';
	$wpdb->featuredposts = $wpdb->base_prefix .'featuredposts';
} else {
	$ra_parent = 'themes.php';
	$wpdb->featuredposts = $wpdb->prefix .'featuredposts';
}
### Add a submenu for site admins
if(strpos($_SERVER['REQUEST_URI'], 'wp-admin')) { // admin panel
	global $ra_featured_admin_show, $ra_featured_admin_keep, $ra_featured_admin_feed;

	require_once('ra-featured-posts/ra-featured-posts-admin.php');
	add_action('admin_menu', 'ra_add_featured_posts_page');

	// Get plugin settings
	$ra_featured_admin_show = get_site_option('ra_featured_admin_show');
	$ra_featured_admin_keep = get_site_option('ra_featured_admin_keep');
	$ra_featured_admin_feed = get_site_option('ra_featured_admin_feed');

	// Install if necessary
	if(!$ra_featured_admin_show) {
		require_once('ra-featured-posts/ra-featured-posts-install.php');
		ra_install_featuredposts();
	}
} 

function ra_featured_show($how_many = 1, $read_more = 1, $show_avatar = 1, $show_check = 0, $display = true) {
	global $wpdb;
	$featured = $wpdb->get_results("SELECT feature_id, blog_id, feature_URI, feature_posttitle,feature_username, feature_excerpt " .
					"FROM $wpdb->featuredposts ORDER BY feature_order LIMIT 0, $how_many"); 
	$result = '';
	if ( $featured ) {
		$result .= '<div>';
		foreach ($featured as $feature ) { 
			if($show_avatar)
				$result .= '<div><div class="ra-avatar">' . get_avatar($feature->blog_id,'128') . '</div>';
			$result .= '
		<div class="ra-feature-post"><h2>';
			if($show_check)
				$result .= '<input type="checkbox" id="feature_' . $feature->feature_id . 
					'" name="ra_feature_check[]" value="' . $feature->feature_id . '" />&nbsp;';
			$result .= '<a href="' . $feature->feature_URI . '">' . $feature->feature_posttitle . '</a>';
			if(!$show_avatar)
				$result .= '&#8212;' . $feature->feature_username; 
			$result .= '</h2><div class="postcontent">' . $feature->feature_excerpt; 
			if($read_more) 
				$result .= '<a class="more-link"  href="' . $feature->feature_URI . '">' . __('Read the rest') . '</a>';
			$result .= '</div>';
			if($show_avatar)
				$result .= '</div>';
		}
		$result .= '</div>';
	}
	if( !$display )
		return $result;
	echo $result;
}
function ra_featured_shortcode( $atts, $content = null) {
	$args = array();
	$defaults = array( 'howmany' => 1, 'readmore' => 1, 'showavatar' => 1 );
	$args = array_merge( $defaults, $atts );
	extract( $args );
	return ra_featured_show( $howmany, $readmore, $showavatar, 0, false );
}
if( function_exists( 'add_shortcode' ) )
	add_shortcode('ra-featured', 'ra_featured_shortcode');

