<?php
defined('ABSPATH') or die("Cannot access pages directly.");

$matches = 0;
$limit_start = 0;
$count_posts = wp_count_posts()->publish;
$pr_included_categories = get_option('pr_included_categories');

while (($matches == 0) && ($limit_start < $count_posts)) {
	$post_categories = wp_get_post_categories(selectedpost($limit_start));
	if (is_array($pr_included_categories)) {
		$comparison = array_intersect($post_categories, $pr_included_categories);
		$matches = count($comparison);
	}
	if ($matches == 0) {
		$limit_start++;
	}
}

function selectedpost($limit_start) {
	global $wpdb;
	$post_type = 'post';
	$post_status = 'publish';
	$selectedpost = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s AND post_status = %s ORDER BY post_date ASC LIMIT $limit_start,1", $post_type, $post_status));
	$wpdb->flush();
	return $selectedpost;
}

function post_rotation() {
	global $wpdb;
	global $limit_start;
	global $matches;
	$pr_interval = get_option ('pr_interval');
	$this_moment  = strtotime(current_time('mysql'));
	$post_type = 'post';
	$post_status = 'publish';
	$latest_post_time = $wpdb->get_var($wpdb->prepare("SELECT post_date FROM $wpdb->posts WHERE post_type = %s AND post_status = %s ORDER BY post_date DESC LIMIT 0,1", $post_type, $post_status));
	$wpdb->flush();
	$selectedpost = selectedpost($limit_start);
	if ($this_moment-strtotime($latest_post_time) > ($pr_interval * 3600)) {
		if ($matches > 0) {
			$mysql_current_time = current_time('mysql');
	        $mysql_gmt_current_time = get_gmt_from_date($mysql_current_time);
	        $wpdb->query($wpdb->prepare("UPDATE $wpdb->posts SET post_date = %s, post_date_gmt = %s WHERE ID = %d", $mysql_current_time, $mysql_gmt_current_time, $selectedpost));
	        if (get_option('pr_also_alter_last_modified') == 1) {
	        	$wpdb->query($wpdb->prepare("UPDATE $wpdb->posts SET post_modified = %s, post_modified_gmt = %s WHERE ID = %d", $mysql_current_time, $mysql_gmt_current_time, $selectedpost));
	        }
	        $wpdb->flush();
		}
    }
}

if (get_option('pr_enabled') == 1) {
	post_rotation();
}
?>