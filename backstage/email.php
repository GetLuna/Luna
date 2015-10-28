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
	confirm_referrer('backstage/email.php', __('Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database (look for o_base_url in the config table) and then clear the cache by deleting all .php files in the /cache directory.', 'luna'));
	
	$form = array(
		'admin_email'			=> strtolower(luna_trim($_POST['form']['admin_email'])),
		'webmaster_email'		=> strtolower(luna_trim($_POST['form']['webmaster_email'])),
		'forum_subscriptions'	=> isset($_POST['form']['forum_subscriptions']) ? '1' : '0',
		'thread_subscriptions'	=> isset($_POST['form']['thread_subscriptions']) ? '1' : '0',
		'smtp_host'				=> luna_trim($_POST['form']['smtp_host']),
		'smtp_user'				=> luna_trim($_POST['form']['smtp_user']),
		'smtp_ssl'				=> isset($_POST['form']['smtp_ssl']) ? '1' : '0',
	);
	
	// Change or enter a SMTP password
	if (isset($_POST['form']['smtp_change_pass'])) {
		$smtp_pass1 = isset($_POST['form']['smtp_pass1']) ? luna_trim($_POST['form']['smtp_pass1']) : '';
		$smtp_pass2 = isset($_POST['form']['smtp_pass2']) ? luna_trim($_POST['form']['smtp_pass2']) : '';

		if ($smtp_pass1 == $smtp_pass2)
			$form['smtp_pass'] = $smtp_pass1;
		else
			message_backstage(__('You need to enter the SMTP password twice exactly the same to change it.', 'luna'));
	}

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

	redirect('backstage/email.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('Global settings', 'luna'));
define('LUNA_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('settings', 'email');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success">'.__('Your settings have been saved.', 'luna').'</div>'
?>
<form class="form-horizontal" method="post" action="email.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Contact settings', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Admin email', 'luna') ?><span class="help-block"><?php _e('The admins email', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[admin_email]" maxlength="80" value="<?php echo luna_htmlspecialchars($luna_config['o_admin_email']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Webmaster email', 'luna') ?><span class="help-block"><?php _e('The email where the boards mails will be addressed from', 'luna') ?></span></label>
						<div class="col-sm-9"><input type="text" class="form-control" name="form[webmaster_email]" maxlength="80" value="<?php echo luna_htmlspecialchars($luna_config['o_webmaster_email']) ?>" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Subscriptions', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Subscriptions', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[forum_subscriptions]" value="1" <?php if ($luna_config['o_forum_subscriptions'] == '1') echo ' checked' ?> />
								<?php _e('Enable users to subscribe to forums.', 'luna') ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[thread_subscriptions]" value="1" <?php if ($luna_config['o_thread_subscriptions'] == '1') echo ' checked' ?> />
								<?php _e('Enable users to subscribe to threads.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('SMTP settings', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('SMTP server address', 'luna') ?><span class="help-block"><?php _e('The address of an external SMTP server to send emails with', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[smtp_host]" maxlength="100" value="<?php echo luna_htmlspecialchars($luna_config['o_smtp_host']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('SMTP username', 'luna') ?><span class="help-block"><?php _e('Username for SMTP server, only if required', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[smtp_user]" maxlength="50" value="<?php echo luna_htmlspecialchars($luna_config['o_smtp_user']) ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('SMTP password', 'luna') ?><span class="help-block"><?php _e('Password and confirmation for SMTP server, only when required', 'luna') ?></span></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[smtp_change_pass]" id="form_smtp_change_pass" value="1" />
								<?php _e('Check this if you want to change or delete the currently stored password.', 'luna') ?>
							</label>
						</div>
<?php $smtp_pass = !empty($luna_config['o_smtp_pass']) ? random_key(luna_strlen($luna_config['o_smtp_pass']), true) : ''; ?>
						<div class="row">
							<div class="col-sm-6">
								<input class="form-control" type="password" name="form[smtp_pass1]" maxlength="50" value="<?php echo $smtp_pass ?>" />
							</div>
							<div class="col-sm-6">
								<input class="form-control" type="password" name="form[smtp_pass2]" maxlength="50" value="<?php echo $smtp_pass ?>" />
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"></label>
					<div class="col-sm-9">
						<input type="checkbox" name="form[smtp_ssl]" value="1" <?php if ($luna_config['o_smtp_ssl'] == '1') echo ' checked' ?> />
						<?php _e('Encrypts the connection to the SMTP server using SSL, only when required and supported.', 'luna') ?>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
<?php

require 'footer.php';
