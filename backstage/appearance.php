<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$is_admin)
	header("Location: login.php");
if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/appearance.php', __('Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database (look for o_base_url in the config table) and then clear the cache by deleting all .php files in the /cache directory.', 'luna'));
	
	$form = array(
		'default_accent'		=> intval($_POST['form']['default_accent']),
		'allow_accent_color'	=> isset($_POST['form']['allow_accent_color']) ? '1' : '0',
		'allow_night_mode'		=> isset($_POST['form']['allow_night_mode']) ? '1' : '0',
		'show_user_info'		=> isset($_POST['form']['show_user_info']) ? '1' : '0',
		'show_post_count'		=> isset($_POST['form']['show_post_count']) ? '1' : '0',
		'moderated_by'			=> isset($_POST['form']['moderated_by']) ? '1' : '0',
		'emoji'					=> isset($_POST['form']['emoji']) ? '1' : '0',
		'emoji_size'			=> intval($_POST['form']['emoji_size']),
		'topic_review'			=> (intval($_POST['form']['topic_review']) >= 0) ? intval($_POST['form']['topic_review']) : 0,
		'disp_topics_default'	=> intval($_POST['form']['disp_topics_default']),
		'disp_posts_default'	=> intval($_POST['form']['disp_posts_default']),
		'board_statistics'		=> isset($_POST['form']['board_statistics']) ? '1' : '0',
		'back_to_top'			=> isset($_POST['form']['back_to_top']) ? '1' : '0',
		'notification_flyout'	=> isset($_POST['form']['notification_flyout']) ? '1' : '0',
		'header_search'			=> isset($_POST['form']['header_search']) ? '1' : '0',
		'show_copyright'		=> isset($_POST['form']['show_copyright']) ? '1' : '0',
		'copyright_type'		=> intval($_POST['form']['copyright_type']),
		'custom_copyright'		=> luna_trim($_POST['form']['custom_copyright']),
	);

	// Make sure the number of displayed threads and comments is between 3 and 75
	if ($form['disp_topics_default'] < 3)
		$form['disp_topics_default'] = 3;
	elseif ($form['disp_topics_default'] > 75)
		$form['disp_topics_default'] = 75;

	if ($form['disp_posts_default'] < 3)
		$form['disp_posts_default'] = 3;
	elseif ($form['disp_posts_default'] > 75)
		$form['disp_posts_default'] = 75;

	foreach ($form as $key => $input) {
		// Only update values that have changed
		if (array_key_exists('o_'.$key, $luna_config) && $luna_config['o_'.$key] != $input) {
			if ($input != '' || is_int($input))
				$value = '\''.$db->escape($input).'\'';
			else
				$value = 'NULL';

			$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$value.' WHERE conf_name=\'o_'.$db->escape($key).'\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());
		}
	}

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/appearance.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('Appearance', 'luna'));
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('settings', 'appearance');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.__('Your settings have been saved.', 'luna').'</h4></div>'
?>
<form class="form-horizontal" method="post" action="appearance.php">
	<input type="hidden" name="form_sent" value="1" />
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Display settings', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Theme settings', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[allow_accent_color]" value="1" <?php if ($luna_config['o_allow_accent_color'] == '1') echo ' checked' ?> />
								<?php _e('Allow users to set their own accent color.', 'luna') ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[allow_night_mode]" value="1" <?php if ($luna_config['o_allow_night_mode'] == '1') echo ' checked' ?> />
								<?php _e('Allow users to change the night mode settings.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Color', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="btn-group accent-group" data-toggle="buttons">
<?php
		$accents = forum_list_accents('main');

		foreach ($accents as $temp) {
			if ($luna_config['o_default_accent'] == $temp)
				echo '<label class="btn btn-primary color-accent accent-'.$temp.' active"><input type="radio" name="form[default_accent]" id="'.$temp.'" value="'.$temp.'" checked></label>';
			else
				echo '<label class="btn btn-primary color-accent accent-'.$temp.'"> <input type="radio" name="form[default_accent]" id="'.$temp.'" value="'.$temp.'"></label>';
		}
?>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('User profile', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[show_user_info]" value="1" <?php if ($luna_config['o_show_user_info'] == '1') echo ' checked' ?> />
								<?php _e('Show information about the commenter under the username in threads.', 'luna') ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[show_post_count]" value="1" <?php if ($luna_config['o_show_post_count'] == '1') echo ' checked' ?> />
								<?php _e('Show the number of comments a user has made in threads, profile and the user list.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Index settings', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[moderated_by]" value="1" <?php if ($luna_config['o_moderated_by'] == '1') echo ' checked' ?> />
								<?php _e('Show the "Moderated by" list when moderators are set on a per-forum base (requires theme support).', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Emoji', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[emoji]" value="1" <?php if ($luna_config['o_emoji'] == '1') echo ' checked' ?> />
								<?php _e('Use emojis instead of emoticons.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Smilie size', 'luna') ?><span class="help-block"><?php _e('The emoticons and emojis are shown, don\'t go above 29 pixels when using normal emoticons', 'luna') ?></span></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" name="form[emoji_size]" maxlength="2" value="<?php echo $luna_config['o_emoji_size'] ?>" />
							<span class="input-group-addon"><?php _e('pixels', 'luna') ?></span>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Thread review', 'luna') ?><span class="help-block"><?php _e('Maximum amount of comments showed when commenting', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[topic_review]" maxlength="2" value="<?php echo $luna_config['o_topic_review'] ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Threads per page', 'luna') ?><span class="help-block"><?php _e('Default amount of Threads per page', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[disp_topics_default]" maxlength="2" value="<?php echo $luna_config['o_disp_topics_default'] ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Comments per page', 'luna') ?><span class="help-block"><?php _e('Default amount of Comments per page', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[disp_posts_default]" maxlength="3" value="<?php echo $luna_config['o_disp_posts_default'] ?>" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Header settings', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Notifications', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[notification_flyout]" value="1" <?php if ($luna_config['o_notification_flyout'] == '1') echo ' checked' ?> />
								<?php _e('Show a fly-out when clicking the notification icon instead of going to the notification page. Disableing this feature might improve performance.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Search', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[header_search]" value="1" <?php if ($luna_config['o_header_search'] == '1') echo ' checked' ?> />
								<?php _e('Show the search bar in the heading.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Footer settings', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Statistics', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[board_statistics]" value="1" <?php if ($luna_config['o_board_statistics'] == '1') echo ' checked' ?> />
								<?php _e('Show the board statistics.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Back to top', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[back_to_top]" value="1" <?php if ($luna_config['o_back_to_top'] == '1') echo ' checked' ?> />
								<?php _e('Show a "Back to top" link in the footer.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Copyright', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[show_copyright]" value="1" <?php if ($luna_config['o_show_copyright'] == '1') echo ' checked' ?> />
								<?php _e('Show the copyright notice in the footer.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Copyright content', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="radio">
							<label>
								<input type="radio" name="form[copyright_type]" id="o_copyright_type_0" value="0"<?php if ($luna_config['o_copyright_type'] == '0') echo ' checked' ?> />
								<?php _e('Show default copyright', 'luna') ?>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="form[copyright_type]" id="o_copyright_type_1" value="1"<?php if ($luna_config['o_copyright_type'] == '1') echo ' checked' ?> />
								<?php _e('Show personalized copyright notices:', 'luna') ?>
							</label><br /><br />
							<input type="text" class="form-control" name="form[custom_copyright]" placeholder="<?php _e('Your copyright', 'luna') ?>" value="<?php echo luna_htmlspecialchars($luna_config['o_custom_copyright']) ?>" />
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
<?php

require 'footer.php';
