<?php
/*
Plugin Name: WPMU-FeaturedPosts
Plugin URI: http://wpmututorials.com/plugins/featured-posts-plugin-and-widget/
Description: Adds a featured post management page for site admins who can input a sitewide feed address, then select posts via a checkbox. Also includes a widget to disply posts on the main (or any) blog.
Version: 2.8
Author: Ron Rennick, Andrea Rennick
Author URI: http://ronandandrea.com

Copyright 2008  Ron Rennick  (email : ron@ronandandrea.com)
Released under the GPL.
*/

### Move this file to the mu-plugins folder

### Define featured posts table
global $wpdb, $wpmu_version;
if($wpmu_version) {
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

function ra_featured_show($how_many = 1, $read_more = 1, $show_avatar = 1, $show_check = 0) {
	global $wpdb;
	$featured = $wpdb->get_results("SELECT feature_id, blog_id, feature_URI, feature_posttitle,feature_username, feature_excerpt " .
												"FROM $wpdb->featuredposts ORDER BY feature_order LIMIT 0, $how_many"); 
	if ( $featured ) { ?>
<div>
<?php 
		foreach ($featured as $feature ) { 
			if($show_avatar) { // in php code the blog id would be $feature->blog_id
				?><div><div class="ra-avatar"><?php echo get_avatar($feature->blog_id,'128'); ?></div><?php
			} ?>
		<div class="ra-feature-post"><h2><?php if($show_check) { 
				?><input type="checkbox" id="feature_<?php echo $feature->feature_id; 
					?>" name="ra_feature_check[]" value="<?php echo $feature->feature_id; ?>" />&nbsp;<?php
			} ?><a href="<?php echo $feature->feature_URI; ?>"><?php echo $feature->feature_posttitle; ?></a><?php
			if(!$show_avatar) {?>&#8212;
		<?php echo $feature->feature_username; 
			} ?></h2><div class="postcontent"><?php 
			echo $feature->feature_excerpt; 
			if($read_more) { 
				?><a class="more-link"  href="<?php echo $feature->feature_URI; ?>"><?php _e('Read the rest'); ?></a><?php
			}
		?></div></div>
<?php	if($show_avatar) { ?>
				</div><?php
			} 
		} ?>
</div>
<?php	
	}
} ?>