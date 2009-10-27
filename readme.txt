=== Featured Posts ===
Contributors: wpmuguru, andrea_r
Tags: wpmu, widget, featured post, sitewide, feeds
Requires at least: 2.6
Tested up to: 2.8.4
Stable tag: 2.8.1

Featured posts plugin & widget for WordPress or WordPress MU. 

== Description ==
Adds a featured post management page for site admins who can input a sitewide feed address, then select posts via a checkbox. Also includes a widget to disply posts on the main (or any) blog.

We recommend using the [Sitewide tags plugin](http://wordpress.org/extend/plugins/wordpress-mu-sitewide-tags/) in WPMU, then placing the feed address from the tags blog in the admin menu. Otherwise, any blog feed can be used.

A widget is included. You can also code the output in your theme with `<?php ra_featured_show($how_many, $read_more, $show_avatar); ?>` where

`$how_many` = how many posts to show - default 1
`$read_more` = add read more link - default show link
`$show_avatar` = show author avatar - default show avatar

For questions & updates, please see this post:
[WPMU Tutorials](http://wpmututorials.com/plugins/featured-posts-plugin-and-widget/)

== Installation ==
Unzip the files and drop the two files and the folder in the mu-plugins folder.

ra-featured-posts.php
ra-featured-posts-widget.php
folder: ra-featured-posts
	ra-featured-posts-admin.php
	ra-featured-posts-install.php

== Changelog ==

= 2.8.1 =
* Fixed misplaced permission check.

= 2.8 =
* Original version.
