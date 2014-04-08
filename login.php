<?php

/**
 * Copyright (C) 2013-2014 ModernBB Group
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License under GPLv3
 */

// Tell header.php to use the admin template
define('FORUM_FORM', 1);

if (isset($_GET['action']))
	define('FORUM_QUIET_VISIT', 1);

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;

if (isset($_POST['form_sent']) && $action == 'in')
{
	$form_username = luna_trim($_POST['req_username']);
	$form_password = luna_trim($_POST['req_password']);
	$save_pass = isset($_POST['save_pass']);

	$username_sql = ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb') ? 'username=\''.$db->escape($form_username).'\'' : 'LOWER(username)=LOWER(\''.$db->escape($form_username).'\')';

	$result = $db->query('SELECT * FROM '.$db->prefix.'users WHERE '.$username_sql) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	$cur_user = $db->fetch_assoc($result);

	$authorized = false;

	if (!empty($cur_user['password']))
	{
		$form_password_hash = luna_hash($form_password); // Will result in a SHA-1 hash

		// If there is a salt in the database we have upgraded from 1.3-legacy though haven't yet logged in
		if (!empty($cur_user['salt']))
		{
			if (sha1($cur_user['salt'].sha1($form_password)) == $cur_user['password']) // 1.3 used sha1(salt.sha1(pass))
			{
				$authorized = true;

				$db->query('UPDATE '.$db->prefix.'users SET password=\''.$form_password_hash.'\', salt=NULL WHERE id='.$cur_user['id']) or error('Unable to update user password', __FILE__, __LINE__, $db->error());
			}
		}
		// If the length isn't 40 then the password isn't using sha1, so it must be md5 from 1.2
		else if (strlen($cur_user['password']) != 40)
		{
			if (md5($form_password) == $cur_user['password'])
			{
				$authorized = true;

				$db->query('UPDATE '.$db->prefix.'users SET password=\''.$form_password_hash.'\' WHERE id='.$cur_user['id']) or error('Unable to update user password', __FILE__, __LINE__, $db->error());
			}
		}
		// Otherwise we should have a normal sha1 password
		else
			$authorized = ($cur_user['password'] == $form_password_hash);
	}

	if (!$authorized)
		message($lang['Wrong user/pass'].' <a href="login.php?action=forget">'.$lang['Forgotten pass'].'</a>');

	// Update the status if this is the first time the user logged in
	if ($cur_user['group_id'] == FORUM_UNVERIFIED)
	{
		$db->query('UPDATE '.$db->prefix.'users SET group_id='.$luna_config['o_default_user_group'].' WHERE id='.$cur_user['id']) or error('Unable to update user status', __FILE__, __LINE__, $db->error());

		// Regenerate the users info cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_users_info_cache();
	}

	// Remove this user's guest entry from the online list
	$db->query('DELETE FROM '.$db->prefix.'online WHERE ident=\''.$db->escape(get_remote_address()).'\'') or error('Unable to delete from online list', __FILE__, __LINE__, $db->error());

	$expire = ($save_pass == '1') ? time() + 1209600 : time() + $luna_config['o_timeout_visit'];
	luna_setcookie($cur_user['id'], $form_password_hash, $expire);

	// Reset tracked topics
	set_tracked_topics(null);

	redirect(luna_htmlspecialchars($_POST['redirect_url']), $lang['Login redirect']);
}


else if ($action == 'out')
{
	if ($luna_user['is_guest'] || !isset($_GET['id']) || $_GET['id'] != $luna_user['id'] || !isset($_GET['csrf_token']) || $_GET['csrf_token'] != luna_hash($luna_user['id'].luna_hash(get_remote_address())))
	{
		header('Location: index.php');
		exit;
	}

	// Remove user from "users online" list
	$db->query('DELETE FROM '.$db->prefix.'online WHERE user_id='.$luna_user['id']) or error('Unable to delete from online list', __FILE__, __LINE__, $db->error());

	// Update last_visit (make sure there's something to update it with)
	if (isset($luna_user['logged']))
		$db->query('UPDATE '.$db->prefix.'users SET last_visit='.$luna_user['logged'].' WHERE id='.$luna_user['id']) or error('Unable to update user visit data', __FILE__, __LINE__, $db->error());

	luna_setcookie(1, luna_hash(uniqid(rand(), true)), time() + 31536000);

	redirect('index.php', $lang['Logout redirect']);
}


else if ($action == 'forget' || $action == 'forget_2')
{
	if (!$luna_user['is_guest'])
	{
		header('Location: index.php');
		exit;
	}

	if (isset($_POST['form_sent']))
	{
		// Start with a clean slate
		$errors = array();

		require FORUM_ROOT.'include/email.php';

		// Validate the email address
		$email = strtolower(luna_trim($_POST['req_email']));
		if (!is_valid_email($email))
			$errors[] = $lang['Invalid email'];

		// Did everything go according to plan?
		if (empty($errors))
		{
			$result = $db->query('SELECT id, username, last_email_sent FROM '.$db->prefix.'users WHERE email=\''.$db->escape($email).'\'') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());

			if ($db->num_rows($result))
			{
				// Load the "activate password" template
				$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/'.$luna_user['language'].'/mail_templates/activate_password.tpl'));

				// The first row contains the subject
				$first_crlf = strpos($mail_tpl, "\n");
				$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
				$mail_message = trim(substr($mail_tpl, $first_crlf));

				// Do the generic replacements first (they apply to all emails sent out here)
				$mail_message = str_replace('<base_url>', get_base_url().'/', $mail_message);
				$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

				// Loop through users we found
				while ($cur_hit = $db->fetch_assoc($result))
				{
					if ($cur_hit['last_email_sent'] != '' && (time() - $cur_hit['last_email_sent']) < 3600 && (time() - $cur_hit['last_email_sent']) >= 0)
					message(sprintf($lang['Email flood'], intval((3600 - (time() - $cur_hit['last_email_sent'])) / 60)), true);
					
					// Generate a new password and a new password activation code
					$new_password = random_pass(8);
					$new_password_key = random_pass(8);

					$db->query('UPDATE '.$db->prefix.'users SET activate_string=\''.luna_hash($new_password).'\', activate_key=\''.$new_password_key.'\', last_email_sent = '.time().' WHERE id='.$cur_hit['id']) or error('Unable to update activation data', __FILE__, __LINE__, $db->error());

					// Do the user specific replacements to the template
					$cur_mail_message = str_replace('<username>', $cur_hit['username'], $mail_message);
					$cur_mail_message = str_replace('<activation_url>', get_base_url().'/profile.php?id='.$cur_hit['id'].'&action=change_pass&key='.$new_password_key, $cur_mail_message);
					$cur_mail_message = str_replace('<new_password>', $new_password, $cur_mail_message);

					luna_mail($email, $mail_subject, $cur_mail_message);
				}

				message($lang['Forget mail'].' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.', true);
			}
			else
				$errors[] = $lang['No email match'].' '.htmlspecialchars($email).'.';
			}
		}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Request pass']);
	$required_fields = array('req_email' => $lang['Email']);
	$focus_element = array('request_pass', 'req_email');
	define ('FORUM_ACTIVE_PAGE', 'login');
	require FORUM_ROOT.'header.php';

// If there are errors, we display them
if (!empty($errors))
{

?>
<div id="posterror">
	<h2><?php echo $lang['New password errors'] ?></h2>
	<div class="error-info">
		<p><?php echo $lang['New passworderrors info'] ?></p>
		<ul class="error-list">
<?php

	foreach ($errors as $cur_error)
		echo "\t\t\t\t".'<li><strong>'.$cur_error.'</strong></li>'."\n";
?>
		</ul>
	</div>
</div>

<?php

}
?>
<form class="form" id="request_pass" method="post" action="login.php?action=forget_2" onsubmit="this.request_pass.disabled=true;if(process_form(this)){return true;}else{this.request_pass.disabled=false;return false;}">
    <h1 class="form-heading"><?php echo $lang['Request pass'] ?></h1>
    <fieldset>
        <input type="hidden" name="form_sent" value="1" />
        <label class="required"><input class="form-control" type="text" name="req_email" placeholder="<?php echo $lang['Email'] ?>" /></label>
        <div class="pull-right" style="margin-top: 60px;">
            <?php if (empty($errors)): ?><a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a><?php endif; ?><input class="btn btn-primary" type="submit" name="request_pass" value="<?php echo $lang['Submit'] ?>" />
        </div>
    </fieldset>
</form>
<?php

	require FORUM_ROOT.'footer.php';
}


if (!$luna_user['is_guest'])
	{
		header('Location: index.php');
		exit;
	}

// Try to determine if the data in HTTP_REFERER is valid (if not, we redirect to index.php after login)
if (!empty($_SERVER['HTTP_REFERER']))
{
	$referrer = parse_url($_SERVER['HTTP_REFERER']);
	// Remove www subdomain if it exists
	if (strpos($referrer['host'], 'www.') === 0)
		$referrer['host'] = substr($referrer['host'], 4);

	// Make sure the path component exists
	if (!isset($referrer['path']))
		$referrer['path'] = '';

	$valid = parse_url(get_base_url());
	// Remove www subdomain if it exists
	if (strpos($valid['host'], 'www.') === 0)
		$valid['host'] = substr($valid['host'], 4);

	// Make sure the path component exists
	if (!isset($valid['path']))
		$valid['path'] = '';

	if ($referrer['host'] == $valid['host'] && preg_match('%^'.preg_quote($valid['path'], '%').'/(.*?)\.php%i', $referrer['path']))
		$redirect_url = $_SERVER['HTTP_REFERER'];
}

if (!isset($redirect_url))
	$redirect_url = 'index.php';
else if (preg_match('%viewtopic\.php\?pid=(\d+)$%', $redirect_url, $matches))  
    $redirect_url .= '#p'.$matches[1];

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Login']);
$required_fields = array('req_username' => $lang['Username'], 'req_password' => $lang['Password']);
$focus_element = array('login', 'req_username');
define('FORUM_ACTIVE_PAGE', 'login');
require FORUM_ROOT.'header.php';

?>
<form class="form" id="login" method="post" action="login.php?action=in" onsubmit="return process_form(this)">
    <fieldset>
        <h1 class="form-heading"><?php echo $lang['Login'] ?></h1>
        <input type="hidden" name="form_sent" value="1" />
        <input type="hidden" name="redirect_url" value="<?php echo luna_htmlspecialchars($redirect_url) ?>" />
        <div>
            <input class="form-control top-form" type="text" name="req_username" maxlength="25" tabindex="1" placeholder="<?php echo $lang['Username'] ?>" />
            <input class="form-control bottom-form" type="password" name="req_password" tabindex="2" placeholder="<?php echo $lang['Password'] ?>" /> 
        </div>
        <div class="form-content">
            <p class="actions"><?php if ($luna_config['o_regs_allow'] == '1') { ?><a href="register.php" tabindex="5"><?php echo $lang['Register'] ?></a> &middot; <?php }; ?><a href="login.php?action=forget" tabindex="6"><?php echo $lang['Forgotten pass'] ?></a></p>
            <div class="control-group">
                <div class="controls remember">
                    <label class="remember"><input type="checkbox" name="save_pass" value="1" tabindex="3" checked="checked" /> <?php echo $lang['Remember me'] ?></label>
                </div>
            </div>
            <div class="control-group pull-right">
                <input class="btn btn-primary" type="submit" name="login" value="<?php echo $lang['Login'] ?>" tabindex="4" />
            </div>
        </div>
    </fieldset>
</form>
<?php

require FORUM_ROOT.'footer.php';
