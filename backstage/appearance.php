<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
	header("Location: ../login.php");
if ($luna_user['g_id'] != FORUM_ADMIN)
	message_backstage($lang['No permission'], false, '403 Forbidden');

if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/appearance.php', $lang['Bad HTTP Referer message']);
	
	$form = array(
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

	// Make sure the number of displayed topics and posts is between 3 and 75
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

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Appearance']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('settings', 'appearance');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>'
?>
<form class="form-horizontal" method="post" action="appearance.php">
	<input type="hidden" name="form_sent" value="1" />
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Display head'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['User profile head'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[show_user_info]" value="1" <?php if ($luna_config['o_show_user_info'] == '1') echo ' checked' ?> />
								<?php echo $lang['Info in posts help'] ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[show_post_count]" value="1" <?php if ($luna_config['o_show_post_count'] == '1') echo ' checked' ?> />
								<?php echo $lang['Post count help'] ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Index panels head'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[moderated_by]" value="1" <?php if ($luna_config['o_moderated_by'] == '1') echo ' checked' ?> />
								<?php echo $lang['Moderated by help'] ?>
							</label>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label">Emoji</label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[emoji]" value="1" <?php if ($luna_config['o_emoji'] == '1') echo ' checked' ?> />
								Use emojis instead of emoticons.
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">Smilies size<span class="help-block">The emoticons and emojis are shown, don't go above 29 pixels when using normal emoticons</span></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" name="form[emoji_size]" maxlength="2" value="<?php echo $luna_config['o_emoji_size'] ?>" />
							<span class="input-group-addon"><?php echo $lang['pixels'] ?></span>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Topic review label'] ?><span class="help-block"><?php echo $lang['Topic review help'] ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[topic_review]" maxlength="2" value="<?php echo $luna_config['o_topic_review'] ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Topics per page'] ?><span class="help-block"><?php echo $lang['Topics per page help'] ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[disp_topics_default]" maxlength="2" value="<?php echo $luna_config['o_disp_topics_default'] ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Posts per page label'] ?><span class="help-block"><?php echo $lang['Posts per page help'] ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[disp_posts_default]" maxlength="3" value="<?php echo $luna_config['o_disp_posts_default'] ?>" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Header settings<span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label">Notifications</label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[notification_flyout]" value="1" <?php if ($luna_config['o_notification_flyout'] == '1') echo ' checked' ?> />
								Show a fly-out when clicking the notification icon instead of going to the notification page. Disableing this feature might improve performance.
							</label>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label">Search</label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[header_search]" value="1" <?php if ($luna_config['o_header_search'] == '1') echo ' checked' ?> />
								Show the search bar in the heading.
							</label>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Footer settings<span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label">Statistics</label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[board_statistics]" value="1" <?php if ($luna_config['o_board_statistics'] == '1') echo ' checked' ?> />
								Show the board statistics.
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">Back to top</label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[back_to_top]" value="1" <?php if ($luna_config['o_back_to_top'] == '1') echo ' checked' ?> />
								Show a "Back to top" link in the footer.
							</label>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label">Copyright</label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[show_copyright]" value="1" <?php if ($luna_config['o_show_copyright'] == '1') echo ' checked' ?> />
								Show the copyright notice in the footer.
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">Copyright content</label>
					<div class="col-sm-9">
						<div class="radio">
							<label>
								<input type="radio" name="form[copyright_type]" id="o_copyright_type_0" value="0"<?php if ($luna_config['o_copyright_type'] == '0') echo ' checked' ?> />
								Show default copyright
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="form[copyright_type]" id="o_copyright_type_1" value="1"<?php if ($luna_config['o_copyright_type'] == '1') echo ' checked' ?> />
								show personalized copyright notices:
							</label><br /><br />
							<input type="text" class="form-control" name="form[custom_copyright]" placeholder="Your copyright" value="<?php echo $luna_config['o_custom_copyright'] ?>" />
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
<?php

require 'footer.php';
