<?php
#######################################
#
#	WordPressMU 1.X Plugin: WPMU-Featured Posts 1.0
#	Copyright (c) 2008 Ron Rennick
#
#	File Written By:
#	- Ron Rennick
#	- http://ronandandrea.com
#
#	File Information:	
#	- Contains installation/setup code for plugin
#	- wp-content/mu-plugins/ra-featured-posts/ra-featured-posts-admin.php
#
#######################################

### Load RSS functions
require_once (ABSPATH . WPINC . '/rss.php');

function ra_add_featured_posts_page() {
	global $wpdb, $ra_featured_admin_show, $ra_featured_admin_keep, $ra_featured_admin_feed;
	global $wpmu_version, $ra_parent;
		
	if ( $_GET['page'] == basename(__FILE__) ) {
		if( !empty( $wpmu_version ) && !is_site_admin() )
			wp_die("You don't have permissions to access this page");
		elseif( function_exists( 'is_multisite' ) && is_multisite() && !is_super_admin() )
			wp_die("You don't have permissions to access this page");
		elseif( !current_user_can( 'edit_posts' ) )
			wp_die("You don't have permissions to access this page");

		if ( 'save' == $_REQUEST['action'] ) {
			$ra_featured_admin_show = $_REQUEST['ra_show'];
			$ra_featured_admin_keep = $_REQUEST['ra_keep'];
			$ra_featured_admin_feed = $_REQUEST['ra_feed'];
			update_site_option('ra_featured_admin_show', $ra_featured_admin_show);
			update_site_option('ra_featured_admin_keep', $ra_featured_admin_keep);
			update_site_option('ra_featured_admin_feed', $ra_featured_admin_feed);
			header("Location: {$ra_parent}?page=ra-featured-posts-admin.php&saved=true");
			die;
		} 
		else if ( 'feature' == $_REQUEST['action'] ) {
			$querystring = '&featured=true';
			$ra_link = $_REQUEST['ra_URI'];
			$ra_title = $_REQUEST['ra_title'];
			$ra_author = $_REQUEST['ra_author'];
			$ra_excerpt = $_REQUEST['ra_excerpt'];
			$ra_featured_admin_keep = get_site_option('ra_featured_admin_keep');
			if($ra_link) {
				$querystring .= '&link=' . $ra_link;
				// get blog id
				$uriparts = split('/',str_replace('http://','',$ra_link));
				if(count($urislices > 2)) {
					$uriparts = array_slice($uriparts,1,2);
				} else {
					$uriparts = array_slice($uriparts,1);
				}
				if($uriparts) {
					$path = str_replace('//','/', ('/' . join('/',$uriparts) . '/'));
					if(isset($wpmu_version)) {
						$blogid = $wpdb->get_var("SELECT blog_id FROM $wpdb->blogs WHERE path = '$path'");
					}
				}
				if(!$blogid) { $blogid = 1;}
				// remove [...] from excerpt
				$index = strlen($ra_excerpt) - 5;
				if($index > 0) {
					if(substr($ra_excerpt,$index) == '[...]') {
						$ra_excerpt = substr($ra_excerpt,0,$index);
					}
				}
				// update existing records in featured post table incrementing display order
				$sqlcmd = "UPDATE $wpdb->featuredposts SET feature_order = feature_order + 1";
				$rowcount = $wpdb->query($sqlcmd);

				// put post at top of featured post list
				$sqlcmd = $wpdb->prepare("INSERT INTO $wpdb->featuredposts (blog_id, feature_order, feature_timestamp, feature_username, ".
					"feature_posttitle, feature_URI, feature_excerpt) VALUES (%d, 1, NOW(), %s, %s, %s, %s)",
					$blogid, $ra_author, $ra_title, $ra_link, $ra_excerpt );
				$wpdb->query($sqlcmd);

				// check for records above keep limit
				$sqlcmd = $wpdb->prepare( "SELECT MAX(feature_order) FROM (" .
					"SELECT feature_order FROM $wpdb->featuredposts ORDER BY feature_order ASC LIMIT 0, %d) x", $ra_featured_admin_keep );
				$index = $wpdb->get_var($sqlcmd);
				if($index >= $ra_featured_admin_keep) {
					$sqlcmd = "DELETE FROM $wpdb->featuredposts WHERE feature_order > $index";
					$wpdb->query($sqlcmd);
				}
			}
			header("Location: $ra_parent?page=ra-featured-posts-admin.php".$querystring);
			die;
		} else if ( 'remove' == $_REQUEST['action'] ) {
			$ra_remove = $_REQUEST['ra_feature_check'];
			if($ra_remove) {
				// remove records 
				$sqlcmd = $wpdb->escape( "DELETE FROM $wpdb->featuredposts WHERE feature_id IN (" . join(',',$ra_remove).")" );
				$rowcount = $wpdb->query($sqlcmd);
				$querystring = '&removed=true';
			}
			header("Location: {$ra_parent}?page=ra-featured-posts-admin.php".$querystring);
			die;
		} 
	} 
	add_submenu_page($ra_parent, 'ra_featured_posts', 'Feature Posts', 0, 'ra-featured-posts-admin.php', 'ra_featured_posts_page');
}

function ra_featured_posts_page() {
	global $wpdb, $ra_featured_admin_show, $ra_featured_admin_keep, $ra_featured_admin_feed;
	$checked = ' checked="checked"';
	$msgopen = '<div id="message" class="updated fade"><p><strong>';
	$msgclose = '.</strong></p></div>';
	if ( $_REQUEST['saved'] ) {
		echo $msgopen.'Options saved'.$msgclose;
	}
	else if ( $_REQUEST['featured'] ) {
		$ra_link = $_REQUEST['link'];
		echo $msgopen.'Featured '. $ra_link.$msgclose;
	}?>
<div class='wrap'>
	<h2><?php _e('Manage Featured Posts'); ?> Options</h2>
	<form id="ra_feature_options" method="POST">
		<div><h3><?php _e('Options'); ?></h3>
			<?php _e('Feed URI for Recent Posts'); ?>&nbsp;
			<input type="text" id="ra_feed" name="ra_feed" size="50" value="<?php echo $ra_featured_admin_feed; ?>" /><br />
			<?php _e('Number of Recent Posts to show on this page'); ?>&nbsp;
			<input type="text" id="ra_show" name="ra_show" size="5" value="<?php echo $ra_featured_admin_show; ?>" /><br />
			<?php _e('Number of Featured Posts to keep in history'); ?>&nbsp;
			<input type="text" id="ra_keep" name="ra_keep" size="5" value="<?php echo $ra_featured_admin_keep; ?>" />&nbsp;&nbsp;
			<input type="submit" name="submitoptions" value="&nbsp;&nbsp;<?php _e('Save Options'); ?>&nbsp;&nbsp;" />
			<input type="hidden" name="action" value="save" />
		</div>
	</form>
	<br /><br />
	<div class="ra-admin-right"><h3><?php _e('Featured Posts'); ?></h3><br />
		<form id="ra_feature_remove" method="POST">
			<?php ra_featured_show($ra_featured_admin_keep, 0, 0, 1); ?>
			<input type="submit" name="submitremove" value="&nbsp;&nbsp;<?php _e('Remove Selected'); ?>&nbsp;&nbsp;" />
			<input type="hidden" name="action" value="remove" />
		</form>
	</div>
	<div class="ra-admin-left">
<?php
	$rss = @fetch_rss($ra_featured_admin_feed); ?>
<h3><?php echo $rss->channel['title'] . ' - ' . $rss->channel['description']; ?></h3>
<form id="ra_rss_posts" method="POST">
<?php 
	if ( isset($rss->items) && 0 != count($rss->items) ) {
		$rss->items = array_slice($rss->items, 0, $ra_featured_admin_show);
		$index = 1;
		foreach ($rss->items as $item ) { ?>
		<h4><a id="rss-link-<?php echo $index; ?>" href='<?php echo wp_filter_kses($item['link']); ?>'><?php echo wp_specialchars($item['title']); ?></a>
		&#8212; 			<div id="rss-author-<?php echo $index; ?>"><?php echo $item['dc']['creator']; ?></div>
		&#8212; 			<?php printf(__('%s ago'), human_time_diff(strtotime($item['pubdate'], time() ) ) ); ?></h4>
		<div><div id="rss-excerpt-<?php echo $index; ?>"><?php echo $item['description']; ?></div>&nbsp;
<?php 	$plug = split('/',$item['link']);
			for($i=count($plug)-1;$i >= 0;$i--) {
				if(strlen($plug[$i]) > 0)
					break;
			}
			if($i>=0) {
				ra_show_hide_begin($plug[$i], "Show/Hide Post");

				echo $item['content']['encoded'];
				ra_show_hide_end(); ?>&nbsp;
				<input type="submit" name="ra-feature" value="&nbsp;&nbsp;<?php _e('Feature'); ?>&nbsp;&gt;&gt;&nbsp;&nbsp;"
					onClick="ra_feature(<?php echo $index; ?>)" />
<?php	}
			$index++;
			echo '</div>';
		}
	} ?>
<input type="hidden" id="ra_URI" name="ra_URI" value="" />
<input type="hidden" id="ra_title" name="ra_title" value="" />
<input type="hidden" id="ra_author" name="ra_author" value="" />
<input type="hidden" id="ra_excerpt" name="ra_excerpt" value="" />
<input type="hidden" name="action" value="feature" />
</form></div>
	</div>
</div>
<?php } 


function ra_featured_posts_head() { ?>

<script type="text/javascript">
//<!--
	function ra_show(id, newclass) {
	  var el = document.getElementById(id);
	  if(el) {
	    if(newclass) {
			if(el.className==newclass) el.className="ra-hide"; 
			else el.className=newclass;
	    } else {
			if(el.className=="") el.className="ra-hide";
			else el.className="";
	    }
	  }
	}
	function ra_feature(index) {
		var r_link = document.getElementById('rss-link-' + index);
		var r_auth = document.getElementById('rss-author-' + index);
		var r_excerpt = document.getElementById('rss-excerpt-' + index);
		var h_link = document.getElementById('ra_URI');
		var h_title = document.getElementById('ra_title');
		var h_auth = document.getElementById('ra_author');
		var h_excerpt = document.getElementById('ra_excerpt');
		
		if(r_link) {
			if(h_link) {
				h_link.value = r_link.href;
			}
			if(h_title) {
				h_title.value = r_link.innerHTML.replace(/<[^>]+>/g,"");
			}
		}
		if((r_auth) && (h_auth)) {
			h_auth.value = r_auth.innerHTML.replace(/<[^>]+>/g,"");
		}
		if((r_excerpt) && (h_excerpt)) {
			h_excerpt.value = r_excerpt.innerHTML.replace(/<[^>]+>/g,"");
		}
	}
//-->
</script>

<style type="text/css">
.ra-hide { display:none; }
.ra-admin-left { width:480px; float:left; border-top: solid 1px #DADADA;}
.ra-admin-right { width:480px; float:right; border-top: solid 1px #DADADA;}
</style> 

<?php }

function ra_show_hide_begin($HTMLid, $linktext = 'Expand/Collapse', $CSSclass = '', $tag = 'div') {
	$q = "'";
	if($HTMLid && $linktext) {
		echo '<a href="javascript:void(0)" onclick="ra_show('.$q.$HTMLid.$q.','.
		$q.$CSSclass.$q.')">'.$linktext.'</a>';
		echo '<'.$tag.' id="'.$HTMLid.'" class="ra-hide">';
	}
}

function ra_show_hide_end($tag = 'div') { 
	echo '</'.$tag.'>';
}

// add javascript to page head
add_action('admin_head','ra_featured_posts_head');
?>
