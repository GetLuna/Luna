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
    header("Location: login.php");
}

// Load the backstage.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/backstage.php';

if (isset($_POST['form_sent']))
{

	$form = array(
		'board_title'			=> pun_trim($_POST['form']['board_title']),
		'board_desc'			=> pun_trim($_POST['form']['board_desc']),
		'base_url'				=> pun_trim($_POST['form']['base_url']),
		'default_timezone'		=> floatval($_POST['form']['default_timezone']),
		'default_dst'			=> $_POST['form']['default_dst'] != '1' ? '0' : '1',
		'default_lang'			=> pun_trim($_POST['form']['default_lang']),
		'default_style'			=> pun_trim($_POST['form']['default_style']),
		'time_format'			=> pun_trim($_POST['form']['time_format']),
		'date_format'			=> pun_trim($_POST['form']['date_format']),
		'timeout_visit'			=> (intval($_POST['form']['timeout_visit']) > 0) ? intval($_POST['form']['timeout_visit']) : 1,
		'timeout_online'		=> (intval($_POST['form']['timeout_online']) > 0) ? intval($_POST['form']['timeout_online']) : 1,
		'redirect_delay'		=> (intval($_POST['form']['redirect_delay']) >= 0) ? intval($_POST['form']['redirect_delay']) : 0,
		'feed_type'				=> intval($_POST['form']['feed_type']),
		'feed_ttl'				=> intval($_POST['form']['feed_ttl']),
		'report_method'			=> intval($_POST['form']['report_method']),
		'mailing_list'			=> pun_trim($_POST['form']['mailing_list']),
		'avatars'				=> $_POST['form']['avatars'] != '1' ? '0' : '1',
		'avatars_dir'			=> pun_trim($_POST['form']['avatars_dir']),
		'avatars_width'			=> (intval($_POST['form']['avatars_width']) > 0) ? intval($_POST['form']['avatars_width']) : 1,
		'avatars_height'		=> (intval($_POST['form']['avatars_height']) > 0) ? intval($_POST['form']['avatars_height']) : 1,
		'avatars_size'			=> (intval($_POST['form']['avatars_size']) > 0) ? intval($_POST['form']['avatars_size']) : 1,
		'regs_allow'			=> $_POST['form']['regs_allow'] != '1' ? '0' : '1',
		'regs_verify'			=> $_POST['form']['regs_verify'] != '1' ? '0' : '1',
		'regs_report'			=> $_POST['form']['regs_report'] != '1' ? '0' : '1',
		'rules'					=> $_POST['form']['rules'] != '1' ? '0' : '1',
		'rules_message'			=> pun_trim($_POST['form']['rules_message']),
		'antispam_api'			=> pun_trim($_POST['form']['antispam_api']),
		'default_email_setting'	=> intval($_POST['form']['default_email_setting']),
		'announcement'			=> $_POST['form']['announcement'] != '1' ? '0' : '1',
		'announcement_message'	=> pun_trim($_POST['form']['announcement_message']),
		'maintenance'			=> $_POST['form']['maintenance'] != '1' ? '0' : '1',
		'maintenance_message'	=> pun_trim($_POST['form']['maintenance_message']),
	);

	if ($form['board_title'] == '')
		message($lang_back['Must enter title message']);

	// Make sure base_url doesn't end with a slash
	if (substr($form['base_url'], -1) == '/')
		$form['base_url'] = substr($form['base_url'], 0, -1);

	$languages = forum_list_langs();
	if (!in_array($form['default_lang'], $languages))
		message($lang_common['Bad request']);

	$styles = forum_list_styles();
	if (!in_array($form['default_style'], $styles))
		message($lang_common['Bad request']);

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
			message($lang_back['SMTP passwords did not match']);
	}

	if ($form['announcement_message'] != '')
		$form['announcement_message'] = pun_linebreaks($form['announcement_message']);
	else
	{
		$form['announcement_message'] = $lang_back['Enter announcement here'];
		$form['announcement'] = '0';
	}

	if ($form['rules_message'] != '')
		$form['rules_message'] = pun_linebreaks($form['rules_message']);
	else
	{
		$form['rules_message'] = $lang_back['Enter rules here'];
		$form['rules'] = '0';
	}

	if ($form['maintenance_message'] != '')
		$form['maintenance_message'] = pun_linebreaks($form['maintenance_message']);
	else
	{
		$form['maintenance_message'] = $lang_back['Default maintenance message'];
		$form['maintenance'] = '0';
	}

	if ($form['feed_type'] < 0 || $form['feed_type'] > 2)
		message($lang_common['Bad request']);

	if ($form['feed_ttl'] < 0)
		message($lang_common['Bad request']);

	if ($form['report_method'] < 0 || $form['report_method'] > 2)
		message($lang_common['Bad request']);

	if ($form['default_email_setting'] < 0 || $form['default_email_setting'] > 2)
		message($lang_common['Bad request']);

	if ($form['timeout_online'] >= $form['timeout_visit'])
		message($lang_back['Timeout error message']);

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

	redirect('backstage/options.php', $lang_back['Options updated redirect']);
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
generate_admin_menu('global');

?>
<h2><?php echo $lang_back['Options head'] ?></h2>
<form method="post" action="options.php">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Essentials subhead'] ?></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <table class="table">
                    <tr>
                        <th class="col-md-2"><?php echo $lang_back['Board title label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[board_title]" size="50" maxlength="255" value="<?php echo pun_htmlspecialchars($pun_config['o_board_title']) ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Board title help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Board desc label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[board_desc]" size="50" maxlength="255" value="<?php echo pun_htmlspecialchars($pun_config['o_board_desc']) ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Board desc help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Base URL label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[base_url]" size="50" maxlength="100" value="<?php echo pun_htmlspecialchars($pun_config['o_base_url']) ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Base URL help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Timezone label'] ?></th>
                        <td>
                            <select class="form-control" name="form[default_timezone]">
                                <option value="-12"<?php if ($pun_config['o_default_timezone'] == -12) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-12:00'] ?></option>
                                <option value="-11"<?php if ($pun_config['o_default_timezone'] == -11) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-11:00'] ?></option>
                                <option value="-10"<?php if ($pun_config['o_default_timezone'] == -10) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-10:00'] ?></option>
                                <option value="-9.5"<?php if ($pun_config['o_default_timezone'] == -9.5) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-09:30'] ?></option>
                                <option value="-9"<?php if ($pun_config['o_default_timezone'] == -9) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-09:00'] ?></option>
                                <option value="-8.5"<?php if ($pun_config['o_default_timezone'] == -8.5) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-08:30'] ?></option>
                                <option value="-8"<?php if ($pun_config['o_default_timezone'] == -8) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-08:00'] ?></option>
                                <option value="-7"<?php if ($pun_config['o_default_timezone'] == -7) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-07:00'] ?></option>
                                <option value="-6"<?php if ($pun_config['o_default_timezone'] == -6) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-06:00'] ?></option>
                                <option value="-5"<?php if ($pun_config['o_default_timezone'] == -5) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-05:00'] ?></option>
                                <option value="-4"<?php if ($pun_config['o_default_timezone'] == -4) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-04:00'] ?></option>
                                <option value="-3.5"<?php if ($pun_config['o_default_timezone'] == -3.5) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-03:30'] ?></option>
                                <option value="-3"<?php if ($pun_config['o_default_timezone'] == -3) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-03:00'] ?></option>
                                <option value="-2"<?php if ($pun_config['o_default_timezone'] == -2) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-02:00'] ?></option>
                                <option value="-1"<?php if ($pun_config['o_default_timezone'] == -1) echo ' selected="selected"' ?>><?php echo $lang_back['UTC-01:00'] ?></option>
                                <option value="0"<?php if ($pun_config['o_default_timezone'] == 0) echo ' selected="selected"' ?>><?php echo $lang_back['UTC'] ?></option>
                                <option value="1"<?php if ($pun_config['o_default_timezone'] == 1) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+01:00'] ?></option>
                                <option value="2"<?php if ($pun_config['o_default_timezone'] == 2) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+02:00'] ?></option>
                                <option value="3"<?php if ($pun_config['o_default_timezone'] == 3) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+03:00'] ?></option>
                                <option value="3.5"<?php if ($pun_config['o_default_timezone'] == 3.5) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+03:30'] ?></option>
                                <option value="4"<?php if ($pun_config['o_default_timezone'] == 4) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+04:00'] ?></option>
                                <option value="4.5"<?php if ($pun_config['o_default_timezone'] == 4.5) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+04:30'] ?></option>
                                <option value="5"<?php if ($pun_config['o_default_timezone'] == 5) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+05:00'] ?></option>
                                <option value="5.5"<?php if ($pun_config['o_default_timezone'] == 5.5) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+05:30'] ?></option>
                                <option value="5.75"<?php if ($pun_config['o_default_timezone'] == 5.75) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+05:45'] ?></option>
                                <option value="6"<?php if ($pun_config['o_default_timezone'] == 6) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+06:00'] ?></option>
                                <option value="6.5"<?php if ($pun_config['o_default_timezone'] == 6.5) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+06:30'] ?></option>
                                <option value="7"<?php if ($pun_config['o_default_timezone'] == 7) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+07:00'] ?></option>
                                <option value="8"<?php if ($pun_config['o_default_timezone'] == 8) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+08:00'] ?></option>
                                <option value="8.75"<?php if ($pun_config['o_default_timezone'] == 8.75) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+08:45'] ?></option>
                                <option value="9"<?php if ($pun_config['o_default_timezone'] == 9) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+09:00'] ?></option>
                                <option value="9.5"<?php if ($pun_config['o_default_timezone'] == 9.5) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+09:30'] ?></option>
                                <option value="10"<?php if ($pun_config['o_default_timezone'] == 10) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+10:00'] ?></option>
                                <option value="10.5"<?php if ($pun_config['o_default_timezone'] == 10.5) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+10:30'] ?></option>
                                <option value="11"<?php if ($pun_config['o_default_timezone'] == 11) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+11:00'] ?></option>
                                <option value="11.5"<?php if ($pun_config['o_default_timezone'] == 11.5) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+11:30'] ?></option>
                                <option value="12"<?php if ($pun_config['o_default_timezone'] == 12) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+12:00'] ?></option>
                                <option value="12.75"<?php if ($pun_config['o_default_timezone'] == 12.75) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+12:45'] ?></option>
                                <option value="13"<?php if ($pun_config['o_default_timezone'] == 13) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+13:00'] ?></option>
                                <option value="14"<?php if ($pun_config['o_default_timezone'] == 14) echo ' selected="selected"' ?>><?php echo $lang_back['UTC+14:00'] ?></option>
                            </select>
                            <span class="help-block"><?php echo $lang_back['Timezone help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['DST label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[default_dst]" value="1"<?php if ($pun_config['o_default_dst'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[default_dst]" value="0"<?php if ($pun_config['o_default_dst'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['DST help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Language label'] ?></th>
                        <td>
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
                            <span class="help-block"><?php echo $lang_back['Language help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Default style label'] ?></th>
                        <td>
                            <select class="form-control" name="form[default_style]">
<?php

		$styles = forum_list_styles();

		foreach ($styles as $temp)
		{
			if ($pun_config['o_default_style'] == $temp)
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$temp.'" selected="selected">'.str_replace('_', ' ', $temp).'</option>'."\n";
			else
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$temp.'">'.str_replace('_', ' ', $temp).'</option>'."\n";
		}

?>
                            </select>
                            <span class="help-block"><?php echo $lang_back['Default style help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
<?php

	$diff = ($pun_user['timezone'] + $pun_user['dst']) * 3600;
	$timestamp = time() + $diff;

?>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Timeouts subhead'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <table class="table">
                    <tr>
                        <th class="col-md-2"><?php echo $lang_back['Time format label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[time_format]" size="25" maxlength="25" value="<?php echo pun_htmlspecialchars($pun_config['o_time_format']) ?>" />
                            <br /><span class="help-block"><?php printf($lang_back['Time format help'], gmdate($pun_config['o_time_format'], $timestamp), '<a href="http://www.php.net/manual/en/function.date.php">'.$lang_back['PHP manual'].'</a>') ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Date format label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[date_format]" size="25" maxlength="25" value="<?php echo pun_htmlspecialchars($pun_config['o_date_format']) ?>" />
                            <br /><span class="help-block"><?php printf($lang_back['Date format help'], gmdate($pun_config['o_date_format'], $timestamp), '<a href="http://www.php.net/manual/en/function.date.php">'.$lang_back['PHP manual'].'</a>') ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Visit timeout label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[timeout_visit]" size="5" maxlength="5" value="<?php echo $pun_config['o_timeout_visit'] ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Visit timeout help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Online timeout label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[timeout_online]" size="5" maxlength="5" value="<?php echo $pun_config['o_timeout_online'] ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Online timeout help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Redirect time label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[redirect_delay]" size="3" maxlength="3" value="<?php echo $pun_config['o_redirect_delay'] ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Redirect time help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Feed subhead'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <table class="table">
                    <tr>
                        <th class="col-md-2"><?php echo $lang_back['Default feed label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[feed_type]" value="0"<?php if ($pun_config['o_feed_type'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['None'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[feed_type]" value="1"<?php if ($pun_config['o_feed_type'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['RSS'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[feed_type]" value="2"<?php if ($pun_config['o_feed_type'] == '2') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Atom'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Default feed help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Feed TTL label'] ?></th>
                        <td>
                            <select class="form-control" name="form[feed_ttl]">
                                <option value="0"<?php if ($pun_config['o_feed_ttl'] == '0') echo ' selected="selected"'; ?>><?php echo $lang_back['No cache'] ?></option>
<?php

		$times = array(5, 15, 30, 60);

		foreach ($times as $time)
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$time.'"'.($pun_config['o_feed_ttl'] == $time ? ' selected="selected"' : '').'>'.sprintf($lang_back['Minutes'], $time).'</option>'."\n";

?>
                            </select>
                            <span class="help-block"><?php echo $lang_back['Feed TTL help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Reports subhead'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <table class="table">
                    <tr>
                        <th class="col-md-2"><?php echo $lang_back['Reporting method label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[report_method]" value="0"<?php if ($pun_config['o_report_method'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Internal'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[report_method]" value="1"<?php if ($pun_config['o_report_method'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['By e-mail'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[report_method]" value="2"<?php if ($pun_config['o_report_method'] == '2') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Both'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Reporting method help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Mailing list label'] ?></th>
                        <td>
                            <textarea class="form-control" name="form[mailing_list]" rows="5" cols="55"><?php echo pun_htmlspecialchars($pun_config['o_mailing_list']) ?></textarea>
                            <span class="help-block"><?php echo $lang_back['Mailing list help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Avatars subhead'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <table class="table">
                    <tr>
                        <th class="col-md-2"><?php echo $lang_back['Use avatars label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[avatars]" value="1"<?php if ($pun_config['o_avatars'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[avatars]" value="0"<?php if ($pun_config['o_avatars'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Use avatars help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Upload directory label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[avatars_dir]" size="35" maxlength="50" value="<?php echo pun_htmlspecialchars($pun_config['o_avatars_dir']) ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Upload directory help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Max width label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[avatars_width]" size="5" maxlength="5" value="<?php echo $pun_config['o_avatars_width'] ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Max width help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Max height label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[avatars_height]" size="5" maxlength="5" value="<?php echo $pun_config['o_avatars_height'] ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Max height help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Max size label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[avatars_size]" size="6" maxlength="6" value="<?php echo $pun_config['o_avatars_size'] ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Max size help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Registration subhead'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <table class="table">
                    <tr>
                        <th class="col-md-2"><?php echo $lang_back['Allow new label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[regs_allow]" value="1"<?php if ($pun_config['o_regs_allow'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[regs_allow]" value="0"<?php if ($pun_config['o_regs_allow'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Allow new help'] ?></span>
                        </td> 
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Verify label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[regs_verify]" value="1"<?php if ($pun_config['o_regs_verify'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[regs_verify]" value="0"<?php if ($pun_config['o_regs_verify'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Verify help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Report new label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[regs_report]" value="1"<?php if ($pun_config['o_regs_report'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[regs_report]" value="0"<?php if ($pun_config['o_regs_report'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Report new help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Use rules label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[rules]" value="1"<?php if ($pun_config['o_rules'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[rules]" value="0"<?php if ($pun_config['o_rules'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Use rules help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Rules label'] ?></th>
                        <td>
                            <textarea class="form-control" name="form[rules_message]" rows="10" cols="55"><?php echo pun_htmlspecialchars($pun_config['o_rules_message']) ?></textarea>
                            <span class="help-block"><?php echo $lang_back['Rules help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['E-mail default label'] ?></th>
                        <td>
                            <span class="help-block"><?php echo $lang_back['E-mail default help'] ?></span>
                            <br /><label><input type="radio" name="form[default_email_setting]" id="form_default_email_setting_0" value="0"<?php if ($pun_config['o_default_email_setting'] == '0') echo ' checked="checked"' ?> />&#160;<?php echo $lang_back['Display e-mail label'] ?></label>
                            <br /><label><input type="radio" name="form[default_email_setting]" id="form_default_email_setting_1" value="1"<?php if ($pun_config['o_default_email_setting'] == '1') echo ' checked="checked"' ?> />&#160;<?php echo $lang_back['Hide allow form label'] ?></label>
                            <br /><label><input type="radio" name="form[default_email_setting]" id="form_default_email_setting_2" value="2"<?php if ($pun_config['o_default_email_setting'] == '2') echo ' checked="checked"' ?> />&#160;<?php echo $lang_back['Hide both label'] ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Antispam API label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[antispam_api]" size="35" maxlength="50" value="<?php echo pun_htmlspecialchars($pun_config['o_antispam_api']) ?>" />
                            <br /><span class="help-block"><?php printf($lang_back['Antispam API help'], '<a href="http://stopforumspam.com/keys">StopForumSpam.com</a>') ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Announcement subhead'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <table class="table">
                    <tr>
                        <th class="col-md-2"><?php echo $lang_back['Display announcement label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[announcement]" value="1"<?php if ($pun_config['o_announcement'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[announcement]" value="0"<?php if ($pun_config['o_announcement'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Display announcement help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Announcement message label'] ?></th>
                        <td>
                            <textarea class="form-control" name="form[announcement_message]" rows="5" cols="55"><?php echo pun_htmlspecialchars($pun_config['o_announcement_message']) ?></textarea>
                            <span class="help-block"><?php echo $lang_back['Announcement message help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Maintenance subhead'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <table class="table">
                    <tr>
                        <th class="col-md-2"><a name="maintenance"></a><?php echo $lang_back['Maintenance mode label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[maintenance]" value="1"<?php if ($pun_config['o_maintenance'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[maintenance]" value="0"<?php if ($pun_config['o_maintenance'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Maintenance mode help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Maintenance message label'] ?></th>
                        <td>
                            <textarea class="form-control" name="form[maintenance_message]" rows="5" cols="55"><?php echo pun_htmlspecialchars($pun_config['o_maintenance_message']) ?></textarea>
                            <span class="help-block"><?php echo $lang_back['Maintenance message help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
    <div class="alert alert-info"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang_back['Save changes'] ?>" /></div>
</form>
<?php

require FORUM_ROOT.'backstage/footer.php';
