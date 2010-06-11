<?php
#######################################
#
#	WordPressMU 1.X Plugin: WPMU-Featured Posts 1.0
#	Copyright (c) 2008 Ron Rennick
# Released under GPL.
#	File Written By:
#	- Ron Rennick
#	- http://ronandandrea.com
#
#	File Information:	
#	- Contains installation/setup code for plugin
#	- wp-content/mu-plugins/ra-featured-posts/ra-featured-posts-install.php
#
#######################################

### Function: Create Featured Posts Table
function ra_install_featuredposts() {
	global $wpdb, $ra_featured_admin_show, $ra_featured_admin_keep;
	$create_fp_sql = "CREATE TABLE IF NOT EXISTS " .
			"{$wpdb->featuredposts} (".
			"feature_id INT(11) NOT NULL auto_increment,".
			"blog_id BIGINT(20) NOT NULL,".
			"feature_order INT(2) NOT NULL ,".
			"feature_timestamp VARCHAR(15) NOT NULL ,".
			"feature_username VARCHAR(50) NOT NULL,".
			"feature_posttitle TEXT NOT NULL,".
			"feature_URI TEXT NOT NULL ,".
			"feature_excerpt TEXT NOT NULL,".
			"PRIMARY KEY (feature_id))";

	$wpdb->query($create_fp_sql);

// Add Site Options
	$ra_featured_admin_show = 20;
	$ra_featured_admin_keep = 10;
	update_site_option('ra_featured_admin_show', $ra_featured_admin_show);
	update_site_option('ra_featured_admin_keep', $ra_featured_admin_keep);
}
?>