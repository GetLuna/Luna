<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$pun_user['is_admmod']) {
    header("Location: ../login.php");
}

if ($pun_user['g_id'] != FORUM_ADMIN)
	message($lang['No permission'], false, '403 Forbidden');

// Load the language file
require FORUM_ROOT.'lang/'.$admin_language.'/language.php';

if (isset($_POST['form_sent']))
{

	$form = array(
		'board_title'			=> pun_trim($_POST['form']['board_title']),
		'board_desc'			=> pun_trim($_POST['form']['board_desc']),
		'base_url'				=> pun_trim($_POST['form']['base_url']),
		'default_timezone'		=> floatval($_POST['form']['default_timezone']),
		'default_dst'			=> isset($_POST['form']['default_dst']) ? '1' : '0',
		'default_lang'			=> pun_trim($_POST['form']['default_lang']),
		'time_format'			=> pun_trim($_POST['form']['time_format']),
		'date_format'			=> pun_trim($_POST['form']['date_format']),
		'timeout_visit'			=> (intval($_POST['form']['timeout_visit']) > 0) ? intval($_POST['form']['timeout_visit']) : 1,
		'timeout_online'		=> (intval($_POST['form']['timeout_online']) > 0) ? intval($_POST['form']['timeout_online']) : 1,
		'redirect_delay'		=> (intval($_POST['form']['redirect_delay']) >= 0) ? intval($_POST['form']['redirect_delay']) : 0,
		'feed_type'				=> intval($_POST['form']['feed_type']),
		'feed_ttl'				=> intval($_POST['form']['feed_ttl']),
		'report_method'			=> intval($_POST['form']['report_method']),
		'mailing_list'			=> pun_trim($_POST['form']['mailing_list']),
		'avatars'				=> isset($_POST['form']['avatars']) ? '1' : '0',
		'avatars_dir'			=> pun_trim($_POST['form']['avatars_dir']),
		'avatars_width'			=> (intval($_POST['form']['avatars_width']) > 0) ? intval($_POST['form']['avatars_width']) : 1,
		'avatars_height'		=> (intval($_POST['form']['avatars_height']) > 0) ? intval($_POST['form']['avatars_height']) : 1,
		'avatars_size'			=> (intval($_POST['form']['avatars_size']) > 0) ? intval($_POST['form']['avatars_size']) : 1,
		'regs_allow'			=> isset($_POST['form']['regs_allow']) ? '1' : '0',
		'regs_verify'			=> isset($_POST['form']['regs_verify']) ? '1' : '0',
		'regs_report'			=> isset($_POST['form']['regs_report']) ? '1' : '0',
		'rules'					=> isset($_POST['form']['rules']) ? '1' : '0',
		'rules_message'			=> pun_trim($_POST['form']['rules_message']),
		'antispam_api'			=> pun_trim($_POST['form']['antispam_api']),
		'default_email_setting'	=> intval($_POST['form']['default_email_setting']),
		'announcement'			=> isset($_POST['form']['announcement']) ? '1' : '0',
		'announcement_message'	=> pun_trim($_POST['form']['announcement_message']),
		'maintenance'			=> isset($_POST['form']['maintenance']) ? '1' : '0',
		'maintenance_message'	=> pun_trim($_POST['form']['maintenance_message']),
	);

	if ($form['board_title'] == '')
		message($lang['Must enter title message']);

	// Make sure base_url doesn't end with a slash
	if (substr($form['base_url'], -1) == '/')
		$form['base_url'] = substr($form['base_url'], 0, -1);
		
	// Convert IDN to Punycode if needed  
	if (preg_match('/[^\x00-\x7F]/', $form['base_url']))  
	{  
		if (!function_exists('idn_to_ascii'))  
			message($lang['Base URL problem']);  
		else  
			$form['base_url'] = idn_to_ascii($form['base_url']);  
	}

	$languages = forum_list_langs();
	if (!in_array($form['default_lang'], $languages))
		message($lang['Bad request'], false, '404 Not Found');

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
	if (isset($_POST['form']['smtp_change_pass']))
	{
		$smtp_pass1 = isset($_POST['form']['smtp_pass1']) ? pun_trim($_POST['form']['smtp_pass1']) : '';
		$smtp_pass2 = isset($_POST['form']['smtp_pass2']) ? pun_trim($_POST['form']['smtp_pass2']) : '';

		if ($smtp_pass1 == $smtp_pass2)
			$form['smtp_pass'] = $smtp_pass1;
		else
			message($lang['SMTP passwords did not match']);
	}

	if ($form['announcement_message'] != '')
		$form['announcement_message'] = pun_linebreaks($form['announcement_message']);
	else
	{
		$form['announcement_message'] = $lang['Enter announcement here'];
		$form['announcement'] = '0';
	}

	if ($form['rules_message'] != '')
		$form['rules_message'] = pun_linebreaks($form['rules_message']);
	else
	{
		$form['rules_message'] = $lang['Enter rules here'];
		$form['rules'] = '0';
	}

	if ($form['maintenance_message'] != '')
		$form['maintenance_message'] = pun_linebreaks($form['maintenance_message']);
	else
	{
		$form['maintenance_message'] = $lang['Default maintenance message'];
		$form['maintenance'] = '0';
	}

	if ($form['feed_type'] < 0 || $form['feed_type'] > 2)
		message($lang['Bad request'], false, '404 Not Found');

	if ($form['feed_ttl'] < 0)
		message($lang['Bad request'], false, '404 Not Found');

	if ($form['report_method'] < 0 || $form['report_method'] > 2)
		message($lang['Bad request'], false, '404 Not Found');

	if ($form['default_email_setting'] < 0 || $form['default_email_setting'] > 2)
		message($lang['Bad request'], false, '404 Not Found');

	if ($form['timeout_online'] >= $form['timeout_visit'])
		message($lang['Timeout error message']);

	foreach ($form as $key => $input)
	{
		// Only update values that have changed
		if (array_key_exists('o_'.$key, $pun_config) && $pun_config['o_'.$key] != $input)
		{
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

	redirect('backstage/settings.php', $lang['Options updated redirect']);
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang['Admin'], $lang['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
generate_admin_menu('global');

?>
<h2><?php echo $lang['Options head'] ?></h2>
<form class="form-horizontal" method="post" action="settings.php">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Essentials subhead'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Board title label'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="form[board_title]" size="50" maxlength="255" value="<?php echo pun_htmlspecialchars($pun_config['o_board_title']) ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Board desc label'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="form[board_desc]" size="50" maxlength="255" value="<?php echo pun_htmlspecialchars($pun_config['o_board_desc']) ?>" />
                        <span class="help-block"><?php echo $lang['Board desc help'] ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Base URL label'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="form[base_url]" size="50" maxlength="100" value="<?php echo pun_htmlspecialchars($pun_config['o_base_url']) ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Language label'] ?></label>
                    <div class="col-sm-10">
                        <select class="form-control" name="form[default_lang]">
<?php

		$languages = forum_list_langs();

		foreach ($languages as $temp)
		{
			if ($pun_config['o_default_lang'] == $temp)
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$temp.'" selected="selected">'.$temp.'</option>'."\n";
			else
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$temp.'">'.$temp.'</option>'."\n";
		}

?>
                        </select>
                        <span class="help-block"><?php echo $lang['Language help'] ?></span>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
<?php

	$diff = ($pun_user['timezone'] + $pun_user['dst']) * 3600;
	$timestamp = time() + $diff;

?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Timeouts subhead'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Time format label'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="form[time_format]" size="25" maxlength="25" value="<?php echo pun_htmlspecialchars($pun_config['o_time_format']) ?>" />
                        <span class="help-block"><?php printf($lang['Time format help'], gmdate($pun_config['o_time_format'], $timestamp), '<a href="http://www.php.net/manual/en/function.date.php">'.$lang['PHP manual'].'</a>') ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Date format label'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="form[date_format]" size="25" maxlength="25" value="<?php echo pun_htmlspecialchars($pun_config['o_date_format']) ?>" />
                        <span class="help-block"><?php printf($lang['Date format help'], gmdate($pun_config['o_date_format'], $timestamp), '<a href="http://www.php.net/manual/en/function.date.php">'.$lang['PHP manual'].'</a>') ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Timezone label'] ?></label>
                    <div class="col-sm-10">
                        <select class="form-control" name="form[default_timezone]">
                            <option value="-12"<?php if ($pun_config['o_default_timezone'] == -12) echo ' selected="selected"' ?>><?php echo $lang['UTC-12:00'] ?></option>
                            <option value="-11"<?php if ($pun_config['o_default_timezone'] == -11) echo ' selected="selected"' ?>><?php echo $lang['UTC-11:00'] ?></option>
                            <option value="-10"<?php if ($pun_config['o_default_timezone'] == -10) echo ' selected="selected"' ?>><?php echo $lang['UTC-10:00'] ?></option>
                            <option value="-9.5"<?php if ($pun_config['o_default_timezone'] == -9.5) echo ' selected="selected"' ?>><?php echo $lang['UTC-09:30'] ?></option>
                            <option value="-9"<?php if ($pun_config['o_default_timezone'] == -9) echo ' selected="selected"' ?>><?php echo $lang['UTC-09:00'] ?></option>
                            <option value="-8.5"<?php if ($pun_config['o_default_timezone'] == -8.5) echo ' selected="selected"' ?>><?php echo $lang['UTC-08:30'] ?></option>
                            <option value="-8"<?php if ($pun_config['o_default_timezone'] == -8) echo ' selected="selected"' ?>><?php echo $lang['UTC-08:00'] ?></option>
                            <option value="-7"<?php if ($pun_config['o_default_timezone'] == -7) echo ' selected="selected"' ?>><?php echo $lang['UTC-07:00'] ?></option>
                            <option value="-6"<?php if ($pun_config['o_default_timezone'] == -6) echo ' selected="selected"' ?>><?php echo $lang['UTC-06:00'] ?></option>
                            <option value="-5"<?php if ($pun_config['o_default_timezone'] == -5) echo ' selected="selected"' ?>><?php echo $lang['UTC-05:00'] ?></option>
                            <option value="-4"<?php if ($pun_config['o_default_timezone'] == -4) echo ' selected="selected"' ?>><?php echo $lang['UTC-04:00'] ?></option>
                            <option value="-3.5"<?php if ($pun_config['o_default_timezone'] == -3.5) echo ' selected="selected"' ?>><?php echo $lang['UTC-03:30'] ?></option>
                            <option value="-3"<?php if ($pun_config['o_default_timezone'] == -3) echo ' selected="selected"' ?>><?php echo $lang['UTC-03:00'] ?></option>
                            <option value="-2"<?php if ($pun_config['o_default_timezone'] == -2) echo ' selected="selected"' ?>><?php echo $lang['UTC-02:00'] ?></option>
                            <option value="-1"<?php if ($pun_config['o_default_timezone'] == -1) echo ' selected="selected"' ?>><?php echo $lang['UTC-01:00'] ?></option>
                            <option value="0"<?php if ($pun_config['o_default_timezone'] == 0) echo ' selected="selected"' ?>><?php echo $lang['UTC'] ?></option>
                            <option value="1"<?php if ($pun_config['o_default_timezone'] == 1) echo ' selected="selected"' ?>><?php echo $lang['UTC+01:00'] ?></option>
                            <option value="2"<?php if ($pun_config['o_default_timezone'] == 2) echo ' selected="selected"' ?>><?php echo $lang['UTC+02:00'] ?></option>
                            <option value="3"<?php if ($pun_config['o_default_timezone'] == 3) echo ' selected="selected"' ?>><?php echo $lang['UTC+03:00'] ?></option>
                            <option value="3.5"<?php if ($pun_config['o_default_timezone'] == 3.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+03:30'] ?></option>
                            <option value="4"<?php if ($pun_config['o_default_timezone'] == 4) echo ' selected="selected"' ?>><?php echo $lang['UTC+04:00'] ?></option>
                            <option value="4.5"<?php if ($pun_config['o_default_timezone'] == 4.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+04:30'] ?></option>
                            <option value="5"<?php if ($pun_config['o_default_timezone'] == 5) echo ' selected="selected"' ?>><?php echo $lang['UTC+05:00'] ?></option>
                            <option value="5.5"<?php if ($pun_config['o_default_timezone'] == 5.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+05:30'] ?></option>
                            <option value="5.75"<?php if ($pun_config['o_default_timezone'] == 5.75) echo ' selected="selected"' ?>><?php echo $lang['UTC+05:45'] ?></option>
                            <option value="6"<?php if ($pun_config['o_default_timezone'] == 6) echo ' selected="selected"' ?>><?php echo $lang['UTC+06:00'] ?></option>
                            <option value="6.5"<?php if ($pun_config['o_default_timezone'] == 6.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+06:30'] ?></option>
                            <option value="7"<?php if ($pun_config['o_default_timezone'] == 7) echo ' selected="selected"' ?>><?php echo $lang['UTC+07:00'] ?></option>
                            <option value="8"<?php if ($pun_config['o_default_timezone'] == 8) echo ' selected="selected"' ?>><?php echo $lang['UTC+08:00'] ?></option>
                            <option value="8.75"<?php if ($pun_config['o_default_timezone'] == 8.75) echo ' selected="selected"' ?>><?php echo $lang['UTC+08:45'] ?></option>
                            <option value="9"<?php if ($pun_config['o_default_timezone'] == 9) echo ' selected="selected"' ?>><?php echo $lang['UTC+09:00'] ?></option>
                            <option value="9.5"<?php if ($pun_config['o_default_timezone'] == 9.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+09:30'] ?></option>
                            <option value="10"<?php if ($pun_config['o_default_timezone'] == 10) echo ' selected="selected"' ?>><?php echo $lang['UTC+10:00'] ?></option>
                            <option value="10.5"<?php if ($pun_config['o_default_timezone'] == 10.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+10:30'] ?></option>
                            <option value="11"<?php if ($pun_config['o_default_timezone'] == 11) echo ' selected="selected"' ?>><?php echo $lang['UTC+11:00'] ?></option>
                            <option value="11.5"<?php if ($pun_config['o_default_timezone'] == 11.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+11:30'] ?></option>
                            <option value="12"<?php if ($pun_config['o_default_timezone'] == 12) echo ' selected="selected"' ?>><?php echo $lang['UTC+12:00'] ?></option>
                            <option value="12.75"<?php if ($pun_config['o_default_timezone'] == 12.75) echo ' selected="selected"' ?>><?php echo $lang['UTC+12:45'] ?></option>
                            <option value="13"<?php if ($pun_config['o_default_timezone'] == 13) echo ' selected="selected"' ?>><?php echo $lang['UTC+13:00'] ?></option>
                            <option value="14"<?php if ($pun_config['o_default_timezone'] == 14) echo ' selected="selected"' ?>><?php echo $lang['UTC+14:00'] ?></option>
                        </select>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="form[default_dst]" value="1" <?php if ($pun_config['o_default_dst'] == '1') echo ' checked="checked"' ?> />
                                <?php echo $lang['DST help'] ?>
                            </label>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Visit timeout label'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="form[timeout_visit]" size="5" maxlength="5" value="<?php echo $pun_config['o_timeout_visit'] ?>" />
                        <span class="help-block"><?php echo $lang['Visit timeout help'] ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Online timeout label'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="form[timeout_online]" size="5" maxlength="5" value="<?php echo $pun_config['o_timeout_online'] ?>" />
                        <span class="help-block"><?php echo $lang['Online timeout help'] ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Redirect time label'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="form[redirect_delay]" size="3" maxlength="3" value="<?php echo $pun_config['o_redirect_delay'] ?>" />
                        <span class="help-block"><?php echo $lang['Redirect time help'] ?></span>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Feed subhead'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Default feed label'] ?></label>
                    <div class="col-sm-10">
                        <label class="radio-inline">
                            <input type="radio" name="form[feed_type]" value="0"<?php if ($pun_config['o_feed_type'] == '0') echo ' checked="checked"' ?>>
                            <?php echo $lang['None'] ?>
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="form[feed_type]" value="1"<?php if ($pun_config['o_feed_type'] == '1') echo ' checked="checked"' ?>>
                            <?php echo $lang['RSS'] ?>
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="form[feed_type]" value="2"<?php if ($pun_config['o_feed_type'] == '2') echo ' checked="checked"' ?>>
                            <?php echo $lang['Atom'] ?>
                        </label>
                        <span class="help-block"><?php echo $lang['Default feed help'] ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Feed TTL label'] ?></label>
                    <div class="col-sm-10">
                        <select class="form-control" name="form[feed_ttl]">
                            <option value="0"<?php if ($pun_config['o_feed_ttl'] == '0') echo ' selected="selected"'; ?>><?php echo $lang['No cache'] ?></option>
<?php

		$times = array(5, 15, 30, 60);

		foreach ($times as $time)
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$time.'"'.($pun_config['o_feed_ttl'] == $time ? ' selected="selected"' : '').'>'.sprintf($lang['Minutes'], $time).'</option>'."\n";

?>
                        </select>
                        <span class="help-block"><?php echo $lang['Feed TTL help'] ?></span>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Reports subhead'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Reporting method label'] ?></label>
                    <div class="col-sm-10">
                        <label class="radio-inline">
                            <input type="radio" name="form[report_method]" value="0"<?php if ($pun_config['o_report_method'] == '0') echo ' checked="checked"' ?> />
                            <?php echo $lang['Internal'] ?>
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="form[report_method]" value="1"<?php if ($pun_config['o_report_method'] == '1') echo ' checked="checked"' ?> />
                            <?php echo $lang['By e-mail'] ?>
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="form[report_method]" value="2"<?php if ($pun_config['o_report_method'] == '2') echo ' checked="checked"' ?> />
                            <?php echo $lang['Both'] ?>
                        </label>
                        <span class="help-block"><?php echo $lang['Reporting method help'] ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Mailing list label'] ?></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="form[mailing_list]" rows="5" cols="55"><?php echo pun_htmlspecialchars($pun_config['o_mailing_list']) ?></textarea>
                        <span class="help-block"><?php echo $lang['Mailing list help'] ?></span>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Avatars subhead'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Use avatars label'] ?></label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="form[avatars]" value="1" <?php if ($pun_config['o_avatars'] == '1') echo ' checked="checked"' ?> />
                                    <?php echo $lang['Use avatars help'] ?>
                                </label>
                            </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Upload directory label'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="form[avatars_dir]" size="35" maxlength="50" value="<?php echo pun_htmlspecialchars($pun_config['o_avatars_dir']) ?>" />
                        <span class="help-block"><?php echo $lang['Upload directory help'] ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Max width label'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="form[avatars_width]" size="5" maxlength="5" value="<?php echo $pun_config['o_avatars_width'] ?>" />
                        <span class="help-block"><?php echo $lang['Max width help'] ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Max height label'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="form[avatars_height]" size="5" maxlength="5" value="<?php echo $pun_config['o_avatars_height'] ?>" />
                        <span class="help-block"><?php echo $lang['Max height help'] ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Max size label'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="form[avatars_size]" size="6" maxlength="6" value="<?php echo $pun_config['o_avatars_size'] ?>" />
                        <span class="help-block"><?php echo $lang['Max size help'] ?></span>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Registration subhead'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Allow new label'] ?></label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="form[regs_allow]" value="1" <?php if ($pun_config['o_regs_allow'] == '1') echo ' checked="checked"' ?> />
                                <?php echo $lang['Allow new help'] ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Verify label'] ?></label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="form[regs_verify]" value="1" <?php if ($pun_config['o_regs_verify'] == '1') echo ' checked="checked"' ?> />
                                <?php echo $lang['Verify help'] ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Report new label'] ?></label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="form[regs_report]" value="1" <?php if ($pun_config['o_regs_report'] == '1') echo ' checked="checked"' ?> />
                                <?php echo $lang['Report new help'] ?>
                            </label>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Use rules label'] ?></label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="form[rules]" value="1" <?php if ($pun_config['o_rules'] == '1') echo ' checked="checked"' ?> />
                                <?php echo $lang['Use rules help'] ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Rules label'] ?></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="form[rules_message]" rows="10" cols="55"><?php echo pun_htmlspecialchars($pun_config['o_rules_message']) ?></textarea>
						<span class="help-block"><?php echo $lang['Rules help'] ?></span>
                    </div>
                </div>
                <hr />
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['E-mail default label'] ?></label>
                    <div class="col-sm-10">
                        <span class="help-block"><?php echo $lang['E-mail default help'] ?></span>
                        <div class="radio">
                            <label>
                                <input type="radio" name="form[default_email_setting]" id="form_default_email_setting_0" value="0"<?php if ($pun_config['o_default_email_setting'] == '0') echo ' checked="checked"' ?> />
                                <?php echo $lang['Display e-mail label'] ?>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="form[default_email_setting]" id="form_default_email_setting_1" value="1"<?php if ($pun_config['o_default_email_setting'] == '1') echo ' checked="checked"' ?> />
                                <?php echo $lang['Hide allow form label'] ?>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="form[default_email_setting]" id="form_default_email_setting_2" value="2"<?php if ($pun_config['o_default_email_setting'] == '2') echo ' checked="checked"' ?> />
                                <?php echo $lang['Hide both label'] ?>
                            </label>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Antispam API label'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="form[antispam_api]" size="35" maxlength="50" value="<?php echo pun_htmlspecialchars($pun_config['o_antispam_api']) ?>" />
                        <span class="help-block"><?php printf($lang['Antispam API help'], '<a href="http://stopforumspam.com/keys">StopForumSpam.com</a>') ?></span>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Announcement subhead'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="form[announcement]" value="1" <?php if ($pun_config['o_announcement'] == '1') echo ' checked="checked"' ?> />
                        <?php echo $lang['Display announcement help'] ?>
                    </label>
                </div>
                <textarea class="form-control full-form" name="form[announcement_message]" rows="5" cols="55"><?php echo pun_htmlspecialchars($pun_config['o_announcement_message']) ?></textarea>
                <span class="help-block"><?php echo $lang['Announcement message help'] ?></span>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title" id="maintenance"><?php echo $lang['Maintenance subhead'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="form[maintenance]" value="1" <?php if ($pun_config['o_maintenance'] == '1') echo ' checked="checked"' ?> />
                        <?php echo $lang['Maintenance mode help'] ?>
                    </label>
                </div>
                <textarea class="form-control" name="form[maintenance_message]" rows="5" cols="55"><?php echo pun_htmlspecialchars($pun_config['o_maintenance_message']) ?></textarea>
                <span class="help-block"><?php echo $lang['Maintenance message help'] ?></span>
            </fieldset>
        </div>
    </div>
</form>
<?php

require FORUM_ROOT.'backstage/footer.php';
