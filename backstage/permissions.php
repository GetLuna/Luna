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
	confirm_referrer('backstage/permissions.php');
	
	$form = array(
		'message_img_tag'		=> isset($_POST['form']['message_img_tag']) ? '1' : '0',
		'message_all_caps'		=> isset($_POST['form']['message_all_caps']) ? '1' : '0',
		'subject_all_caps'		=> isset($_POST['form']['subject_all_caps']) ? '1' : '0',
		'force_guest_email'		=> isset($_POST['form']['force_guest_email']) ? '1' : '0',
		'sig_img_tag'			=> isset($_POST['form']['sig_img_tag']) ? '1' : '0',
		'sig_all_caps'			=> isset($_POST['form']['sig_all_caps']) ? '1' : '0',
		'allow_banned_email'	=> isset($_POST['form']['allow_banned_email']) ? '1' : '0',
		'allow_dupe_email'		=> isset($_POST['form']['allow_dupe_email']) ? '1' : '0',
		'sig_length'			=> luna_trim($_POST['form']['sig_length']),
		'sig_lines'				=> luna_trim($_POST['form']['sig_lines']),
	);

	foreach ($form as $key => $input) {
		// Make sure the input is never a negative value
		if($input < 0)
			$input = 0;

		// Only update values that have changed
		if (array_key_exists('p_'.$key, $luna_config) && $luna_config['p_'.$key] != $input)
			$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$input.' WHERE conf_name=\'p_'.$db->escape($key).'\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());
	}

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();

	redirect('backstage/permissions.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('Permissions', 'luna'));
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('users', 'permissions');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.__('Your settings have been saved.', 'luna').'</h4></div>'
?>
<form class="form-horizontal" method="post" action="permissions.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Commenting', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('BBCode', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[message_img_tag]" value="1" <?php if ($luna_config['p_message_img_tag'] == '1') echo ' checked' ?> />
								<?php _e('Allow the BBCode [img] tag in comments.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('All caps', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[message_all_caps]" value="1" <?php if ($luna_config['p_message_all_caps'] == '1') echo ' checked' ?> />
								<?php _e('Allow a message to contain only capital letters.', 'luna') ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[subject_all_caps]" value="1" <?php if ($luna_config['p_subject_all_caps'] == '1') echo ' checked' ?> />
								<?php _e('Allow a subject to contain only capital letters.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Guests', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[force_guest_email]" value="1" <?php if ($luna_config['p_force_guest_email'] == '1') echo ' checked' ?> />
								<?php _e('Require guests to supply an email address when commenting.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Signatures', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('BBCode', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[sig_img_tag]" value="1" <?php if ($luna_config['p_sig_img_tag'] == '1') echo ' checked' ?> />
								<?php _e('Allow the BBCode [img] tag in user signatures.', 'luna') ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[sig_all_caps]" value="1" <?php if ($luna_config['p_sig_all_caps'] == '1') echo ' checked' ?> />
								<?php _e('Allow a signature to contain only capital letters.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Maximum signature length', 'luna') ?><span class="help-block"><?php _e('Maximum amount of characters a signature can have', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[sig_length]" maxlength="5" value="<?php echo $luna_config['p_sig_length'] ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Maximum signature lines', 'luna') ?><span class="help-block"><?php _e('Maximum amount of lines a signature can have', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[sig_lines]" maxlength="3" value="<?php echo $luna_config['p_sig_lines'] ?>" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Registration', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Registration', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[allow_banned_email]" value="1" <?php if ($luna_config['p_allow_banned_email'] == '1') echo ' checked' ?> />
								<?php _e('Allow users to use a banned email address, mailing list will be warned when this happens.', 'luna') ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[allow_dupe_email]" value="1" <?php if ($luna_config['p_allow_dupe_email'] == '1') echo ' checked' ?> />
								<?php _e('Allow users to use an email address that is already used, mailing list will be warned when this happens.', 'luna') ?>
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
