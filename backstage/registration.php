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
	confirm_referrer('backstage/registration.php', __('Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database (look for o_base_url in the config table) and then clear the cache by deleting all .php files in the /cache directory.', 'luna'));
	
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
		$form['rules_message'] = __('Enter your rules here.', 'luna');
		$form['rules'] = '0';
	}

	if ($form['default_email_setting'] < 0 || $form['default_email_setting'] > 2)
		message_backstage(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

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

	redirect('backstage/registration.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('Registration', 'luna'));
define('LUNA_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('settings', 'registration');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success">'.__('Your settings have been saved.', 'luna').'</div>'
?>
<form class="form-horizontal" method="post" action="registration.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Registration', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
			<input type="hidden" name="form_sent" value="1" />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Allow new registrations', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[regs_allow]" value="1" <?php if ($luna_config['o_regs_allow'] == '1') echo ' checked' ?> />
								<?php _e('Allow new users to be made by people.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Verify registrations', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[regs_verify]" value="1" <?php if ($luna_config['o_regs_verify'] == '1') echo ' checked' ?> />
								<?php _e('Send a random password to users to verify their email address.  ', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Report new registrations', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[regs_report]" value="1" <?php if ($luna_config['o_regs_report'] == '1') echo ' checked' ?> />
								<?php _e('Notify people on the mailing list when new user registers.  ', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('User forum rules', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[rules]" value="1" <?php if ($luna_config['o_rules'] == '1') echo ' checked' ?> />
								<?php _e('Require users to agree with the rules. This will enable a "Rules" panel in Help.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Enter your rules here', 'luna') ?><span class="help-block"><?php _e('Enter rules or useful information, required when rules are enabled', 'luna') ?></span></label>
					<div class="col-sm-9">
						<textarea class="form-control" name="form[rules_message]" rows="10"><?php echo luna_htmlspecialchars($luna_config['o_rules_message']) ?></textarea>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Default email setting', 'luna') ?></label>
					<div class="col-sm-9">
						<span class="help-block"><?php _e('Default privacy setting for new registrations', 'luna') ?></span>
						<div class="radio">
							<label>
								<input type="radio" name="form[default_email_setting]" id="form_default_email_setting_0" value="0"<?php if ($luna_config['o_default_email_setting'] == '0') echo ' checked' ?> />
								<?php _e('Display email address to other users.', 'luna') ?>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="form[default_email_setting]" id="form_default_email_setting_1" value="1"<?php if ($luna_config['o_default_email_setting'] == '1') echo ' checked' ?> />
								<?php _e('Hide email address but allow form e-mail.', 'luna') ?>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="form[default_email_setting]" id="form_default_email_setting_2" value="2"<?php if ($luna_config['o_default_email_setting'] == '2') echo ' checked' ?> />
								<?php _e('Hide email address and disallow form email.', 'luna') ?>
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
