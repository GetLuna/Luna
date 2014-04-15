<?php

/**
 * Copyright (C) 2013-2014 ModernBB Group
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License under GPLv3
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';

// If we are logged in, we shouldn't be here
if (!$luna_user['is_guest'])
{
	header('Location: index.php');
	exit;
}

if ($luna_config['o_regs_allow'] == '0')
	message($lang['No new regs']);


// User pressed the cancel button
if (isset($_GET['cancel']))
	redirect('index.php');


else if ($luna_config['o_rules'] == '1' && !isset($_GET['agree']) && !isset($_POST['form_sent']))
{
	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Register'], $lang['Forum rules']);
	define('FORUM_ACTIVE_PAGE', 'register');
	require FORUM_ROOT.'header.php';

?>
<h2><?php echo $lang['Forum rules'] ?></h2>
<form method="get" action="register.php">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Rules legend'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="usercontent"><?php echo $luna_config['o_rules_message'] ?></div>
            </fieldset>
        </div>
    </div>
    <div class="alert alert-info"><input type="submit" class="btn btn-primary" name="agree" value="<?php echo $lang['Agree'] ?>" /> <input type="submit" class="btn btn-default" name="cancel" value="<?php echo $lang['Cancel'] ?>" /></div>
</form>
<?php

	require FORUM_ROOT.'footer.php';
}

// Start with a clean slate
$errors = array();

if (isset($_POST['form_sent']))
{
	// Check that someone from this IP didn't register a user within the last hour (DoS prevention)
	$result = $db->query('SELECT 1 FROM '.$db->prefix.'users WHERE registration_ip=\''.$db->escape(get_remote_address()).'\' AND registered>'.(time() - 3600)) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());

	if ($db->num_rows($result))
		message($lang['Registration flood']);


	$username = luna_trim($_POST['req_user']);
	$email1 = strtolower(luna_trim($_POST['req_email1']));

	if ($luna_config['o_regs_verify'] == '1')
	{
		$email2 = strtolower(luna_trim($_POST['req_email2']));

		$password1 = random_pass(8);
		$password2 = $password1;
	}
	else
	{
		$password1 = luna_trim($_POST['req_password1']);
		$password2 = luna_trim($_POST['req_password2']);
	}

	// Validate username and passwords
	check_username($username);

	if (luna_strlen($password1) < 6)
		$errors[] = $lang['Pass too short'];
	else if ($password1 != $password2)
		$errors[] = $lang['Pass not match'];

	// Validate email
	require FORUM_ROOT.'include/email.php';

	if (!is_valid_email($email1))
		$errors[] = $lang['Invalid email'];
	else if ($luna_config['o_regs_verify'] == '1' && $email1 != $email2)
		$errors[] = $lang['Email not match'];

	// Check if it's a banned email address
	if (is_banned_email($email1))
	{
		if ($luna_config['p_allow_banned_email'] == '0')
			$errors[] = $lang['Banned email'];

		$banned_email = true; // Used later when we send an alert email
	}
	else
		$banned_email = false;

	// Check if someone else already has registered with that email address
	$dupe_list = array();

	$result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE email=\''.$db->escape($email1).'\'') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result))
	{
		if ($luna_config['p_allow_dupe_email'] == '0')
			$errors[] = $lang['Dupe email'];

		while ($cur_dupe = $db->fetch_assoc($result))
			$dupe_list[] = $cur_dupe['username'];
	}

	// Make sure we got a valid language string
	if (isset($_POST['language']))
	{
		$language = preg_replace('%[\.\\\/]%', '', $_POST['language']);
		if (!file_exists(FORUM_ROOT.'lang/'.$language.'/common.php'))
			message($lang['Bad request'], false, '404 Not Found');
	}
	else
		$language = $luna_config['o_default_lang'];

  	// Include the antispam library
  	require FORUM_ROOT.'include/nospam.php';

	$req_username = empty($username) ? luna_trim($_POST['req_username']) : $username;
	if (!empty($_POST['req_username']) || stopforumspam_check(get_remote_address(), $email1, $req_username))
  	{
  		// Since we found a spammer, lets report the bastard!
  		stopforumspam_report(get_remote_address(), $email1, $req_username);

  		message($lang['Spam catch'].' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.', true);
  	}

	// Did everything go according to plan?
	if (empty($errors))
	{
		// Insert the new user into the database. We do this now to get the last inserted ID for later use
		$now = time();

		$intial_group_id = ($luna_config['o_regs_verify'] == '0') ? $luna_config['o_default_user_group'] : FORUM_UNVERIFIED;
		$password_hash = luna_hash($password1);

		// Add the user
		$db->query('INSERT INTO '.$db->prefix.'users (username, group_id, password, email, language, style, registered, registration_ip, last_visit) VALUES(\''.$db->escape($username).'\', '.$intial_group_id.', \''.$password_hash.'\', \''.$db->escape($email1).'\', \''.$db->escape($language).'\', \''.$luna_config['o_default_style'].'\', '.$now.', \''.$db->escape(get_remote_address()).'\', '.$now.')') or error('Unable to create user', __FILE__, __LINE__, $db->error());
		$new_uid = $db->insert_id();

		if ($luna_config['o_regs_verify'] == '0')
		{
			// Regenerate the users info cache
			if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
				require FORUM_ROOT.'include/cache.php';

			generate_users_info_cache();
		}

		// If the mailing list isn't empty, we may need to send out some alerts
		if ($luna_config['o_mailing_list'] != '')
		{
			// If we previously found out that the email was banned
			if ($banned_email)
			{
				// Load the "banned email register" template
				$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/'.$luna_user['language'].'/mail_templates/banned_email_register.tpl'));

				// The first row contains the subject
				$first_crlf = strpos($mail_tpl, "\n");
				$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
				$mail_message = trim(substr($mail_tpl, $first_crlf));

				$mail_message = str_replace('<username>', $username, $mail_message);
				$mail_message = str_replace('<email>', $email1, $mail_message);
				$mail_message = str_replace('<profile_url>', get_base_url().'/profile.php?id='.$new_uid, $mail_message);
				$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

				luna_mail($luna_config['o_mailing_list'], $mail_subject, $mail_message);
			}

			// If we previously found out that the email was a dupe
			if (!empty($dupe_list))
			{
				// Load the "dupe email register" template
				$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/'.$luna_user['language'].'/mail_templates/dupe_email_register.tpl'));

				// The first row contains the subject
				$first_crlf = strpos($mail_tpl, "\n");
				$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
				$mail_message = trim(substr($mail_tpl, $first_crlf));

				$mail_message = str_replace('<username>', $username, $mail_message);
				$mail_message = str_replace('<dupe_list>', implode(', ', $dupe_list), $mail_message);
				$mail_message = str_replace('<profile_url>', get_base_url().'/profile.php?id='.$new_uid, $mail_message);
				$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

				luna_mail($luna_config['o_mailing_list'], $mail_subject, $mail_message);
			}

			// Should we alert people on the admin mailing list that a new user has registered?
			if ($luna_config['o_regs_report'] == '1')
			{
				// Load the "new user" template
				$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/'.$luna_user['language'].'/mail_templates/new_user.tpl'));

				// The first row contains the subject
				$first_crlf = strpos($mail_tpl, "\n");
				$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
				$mail_message = trim(substr($mail_tpl, $first_crlf));

				$mail_message = str_replace('<username>', $username, $mail_message);
				$mail_message = str_replace('<base_url>', get_base_url().'/', $mail_message);
				$mail_message = str_replace('<profile_url>', get_base_url().'/profile.php?id='.$new_uid, $mail_message);
				$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

				luna_mail($luna_config['o_mailing_list'], $mail_subject, $mail_message);
			}
		}

		// Must the user verify the registration or do we log him/her in right now?
		if ($luna_config['o_regs_verify'] == '1')
		{
			// Load the "welcome" template
			$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/'.$luna_user['language'].'/mail_templates/welcome.tpl'));

			// The first row contains the subject
			$first_crlf = strpos($mail_tpl, "\n");
			$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
			$mail_message = trim(substr($mail_tpl, $first_crlf));

			$mail_subject = str_replace('<board_title>', $luna_config['o_board_title'], $mail_subject);
			$mail_message = str_replace('<base_url>', get_base_url().'/', $mail_message);
			$mail_message = str_replace('<username>', $username, $mail_message);
			$mail_message = str_replace('<password>', $password1, $mail_message);
			$mail_message = str_replace('<login_url>', get_base_url().'/login.php', $mail_message);
			$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

			luna_mail($email1, $mail_subject, $mail_message);

			message($lang['Reg email'].' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.', true);
		}

		luna_setcookie($new_uid, $password_hash, time() + $luna_config['o_timeout_visit']);

		redirect('index.php');
	}
}


$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Register']);
$required_fields = array('req_user' => $lang['Username'], 'req_password1' => $lang['Password'], 'req_password2' => $lang['Confirm password'], 'req_email1' => $lang['Email'], 'req_email2' => $lang['Email'].' 2');
$focus_element = array('register', 'req_user');
$page_head = array('<style type="text/css">#register label.usernamefield { display: none }</style>');
define('FORUM_ACTIVE_PAGE', 'register');
require FORUM_ROOT.'header.php';

// If there are errors, we display them
if (!empty($errors))
{

?>
<div class="alert alert-danger">
	<h4><?php echo $lang['Registration errors'] ?></h4>
<?php

	foreach ($errors as $cur_error)
		echo "\t\t\t\t".'<span class="error-list">'.$cur_error.'</span>'."<br />";
?>
</div>

<?php

}
?>
<h2><?php echo $lang['Register'] ?></h2>
<form class="form-horizontal" id="register" method="post" action="register.php?action=register" onsubmit="this.register.disabled=true;if(process_form(this)){return true;}else{this.register.disabled=false;return false;}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Register legend'] ?><span class="pull-right"><input type="submit" class="btn btn-primary" name="register" value="<?php echo $lang['Register'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <input type="hidden" name="form_sent" value="1" />
                <label class="required usernamefield"><?php echo $lang['If human'] ?><input type="text" class="form-control" name="req_username" value="" maxlength="25" /></label>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Username'] ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="req_user" value="<?php if (isset($_POST['req_user'])) echo luna_htmlspecialchars($_POST['req_user']); ?>" maxlength="25" />
                        <span class="help-block"><?php echo $lang['Username legend'] ?></span>
                    </div>
                </div>
<?php if ($luna_config['o_regs_verify'] == '0'): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Password'] ?></label>
                    <div class="col-sm-10">
						<input type="password" class="form-control" name="req_password1" value="<?php if (isset($_POST['req_password1'])) echo luna_htmlspecialchars($_POST['req_password1']); ?>" />
                        <input type="password" class="form-control" name="req_password2" value="<?php if (isset($_POST['req_password2'])) echo luna_htmlspecialchars($_POST['req_password2']); ?>" />
                        <span class="help-block"><?php echo $lang['Pass info'] ?></span>
                    </div>
                </div>
<?php endif; ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Email'] ?></label>
                    <div class="col-sm-10">
						<input type="text" class="form-control" name="req_email1" value="<?php if (isset($_POST['req_email1'])) echo luna_htmlspecialchars($_POST['req_email1']); ?>" maxlength="80" />
                        <?php if ($luna_config['o_regs_verify'] == '1'): ?><input type="text" class="form-control" name="req_email2" value="<?php if (isset($_POST['req_email2'])) echo luna_htmlspecialchars($_POST['req_email2']); ?>" maxlength="80" /><?php endif; ?>
                        <?php if ($luna_config['o_regs_verify'] == '1'): ?><span class="help-block"><?php echo $lang['Email help info'] ?></span><?php endif; ?>
                    </div>
                </div>
<?php

		$languages = forum_list_langs();

		// Only display the language selection box if there's more than one language available
		if (count($languages) > 1)
		{

?>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Language'] ?></label>
                    <div class="col-sm-10">
						<select class="form-control" name="language">
<?php

			foreach ($languages as $temp)
			{
				if ($luna_config['o_default_lang'] == $temp)
					echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'" selected="selected">'.$temp.'</option>'."\n";
				else
					echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'">'.$temp.'</option>'."\n";
			}

?>
						</select>
                    </div>
                </div>
<?php

		}
?>
            </fieldset>
        </div>
    </div>
</form>
<?php

require FORUM_ROOT.'footer.php';
