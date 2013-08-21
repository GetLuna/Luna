<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the admin template
define('FORUM_FORM', 1);

if (isset($_GET['action']))
	define('FORUM_QUIET_VISIT', 1);

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';


// Load the frontend.php language file
require FORUM_ROOT.'lang/'.$pun_user['language'].'/frontend.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;

if (isset($_POST['form_sent']) && $action == 'in')
{
	$form_username = pun_trim($_POST['req_username']);
	$form_password = pun_trim($_POST['req_password']);
	$save_pass = isset($_POST['save_pass']);

	$username_sql = ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb') ? 'username=\''.$db->escape($form_username).'\'' : 'LOWER(username)=LOWER(\''.$db->escape($form_username).'\')';

	$result = $db->query('SELECT * FROM '.$db->prefix.'users WHERE '.$username_sql) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	$cur_user = $db->fetch_assoc($result);

	$authorized = false;

	if (!empty($cur_user['password']))
	{
		$form_password_hash = pun_hash($form_password); // Will result in a SHA-1 hash

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
		message($lang_front['Wrong user/pass'].' <a href="login.php?action=forget">'.$lang_front['Forgotten pass'].'</a>');

	// Update the status if this is the first time the user logged in
	if ($cur_user['group_id'] == FORUM_UNVERIFIED)
	{
		$db->query('UPDATE '.$db->prefix.'users SET group_id='.$pun_config['o_default_user_group'].' WHERE id='.$cur_user['id']) or error('Unable to update user status', __FILE__, __LINE__, $db->error());

		// Regenerate the users info cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_users_info_cache();
	}

	// Remove this user's guest entry from the online list
	$db->query('DELETE FROM '.$db->prefix.'online WHERE ident=\''.$db->escape(get_remote_address()).'\'') or error('Unable to delete from online list', __FILE__, __LINE__, $db->error());

	$expire = ($save_pass == '1') ? time() + 1209600 : time() + $pun_config['o_timeout_visit'];
	pun_setcookie($cur_user['id'], $form_password_hash, $expire);

	// Reset tracked topics
	set_tracked_topics(null);

	redirect(pun_htmlspecialchars($_POST['redirect_url']), $lang_front['Login redirect']);
}


else if ($action == 'out')
{
	if ($pun_user['is_guest'] || !isset($_GET['id']) || $_GET['id'] != $pun_user['id'] || !isset($_GET['csrf_token']) || $_GET['csrf_token'] != pun_hash($pun_user['id'].pun_hash(get_remote_address())))
	{
		header('Location: index.php');
		exit;
	}

	// Remove user from "users online" list
	$db->query('DELETE FROM '.$db->prefix.'online WHERE user_id='.$pun_user['id']) or error('Unable to delete from online list', __FILE__, __LINE__, $db->error());

	// Update last_visit (make sure there's something to update it with)
	if (isset($pun_user['logged']))
		$db->query('UPDATE '.$db->prefix.'users SET last_visit='.$pun_user['logged'].' WHERE id='.$pun_user['id']) or error('Unable to update user visit data', __FILE__, __LINE__, $db->error());

	pun_setcookie(1, pun_hash(uniqid(rand(), true)), time() + 31536000);

	redirect('index.php', $lang_front['Logout redirect']);
}


else if ($action == 'forget' || $action == 'forget_2')
{
	if (!$pun_user['is_guest'])
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
		$email = strtolower(pun_trim($_POST['req_email']));
		if (!is_valid_email($email))
			$errors[] = $lang_common['Invalid email'];

		// Did everything go according to plan?
		if (empty($errors))
		{
			$result = $db->query('SELECT id, username, last_email_sent FROM '.$db->prefix.'users WHERE email=\''.$db->escape($email).'\'') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());

			if ($db->num_rows($result))
			{
				// Load the "activate password" template
				$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/'.$pun_user['language'].'/mail_templates/activate_password.tpl'));

				// The first row contains the subject
				$first_crlf = strpos($mail_tpl, "\n");
				$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
				$mail_message = trim(substr($mail_tpl, $first_crlf));

				// Do the generic replacements first (they apply to all emails sent out here)
				$mail_message = str_replace('<base_url>', get_base_url().'/', $mail_message);
				$mail_message = str_replace('<board_mailer>', $pun_config['o_board_title'], $mail_message);

				// Loop through users we found
				while ($cur_hit = $db->fetch_assoc($result))
				{
					if ($cur_hit['last_email_sent'] != '' && (time() - $cur_hit['last_email_sent']) < 3600 && (time() - $cur_hit['last_email_sent']) >= 0)
					message(sprintf($lang_front['Email flood'], intval((3600 - (time() - $cur_hit['last_email_sent'])) / 60)), true);
					
					// Generate a new password and a new password activation code
					$new_password = random_pass(8);
					$new_password_key = random_pass(8);

					$db->query('UPDATE '.$db->prefix.'users SET activate_string=\''.pun_hash($new_password).'\', activate_key=\''.$new_password_key.'\', last_email_sent = '.time().' WHERE id='.$cur_hit['id']) or error('Unable to update activation data', __FILE__, __LINE__, $db->error());

					// Do the user specific replacements to the template
					$cur_mail_message = str_replace('<username>', $cur_hit['username'], $mail_message);
					$cur_mail_message = str_replace('<activation_url>', get_base_url().'/profile.php?id='.$cur_hit['id'].'&action=change_pass&key='.$new_password_key, $cur_mail_message);
					$cur_mail_message = str_replace('<new_password>', $new_password, $cur_mail_message);

					pun_mail($email, $mail_subject, $cur_mail_message);
				}

				message($lang_front['Forget mail'].' <a href="mailto:'.pun_htmlspecialchars($pun_config['o_admin_email']).'">'.pun_htmlspecialchars($pun_config['o_admin_email']).'</a>.', true);
			}
			else
				$errors[] = $lang_front['No email match'].' '.htmlspecialchars($email).'.';
			}
		}

	$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_front['Request pass']);
	$required_fields = array('req_email' => $lang_common['Email']);
	$focus_element = array('request_pass', 'req_email');
	define ('FORUM_ACTIVE_PAGE', 'login');
	require FORUM_ROOT.'header.php';

// If there are errors, we display them
if (!empty($errors))
{

?>
<div id="posterror" class="block">
	<h2><span><?php echo $lang_front['New password errors'] ?></span></h2>
	<div class="box">
		<div class="inbox error-info">
			<p><?php echo $lang_front['New passworderrors info'] ?></p>
			<ul class="error-list">
<?php

	foreach ($errors as $cur_error)
		echo "\t\t\t\t".'<li><strong>'.$cur_error.'</strong></li>'."\n";
?>
			</ul>
		</div>
	</div>
</div>

<?php

}
?>
<form class="form-signin form-pass" id="request_pass" method="post" action="login.php?action=forget_2" onsubmit="this.request_pass.disabled=true;if(process_form(this)){return true;}else{this.request_pass.disabled=false;return false;}">
    <h1 class="form-signin-heading"><?php echo $lang_front['Request pass'] ?></h1>
    <fieldset>
        <h4><?php echo $lang_common['Email'] ?> <?php echo $lang_common['Required'] ?></h4>
        <input type="hidden" name="form_sent" value="1" />
        <label class="required"><input id="req_email input-large" type="text" name="req_email" size="50" maxlength="80" placeholder="<?php echo $lang_common['Email'] ?> <?php echo $lang_common['Required'] ?>" /><br /></label>
        <div class="control-group pull-right" style="margin-top: 60px;">
            <?php if (empty($errors)): ?><a class="btn" href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a><?php endif; ?><input class="btn btn-primary" type="submit" name="request_pass" value="<?php echo $lang_common['Submit'] ?>" />
        </div>
    </fieldset>
</form>
<?php

	require FORUM_ROOT.'footer.php';
}


if (!$pun_user['is_guest'])
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

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_common['Login']);
$required_fields = array('req_username' => $lang_common['Username'], 'req_password' => $lang_common['Password']);
$focus_element = array('login', 'req_username');
define('FORUM_ACTIVE_PAGE', 'login');
require FORUM_ROOT.'header.php';

?>
<form class="form-signin" id="login" method="post" action="login.php?action=in" onsubmit="return process_form(this)">
    <fieldset>
        <h1 class="form-signin-heading">ModernBB</h1>
        <input type="hidden" name="form_sent" value="1" />
        <input type="hidden" name="redirect_url" value="<?php echo pun_htmlspecialchars($redirect_url) ?>" />
        <div class"control-group">
            <label class="control-label"><?php echo $lang_common['Username'] ?></label>
            <div class="controls">
                <input class="form-control" type="text" name="req_username" size="25" maxlength="25" tabindex="1" placeholder="Username" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo $lang_common['Password'] ?></label>
            <div class="controls">
                <input class="form-control" type="password" name="req_password" size="25" tabindex="2" placeholder="Password" />
            </div>
        </div>
        <p class="actions"><span><?php if ($pun_config['o_regs_allow'] == '1') { ?><a href="register.php" tabindex="5"><?php echo $lang_front['Not registered'] ?></a></span> &middot; <span><?php }; ?><a href="login.php?action=forget" tabindex="6"><?php echo $lang_front['Forgotten pass'] ?></a> &middot; <a href="index.php" tabindex="4"><?php echo $lang_common['Go back'] ?></a></span></p>
        <div class="control-group">
            <div class="controls">
                <label><input type="checkbox" name="save_pass" value="1" tabindex="3" checked="checked" /> <?php echo $lang_front['Remember me'] ?></label>
            </div>
        </div>
        <div class="control-group pull-right">
            <input class="btn btn-primary" type="submit" name="login" value="<?php echo $lang_common['Login'] ?>" tabindex="4" />
        </div>
    </fieldset>
</form>
<?php

require FORUM_ROOT.'footer.php';
