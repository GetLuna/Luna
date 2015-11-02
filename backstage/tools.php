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
// Create new user
if (isset($_POST['add_user'])) {
	$username = luna_trim($_POST['username']);
	$email1 = strtolower(trim($_POST['email']));
	$email2 = strtolower(trim($_POST['email']));
		
	$trimpassword = trim($_POST['password']);

	if (isset($_POST['random_pass']))
		$password = random_pass(8);
	elseif (!empty($trimpassword))
		$password = trim($_POST['password']);
	else
		redirect('backstage/users.php?user_failed=true');

	$errors = array();

	// Convert multiple whitespace characters into one (to prevent people from registering with indistinguishable usernames)
	$username = preg_replace('#\s+#s', ' ', $username);

	// Validate username and passwords
	if (strlen($username) < 2)
		message_backstage(__('Usernames must be at least 2 characters long. Please choose another (longer) username.', 'luna'));
	elseif (luna_strlen($username) > 25) // This usually doesn't happen since the form element only accepts 25 characters
		message_backstage(__('Passwords must be at least 6 characters long. Please choose another (longer) password.', 'luna'));
	elseif (!strcasecmp($username, 'Guest') || !strcasecmp($username, __('Guest', 'luna')))
		message_backstage(__('The username guest is reserved. Please choose another username.', 'luna'));
	elseif (preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $username))
		message_backstage(__('Usernames may not be in the form of an IP address. Please choose another username.', 'luna'));
	elseif ((strpos($username, '[') !== false || strpos($username, ']') !== false) && strpos($username, '\'') !== false && strpos($username, '"') !== false)
		message_backstage(__('Usernames may not contain all the characters \', " and [ or ] at once. Please choose another username.', 'luna'));
	elseif (preg_match('#\[b\]|\[/b\]|\[u\]|\[/u\]|\[i\]|\[/i\]|\[color|\[/color\]|\[quote\]|\[quote=|\[/quote\]|\[code\]|\[/code\]|\[img\]|\[/img\]|\[url|\[/url\]|\[email|\[/email\]#i', $username))
		message_backstage(__('Usernames may not contain any of the text formatting tags (BBCode) that the forum uses. Please choose another username.', 'luna'));

	// Check that the username (or a too similar username) is not already registered
	$result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE username=\''.$db->escape($username).'\' OR username=\''.$db->escape(preg_replace('/[^\w]/', '', $username)).'\'') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());

	if ($db->num_rows($result)) {
		$busy = $db->result($result);
		message_backstage(__('Someone is already registered with the username', 'luna').' '.luna_htmlspecialchars($busy).'. '.__('The username you entered is too similar. The username must differ from that by at least one alphanumerical character (a-z or 0-9). Please choose a different username.', 'luna'));
	}

	$timezone = '0';
	$language = $luna_config['o_default_lang'];

	$email_setting = intval(1);

	// Insert the new user into the database. We do this now to get the last inserted id for later use.
	$now = time();

	$intial_group_id = ($_POST['random_pass'] == '0') ? $luna_config['o_default_user_group'] : LUNA_UNVERIFIED;
	$password_hash = luna_hash($password);

	// Add the user
	$db->query('INSERT INTO '.$db->prefix.'users (username, group_id, password, email, email_setting, php_timezone, language, style, registered, registration_ip, last_visit) VALUES(\''.$db->escape($username).'\', '.$intial_group_id.', \''.$password_hash.'\', \''.$email1.'\', '.$email_setting.', '.$timezone.' , \''.$language.'\', \''.$luna_config['o_default_style'].'\', '.$now.', \''.get_remote_address().'\', '.$now.')') or error('Unable to create user', __FILE__, __LINE__, $db->error());
	$new_uid = $db->insert_id();

	// Must the user verify the registration?
	if ($_POST['random_pass'] == '1') {
		// Validate e-mail
		require LUNA_ROOT.'include/email.php';

		// Load the "welcome" template
		$mail_tpl = trim(__('Subject: Welcome to <board_title>!

Thank you for registering in the forums at <base_url>. Your account details are:

Username: <username>
Password: <password>

Login at <login_url> to activate the account.

--
<board_mailer> Mailer
(Do not reply to this message)', 'luna'));

		// The first row contains the subject
		$first_crlf = strpos($mail_tpl, "\n");
		$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
		$mail_message = trim(substr($mail_tpl, $first_crlf));
		$mail_subject = str_replace('<board_title>', $luna_config['o_board_title'], $mail_subject);
		$mail_message = str_replace('<base_url>', $luna_config['o_base_url'].'/', $mail_message);
		$mail_message = str_replace('<username>', $username, $mail_message);
		$mail_message = str_replace('<password>', $password, $mail_message);
		$mail_message = str_replace('<login_url>', $luna_config['o_base_url'].'/login.php', $mail_message);
		$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);
		luna_mail($email1, $mail_subject, $mail_message);
	}

	// Regenerate the users info cache
	if (!defined('LUNA_CACHE_FUNCTIONS_LOADED'))
		require LUNA_ROOT.'include/cache.php';

	generate_users_info_cache();
	
	redirect('backstage/users.php?user_created=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('Users', 'luna'));
define('LUNA_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('users', 'tools');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success">'.__('Your settings have been saved.', 'luna').'</div>';
if (isset($_GET['user_created']))
	echo '<div class="alert alert-success">'.__('User created', 'luna').'</div>';
if (isset($_GET['user_failed']))
	echo '<div class="alert alert-danger">'.__('Failed to create user, no password was given.', 'luna').'</div>';
?>
<form class="form-horizontal" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Add user', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="add_user"><span class="fa fa-fw fa-plus"></span> <?php _e('Add', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Username', 'luna') ?></label>
					<div class="col-sm-9">
						<input type="text" maxlength="25" class="form-control" name="username" tabindex="26" required="required" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Email', 'luna') ?></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="email" tabindex="27" required="required" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Password', 'luna') ?></label>
					<div class="col-sm-9">
						<input type="password" class="form-control" name="password" tabindex="28" />
						<div class="checkbox">
							<label>
								<input type="checkbox" name="random_pass" value="1" checked tabindex="29" />
								<?php _e('Generate a random password, this will be emailed to the above address. When checked, leave "Password" empty.', 'luna') ?>
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