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
	confirm_referrer('backstage/settings.php', $lang['Bad HTTP Referer message']);

	$form = array(
		'board_title'			=> luna_trim($_POST['form']['board_title']),
		'board_desc'			=> luna_trim($_POST['form']['board_desc']),
		'base_url'				=> luna_trim($_POST['form']['base_url']),
		'default_timezone'		=> floatval($_POST['form']['default_timezone']),
		'default_dst'			=> isset($_POST['form']['default_dst']) ? '1' : '0',
		'default_lang'			=> luna_trim($_POST['form']['default_lang']),
		'time_format'			=> luna_trim($_POST['form']['time_format']),
		'date_format'			=> luna_trim($_POST['form']['date_format']),
		'timeout_visit'			=> (intval($_POST['form']['timeout_visit']) > 0) ? intval($_POST['form']['timeout_visit']) : 1,
		'timeout_online'		=> (intval($_POST['form']['timeout_online']) > 0) ? intval($_POST['form']['timeout_online']) : 1,
		'feed_type'				=> intval($_POST['form']['feed_type']),
		'feed_ttl'				=> intval($_POST['form']['feed_ttl']),
		'report_method'			=> intval($_POST['form']['report_method']),
		'mailing_list'			=> luna_trim($_POST['form']['mailing_list']),
		'cookie_bar'			=> isset($_POST['form']['cookie_bar']) ? '1' : '0',
		'avatars'				=> isset($_POST['form']['avatars']) ? '1' : '0',
		'avatars_dir'			=> luna_trim($_POST['form']['avatars_dir']),
		'avatars_width'			=> (intval($_POST['form']['avatars_width']) > 0) ? intval($_POST['form']['avatars_width']) : 1,
		'avatars_height'		=> (intval($_POST['form']['avatars_height']) > 0) ? intval($_POST['form']['avatars_height']) : 1,
		'avatars_size'			=> (intval($_POST['form']['avatars_size']) > 0) ? intval($_POST['form']['avatars_size']) : 1,
		'announcement'			=> isset($_POST['form']['announcement']) ? '1' : '0',
		'announcement_message'	=> luna_trim($_POST['form']['announcement_message']),
	);

	if ($form['board_title'] == '')
		message_backstage($lang['Must enter title message']);

	// Make sure base_url doesn't end with a slash
	if (substr($form['base_url'], -1) == '/')
		$form['base_url'] = substr($form['base_url'], 0, -1);
		
	// Convert IDN to Punycode if needed  
	if (preg_match('/[^\x00-\x7F]/', $form['base_url']))   {  
		if (!function_exists('idn_to_ascii'))  
			message_backstage($lang['Base URL problem']);  
		else  
			$form['base_url'] = idn_to_ascii($form['base_url']);  
	}

	$languages = forum_list_langs();
	if (!in_array($form['default_lang'], $languages))
		message_backstage($lang['Bad request'], false, '404 Not Found');

	if ($form['time_format'] == '')
		$form['time_format'] = 'H:i:s';

	if ($form['date_format'] == '')
		$form['date_format'] = 'Y-m-d';


	require FORUM_ROOT.'include/email.php';

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
			message_backstage($lang['SMTP passwords did not match']);
	}

	if ($form['announcement_message'] != '')
		$form['announcement_message'] = luna_linebreaks($form['announcement_message']);
	else {
		$form['announcement_message'] = $lang['Enter announcement here'];
		$form['announcement'] = '0';
	}

	if ($form['feed_type'] < 0 || $form['feed_type'] > 2)
		message_backstage($lang['Bad request'], false, '404 Not Found');

	if ($form['feed_ttl'] < 0)
		message_backstage($lang['Bad request'], false, '404 Not Found');

	if ($form['report_method'] < 0 || $form['report_method'] > 2)
		message_backstage($lang['Bad request'], false, '404 Not Found');

	if ($form['timeout_online'] >= $form['timeout_visit'])
		message_backstage($lang['Timeout error message']);

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

	redirect('backstage/settings.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('settings', 'settings');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>'
?>
<form class="form-horizontal" method="post" action="settings.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Essentials subhead'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Board title'] ?></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[board_title]" maxlength="255" value="<?php echo luna_htmlspecialchars($luna_config['o_board_title']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Board description'] ?><span class="help-block"><?php echo $lang['Board desc help'] ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[board_desc]" maxlength="255" value="<?php echo luna_htmlspecialchars($luna_config['o_board_desc']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Base URL label'] ?></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[base_url]" maxlength="100" value="<?php echo luna_htmlspecialchars($luna_config['o_base_url']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Default language'] ?><span class="help-block"><?php echo $lang['Language help'] ?></span></label>
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
<?php

	$diff = ($luna_user['timezone'] + $luna_user['dst']) * 3600;
	$timestamp = time() + $diff;

?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Timeouts subhead'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Time format'] ?><span class="help-block"><?php printf($lang['Time format help'], gmdate($luna_config['o_time_format'], $timestamp), '<a href="http://www.php.net/manual/en/function.date.php">'.$lang['PHP manual'].'</a>') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[time_format]" maxlength="25" value="<?php echo luna_htmlspecialchars($luna_config['o_time_format']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Date format'] ?><span class="help-block"><?php printf($lang['Date format help'], gmdate($luna_config['o_date_format'], $timestamp), '<a href="http://www.php.net/manual/en/function.date.php">'.$lang['PHP manual'].'</a>') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[date_format]" maxlength="25" value="<?php echo luna_htmlspecialchars($luna_config['o_date_format']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Timezone label'] ?></label>
					<div class="col-sm-9">
						<select class="form-control" name="form[default_timezone]">
							<option value="-12"<?php if ($luna_config['o_default_timezone'] == -12) echo ' selected' ?>><?php echo $lang['UTC-12:00'] ?></option>
							<option value="-11"<?php if ($luna_config['o_default_timezone'] == -11) echo ' selected' ?>><?php echo $lang['UTC-11:00'] ?></option>
							<option value="-10"<?php if ($luna_config['o_default_timezone'] == -10) echo ' selected' ?>><?php echo $lang['UTC-10:00'] ?></option>
							<option value="-9.5"<?php if ($luna_config['o_default_timezone'] == -9.5) echo ' selected' ?>><?php echo $lang['UTC-09:30'] ?></option>
							<option value="-9"<?php if ($luna_config['o_default_timezone'] == -9) echo ' selected' ?>><?php echo $lang['UTC-09:00'] ?></option>
							<option value="-8.5"<?php if ($luna_config['o_default_timezone'] == -8.5) echo ' selected' ?>><?php echo $lang['UTC-08:30'] ?></option>
							<option value="-8"<?php if ($luna_config['o_default_timezone'] == -8) echo ' selected' ?>><?php echo $lang['UTC-08:00'] ?></option>
							<option value="-7"<?php if ($luna_config['o_default_timezone'] == -7) echo ' selected' ?>><?php echo $lang['UTC-07:00'] ?></option>
							<option value="-6"<?php if ($luna_config['o_default_timezone'] == -6) echo ' selected' ?>><?php echo $lang['UTC-06:00'] ?></option>
							<option value="-5"<?php if ($luna_config['o_default_timezone'] == -5) echo ' selected' ?>><?php echo $lang['UTC-05:00'] ?></option>
							<option value="-4"<?php if ($luna_config['o_default_timezone'] == -4) echo ' selected' ?>><?php echo $lang['UTC-04:00'] ?></option>
							<option value="-3.5"<?php if ($luna_config['o_default_timezone'] == -3.5) echo ' selected' ?>><?php echo $lang['UTC-03:30'] ?></option>
							<option value="-3"<?php if ($luna_config['o_default_timezone'] == -3) echo ' selected' ?>><?php echo $lang['UTC-03:00'] ?></option>
							<option value="-2"<?php if ($luna_config['o_default_timezone'] == -2) echo ' selected' ?>><?php echo $lang['UTC-02:00'] ?></option>
							<option value="-1"<?php if ($luna_config['o_default_timezone'] == -1) echo ' selected' ?>><?php echo $lang['UTC-01:00'] ?></option>
							<option value="0"<?php if ($luna_config['o_default_timezone'] == 0) echo ' selected' ?>><?php echo $lang['UTC'] ?></option>
							<option value="1"<?php if ($luna_config['o_default_timezone'] == 1) echo ' selected' ?>><?php echo $lang['UTC+01:00'] ?></option>
							<option value="2"<?php if ($luna_config['o_default_timezone'] == 2) echo ' selected' ?>><?php echo $lang['UTC+02:00'] ?></option>
							<option value="3"<?php if ($luna_config['o_default_timezone'] == 3) echo ' selected' ?>><?php echo $lang['UTC+03:00'] ?></option>
							<option value="3.5"<?php if ($luna_config['o_default_timezone'] == 3.5) echo ' selected' ?>><?php echo $lang['UTC+03:30'] ?></option>
							<option value="4"<?php if ($luna_config['o_default_timezone'] == 4) echo ' selected' ?>><?php echo $lang['UTC+04:00'] ?></option>
							<option value="4.5"<?php if ($luna_config['o_default_timezone'] == 4.5) echo ' selected' ?>><?php echo $lang['UTC+04:30'] ?></option>
							<option value="5"<?php if ($luna_config['o_default_timezone'] == 5) echo ' selected' ?>><?php echo $lang['UTC+05:00'] ?></option>
							<option value="5.5"<?php if ($luna_config['o_default_timezone'] == 5.5) echo ' selected' ?>><?php echo $lang['UTC+05:30'] ?></option>
							<option value="5.75"<?php if ($luna_config['o_default_timezone'] == 5.75) echo ' selected' ?>><?php echo $lang['UTC+05:45'] ?></option>
							<option value="6"<?php if ($luna_config['o_default_timezone'] == 6) echo ' selected' ?>><?php echo $lang['UTC+06:00'] ?></option>
							<option value="6.5"<?php if ($luna_config['o_default_timezone'] == 6.5) echo ' selected' ?>><?php echo $lang['UTC+06:30'] ?></option>
							<option value="7"<?php if ($luna_config['o_default_timezone'] == 7) echo ' selected' ?>><?php echo $lang['UTC+07:00'] ?></option>
							<option value="8"<?php if ($luna_config['o_default_timezone'] == 8) echo ' selected' ?>><?php echo $lang['UTC+08:00'] ?></option>
							<option value="8.75"<?php if ($luna_config['o_default_timezone'] == 8.75) echo ' selected' ?>><?php echo $lang['UTC+08:45'] ?></option>
							<option value="9"<?php if ($luna_config['o_default_timezone'] == 9) echo ' selected' ?>><?php echo $lang['UTC+09:00'] ?></option>
							<option value="9.5"<?php if ($luna_config['o_default_timezone'] == 9.5) echo ' selected' ?>><?php echo $lang['UTC+09:30'] ?></option>
							<option value="10"<?php if ($luna_config['o_default_timezone'] == 10) echo ' selected' ?>><?php echo $lang['UTC+10:00'] ?></option>
							<option value="10.5"<?php if ($luna_config['o_default_timezone'] == 10.5) echo ' selected' ?>><?php echo $lang['UTC+10:30'] ?></option>
							<option value="11"<?php if ($luna_config['o_default_timezone'] == 11) echo ' selected' ?>><?php echo $lang['UTC+11:00'] ?></option>
							<option value="11.5"<?php if ($luna_config['o_default_timezone'] == 11.5) echo ' selected' ?>><?php echo $lang['UTC+11:30'] ?></option>
							<option value="12"<?php if ($luna_config['o_default_timezone'] == 12) echo ' selected' ?>><?php echo $lang['UTC+12:00'] ?></option>
							<option value="12.75"<?php if ($luna_config['o_default_timezone'] == 12.75) echo ' selected' ?>><?php echo $lang['UTC+12:45'] ?></option>
							<option value="13"<?php if ($luna_config['o_default_timezone'] == 13) echo ' selected' ?>><?php echo $lang['UTC+13:00'] ?></option>
							<option value="14"<?php if ($luna_config['o_default_timezone'] == 14) echo ' selected' ?>><?php echo $lang['UTC+14:00'] ?></option>
						</select>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[default_dst]" value="1" <?php if ($luna_config['o_default_dst'] == '1') echo ' checked' ?> />
								<?php echo $lang['DST help'] ?>
							</label>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Visit timeout label'] ?><span class="help-block"><?php echo $lang['Visit timeout help'] ?></span></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" name="form[timeout_visit]" maxlength="5" value="<?php echo $luna_config['o_timeout_visit'] ?>" />
							<span class="input-group-addon"><?php echo $lang['seconds'] ?></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Online timeout label'] ?><span class="help-block"><?php echo $lang['Online timeout help'] ?></span>
</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" name="form[timeout_online]" maxlength="5" value="<?php echo $luna_config['o_timeout_online'] ?>" />
							<span class="input-group-addon"><?php echo $lang['seconds'] ?></span>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Feed subhead'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Default feed label'] ?><span class="help-block"><?php echo $lang['Default feed help'] ?></span></label>
					<div class="col-sm-9">
						<label class="radio-inline">
							<input type="radio" name="form[feed_type]" value="0"<?php if ($luna_config['o_feed_type'] == '0') echo ' checked' ?>>
							<?php echo $lang['None'] ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="form[feed_type]" value="1"<?php if ($luna_config['o_feed_type'] == '1') echo ' checked' ?>>
							<?php echo $lang['RSS'] ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="form[feed_type]" value="2"<?php if ($luna_config['o_feed_type'] == '2') echo ' checked' ?>>
							<?php echo $lang['Atom'] ?>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Feed TTL label'] ?><span class="help-block"><?php echo $lang['Feed TTL help'] ?></span></label>
					<div class="col-sm-9">
						<select class="form-control" name="form[feed_ttl]">
							<option value="0"<?php if ($luna_config['o_feed_ttl'] == '0') echo ' selected'; ?>><?php echo $lang['No cache'] ?></option>
<?php

		$times = array(5, 15, 30, 60);

		foreach ($times as $time)
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$time.'"'.($luna_config['o_feed_ttl'] == $time ? ' selected' : '').'>'.sprintf($lang['Minutes'], $time).'</option>'."\n";

?>
						</select>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Reports'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Reporting method label'] ?><span class="help-block"><?php echo $lang['Reporting method help'] ?></span></label>
					<div class="col-sm-9">
						<label class="radio-inline">
							<input type="radio" name="form[report_method]" value="0"<?php if ($luna_config['o_report_method'] == '0') echo ' checked' ?> />
							<?php echo $lang['Internal'] ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="form[report_method]" value="1"<?php if ($luna_config['o_report_method'] == '1') echo ' checked' ?> />
							<?php echo $lang['Email'] ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="form[report_method]" value="2"<?php if ($luna_config['o_report_method'] == '2') echo ' checked' ?> />
							<?php echo $lang['Both'] ?>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Mailing list label'] ?><span class="help-block"><?php echo $lang['Mailing list help'] ?></span></label>
					<div class="col-sm-9">
						<textarea class="form-control" name="form[mailing_list]" rows="5"><?php echo luna_htmlspecialchars($luna_config['o_mailing_list']) ?></textarea>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Cookie bar'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Cookie bar'] ?><span class="help-block"><a href="http://getluna.org/docs/cookies.php"><?php echo $lang['More info'] ?></a></span></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[cookie_bar]" value="1" <?php if ($luna_config['o_cookie_bar'] == '1') echo ' checked' ?> />
								<?php echo $lang['Cookie set info'] ?>
							</label>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Avatars subhead'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Use avatars label'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[avatars]" value="1" <?php if ($luna_config['o_avatars'] == '1') echo ' checked' ?> />
								<?php echo $lang['Use avatars help'] ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Upload directory label'] ?><span class="help-block"><?php echo $lang['Upload directory help'] ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[avatars_dir]" maxlength="50" value="<?php echo luna_htmlspecialchars($luna_config['o_avatars_dir']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Max width label'] ?></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" name="form[avatars_width]" maxlength="5" value="<?php echo $luna_config['o_avatars_width'] ?>" />
							<span class="input-group-addon"><?php echo $lang['pixels'] ?></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Max height label'] ?></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" name="form[avatars_height]" maxlength="5" value="<?php echo $luna_config['o_avatars_height'] ?>" />
							<span class="input-group-addon"><?php echo $lang['pixels'] ?></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Max size label'] ?></label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" name="form[avatars_size]" maxlength="6" value="<?php echo $luna_config['o_avatars_size'] ?>" />
							<span class="input-group-addon"><?php echo $lang['bytes'] ?></span>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Announcements'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Announcements'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[announcement]" value="1" <?php if ($luna_config['o_announcement'] == '1') echo ' checked' ?> />
								<?php echo $lang['Display announcement help'] ?>
							</label>
						</div>
						<textarea class="form-control" name="form[announcement_message]" rows="5"><?php echo luna_htmlspecialchars($luna_config['o_announcement_message']) ?></textarea>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
<?php

require 'footer.php';
