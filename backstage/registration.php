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
	confirm_referrer('backstage/registration.php', $lang['Bad HTTP Referer message']);
	
	$form = array(
		'regs_allow'			=> isset($_POST['form']['regs_allow']) ? '1' : '0',
		'regs_verify'			=> isset($_POST['form']['regs_verify']) ? '1' : '0',
		'regs_report'			=> isset($_POST['form']['regs_report']) ? '1' : '0',
		'rules'					=> isset($_POST['form']['rules']) ? '1' : '0',
		'rules_message'			=> luna_trim($_POST['form']['rules_message']),
		'default_email_setting'	=> intval($_POST['form']['default_email_setting']),
	);

	if ($form['rules_message'] != '')
		$form['rules_message'] = luna_linebreaks($form['rules_message']);
	else {
		$form['rules_message'] = $lang['Enter rules here'];
		$form['rules'] = '0';
	}

	if ($form['default_email_setting'] < 0 || $form['default_email_setting'] > 2)
		message_backstage($lang['Bad request'], false, '404 Not Found');

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

	redirect('backstage/registration.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Registration']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('settings', 'registration');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>'
?>
<form class="form-horizontal" method="post" action="registration.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Registration'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
			<input type="hidden" name="form_sent" value="1" />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Allow new label'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[regs_allow]" value="1" <?php if ($luna_config['o_regs_allow'] == '1') echo ' checked' ?> />
								<?php echo $lang['Allow new help'] ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Verify label'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[regs_verify]" value="1" <?php if ($luna_config['o_regs_verify'] == '1') echo ' checked' ?> />
								<?php echo $lang['Verify help'] ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Report new label'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[regs_report]" value="1" <?php if ($luna_config['o_regs_report'] == '1') echo ' checked' ?> />
								<?php echo $lang['Report new help'] ?>
							</label>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Use rules label'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[rules]" value="1" <?php if ($luna_config['o_rules'] == '1') echo ' checked' ?> />
								<?php echo $lang['Use rules help'] ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Rules label'] ?><span class="help-block"><?php echo $lang['Rules help'] ?></span></label>
					<div class="col-sm-9">
						<textarea class="form-control" name="form[rules_message]" rows="10"><?php echo luna_htmlspecialchars($luna_config['o_rules_message']) ?></textarea>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['E-mail default label'] ?></label>
					<div class="col-sm-9">
						<span class="help-block"><?php echo $lang['E-mail default help'] ?></span>
						<div class="radio">
							<label>
								<input type="radio" name="form[default_email_setting]" id="form_default_email_setting_0" value="0"<?php if ($luna_config['o_default_email_setting'] == '0') echo ' checked' ?> />
								<?php echo $lang['Display e-mail label'] ?>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="form[default_email_setting]" id="form_default_email_setting_1" value="1"<?php if ($luna_config['o_default_email_setting'] == '1') echo ' checked' ?> />
								<?php echo $lang['Hide allow form label'] ?>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="form[default_email_setting]" id="form_default_email_setting_2" value="2"<?php if ($luna_config['o_default_email_setting'] == '2') echo ' checked' ?> />
								<?php echo $lang['Hide both label'] ?>
							</label>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
<?php

require 'footer.php';
