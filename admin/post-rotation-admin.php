<?php
defined('ABSPATH') or die("Cannot access pages directly.");

global $wpdb;
global $matches;
global $limit_start;

function convert($seconds) {
	$s = $seconds%60;
	$m = floor(($seconds%3600)/60);
	$h = floor(($seconds%86400)/3600);
	$d = floor($seconds/86400);
	return "$d days, $h hours, $m ' and $s ''";
}
?>

<div class="wrap">

<h2>Post Rotation</h2>

<?php settings_errors(); ?>

<form action="options.php" method="post">

<?php
settings_fields('pr-settings-group');

if (get_option('pr_enabled') == 1) {
	$this_moment  = strtotime(current_time('mysql'));
	$post_type = 'post';
	$post_status = 'publish';
	$newest_post_time = $wpdb->get_var($wpdb->prepare("SELECT post_date FROM $wpdb->posts WHERE post_type = %s AND post_status = %s ORDER BY post_date DESC LIMIT 0,1", $post_type, $post_status));
	$wpdb->flush();
	$newest_post_time  = strtotime($newest_post_time);
	$diference = $this_moment - $newest_post_time;
	$pr_interval = get_option('pr_interval');
	$pr_interval = $pr_interval * 3600;
	echo '<h3>Rotation is enabled</h3>';
	if ($matches == 0) {
		echo '<p class="detail">... but there is no post that matches with your criteria!</p>';
	}
	else {
		echo '<p class="detail">In ' .convert($pr_interval - $diference). ' this post will be ready for being altered:</p>';
		echo '<p class="detail">(post ID: ' .selectedpost($limit_start). ') - ' .get_post(selectedpost($limit_start)) -> post_title. '</p>';
	}
}
else {
	echo '<h3>Rotation is disabled</h3>';
}
?>

<table class="form-table">
<tr>
<td>
<input type="checkbox" id="pr_enabled" name="pr_enabled" value="1" <?php checked(get_option('pr_enabled')); ?>/>
<label>Enable rotation.</label>
</td>
</tr>

<tr>
<td>
<input type="text" size="3" maxlength="3" id="pr_interval" name="pr_interval" value="<?php echo get_option('pr_interval'); ?>"/>
<label>Interval in hours without new posts (1-999).</label>
</td>
</tr>

<tr>
<td>
<input type="checkbox" id="pr_also_alter_last_modified" name="pr_also_alter_last_modified" value="1" <?php checked(get_option('pr_also_alter_last_modified')); ?>/>
<label>Also alter 'last_modified'.</label>
</td>
</tr>

</table>

<h3>Included categories</h3>

<script>
jQuery(function($) {
	$('#select_all_categories').on('click',function() {
		if ($(this).is(':checked')) {
		$('.chkbx').each(function() {
		this.checked = true;
		});
		}
		else {
		$('.chkbx').each(function() {
		this.checked = false;
		});
		}
	})
});
</script>

<table class="form-table">
<tr>
<td>
<input type="checkbox" id="select_all_categories">
<label class="check_all">all</label>
</td>
</tr>
</table>

<div id="categories_container">

<?php
$categories_arg = array('hide_empty' => 0);
$categories = get_categories($categories_arg);
foreach ($categories as $key => $value) {
?>

<div class="categories_block">

<input type="checkbox" class="chkbx" id="pr_included_categories" name="pr_included_categories[]" value="<?php echo $value->term_id; ?>"
<?php
if (is_array(get_option('pr_included_categories')) && in_array($value->term_id, get_option('pr_included_categories'))) {
    echo 'checked="checked"';
}
?>
/>

<label><?php echo $value->name; ?></label>

</div>

<?php
}
?>

</div>

<h3>Clean uninstall</h3>

<table class="form-table">
<tr>
<td>
<input type="checkbox" id="pr_clean_uninstall" name="pr_clean_uninstall" value="1" <?php checked(get_option('pr_clean_uninstall')); ?>/>
<label>Delete all options from database when you delete this plugin (if you only deactivate the plugin, the options won't be deleted).</label>
</td>
</tr>
</table>

<?php submit_button(); ?>

</form>

<h4>Do you like this plugin?</h4>

  <ul>        
    <li>Please, <a href="http://wordpress.org/support/view/plugin-reviews/post-rotation" target="_blank">rate it on the repository</a>.</li>
    <li>Please, visit <a href="http://www.digitalemphasis.com/donate/" target="_blank">http://www.digitalemphasis.com/donate/</a>.</li>
  </ul>
  
<h4>Thank you!</h4>

</div>