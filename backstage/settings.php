<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', '../');
require LUNA_ROOT.'include/common.php';

if (!$is_admin)
	header("Location: login.php");
if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/settings.php', __('Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database (look for o_base_url in the config table) and then clear the cache by deleting all .php files in the /cache directory.', 'luna'));

	$form = array(
		'board_title'			=> luna_trim($_POST['form']['board_title']),
		'board_desc'			=> luna_trim($_POST['form']['board_desc']),
		'default_lang'			=> luna_trim($_POST['form']['default_lang']),
		'board_tags'			=> luna_trim($_POST['form']['board_tags']),
		'base_url'				=> luna_trim($_POST['form']['base_url']),
		'timezone'				=> luna_trim($_POST['form']['timezone']),
		'time_format'			=> luna_trim($_POST['form']['time_format']),
		'date_format'			=> luna_trim($_POST['form']['date_format']),
		'timeout_visit'			=> (intval($_POST['form']['timeout_visit']) > 0) ? intval($_POST['form']['timeout_visit']) : 1,
		'timeout_online'		=> (intval($_POST['form']['timeout_online']) > 0) ? intval($_POST['form']['timeout_online']) : 1,
		'feed_type'				=> intval($_POST['form']['feed_type']),
		'feed_ttl'				=> intval($_POST['form']['feed_ttl']),
		'report_method'			=> intval($_POST['form']['report_method']),
		'mailing_list'			=> luna_trim($_POST['form']['mailing_list']),
		'cookie_bar'			=> isset($_POST['form']['cookie_bar']) ? '1' : '0',
		'cookie_bar_url'		=> luna_trim($_POST['form']['cookie_bar_url']),
		'avatars'				=> isset($_POST['form']['avatars']) ? '1' : '0',
		'avatars_dir'			=> luna_trim($_POST['form']['avatars_dir']),
		'avatars_width'			=> (intval($_POST['form']['avatars_width']) > 0) ? intval($_POST['form']['avatars_width']) : 1,
		'avatars_height'		=> (intval($_POST['form']['avatars_height']) > 0) ? intval($_POST['form']['avatars_height']) : 1,
		'avatars_size'			=> (intval($_POST['form']['avatars_size']) > 0) ? intval($_POST['form']['avatars_size']) : 1,
		'announcement'			=> isset($_POST['form']['announcement']) ? '1' : '0',
		'announcement_title'	=> luna_trim($_POST['form']['announcement_title']),
		'announcement_type'		=> luna_trim($_POST['form']['announcement_type']),
		'announcement_message'	=> luna_trim($_POST['form']['announcement_message']),
	);

	if ($form['board_title'] == '')
		message_backstage(__('You must enter a title.', 'luna'));

	// Make sure base_url doesn't end with a slash
	if (substr($form['base_url'], -1) == '/')
		$form['base_url'] = substr($form['base_url'], 0, -1);
		
	// Convert IDN to Punycode if needed  
	if (preg_match('/[^\x00-\x7F]/', $form['base_url'])) {
		if (!function_exists('idn_to_ascii'))
			message_backstage(__('Your installation does not support automatic conversion of internationalized domain names. As your base URL contains special characters, you <strong>must</strong> use an online converter.', 'luna'));
		else
			$form['base_url'] = idn_to_ascii($form['base_url']);
	}

	$languages = forum_list_langs();
	if (!in_array($form['default_lang'], $languages))
		message_backstage(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

	if ($form['time_format'] == '')
		$form['time_format'] = 'H:i:s';

	if ($form['date_format'] == '')
		$form['date_format'] = 'Y-m-d';

	require LUNA_ROOT.'include/email.php';

	if ($form['mailing_list'] != '')
		$form['mailing_list'] = strtolower(preg_replace('%\s%S', '', $form['mailing_list']));

	// Make sure avatars_dir doesn't end with a slash
	if (substr($form['avatars_dir'], -1) == '/')
		$form['avatars_dir'] = substr($form['avatars_dir'], 0, -1);

	// Change or enter a SMTP password
	if (isset($_POST['form']['smtp_change_pass'])) {
		$smtp_pass1 = isset($_POST['form']['smtp_pass1']) ? luna_trim($_POST['form']['smtp_pass1']) : '';
		$smtp_pass2 = isset($_POST['form']['smtp_pass2']) ? luna_trim($_POST['form']['smtp_pass2']) : '';

		if ($smtp_pass1 == $smtp_pass2)
			$form['smtp_pass'] = $smtp_pass1;
		else
			message_backstage(__('You need to enter the SMTP password twice exactly the same to change it.', 'luna'));
	}

	if ($form['announcement_message'] != '')
		$form['announcement_message'] = luna_linebreaks($form['announcement_message']);
	else {
		$form['announcement_message'] = __('Enter your announcement here.', 'luna');
		$form['announcement'] = '0';
	}

	if ($form['feed_type'] < 0 || $form['feed_type'] > 2)
		message_backstage(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

	if ($form['feed_ttl'] < 0)
		message_backstage(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

	if ($form['report_method'] < 0 || $form['report_method'] > 2)
		message_backstage(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

	if ($form['timeout_online'] >= $form['timeout_visit'])
		message_backstage(__('The value of "Timeout online" must be smaller than the value of "Timeout visit".', 'luna'));

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
	if (!defined('LUNA_CACHE_FUNCTIONS_LOADED'))
		require LUNA_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/settings.php?saved=true');
}

$timestamp = time();

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('Global settings', 'luna'));
define('LUNA_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('settings', 'settings');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success">'.__('Your settings have been saved.', 'luna').'</div>'
?>
<form class="form-horizontal" method="post" action="settings.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Essentials', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Board title', 'luna') ?></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[board_title]" maxlength="255" value="<?php echo luna_htmlspecialchars($luna_config['o_board_title']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Board description', 'luna') ?><span class="help-block"><?php _e('What\'s this board about?', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[board_desc]" maxlength="255" value="<?php echo luna_htmlspecialchars($luna_config['o_board_desc']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Default language', 'luna') ?><span class="help-block"><?php _e('The default language', 'luna') ?></span></label>
					<div class="col-sm-9">
						<select class="form-control" name="form[default_lang]">
<?php

		$languages = forum_list_langs();

		foreach ($languages as $temp) {
			if ($luna_config['o_default_lang'] == $temp)
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$temp.'" selected>'.$temp.'</option>'."\n";
			else
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$temp.'">'.$temp.'</option>'."\n";
		}

?>
						</select>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Meta', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Board tags', 'luna') ?><span class="help-block"><?php _e('Add some words that describe your board, separated by a comma', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[board_tags]" maxlength="255" value="<?php echo luna_htmlspecialchars($luna_config['o_board_tags']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Board URL', 'luna') ?></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[base_url]" maxlength="100" value="<?php echo luna_htmlspecialchars($luna_config['o_base_url']) ?>" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Time and timeouts', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Time format', 'luna') ?><span class="help-block"><?php printf(__('Now: %s. See %s for more info', 'luna'), date($luna_config['o_time_format'], $timestamp), '<a href="http://www.php.net/manual/en/function.date.php">'.__('PHP manual', 'luna').'</a>') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[time_format]" maxlength="25" value="<?php echo luna_htmlspecialchars($luna_config['o_time_format']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Date format', 'luna') ?><span class="help-block"><?php printf(__('Now: %s. See %s for more info', 'luna'), date($luna_config['o_date_format'], $timestamp), '<a href="http://www.php.net/manual/en/function.date.php">'.__('PHP manual', 'luna').'</a>') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[date_format]" maxlength="25" value="<?php echo luna_htmlspecialchars($luna_config['o_date_format']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Default time zone', 'luna') ?></label>
					<div class="col-sm-9">
						<select class="form-control" name="form[timezone]">
<?php
$timezones = DateTimeZone::listIdentifiers();
foreach ($timezones as $timezone) {
?>
								<option value="<?php echo $timezone ?>"<?php if ($luna_config['o_timezone'] == $timezone) echo ' selected' ?>><?php echo $timezone ?></option>
<?php
}
?>
						</select>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Visit timeout', 'luna') ?><span class="help-block"><?php _e('Time before a visit ends', 'luna') ?></span></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="number" class="form-control" name="form[timeout_visit]" maxlength="5" value="<?php echo $luna_config['o_timeout_visit'] ?>" />
							<span class="input-group-addon"><?php _e('seconds', 'luna') ?></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Online timeout', 'luna') ?><span class="help-block"><?php _e('Time before someone isn\'t online anymore', 'luna') ?></span>
</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="number" class="form-control" name="form[timeout_online]" maxlength="5" value="<?php echo $luna_config['o_timeout_online'] ?>" />
							<span class="input-group-addon"><?php _e('seconds', 'luna') ?></span>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Syndication', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Default feed type', 'luna') ?><span class="help-block"><?php _e('Select a feed', 'luna') ?></span></label>
					<div class="col-sm-9">
						<label class="radio-inline">
							<input type="radio" name="form[feed_type]" value="0"<?php if ($luna_config['o_feed_type'] == '0') echo ' checked' ?>>
							<?php _e('None', 'luna') ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="form[feed_type]" value="1"<?php if ($luna_config['o_feed_type'] == '1') echo ' checked' ?>>
							<?php _e('RSS', 'luna') ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="form[feed_type]" value="2"<?php if ($luna_config['o_feed_type'] == '2') echo ' checked' ?>>
							<?php _e('Atom', 'luna') ?>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Duration to cache feeds', 'luna') ?><span class="help-block"><?php _e('Reduce sources by caching feeds', 'luna') ?></span></label>
					<div class="col-sm-9">
						<select class="form-control" name="form[feed_ttl]">
							<option value="0"<?php if ($luna_config['o_feed_ttl'] == '0') echo ' selected'; ?>><?php _e('Don\'t cache', 'luna') ?></option>
<?php

		$times = array(5, 15, 30, 60);

		foreach ($times as $time)
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$time.'"'.($luna_config['o_feed_ttl'] == $time ? ' selected' : '').'>'.sprintf(__('%d minutes', 'luna'), $time).'</option>'."\n";

?>
						</select>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Reports', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Reporting method', 'luna') ?><span class="help-block"><?php _e('How should we handle reports?', 'luna') ?></span></label>
					<div class="col-sm-9">
						<label class="radio-inline">
							<input type="radio" name="form[report_method]" value="0"<?php if ($luna_config['o_report_method'] == '0') echo ' checked' ?> />
							<?php _e('Internal', 'luna') ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="form[report_method]" value="1"<?php if ($luna_config['o_report_method'] == '1') echo ' checked' ?> />
							<?php _e('Email', 'luna') ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="form[report_method]" value="2"<?php if ($luna_config['o_report_method'] == '2') echo ' checked' ?> />
							<?php _e('Both', 'luna') ?>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Mailing list', 'luna') ?><span class="help-block"><?php _e('A comma separated list of subscribers who get e-mails when new reports are made', 'luna') ?></span></label>
					<div class="col-sm-9">
						<textarea class="form-control" name="form[mailing_list]" rows="5"><?php echo luna_htmlspecialchars($luna_config['o_mailing_list']) ?></textarea>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Cookie bar', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Cookie bar', 'luna') ?><span class="help-block"><a href="http://getluna.org/docs/cookies.php"><?php _e('More info', 'luna') ?></a></span></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[cookie_bar]" value="1" <?php if ($luna_config['o_cookie_bar'] == '1') echo ' checked' ?> />
								<?php _e('Show a bar with information about cookies at the bottom of the page.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Cookie bar URL', 'luna') ?><span class="help-block"><?php _e('Use your own URL for cookie information, by default, we provide our own page', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[cookie_bar_url]" maxlength="255" value="<?php echo luna_htmlspecialchars($luna_config['o_cookie_bar_url']) ?>" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Avatars', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Use avatars', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[avatars]" value="1" <?php if ($luna_config['o_avatars'] == '1') echo ' checked' ?> />
								<?php _e('Enable so users can upload avatars.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Upload directory', 'luna') ?><span class="help-block"><?php _e('Where avatars will be stored relative to Lunas root, write permission required', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[avatars_dir]" maxlength="50" value="<?php echo luna_htmlspecialchars($luna_config['o_avatars_dir']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Max width', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="number" class="form-control" name="form[avatars_width]" maxlength="5" value="<?php echo $luna_config['o_avatars_width'] ?>" />
							<span class="input-group-addon"><?php _e('pixels', 'luna') ?></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Max height', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="number" class="form-control" name="form[avatars_height]" maxlength="5" value="<?php echo $luna_config['o_avatars_height'] ?>" />
							<span class="input-group-addon"><?php _e('pixels', 'luna') ?></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Max size', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="number" class="form-control" name="form[avatars_size]" maxlength="6" value="<?php echo $luna_config['o_avatars_size'] ?>" />
							<span class="input-group-addon"><?php _e('bytes', 'luna') ?></span>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Announcements', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Announcements', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[announcement]" value="1" <?php if ($luna_config['o_announcement'] == '1') echo ' checked' ?> />
								<?php _e('Enable this to display the below message in the board.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Announcement title', 'luna') ?><span class="help-block"><?php _e('You can leave this empty if there is no title', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[announcement_title]" value="<?php echo $luna_config['o_announcement_title'] ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Announcement type', 'luna') ?></label>
					<div class="col-sm-9">
						<label class="radio-inline">
							<input type="radio" name="form[announcement_type]" value="default"<?php if ($luna_config['o_announcement_type'] == 'default') echo ' checked' ?>>
							<?php _e('Default', 'luna') ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="form[announcement_type]" value="info"<?php if ($luna_config['o_announcement_type'] == 'info') echo ' checked' ?>>
							<?php _e('Info', 'luna') ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="form[announcement_type]" value="success"<?php if ($luna_config['o_announcement_type'] == 'success') echo ' checked' ?>>
							<?php _e('Success', 'luna') ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="form[announcement_type]" value="warning"<?php if ($luna_config['o_announcement_type'] == 'warning') echo ' checked' ?>>
							<?php _e('Warning', 'luna') ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="form[announcement_type]" value="danger"<?php if ($luna_config['o_announcement_type'] == 'danger') echo ' checked' ?>>
							<?php _e('Danger', 'luna') ?>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Announcement message', 'luna') ?></label>
					<div class="col-sm-9">
						<textarea class="form-control" name="form[announcement_message]" rows="5"><?php echo luna_htmlspecialchars($luna_config['o_announcement_message']) ?></textarea>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
<?php

require 'footer.php';
