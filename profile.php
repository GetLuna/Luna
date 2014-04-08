<?php

/**
 * Copyright (C) 2013-2014 ModernBB Group
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License under GPLv3
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';

// Include UTF-8 function
require FORUM_ROOT.'include/utf8/substr_replace.php';
require FORUM_ROOT.'include/utf8/ucwords.php'; // utf8_ucwords needs utf8_substr_replace
require FORUM_ROOT.'include/utf8/strcasecmp.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$section = isset($_GET['section']) ? $_GET['section'] : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 2)
	message($lang['Bad request'], false, '404 Not Found');

if ($action != 'change_pass' || !isset($_GET['key']))
{
	if ($luna_user['g_read_board'] == '0')
		message($lang['No view'], false, '403 Forbidden');
	else if ($luna_user['g_view_users'] == '0' && ($luna_user['is_guest'] || $luna_user['id'] != $id))
		message($lang['No permission'], false, '403 Forbidden');
}

if ($action == 'change_pass')
{
	if (isset($_GET['key']))
	{
		// If the user is already logged in we shouldn't be here :)
		if (!$luna_user['is_guest'])
		{
			header('Location: index.php');
			exit;
		}

		$key = $_GET['key'];

		$result = $db->query('SELECT * FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch new password', __FILE__, __LINE__, $db->error());
		$cur_user = $db->fetch_assoc($result);

		if ($key == '' || $key != $cur_user['activate_key'])
			message($lang['Pass key bad'].' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.');
		else
		{
			$db->query('UPDATE '.$db->prefix.'users SET password=\''.$cur_user['activate_string'].'\', activate_string=NULL, activate_key=NULL'.(!empty($cur_user['salt']) ? ', salt=NULL' : '').' WHERE id='.$id) or error('Unable to update password', __FILE__, __LINE__, $db->error());

			message($lang['Pass updated'], true);
		}
	}

	// Make sure we are allowed to change this user's password
	if ($luna_user['id'] != $id)
	{
		if (!$luna_user['is_admmod']) // A regular user trying to change another user's password?
			message($lang['No permission'], false, '403 Forbidden');
		else if ($luna_user['g_moderator'] == '1') // A moderator trying to change a user's password?
		{
			$result = $db->query('SELECT u.group_id, g.g_moderator FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'groups AS g ON (g.g_id=u.group_id) WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
			if (!$db->num_rows($result))
				message($lang['Bad request'], false, '404 Not Found');

			list($group_id, $is_moderator) = $db->fetch_row($result);

			if ($luna_user['g_mod_edit_users'] == '0' || $luna_user['g_mod_change_passwords'] == '0' || $group_id == FORUM_ADMIN || $is_moderator == '1')
				message($lang['No permission'], false, '403 Forbidden');
		}
	}

	if (isset($_POST['form_sent']))
	{
		// Make sure they got here from the site
		confirm_referrer('profile.php');

		$old_password = isset($_POST['req_old_password']) ? luna_trim($_POST['req_old_password']) : '';
		$new_password1 = luna_trim($_POST['req_new_password1']);
		$new_password2 = luna_trim($_POST['req_new_password2']);

		if ($new_password1 != $new_password2)
			message($lang['Pass not match']);
		if (luna_strlen($new_password1) < 6)
			message($lang['Pass too short']);

		$result = $db->query('SELECT * FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch password', __FILE__, __LINE__, $db->error());
		$cur_user = $db->fetch_assoc($result);

		$authorized = false;

		if (!empty($cur_user['password']))
		{
			$old_password_hash = luna_hash($old_password);

			if ($cur_user['password'] == $old_password_hash || $luna_user['is_admmod'])
				$authorized = true;
		}

		if (!$authorized)
			message($lang['Wrong pass']);

		$new_password_hash = luna_hash($new_password1);

		$db->query('UPDATE '.$db->prefix.'users SET password=\''.$new_password_hash.'\''.(!empty($cur_user['salt']) ? ', salt=NULL' : '').' WHERE id='.$id) or error('Unable to update password', __FILE__, __LINE__, $db->error());

		if ($luna_user['id'] == $id)
			luna_setcookie($luna_user['id'], $new_password_hash, time() + $luna_config['o_timeout_visit']);

		redirect('profile.php?section=personality&amp;id='.$id, $lang['Pass updated redirect']);
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Change pass']);
	$required_fields = array('req_old_password' => $lang['Old pass'], 'req_new_password1' => $lang['New pass'], 'req_new_password2' => $lang['Confirm new pass']);
	$focus_element = array('change_pass', ((!$luna_user['is_admmod']) ? 'req_old_password' : 'req_new_password1'));
	define('FORUM_ACTIVE_PAGE', 'profile');
	require FORUM_ROOT.'header.php';

?>
<h2 class="profile-h2"><?php echo $lang['Change pass'] ?></h2>
<form class="form-horizontal" id="change_pass" method="post" action="profile.php?action=change_pass&id=<?php echo $id ?>" onsubmit="return process_form(this)">
	<div class="panel panel-default">
    	<div class="panel-heading">
        	<h3 class="panel-title"><?php echo $lang['Change pass'] ?></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
				<?php if (!$luna_user['is_admmod']): ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Old pass'] ?></label>
                        <div class="col-sm-10">
                            <input class="form-control" type="password" name="req_old_password" />
                        </div>
                    </div>
				<?php endif; ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['New pass'] ?></label>
                    <div class="col-sm-10">
                        <input class="form-control" type="password" name="req_new_password1" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Confirm new pass'] ?></label>
                    <div class="col-sm-10">
                        <input class="form-control" type="password" name="req_new_password2" />
                    </div>
                </div>
                <p class="help-block"><?php echo $lang['Pass info'] ?></p>
            </fieldset>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" name="update" value="<?php echo $lang['Submit'] ?>" /> <a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a>
        </div>
    </div>
</form>
<?php

	require FORUM_ROOT.'footer.php';
}


else if ($action == 'change_email')
{
	// Make sure we are allowed to change this user's email
	if ($luna_user['id'] != $id)
	{
		if (!$luna_user['is_admmod']) // A regular user trying to change another user's email?
			message($lang['No permission'], false, '403 Forbidden');
		else if ($luna_user['g_moderator'] == '1') // A moderator trying to change a user's email?
		{
			$result = $db->query('SELECT u.group_id, g.g_moderator FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'groups AS g ON (g.g_id=u.group_id) WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
			if (!$db->num_rows($result))
				message($lang['Bad request'], false, '404 Not Found');

			list($group_id, $is_moderator) = $db->fetch_row($result);

			if ($luna_user['g_mod_edit_users'] == '0' || $group_id == FORUM_ADMIN || $is_moderator == '1')
				message($lang['No permission'], false, '403 Forbidden');
		}
	}

	if (isset($_GET['key']))
	{
		$key = $_GET['key'];

		$result = $db->query('SELECT activate_string, activate_key FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch activation data', __FILE__, __LINE__, $db->error());
		list($new_email, $new_email_key) = $db->fetch_row($result);

		if ($key == '' || $key != $new_email_key)
			message($lang['Email key bad'].' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.');
		else
		{
			$db->query('UPDATE '.$db->prefix.'users SET email=activate_string, activate_string=NULL, activate_key=NULL WHERE id='.$id) or error('Unable to update email address', __FILE__, __LINE__, $db->error());

			message($lang['Email updated'], true);
		}
	}
	else if (isset($_POST['form_sent']))
	{
		if (luna_hash($_POST['req_password']) !== $luna_user['password'])
			message($lang['Wrong pass']);

		// Make sure they got here from the site
		confirm_referrer('profile.php');

		require FORUM_ROOT.'include/email.php';

		// Validate the email address
		$new_email = strtolower(luna_trim($_POST['req_new_email']));
		if (!is_valid_email($new_email))
			message($lang['Invalid email']);

		// Check if it's a banned email address
		if (is_banned_email($new_email))
		{
			if ($luna_config['p_allow_banned_email'] == '0')
				message($lang['Banned email']);
			else if ($luna_config['o_mailing_list'] != '')
			{
				// Load the "banned email change" template
				$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/'.$luna_user['language'].'/mail_templates/banned_email_change.tpl'));

				// The first row contains the subject
				$first_crlf = strpos($mail_tpl, "\n");
				$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
				$mail_message = trim(substr($mail_tpl, $first_crlf));

				$mail_message = str_replace('<username>', $luna_user['username'], $mail_message);
				$mail_message = str_replace('<email>', $new_email, $mail_message);
				$mail_message = str_replace('<profile_url>', get_base_url().'/profile.php?id='.$id, $mail_message);
				$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

				luna_mail($luna_config['o_mailing_list'], $mail_subject, $mail_message);
			}
		}

		// Check if someone else already has registered with that email address
		$result = $db->query('SELECT id, username FROM '.$db->prefix.'users WHERE email=\''.$db->escape($new_email).'\'') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result))
		{
			if ($luna_config['p_allow_dupe_email'] == '0')
				message($lang['Dupe email']);
			else if ($luna_config['o_mailing_list'] != '')
			{
				while ($cur_dupe = $db->fetch_assoc($result))
					$dupe_list[] = $cur_dupe['username'];

				// Load the "dupe email change" template
				$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/'.$luna_user['language'].'/mail_templates/dupe_email_change.tpl'));

				// The first row contains the subject
				$first_crlf = strpos($mail_tpl, "\n");
				$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
				$mail_message = trim(substr($mail_tpl, $first_crlf));

				$mail_message = str_replace('<username>', $luna_user['username'], $mail_message);
				$mail_message = str_replace('<dupe_list>', implode(', ', $dupe_list), $mail_message);
				$mail_message = str_replace('<profile_url>', get_base_url().'/profile.php?id='.$id, $mail_message);
				$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

				luna_mail($luna_config['o_mailing_list'], $mail_subject, $mail_message);
			}
		}


		$new_email_key = random_pass(8);

		$db->query('UPDATE '.$db->prefix.'users SET activate_string=\''.$db->escape($new_email).'\', activate_key=\''.$new_email_key.'\' WHERE id='.$id) or error('Unable to update activation data', __FILE__, __LINE__, $db->error());

		// Load the "activate email" template
		$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/'.$luna_user['language'].'/mail_templates/activate_email.tpl'));

		// The first row contains the subject
		$first_crlf = strpos($mail_tpl, "\n");
		$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
		$mail_message = trim(substr($mail_tpl, $first_crlf));

		$mail_message = str_replace('<username>', $luna_user['username'], $mail_message);
		$mail_message = str_replace('<base_url>', get_base_url(), $mail_message);
		$mail_message = str_replace('<activation_url>', get_base_url().'/profile.php?action=change_email&id='.$id.'&key='.$new_email_key, $mail_message);
		$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

		luna_mail($new_email, $mail_subject, $mail_message);

		message($lang['Activate email sent'].' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.', true);
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Change email']);
	$required_fields = array('req_new_email' => $lang['New email'], 'req_password' => $lang['Password']);
	$focus_element = array('change_email', 'req_new_email');
	define('FORUM_ACTIVE_PAGE', 'profile');
	require FORUM_ROOT.'header.php';

?>
<h2 class="profile-h2"><?php echo $lang['Change email'] ?></h2>
<form id="change_email" method="post" action="profile.php?action=change_email&amp;id=<?php echo $id ?>" onsubmit="return process_form(this)">
    <fieldset>
        <h3><?php echo $lang['Email legend'] ?></h3>
        <input type="hidden" name="form_sent" value="1" />
        <label><strong><?php echo $lang['New email'] ?></strong><br /><input type="text" class="form-control" name="req_new_email" maxlength="80" /></label>
        <label><strong><?php echo $lang['Password'] ?></strong><br /><input type="password" name="req_password" /></label>
        <p><?php echo $lang['Email instructions'] ?></p>
    </fieldset>
    <p><input type="submit" class="btn btn-primary" name="new_email" value="<?php echo $lang['Submit'] ?>" /> <a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a></p>
</form>
<?php

	require FORUM_ROOT.'footer.php';
}


else if ($action == 'upload_avatar' || $action == 'upload_avatar2')
{
	if ($luna_config['o_avatars'] == '0')
		message($lang['Avatars disabled']);

	if ($luna_user['id'] != $id && !$luna_user['is_admmod'])
		message($lang['No permission'], false, '403 Forbidden');

	if (isset($_POST['form_sent']))
	{
		if (!isset($_FILES['req_file']))
			message($lang['No file']);

		// Make sure they got here from the site
		confirm_referrer('profile.php');

		$uploaded_file = $_FILES['req_file'];

		// Make sure the upload went smooth
		if (isset($uploaded_file['error']))
		{
			switch ($uploaded_file['error'])
			{
				case 1: // UPLOAD_ERR_INI_SIZE
				case 2: // UPLOAD_ERR_FORM_SIZE
					message($lang['Too large ini']);
					break;

				case 3: // UPLOAD_ERR_PARTIAL
					message($lang['Partial upload']);
					break;

				case 4: // UPLOAD_ERR_NO_FILE
					message($lang['No file']);
					break;

				case 6: // UPLOAD_ERR_NO_TMP_DIR
					message($lang['No tmp directory']);
					break;

				default:
					// No error occured, but was something actually uploaded?
					if ($uploaded_file['size'] == 0)
						message($lang['No file']);
					break;
			}
		}

		if (is_uploaded_file($uploaded_file['tmp_name']))
		{
			// Preliminary file check, adequate in most cases
			$allowed_types = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
			if (!in_array($uploaded_file['type'], $allowed_types))
				message($lang['Bad type']);

			// Make sure the file isn't too big
			if ($uploaded_file['size'] > $luna_config['o_avatars_size'])
				message($lang['Too large'].' '.forum_number_format($luna_config['o_avatars_size']).' '.$lang['bytes'].'.');

			// Move the file to the avatar directory. We do this before checking the width/height to circumvent open_basedir restrictions
			if (!@move_uploaded_file($uploaded_file['tmp_name'], FORUM_ROOT.$luna_config['o_avatars_dir'].'/'.$id.'.tmp'))
				message($lang['Move failed'].' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.');

			list($width, $height, $type,) = @getimagesize(FORUM_ROOT.$luna_config['o_avatars_dir'].'/'.$id.'.tmp');

			// Determine type
			if ($type == IMAGETYPE_GIF)
				$extension = '.gif';
			else if ($type == IMAGETYPE_JPEG)
				$extension = '.jpg';
			else if ($type == IMAGETYPE_PNG)
				$extension = '.png';
			else
			{
				// Invalid type
				@unlink(FORUM_ROOT.$luna_config['o_avatars_dir'].'/'.$id.'.tmp');
				message($lang['Bad type']);
			}

			// Now check the width/height
			if (empty($width) || empty($height) || $width > $luna_config['o_avatars_width'] || $height > $luna_config['o_avatars_height'])
			{
				@unlink(FORUM_ROOT.$luna_config['o_avatars_dir'].'/'.$id.'.tmp');
				message($lang['Too wide or high'].' '.$luna_config['o_avatars_width'].'x'.$luna_config['o_avatars_height'].' '.$lang['pixels'].'.');
			}


			// Delete any old avatars and put the new one in place
			delete_avatar($id);
			@rename(FORUM_ROOT.$luna_config['o_avatars_dir'].'/'.$id.'.tmp', FORUM_ROOT.$luna_config['o_avatars_dir'].'/'.$id.$extension);
			@chmod(FORUM_ROOT.$luna_config['o_avatars_dir'].'/'.$id.$extension, 0644);
		}
		else
			message($lang['Unknown failure']);

		redirect('profile.php?section=personality&amp;id='.$id, $lang['Avatar upload redirect']);
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Upload avatar']);
	$required_fields = array('req_file' => $lang['File']);
	$focus_element = array('upload_avatar', 'req_file');
	define('FORUM_ACTIVE_PAGE', 'profile');
	require FORUM_ROOT.'header.php';

?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $lang['Upload avatar'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="upload_avatar" method="post" enctype="multipart/form-data" action="profile.php?action=upload_avatar2&amp;id=<?php echo $id ?>" onsubmit="return process_form(this)">
            <fieldset>
                <input type="hidden" name="form_sent" value="1" />
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $luna_config['o_avatars_size'] ?>" />
                <label><strong><?php echo $lang['File'] ?></strong><br /><input name="req_file" type="file" /></label>
                <span class="help-block"><?php echo $lang['Avatar desc'].' '.$luna_config['o_avatars_width'].' x '.$luna_config['o_avatars_height'].' '.$lang['pixels'].' '.$lang['and'].' '.forum_number_format($luna_config['o_avatars_size']).' '.$lang['bytes'].' ('.file_size($luna_config['o_avatars_size']).').' ?></span>
            </fieldset>
            <input type="submit" class="btn btn-primary" name="upload" value="<?php echo $lang['Upload'] ?>" /> <a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a>
        </form>
    </div>
</div>
<?php

	require FORUM_ROOT.'footer.php';
}


else if ($action == 'delete_avatar')
{
	if ($luna_user['id'] != $id && !$luna_user['is_admmod'])
		message($lang['No permission'], false, '403 Forbidden');

	confirm_referrer('profile.php');

	delete_avatar($id);

	redirect('profile.php?section=personality&amp;id='.$id, $lang['Avatar deleted redirect']);
}


else if (isset($_POST['update_group_membership']))
{
	if ($luna_user['g_id'] > FORUM_ADMIN)
		message($lang['No permission'], false, '403 Forbidden');

	confirm_referrer('profile.php');

	$new_group_id = intval($_POST['group_id']);

	$result = $db->query('SELECT group_id FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch user group', __FILE__, __LINE__, $db->error());
	$old_group_id = $db->result($result);

	$db->query('UPDATE '.$db->prefix.'users SET group_id='.$new_group_id.' WHERE id='.$id) or error('Unable to change user group', __FILE__, __LINE__, $db->error());

	// Regenerate the users info cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_users_info_cache();

	if ($old_group_id == FORUM_ADMIN || $new_group_id == FORUM_ADMIN)
		generate_admins_cache();

	$result = $db->query('SELECT g_moderator FROM '.$db->prefix.'groups WHERE g_id='.$new_group_id) or error('Unable to fetch group', __FILE__, __LINE__, $db->error());
	$new_group_mod = $db->result($result);

	// If the user was a moderator or an administrator, we remove him/her from the moderator list in all forums as well
	if ($new_group_id != FORUM_ADMIN && $new_group_mod != '1')
	{
		$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

		while ($cur_forum = $db->fetch_assoc($result))
		{
			$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

			if (in_array($id, $cur_moderators))
			{
				$username = array_search($id, $cur_moderators);
				unset($cur_moderators[$username]);
				$cur_moderators = (!empty($cur_moderators)) ? '\''.$db->escape(serialize($cur_moderators)).'\'' : 'NULL';

				$db->query('UPDATE '.$db->prefix.'forums SET moderators='.$cur_moderators.' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
			}
		}
	}

	redirect('profile.php?section=admin&amp;id='.$id, $lang['Group membership redirect']);
}


else if (isset($_POST['update_forums']))
{
	if ($luna_user['g_id'] > FORUM_ADMIN)
		message($lang['No permission'], false, '403 Forbidden');

	confirm_referrer('profile.php');

	// Get the username of the user we are processing
	$result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	$username = $db->result($result);

	$moderator_in = (isset($_POST['moderator_in'])) ? array_keys($_POST['moderator_in']) : array();

	// Loop through all forums
	$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

	while ($cur_forum = $db->fetch_assoc($result))
	{
		$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();
		// If the user should have moderator access (and he/she doesn't already have it)
		if (in_array($cur_forum['id'], $moderator_in) && !in_array($id, $cur_moderators))
		{
			$cur_moderators[$username] = $id;
			uksort($cur_moderators, 'utf8_strcasecmp');

			$db->query('UPDATE '.$db->prefix.'forums SET moderators=\''.$db->escape(serialize($cur_moderators)).'\' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
		}
		// If the user shouldn't have moderator access (and he/she already has it)
		else if (!in_array($cur_forum['id'], $moderator_in) && in_array($id, $cur_moderators))
		{
			unset($cur_moderators[$username]);
			$cur_moderators = (!empty($cur_moderators)) ? '\''.$db->escape(serialize($cur_moderators)).'\'' : 'NULL';

			$db->query('UPDATE '.$db->prefix.'forums SET moderators='.$cur_moderators.' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
		}
	}

	redirect('profile.php?section=admin&amp;id='.$id, $lang['Update forums redirect']);
}


else if (isset($_POST['ban']))
{
	if ($luna_user['g_id'] != FORUM_ADMIN && ($luna_user['g_moderator'] != '1' || $luna_user['g_mod_ban_users'] == '0'))
		message($lang['No permission'], false, '403 Forbidden');

	confirm_referrer('profile.php');

	// Get the username of the user we are banning
	$result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch username', __FILE__, __LINE__, $db->error());
	$username = $db->result($result);

	// Check whether user is already banned
	$result = $db->query('SELECT id FROM '.$db->prefix.'bans WHERE username = \''.$db->escape($username).'\' ORDER BY expire IS NULL DESC, expire DESC LIMIT 1') or error('Unable to fetch ban ID', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result))
	{
		$ban_id = $db->result($result);
		redirect('backstage/bans.php?edit_ban='.$ban_id.'&amp;exists', $lang['Ban redirect']);
	}
	else
		redirect('backstage/bans.php?add_ban='.$id, $lang['Ban redirect']);
}


else if (isset($_POST['delete_user']) || isset($_POST['delete_user_comply']))
{
	if ($luna_user['g_id'] > FORUM_ADMIN)
		message($lang['No permission'], false, '403 Forbidden');

	// Get the username and group of the user we are deleting
	$result = $db->query('SELECT group_id, username FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	list($group_id, $username) = $db->fetch_row($result);

	if ($group_id == FORUM_ADMIN)
		message($lang['No delete admin message']);

	if (isset($_POST['delete_user_comply']))
	{
		// If the user is a moderator or an administrator, we remove him/her from the moderator list in all forums as well
		$result = $db->query('SELECT g_moderator FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to fetch group', __FILE__, __LINE__, $db->error());
		$group_mod = $db->result($result);

		if ($group_id == FORUM_ADMIN || $group_mod == '1')
		{
			$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

			while ($cur_forum = $db->fetch_assoc($result))
			{
				$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

				if (in_array($id, $cur_moderators))
				{
					unset($cur_moderators[$username]);
					$cur_moderators = (!empty($cur_moderators)) ? '\''.$db->escape(serialize($cur_moderators)).'\'' : 'NULL';

					$db->query('UPDATE '.$db->prefix.'forums SET moderators='.$cur_moderators.' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
				}
			}
		}

		// Delete any subscriptions
		$db->query('DELETE FROM '.$db->prefix.'topic_subscriptions WHERE user_id='.$id) or error('Unable to delete topic subscriptions', __FILE__, __LINE__, $db->error());
		$db->query('DELETE FROM '.$db->prefix.'forum_subscriptions WHERE user_id='.$id) or error('Unable to delete forum subscriptions', __FILE__, __LINE__, $db->error());

		// Remove him/her from the online list (if they happen to be logged in)
		$db->query('DELETE FROM '.$db->prefix.'online WHERE user_id='.$id) or error('Unable to remove user from online list', __FILE__, __LINE__, $db->error());

		// Should we delete all posts made by this user?
		if (isset($_POST['delete_posts']))
		{
			require FORUM_ROOT.'include/search_idx.php';
			@set_time_limit(0);

			// Find all posts made by this user
			$result = $db->query('SELECT p.id, p.topic_id, t.forum_id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id WHERE p.poster_id='.$id) or error('Unable to fetch posts', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result))
			{
				while ($cur_post = $db->fetch_assoc($result))
				{
					// Determine whether this post is the "topic post" or not
					$result2 = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE topic_id='.$cur_post['topic_id'].' ORDER BY posted LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());

					if ($db->result($result2) == $cur_post['id'])
						delete_topic($cur_post['topic_id']);
					else
						delete_post($cur_post['id'], $cur_post['topic_id']);

					update_forum($cur_post['forum_id']);
				}
			}
		}
		else
			// Set all his/her posts to guest
			$db->query('UPDATE '.$db->prefix.'posts SET poster_id=1 WHERE poster_id='.$id) or error('Unable to update posts', __FILE__, __LINE__, $db->error());

		// Delete the user
		$db->query('DELETE FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to delete user', __FILE__, __LINE__, $db->error());

		// Delete user avatar
		delete_avatar($id);

		// Regenerate the users info cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_users_info_cache();

		if ($group_id == FORUM_ADMIN)
			generate_admins_cache();

		redirect('index.php', $lang['User delete redirect']);
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Confirm delete user']);
	define('FORUM_ACTIVE_PAGE', 'profile');
	require FORUM_ROOT.'header.php';

?>
<h2 class="profile-h2"><?php echo $lang['Confirm delete user'] ?></h2>
<form id="confirm_del_user" method="post" action="profile.php?id=<?php echo $id ?>">
    <fieldset>
        <div class="panel panel-danger">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $lang['Confirmation info'].' <strong>'.luna_htmlspecialchars($username).'</strong>' ?></h3>
            </div>
            <div class="panel-body">
				<?php echo $lang['Delete warning'] ?>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="delete_posts" value="1" checked="checked" />
                        <?php echo $lang['Delete posts'] ?>
                    </label>
                </div>
			</div>
            <div class="panel-footer">
				<input type="submit" class="btn btn-primary" name="delete_user_comply" value="<?php echo $lang['Delete'] ?>" /> <a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a>
    		</div>
        </div>
    </fieldset>
</form>
<?php

	require FORUM_ROOT.'footer.php';
}


else if (isset($_POST['form_sent']))
{
	// Fetch the user group of the user we are editing
	$result = $db->query('SELECT u.username, u.group_id, g.g_moderator FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON (g.g_id=u.group_id) WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message($lang['Bad request'], false, '404 Not Found');

	list($old_username, $group_id, $is_moderator) = $db->fetch_row($result);

	if ($luna_user['id'] != $id &&																	// If we aren't the user (i.e. editing your own profile)
		(!$luna_user['is_admmod'] ||																	// and we are not an admin or mod
		($luna_user['g_id'] != FORUM_ADMIN &&														// or we aren't an admin and ...
		($luna_user['g_mod_edit_users'] == '0' ||													// mods aren't allowed to edit users
		$group_id == FORUM_ADMIN ||																	// or the user is an admin
		$is_moderator))))																			// or the user is another mod
		message($lang['No permission'], false, '403 Forbidden');

	// Make sure they got here from the site
	confirm_referrer('profile.php');

	$username_updated = false;

	// Validate input depending on section
	switch ($section)
	{
		case 'admin':
		{
			$form = array();

			if ($luna_user['is_admmod'])
			{
				$form['admin_note'] = luna_trim($_POST['admin_note']);

				// We only allow administrators to update the post count
				if ($luna_user['g_id'] == FORUM_ADMIN)
					$form['num_posts'] = intval($_POST['num_posts']);
			}

			break;
		}

		case 'personality':
		{
			$form = array(
				'realname'		=> luna_trim($_POST['form']['realname']),
				'url'			=> luna_trim($_POST['form']['url']),
				'location'		=> luna_trim($_POST['form']['location']),
				'jabber'		=> luna_trim($_POST['form']['jabber']),
				'icq'			=> luna_trim($_POST['form']['icq']),
				'msn'			=> luna_trim($_POST['form']['msn']),
				'aim'			=> luna_trim($_POST['form']['aim']),
				'yahoo'			=> luna_trim($_POST['form']['yahoo']),
			);

			if ($luna_user['is_admmod'])
			{
				// Are we allowed to change usernames?
				if ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_rename_users'] == '1'))
				{
					$form['username'] = luna_trim($_POST['req_username']);

					if ($form['username'] != $old_username)
					{
						// Check username
						$errors = array();
						check_username($form['username'], $id);
						if (!empty($errors))
							message($errors[0]);

						$username_updated = true;
					}
				}
			}

			if ($luna_config['o_regs_verify'] == '0' || $luna_user['is_admmod'])
			{
				require FORUM_ROOT.'include/email.php';

				// Validate the email address
				$form['email'] = strtolower(luna_trim($_POST['req_email']));
				if (!is_valid_email($form['email']))
					message($lang['Invalid email']);
			}

			// Add http:// if the URL doesn't contain it already (while allowing https://, too)
			if ($form['url'] != '')
			{
				$url = url_valid($form['url']);

				if ($url === false)
					message($lang['Invalid website URL']);

				$form['url'] = $url['url'];
			}

			if ($luna_user['g_id'] == FORUM_ADMIN)
				$form['title'] = luna_trim($_POST['title']);
			else if ($luna_user['g_set_title'] == '1')
			{
				$form['title'] = luna_trim($_POST['title']);

				if ($form['title'] != '')
				{
					// A list of words that the title may not contain
					// If the language is English, there will be some duplicates, but it's not the end of the world
					$forbidden = array('member', 'moderator', 'administrator', 'banned', 'guest', utf8_strtolower($lang['Member']), utf8_strtolower($lang['Moderator']), utf8_strtolower($lang['Administrator']), utf8_strtolower($lang['Banned']), utf8_strtolower($lang['Guest']));

					if (in_array(utf8_strtolower($form['title']), $forbidden))
						message($lang['Forbidden title']);
				}
			}

			// If the ICQ UIN contains anything other than digits it's invalid
			if (preg_match('%[^0-9]%', $form['icq']))
				message($lang['Bad ICQ']);

			// Clean up signature from POST
			if ($luna_config['o_signatures'] == '1')
			{
				$form['signature'] = luna_linebreaks(luna_trim($_POST['signature']));

				// Validate signature
				if (luna_strlen($form['signature']) > $luna_config['p_sig_length'])
					message(sprintf($lang['Sig too long'], $luna_config['p_sig_length'], luna_strlen($form['signature']) - $luna_config['p_sig_length']));
				else if (substr_count($form['signature'], "\n") > ($luna_config['p_sig_lines']-1))
					message(sprintf($lang['Sig too many lines'], $luna_config['p_sig_lines']));
				else if ($form['signature'] && $luna_config['p_sig_all_caps'] == '0' && is_all_uppercase($form['signature']) && !$luna_user['is_admmod'])
					$form['signature'] = utf8_ucwords(utf8_strtolower($form['signature']));

				// Validate BBCode syntax
				if ($luna_config['p_sig_bbcode'] == '1')
				{
					require FORUM_ROOT.'include/parser.php';

					$errors = array();

					$form['signature'] = preparse_bbcode($form['signature'], $errors, true);

					if(count($errors) > 0)
						message('<ul><li>'.implode('</li><li>', $errors).'</li></ul>');
				}
			}

			break;
		}

		case 'settings':
		{
			$form = array(
				'timezone'			=> floatval($_POST['form']['timezone']),
				'dst'				=> isset($_POST['form']['dst']) ? '1' : '0',
				'time_format'		=> intval($_POST['form']['time_format']),
				'date_format'		=> intval($_POST['form']['date_format']),
				'disp_topics'		=> luna_trim($_POST['form']['disp_topics']),
				'disp_posts'		=> luna_trim($_POST['form']['disp_posts']),
				'show_smilies'		=> isset($_POST['form']['show_smilies']) ? '1' : '0',
				'show_img'			=> isset($_POST['form']['show_img']) ? '1' : '0',
				'show_img_sig'		=> isset($_POST['form']['show_img_sig']) ? '1' : '0',
				'show_avatars'		=> isset($_POST['form']['show_avatars']) ? '1' : '0',
				'show_sig'			=> isset($_POST['form']['show_sig']) ? '1' : '0',
				'email_setting'		=> intval($_POST['form']['email_setting']),
				'notify_with_post'	=> isset($_POST['form']['notify_with_post']) ? '1' : '0',
				'auto_notify'		=> isset($_POST['form']['auto_notify']) ? '1' : '0',
			);

			if ($form['disp_topics'] != '')
			{
				$form['disp_topics'] = intval($form['disp_topics']);
				if ($form['disp_topics'] < 3)
					$form['disp_topics'] = 3;
				else if ($form['disp_topics'] > 75)
					$form['disp_topics'] = 75;
			}

			if ($form['disp_posts'] != '')
			{
				$form['disp_posts'] = intval($form['disp_posts']);
				if ($form['disp_posts'] < 3)
					$form['disp_posts'] = 3;
				else if ($form['disp_posts'] > 75)
					$form['disp_posts'] = 75;
			}

			// Make sure we got a valid language string
			if (isset($_POST['form']['language']))
			{
				$languages = forum_list_langs();
				$form['language'] = luna_trim($_POST['form']['language']);
				if (!in_array($form['language'], $languages))
					message($lang['Bad request'], false, '404 Not Found');
			}

			// Make sure we got a valid style string
			if (isset($_POST['form']['style']))
			{
				$styles = forum_list_styles();
				$form['style'] = luna_trim($_POST['form']['style']);
				if (!in_array($form['style'], $styles))
					message($lang['Bad request'], false, '404 Not Found');
			}

			// Make sure we got a valid Backstage style string
			if (isset($_POST['form']['backstage_style']))
			{
				$backstage_styles = backstage_list_styles();
				$form['backstage_style'] = luna_trim($_POST['form']['backstage_style']);
				if (!in_array($form['backstage_style'], $backstage_styles))
					message($lang['Bad request'], false, '404 Not Found');
			}

			if ($form['email_setting'] < 0 || $form['email_setting'] > 2)
				$form['email_setting'] = $luna_config['o_default_email_setting'];

			break;
		}

		default:
			message($lang['Bad request'], false, '404 Not Found');
	}


	// Single quotes around non-empty values and NULL for empty values
	$temp = array();
	foreach ($form as $key => $input)
	{
		$value = ($input !== '') ? '\''.$db->escape($input).'\'' : 'NULL';

		$temp[] = $key.'='.$value;
	}

	if (empty($temp))
		message($lang['Bad request'], false, '404 Not Found');


	$db->query('UPDATE '.$db->prefix.'users SET '.implode(',', $temp).' WHERE id='.$id) or error('Unable to update profile', __FILE__, __LINE__, $db->error());

	// If we changed the username we have to update some stuff
	if ($username_updated)
	{
		$db->query('UPDATE '.$db->prefix.'bans SET username=\''.$db->escape($form['username']).'\' WHERE username=\''.$db->escape($old_username).'\'') or error('Unable to update bans', __FILE__, __LINE__, $db->error());
		// If any bans were updated, we will need to know because the cache will need to be regenerated.
		if ($db->affected_rows() > 0)
			$bans_updated = true;
		$db->query('UPDATE '.$db->prefix.'posts SET poster=\''.$db->escape($form['username']).'\' WHERE poster_id='.$id) or error('Unable to update posts', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'posts SET edited_by=\''.$db->escape($form['username']).'\' WHERE edited_by=\''.$db->escape($old_username).'\'') or error('Unable to update posts', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'topics SET poster=\''.$db->escape($form['username']).'\' WHERE poster=\''.$db->escape($old_username).'\'') or error('Unable to update topics', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'topics SET last_poster=\''.$db->escape($form['username']).'\' WHERE last_poster=\''.$db->escape($old_username).'\'') or error('Unable to update topics', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'forums SET last_poster=\''.$db->escape($form['username']).'\' WHERE last_poster=\''.$db->escape($old_username).'\'') or error('Unable to update forums', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'online SET ident=\''.$db->escape($form['username']).'\' WHERE ident=\''.$db->escape($old_username).'\'') or error('Unable to update online list', __FILE__, __LINE__, $db->error());

		// If the user is a moderator or an administrator we have to update the moderator lists
		$result = $db->query('SELECT group_id FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
		$group_id = $db->result($result);

		$result = $db->query('SELECT g_moderator FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to fetch group', __FILE__, __LINE__, $db->error());
		$group_mod = $db->result($result);

		if ($group_id == FORUM_ADMIN || $group_mod == '1')
		{
			$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

			while ($cur_forum = $db->fetch_assoc($result))
			{
				$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

				if (in_array($id, $cur_moderators))
				{
					unset($cur_moderators[$old_username]);
					$cur_moderators[$form['username']] = $id;
					uksort($cur_moderators, 'utf8_strcasecmp');

					$db->query('UPDATE '.$db->prefix.'forums SET moderators=\''.$db->escape(serialize($cur_moderators)).'\' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
				}
			}
		}

		// Regenerate the users info cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_users_info_cache();

		// Check if the bans table was updated and regenerate the bans cache when needed
		if (isset($bans_updated))
			generate_bans_cache();
	}

	redirect('profile.php?section='.$section.'&amp;id='.$id, $lang['Profile redirect']);
}


$result = $db->query('SELECT u.username, u.email, u.title, u.realname, u.url, u.jabber, u.icq, u.msn, u.aim, u.yahoo, u.location, u.signature, u.disp_topics, u.disp_posts, u.email_setting, u.notify_with_post, u.auto_notify, u.show_smilies, u.show_img, u.show_img_sig, u.show_avatars, u.show_sig, u.timezone, u.dst, u.language, u.style, u.backstage_style, u.num_posts, u.last_post, u.registered, u.registration_ip, u.admin_note, u.date_format, u.time_format, u.last_visit, g.g_id, g.g_user_title, g.g_moderator FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message($lang['Bad request'], false, '404 Not Found');

$user = $db->fetch_assoc($result);

$last_post = format_time($user['last_post']);

if ($user['signature'] != '')
{
	require FORUM_ROOT.'include/parser.php';
	$parsed_signature = parse_signature($user['signature']);
}


// View or edit?
if ($luna_user['id'] != $id &&																	// If we aren't the user (i.e. editing your own profile)
	(!$luna_user['is_admmod'] ||																	// and we are not an admin or mod
	($luna_user['g_id'] != FORUM_ADMIN &&															// or we aren't an admin and ...
	($luna_user['g_mod_edit_users'] == '0' ||													// mods aren't allowed to edit users
	$user['g_id'] == FORUM_ADMIN ||																// or the user is an admin
	$user['g_moderator'] == '1'))))																// or the user is another mod
{
	$user_personality = array();

	$user_personality[] = '<tr><th class="col-md-2">'.$lang['Username'].'</th>';
	$user_personality[] = '<td>'.luna_htmlspecialchars($user['username']).'</td></tr>';

	$user_title_field = get_title($user);
	$user_personality[] = '<tr><th>'.$lang['Title'].'</th>';
	$user_personality[] = '<td>'.(($luna_config['o_censoring'] == '1') ? censor_words($user_title_field) : $user_title_field).'</td></tr>';

	if ($user['realname'] != '')
	{
		$user_personality[] = '<tr><th>'.$lang['Realname'].'</th>';
		$user_personality[] = '<td>'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['realname']) : $user['realname']).'</td></tr>';
	}

	if ($user['location'] != '')
	{
		$user_personality[] = '<tr><th>'.$lang['Location'].'</th>';
		$user_personality[] = '<td>'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['location']) : $user['location']).'</td></tr>';
	}

	if ($user['url'] != '')
	{
		$user['url'] = luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['url']) : $user['url']);
		$user_personality[] = '<tr><th>'.$lang['Website'].'</th>';
		$user_personality[] = '<td><span class="website"><a href="'.$user['url'].'" rel="nofollow">'.$user['url'].'</a></span></td></tr>';
	}

	if ($user['email_setting'] == '0' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
		$email_field = '<a href="mailto:'.luna_htmlspecialchars($user['email']).'">'.luna_htmlspecialchars($user['email']).'</a>';
	else if ($user['email_setting'] == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
		$email_field = '<a class="btn btn-primary" href="misc.php?email='.$id.'">'.$lang['Send email'].'</a>';
	else
		$email_field = '';
	if ($email_field != '')
	{
		$user_personality[] = '<tr><th>'.$lang['Email'].'</th>';
		$user_personality[] = '<td><span class="email">'.$email_field.'</span></td></tr>';
	}

	$user_messaging = array();

	if ($user['jabber'] != '')
	{
		$user_messaging[] = '<tr><th>'.$lang['Jabber'].'</th>';
		$user_messaging[] = '<td>'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['jabber']) : $user['jabber']).'</td></tr>';
	}

	if ($user['icq'] != '')
	{
		$user_messaging[] = '<tr><th>'.$lang['ICQ'].'</th>';
		$user_messaging[] = '<td>'.$user['icq'].'</td></tr>';
	}

	if ($user['msn'] != '')
	{
		$user_messaging[] = '<tr><th>'.$lang['MSN'].'</th>';
		$user_messaging[] = '<td>'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['msn']) : $user['msn']).'</td></tr>';
	}

	if ($user['aim'] != '')
	{
		$user_messaging[] = '<tr><th>'.$lang['AOL IM'].'</th>';
		$user_messaging[] = '<td>'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['aim']) : $user['aim']).'</td></tr>';
	}

	if ($user['yahoo'] != '')
	{
		$user_messaging[] = '<tr><th>'.$lang['Yahoo'].'</th>';
		$user_messaging[] = '<td>'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['yahoo']) : $user['yahoo']).'</td></tr>';
	}

	if ($luna_config['o_avatars'] == '1')
	{
		$avatar_field = generate_avatar_markup($id);
		if ($avatar_field != '')
		{
			$user_personality[] = '<tr><th>'.$lang['Avatar'].'</th>';
			$user_personality[] = '<td>'.$avatar_field.'</td></tr>';
		}
	}

	if ($luna_config['o_signatures'] == '1')
	{
		if (isset($parsed_signature))
		{
			$user_personality[] = '<tr><th>'.$lang['Signature'].'</th>';
			$user_personality[] = '<td><div class="postsignature postmsg">'.$parsed_signature.'</div></td></tr>';
		}
	}

	$user_activity = array();

	$posts_field = '';
	if ($luna_config['o_show_post_count'] == '1' || $luna_user['is_admmod'])
		$posts_field = forum_number_format($user['num_posts']);
	if ($luna_user['g_search'] == '1')
	{
		$quick_searches = array();
		if ($user['num_posts'] > 0)
		{
			$quick_searches[] = '<a class="btn btn-primary" href="search.php?action=show_user_topics&amp;user_id='.$id.'">'.$lang['Show topics'].'</a>';
			$quick_searches[] = '<a class="btn btn-primary" href="search.php?action=show_user_posts&amp;user_id='.$id.'">'.$lang['Show posts'].'</a>';
		}
		if ($luna_user['is_admmod'] && $luna_config['o_topic_subscriptions'] == '1')
			$quick_searches[] = '<a class="btn btn-primary" href="search.php?action=show_subscriptions&amp;user_id='.$id.'">'.$lang['Show subscriptions'].'</a>';

		if (!empty($quick_searches))
			$posts_field .= (($posts_field != '') ? ' &middot; ' : '').implode(' &middot; ', $quick_searches);
	}
	if ($posts_field != '')
	{
		$user_activity[] = '<tr><th>'.$lang['Posts table'].'</th>';
		$user_activity[] = '<td>'.$posts_field.'</td></tr>';
	}

	if ($user['num_posts'] > 0)
	{
		$user_activity[] = '<tr><th>'.$lang['Last post'].'</th>';
		$user_activity[] = '<td>'.$last_post.'</td></tr>';
	}

	$user_activity[] = '<tr><th>'.$lang['Registered table'].'</th>';
	$user_activity[] = '<td>'.format_time($user['registered'], true).'</td></tr>';

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), sprintf($lang['Users profile'], luna_htmlspecialchars($user['username'])));
	define('FORUM_ALLOW_INDEX', 1);
	define('FORUM_ACTIVE_PAGE', 'index');
	require FORUM_ROOT.'header.php';

?>
<h2><?php echo luna_htmlspecialchars($user['username']) ?></h2>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Profile'] ?></h3>
    </div>
    <table class="table">
        <thead></thead>
        <tbody>
            <tr><th class="active" colspan="2"><h3><?php echo $lang['Section personality'] ?></h3></th></tr>
            <?php echo implode("\n\t\t\t\t\t\t\t", $user_personality)."\n" ?>
            <?php if (!empty($user_messaging)): ?>
                <tr><th class="active" colspan="2"><h3><?php echo $lang['Section messaging'] ?></h3></th></tr>
                <?php echo implode("\n\t\t\t\t\t\t\t", $user_messaging)."\n" ?>
            <?php endif; ?>
            <tr><th class="active" colspan="2"><h3><?php echo $lang['User activity'] ?></h3></th></tr>
            <?php echo implode("\n\t\t\t\t\t\t\t", $user_activity)."\n" ?>
        </tbody>
    </table>
</div>

<?php

	require FORUM_ROOT.'footer.php';
}
else
{
	if (!$section || $section == 'view')
	{

		$user_personality = array();

		$user_personality[] = '<tr><th class="col-md-2">'.$lang['Username'].'</th>';
		$user_personality[] = '<td>'.luna_htmlspecialchars($user['username']).'</td></tr>';

		$user_title_field = get_title($user);
		$user_personality[] = '<tr><th>'.$lang['Title'].'</th>';
		$user_personality[] = '<td>'.(($luna_config['o_censoring'] == '1') ? censor_words($user_title_field) : $user_title_field).'</td></tr>';

		if ($user['realname'] != '')
		{
			$user_personality[] = '<tr><th>'.$lang['Realname'].'</th>';
			$user_personality[] = '<td>'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['realname']) : $user['realname']).'</td></tr>';
		}

		if ($user['location'] != '')
		{
			$user_personality[] = '<tr><th>'.$lang['Location'].'</th>';
			$user_personality[] = '<td>'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['location']) : $user['location']).'</td></tr>';
		}

		if ($user['url'] != '')
		{
			$user['url'] = luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['url']) : $user['url']);
			$user_personality[] = '<tr><th>'.$lang['Website'].'</th>';
			$user_personality[] = '<td><span class="website"><a href="'.$user['url'].'" rel="nofollow">'.$user['url'].'</a></span></td></tr>';
		}

		if ($user['email_setting'] == '0' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
			$email_field = '<a href="mailto:'.luna_htmlspecialchars($user['email']).'">'.luna_htmlspecialchars($user['email']).'</a>';
		else if ($user['email_setting'] == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
			$email_field = '<a class="btn btn-primary" href="misc.php?email='.$id.'">'.$lang['Send email'].'</a>';
		else
			$email_field = '';
		if ($email_field != '')
		{
			$user_personality[] = '<tr><th>'.$lang['Email'].'</th>';
			$user_personality[] = '<td><span class="email">'.$email_field.'</span></td></tr>';
		}

		$user_messaging = array();

		if ($user['jabber'] != '')
		{
			$user_messaging[] = '<tr><th>'.$lang['Jabber'].'</th>';
			$user_messaging[] = '<td>'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['jabber']) : $user['jabber']).'</td></tr>';
		}

		if ($user['icq'] != '')
		{
			$user_messaging[] = '<tr><th>'.$lang['ICQ'].'</th>';
			$user_messaging[] = '<td>'.$user['icq'].'</td></tr>';
		}

		if ($user['msn'] != '')
		{
			$user_messaging[] = '<tr><th>'.$lang['MSN'].'</th>';
			$user_messaging[] = '<td>'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['msn']) : $user['msn']).'</td></tr>';
		}

		if ($user['aim'] != '')
		{
			$user_messaging[] = '<tr><th>'.$lang['AOL IM'].'</th>';
			$user_messaging[] = '<td>'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['aim']) : $user['aim']).'</td></tr>';
		}

		if ($user['yahoo'] != '')
		{
			$user_messaging[] = '<tr><th>'.$lang['Yahoo'].'</th>';
			$user_messaging[] = '<td>'.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['yahoo']) : $user['yahoo']).'</td></tr>';
		}

		if ($luna_config['o_avatars'] == '1')
		{
			$avatar_field = generate_avatar_markup($id);
			if ($avatar_field != '')
			{
				$user_personality[] = '<tr><th>'.$lang['Avatar'].'</th>';
				$user_personality[] = '<td>'.$avatar_field.'</td></tr>';
			}
		}

		if ($luna_config['o_signatures'] == '1')
		{
			if (isset($parsed_signature))
			{
				$user_personality[] = '<tr><th>'.$lang['Signature'].'</th>';
				$user_personality[] = '<td><div class="postsignature postmsg">'.$parsed_signature.'</div></td></tr>';
			}
		}

		$user_activity = array();

		$posts_field = '';
		if ($luna_config['o_show_post_count'] == '1' || $luna_user['is_admmod'])
			$posts_field = forum_number_format($user['num_posts']);
		if ($luna_user['g_search'] == '1')
		{
			$quick_searches = array();
			if ($user['num_posts'] > 0)
			{
				$quick_searches[] = '<a class="btn btn-primary" href="search.php?action=show_user_topics&amp;user_id='.$id.'">'.$lang['Show topics'].'</a>';
				$quick_searches[] = '<a class="btn btn-primary" href="search.php?action=show_user_posts&amp;user_id='.$id.'">'.$lang['Show posts'].'</a>';
			}
			if ($luna_user['is_admmod'] && $luna_config['o_topic_subscriptions'] == '1')
				$quick_searches[] = '<a class="btn btn-primary" href="search.php?action=show_subscriptions&amp;user_id='.$id.'">'.$lang['Show subscriptions'].'</a>';

			if (!empty($quick_searches))
				$posts_field .= (($posts_field != '') ? ' &middot; ' : '').implode(' &middot; ', $quick_searches);
		}
		if ($posts_field != '')
		{
			$user_activity[] = '<tr><th>'.$lang['Posts table'].'</th>';
			$user_activity[] = '<td>'.$posts_field.'</td></tr>';
		}

		if ($user['num_posts'] > 0)
		{
			$user_activity[] = '<tr><th>'.$lang['Last post'].'</th>';
			$user_activity[] = '<td>'.$last_post.'</td></tr>';
		}

		$user_activity[] = '<tr><th>'.$lang['Registered table'].'</th>';
		$user_activity[] = '<td>'.format_time($user['registered'], true).'</td></tr>';

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']).' / '.$lang['Profile']);
	define('FORUM_ACTIVE_PAGE', 'profile');
	require FORUM_ROOT.'header.php';

	generate_profile_menu('view');

?>

<div class="col-sm-10 col-profile">
	<h2><?php echo luna_htmlspecialchars($user['username']) ?> <small><?php echo $lang['View info'] ?></small></h2>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Profile'] ?></h3>
        </div>
        <table class="table">
            <thead></thead>
            <tbody>
                <tr><th class="active" colspan="2"><h3><?php echo $lang['Section personality'] ?></h3></th></tr>
                <?php echo implode("\n\t\t\t\t\t\t\t", $user_personality)."\n" ?>
				<?php if (!empty($user_messaging)): ?>
                    <tr><th class="active" colspan="2"><h3><?php echo $lang['Section messaging'] ?></h3></th></tr>
                    <?php echo implode("\n\t\t\t\t\t\t\t", $user_messaging)."\n" ?>
				<?php endif; ?>
                <tr><th class="active" colspan="2"><h3><?php echo $lang['User activity'] ?></h3></th></tr>
                <?php echo implode("\n\t\t\t\t\t\t\t", $user_activity)."\n" ?>
            </tbody>
        </table>
    </div>
</div>

<?php

	}
	else if ($section == 'personality')
	{
		if ($luna_user['is_admmod'])
		{
			if ($luna_user['g_id'] == FORUM_ADMIN || $luna_user['g_mod_rename_users'] == '1')
				$username_field = '<div class="form-group"><label class="col-sm-2 control-label">'.$lang['Username'].'</label><div class="col-sm-10"><input type="text" class="form-control" name="req_username" value="'.luna_htmlspecialchars($user['username']).'" maxlength="25" /></div></div>'."\n";
			else
				$username_field = '<div class="form-group"><label class="col-sm-2 control-label">'.$lang['Username'].'</label><div class="col-sm-10">'.luna_htmlspecialchars($user['username']).'</div></div>'."\n";

			$email_field = '<div class="form-group"><label class="col-sm-2 control-label">'.$lang['Email'].'</label><div class="col-sm-10"><div class="input-group"><input type="text" class="form-control" name="req_email" value="'.luna_htmlspecialchars($user['email']).'" maxlength="80" /><span class="input-group-btn"><a class="btn btn-primary" href="misc.php?email='.$id.'">'.$lang['Send email'].'</a></span></div></div></div>'."\n";
		}
		else
		{
			$username_field = '<div class="form-group"><label class="col-sm-2 control-label">'.$lang['Username'].'</label><div class="col-sm-10"><input class="form-control" type="text"  value="'.luna_htmlspecialchars($user['username']).'" disabled="disabled" /></div></div>'."\n";

			if ($luna_config['o_regs_verify'] == '1')
				$email_field = '<p>'.sprintf($lang['Email info'], luna_htmlspecialchars($user['email']).' &middot; <a href="profile.php?action=change_email&amp;id='.$id.'">'.$lang['Change email'].'</a>').'</p>'."\n";
			else
				$email_field = '<div class="form-group"><label class="col-sm-2 control-label">'.$lang['Email'].'</label><div class="col-sm-10"><input type="text" class="form-control" name="req_email" value="'.$user['email'].'" maxlength="80" /></div></div>'."\n";
		}

		if ($luna_user['g_set_title'] == '1')
			$title_field = '<div class="form-group"><label class="col-sm-2 control-label">'.$lang['Title'].'</label><div class="col-sm-10"><input class="form-control" type="text" class="form-control" name="title" value="'.luna_htmlspecialchars($user['title']).'" maxlength="50" /><span class="help-block">'.$lang['Leave blank'].'</div></div>'."\n";

		if ($luna_config['o_avatars'] == '0' && $luna_config['o_signatures'] == '0')
			message($lang['Bad request'], false, '404 Not Found');

		$avatar_field = '<a class="btn btn-primary" href="profile.php?action=upload_avatar&amp;id='.$id.'">'.$lang['Change avatar'].'</a>';

		$user_avatar = generate_avatar_markup($id);
		if ($user_avatar)
			$avatar_field .= ' <a class="btn btn-primary" href="profile.php?action=delete_avatar&amp;id='.$id.'">'.$lang['Delete avatar'].'</a>';
		else
			$avatar_field = '<a class="btn btn-primary" href="profile.php?action=upload_avatar&amp;id='.$id.'">'.$lang['Upload avatar'].'</a>';

		if ($user['signature'] != '')
			$signature_preview = '<p>'.$lang['Sig preview'].'</p>'."\n\t\t\t\t\t\t\t".'<div class="postsignature postmsg">'."\n\t\t\t\t\t\t\t\t".'<hr />'."\n\t\t\t\t\t\t\t\t".$parsed_signature."\n\t\t\t\t\t\t\t".'</div>'."\n";
		else
			$signature_preview = '<p>'.$lang['No sig'].'</p>'."\n";

		$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Section personality']);
		define('FORUM_ACTIVE_PAGE', 'profile');
		require FORUM_ROOT.'header.php';

		generate_profile_menu('personality');

?>
<div class="col-sm-10 col-profile">
    <h2 class="profile-h2"><?php echo $lang['Section personality'] ?></h2>
    <form id="profile2" class="form-horizontal" method="post" action="profile.php?section=personality&amp;id=<?php echo $id ?>">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $lang['Personal details legend'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="update" value="<?php echo $lang['Submit'] ?>" /></span></h3>
            </div>
            <div class="panel-body">
                <fieldset>
                    <input type="hidden" name="form_sent" value="1" />
                    <?php echo $username_field ?>
					<?php if ($luna_user['id'] == $id || $luna_user['g_id'] == FORUM_ADMIN || ($user['g_moderator'] == '0' && $luna_user['g_mod_change_passwords'] == '1')): ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Password'] ?></label>
                        <div class="col-sm-10">
                            <a class="btn btn-primary" href="profile.php?action=change_pass&amp;id=<?php echo $id ?>"><?php echo $lang['Change pass'] ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php echo $email_field ?>
                	<hr />
                    <input type="hidden" name="form_sent" value="1" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Realname'] ?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="form[realname]" value="<?php echo luna_htmlspecialchars($user['realname']) ?>" maxlength="40" />
                        </div>
                    </div>
                    <?php if (isset($title_field)): ?>
                        <?php echo $title_field ?>
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Location'] ?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="form[location]" value="<?php echo luna_htmlspecialchars($user['location']) ?>" maxlength="30" />
                        </div>
                    </div>
                    <hr />
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Website'] ?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="form[url]" value="<?php echo luna_htmlspecialchars($user['url']) ?>" maxlength="80" />
                        </div>
                    </div>
                    <hr />
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Jabber'] ?></label>
                        <div class="col-sm-10">
                            <input id="jabber" type="text" class="form-control" name="form[jabber]" value="<?php echo luna_htmlspecialchars($user['jabber']) ?>" maxlength="75" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['ICQ'] ?></label>
                        <div class="col-sm-10">
                            <input id="icq" type="text" class="form-control" name="form[icq]" value="<?php echo $user['icq'] ?>" maxlength="12" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['MSN'] ?></label>
                        <div class="col-sm-10">
                            <input id="msn" type="text" class="form-control" name="form[msn]" value="<?php echo luna_htmlspecialchars($user['msn']) ?>" maxlength="50" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['AOL IM'] ?></label>
                        <div class="col-sm-10">
                            <input id="aim" type="text" class="form-control" name="form[aim]" value="<?php echo luna_htmlspecialchars($user['aim']) ?>" maxlength="30" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Yahoo'] ?></label>
                        <div class="col-sm-10">
                            <input id="yahoo" type="text" class="form-control" name="form[yahoo]" value="<?php echo luna_htmlspecialchars($user['yahoo']) ?>" maxlength="30" />
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
<?php if ($luna_config['o_avatars'] == '1'): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
                <h3 class="panel-title"><?php echo $lang['Avatar legend'] ?></h3>
            </div>
            <div class="panel-body">
                <fieldset id="profileavatar">
<?php if ($user_avatar): ?>
					<div class="useravatar"><?php echo $user_avatar ?></div>
<?php endif; ?>
					<p><?php echo $lang['Avatar info'] ?></p>
                    <p><?php echo $avatar_field ?></p>
                </fieldset>
            </div>
        </div>
<?php endif; if ($luna_config['o_signatures'] == '1'): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $lang['Signature legend'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="update" value="<?php echo $lang['Submit'] ?>" /></span></h3>
            </div>
            <div class="panel-body">
                <fieldset>
                    <p><?php echo $lang['Signature info'] ?></p>
                    <label><?php printf($lang['Sig max size'], forum_number_format($luna_config['p_sig_length']), $luna_config['p_sig_lines']) ?><br />
                    <textarea class="form-control" name="signature" rows="4"><?php echo luna_htmlspecialchars($user['signature']) ?></textarea></label>
                    <ul class="bblinks">
                        <li><a class="label <?php echo ($luna_config['p_sig_bbcode'] == '1') ? "label-success" : "label-danger"; ?>" href="help.php#bbcode" onclick="window.open(this.href); return false;"><?php echo $lang['BBCode'] ?></a></li>
                        <li><a class="label <?php echo ($luna_config['p_sig_bbcode'] == '1' && $luna_config['p_sig_img_tag'] == '1') ? "label-success" : "label-danger"; ?>" href="help.php#img" onclick="window.open(this.href); return false;"><?php echo $lang['img tag'] ?></a></li>
                        <li><a class="label <?php echo ($luna_config['o_smilies_sig'] == '1') ? "label-success" : "label-danger"; ?>" href="help.php#smilies" onclick="window.open(this.href); return false;"><?php echo $lang['Smilies'] ?></a></li>
                    </ul>
                    <?php echo $signature_preview ?>
                </fieldset>
			</div>
        </div>
<?php endif; ?>
    </form>
<?php

	}
	else if ($section == 'settings')
	{

		$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Section settings']);
		define('FORUM_ACTIVE_PAGE', 'profile');
		require FORUM_ROOT.'header.php';

		generate_profile_menu('settings');

?>
<div class="col-sm-10 col-profile">
    <h2 class="profile-h2"><?php echo $lang['Section settings'] ?></h2>
    <form id="profile3" class="form-horizontal" method="post" action="profile.php?section=settings&amp;id=<?php echo $id ?>" onsubmit="return process_form(this)">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $lang['Settings legend'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="update" value="<?php echo $lang['Submit'] ?>" /></span></h3>
            </div>
            <div class="panel-body">
                <fieldset>
                    <input type="hidden" name="form_sent" value="1" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Time zone'] ?></label>
                        <div class="col-sm-10">
                            <select class="form-control" name="form[timezone]">
                                <option value="-12"<?php if ($user['timezone'] == -12) echo ' selected="selected"' ?>><?php echo $lang['UTC-12:00'] ?></option>
                                <option value="-11"<?php if ($user['timezone'] == -11) echo ' selected="selected"' ?>><?php echo $lang['UTC-11:00'] ?></option>
                                <option value="-10"<?php if ($user['timezone'] == -10) echo ' selected="selected"' ?>><?php echo $lang['UTC-10:00'] ?></option>
                                <option value="-9.5"<?php if ($user['timezone'] == -9.5) echo ' selected="selected"' ?>><?php echo $lang['UTC-09:30'] ?></option>
                                <option value="-9"<?php if ($user['timezone'] == -9) echo ' selected="selected"' ?>><?php echo $lang['UTC-09:00'] ?></option>
                                <option value="-8.5"<?php if ($user['timezone'] == -8.5) echo ' selected="selected"' ?>><?php echo $lang['UTC-08:30'] ?></option>
                                <option value="-8"<?php if ($user['timezone'] == -8) echo ' selected="selected"' ?>><?php echo $lang['UTC-08:00'] ?></option>
                                <option value="-7"<?php if ($user['timezone'] == -7) echo ' selected="selected"' ?>><?php echo $lang['UTC-07:00'] ?></option>
                                <option value="-6"<?php if ($user['timezone'] == -6) echo ' selected="selected"' ?>><?php echo $lang['UTC-06:00'] ?></option>
                                <option value="-5"<?php if ($user['timezone'] == -5) echo ' selected="selected"' ?>><?php echo $lang['UTC-05:00'] ?></option>
                                <option value="-4"<?php if ($user['timezone'] == -4) echo ' selected="selected"' ?>><?php echo $lang['UTC-04:00'] ?></option>
                                <option value="-3.5"<?php if ($user['timezone'] == -3.5) echo ' selected="selected"' ?>><?php echo $lang['UTC-03:30'] ?></option>
                                <option value="-3"<?php if ($user['timezone'] == -3) echo ' selected="selected"' ?>><?php echo $lang['UTC-03:00'] ?></option>
                                <option value="-2"<?php if ($user['timezone'] == -2) echo ' selected="selected"' ?>><?php echo $lang['UTC-02:00'] ?></option>
                                <option value="-1"<?php if ($user['timezone'] == -1) echo ' selected="selected"' ?>><?php echo $lang['UTC-01:00'] ?></option>
                                <option value="0"<?php if ($user['timezone'] == 0) echo ' selected="selected"' ?>><?php echo $lang['UTC'] ?></option>
                                <option value="1"<?php if ($user['timezone'] == 1) echo ' selected="selected"' ?>><?php echo $lang['UTC+01:00'] ?></option>
                                <option value="2"<?php if ($user['timezone'] == 2) echo ' selected="selected"' ?>><?php echo $lang['UTC+02:00'] ?></option>
                                <option value="3"<?php if ($user['timezone'] == 3) echo ' selected="selected"' ?>><?php echo $lang['UTC+03:00'] ?></option>
                                <option value="3.5"<?php if ($user['timezone'] == 3.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+03:30'] ?></option>
                                <option value="4"<?php if ($user['timezone'] == 4) echo ' selected="selected"' ?>><?php echo $lang['UTC+04:00'] ?></option>
                                <option value="4.5"<?php if ($user['timezone'] == 4.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+04:30'] ?></option>
                                <option value="5"<?php if ($user['timezone'] == 5) echo ' selected="selected"' ?>><?php echo $lang['UTC+05:00'] ?></option>
                                <option value="5.5"<?php if ($user['timezone'] == 5.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+05:30'] ?></option>
                                <option value="5.75"<?php if ($user['timezone'] == 5.75) echo ' selected="selected"' ?>><?php echo $lang['UTC+05:45'] ?></option>
                                <option value="6"<?php if ($user['timezone'] == 6) echo ' selected="selected"' ?>><?php echo $lang['UTC+06:00'] ?></option>
                                <option value="6.5"<?php if ($user['timezone'] == 6.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+06:30'] ?></option>
                                <option value="7"<?php if ($user['timezone'] == 7) echo ' selected="selected"' ?>><?php echo $lang['UTC+07:00'] ?></option>
                                <option value="8"<?php if ($user['timezone'] == 8) echo ' selected="selected"' ?>><?php echo $lang['UTC+08:00'] ?></option>
                                <option value="8.75"<?php if ($user['timezone'] == 8.75) echo ' selected="selected"' ?>><?php echo $lang['UTC+08:45'] ?></option>
                                <option value="9"<?php if ($user['timezone'] == 9) echo ' selected="selected"' ?>><?php echo $lang['UTC+09:00'] ?></option>
                                <option value="9.5"<?php if ($user['timezone'] == 9.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+09:30'] ?></option>
                                <option value="10"<?php if ($user['timezone'] == 10) echo ' selected="selected"' ?>><?php echo $lang['UTC+10:00'] ?></option>
                                <option value="10.5"<?php if ($user['timezone'] == 10.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+10:30'] ?></option>
                                <option value="11"<?php if ($user['timezone'] == 11) echo ' selected="selected"' ?>><?php echo $lang['UTC+11:00'] ?></option>
                                <option value="11.5"<?php if ($user['timezone'] == 11.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+11:30'] ?></option>
                                <option value="12"<?php if ($user['timezone'] == 12) echo ' selected="selected"' ?>><?php echo $lang['UTC+12:00'] ?></option>
                                <option value="12.75"<?php if ($user['timezone'] == 12.75) echo ' selected="selected"' ?>><?php echo $lang['UTC+12:45'] ?></option>
                                <option value="13"<?php if ($user['timezone'] == 13) echo ' selected="selected"' ?>><?php echo $lang['UTC+13:00'] ?></option>
                                <option value="14"<?php if ($user['timezone'] == 14) echo ' selected="selected"' ?>><?php echo $lang['UTC+14:00'] ?></option>
                            </select>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="form[dst]" value="1"<?php if ($user['dst'] == '1') echo ' checked="checked"' ?> />
                                    <?php echo $lang['DST'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Time format'] ?></label>
                        <div class="col-sm-10">
                            <select class="form-control" name="form[time_format]">
<?php
                        foreach (array_unique($forum_time_formats) as $key => $time_format)
                        {
                            echo "\t\t\t\t\t\t\t\t".'<option value="'.$key.'"';
                            if ($user['time_format'] == $key)
                                echo ' selected="selected"';
                            echo '>'. format_time(time(), false, null, $time_format, true, true);
                            if ($key == 0)
                                echo ' ('.$lang['Default'].')';
                            echo "</option>\n";
                        }
?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Date format'] ?></label>
                        <div class="col-sm-10">
                            <select class="form-control" name="form[date_format]">
<?php
                        foreach (array_unique($forum_date_formats) as $key => $date_format)
                        {
                            echo "\t\t\t\t\t\t\t\t".'<option value="'.$key.'"';
                            if ($user['date_format'] == $key)
                                echo ' selected="selected"';
                            echo '>'. format_time(time(), true, $date_format, null, false, true);
                            if ($key == 0)
                                echo ' ('.$lang['Default'].')';
                            echo "</option>\n";
                        }
?>
                            </select>
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
                    		<select class="form-control" name="form[language]">
<?php
			foreach ($languages as $temp)
			{
				if ($user['language'] == $temp)
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
		$styles = forum_list_styles();

		// Only display the style selection box if there's more than one style available
		if (count($styles) == 1)
			echo "\t\t\t".'<div><input type="hidden" name="form[style]" value="'.$styles[0].'" /></div>'."\n";
		else if (count($styles) > 1)
		{
?>
					<hr />
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Style'] ?></label>
                        <div class="col-sm-10">
                            <select class="form-control" name="form[style]">
<?php
			foreach ($styles as $temp)
			{
				if ($user['style'] == $temp)
					echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'" selected="selected">'.str_replace('_', ' ', $temp).'</option>'."\n";
				else
					echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'">'.str_replace('_', ' ', $temp).'</option>'."\n";
			}
?>
							</select>
                        </div>
                    </div>
<?php
		}
		if ($luna_user['is_admmod']) {
			$backstage_styles = backstage_list_styles();

			// Only display the style selection box if there's more than one style available
			if (count($backstage_styles) == 1)
				echo "\t\t\t".'<div><input type="hidden" name="form[backstage_style]" value="'.$backstage_styles[0].'" /></div>'."\n";
			else if (count($backstage_styles) > 1)
			{
?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Backstage style'] ?></label>
                        <div class="col-sm-10">
                            <select class="form-control" name="form[backstage_style]">
<?php
				foreach ($backstage_styles as $temp)
				{
					if ($user['backstage_style'] == $temp)
						echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'" selected="selected">'.str_replace('_', ' ', $temp).'</option>'."\n";
					else
						echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'">'.str_replace('_', ' ', $temp).'</option>'."\n";
				}
?>
							</select>
                        </div>
                    </div>
<?php
			}
		}
		if ($luna_config['o_smilies'] == '1' || $luna_config['o_smilies_sig'] == '1' || $luna_config['o_signatures'] == '1' || $luna_config['o_avatars'] == '1' || ($luna_config['p_message_bbcode'] == '1' && $luna_config['p_message_img_tag'] == '1')): ?>
                    <hr />
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Post display'] ?></label>
                        <div class="col-sm-10">
<?php if ($luna_config['o_smilies'] == '1' || $luna_config['o_smilies_sig'] == '1'): ?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="form[show_smilies]" value="1"<?php if ($user['show_smilies'] == '1') echo ' checked="checked"' ?> />
                                    <?php echo $lang['Show smilies'] ?>
                                </label>
                            </div>
<?php endif; if ($luna_config['o_signatures'] == '1'): ?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="form[show_sig]" value="1"<?php if ($user['show_sig'] == '1') echo ' checked="checked"' ?> />
                                    <?php echo $lang['Show sigs'] ?>

                                </label>
                            </div>
<?php endif; if ($luna_config['o_avatars'] == '1'): ?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="form[show_avatars]" value="1"<?php if ($user['show_avatars'] == '1') echo ' checked="checked"' ?> />
                                    <?php echo $lang['Show avatars'] ?>
                                </label>
                            </div>
<?php endif; if ($luna_config['p_message_bbcode'] == '1' && $luna_config['p_message_img_tag'] == '1'): ?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="form[show_img]" value="1"<?php if ($user['show_img'] == '1') echo ' checked="checked"' ?> />
                                    <?php echo $lang['Show images'] ?>
                                </label>
                            </div>
<?php endif; if ($luna_config['o_signatures'] == '1' && $luna_config['p_sig_bbcode'] == '1' && $luna_config['p_sig_img_tag'] == '1'): ?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="form[show_smilies]" value="1"<?php if ($user['show_img_sig'] == '1') echo ' checked="checked"' ?> />
                                    <?php echo $lang['Show images sigs'] ?>
                                </label>
                            </div>
<?php endif; ?>
                        </div>
                    </div>
<?php endif; ?>
                    <hr />
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Topics per page'] ?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="form[disp_topics]" value="<?php echo $user['disp_topics'] ?>" maxlength="3" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Posts per page'] ?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="form[disp_posts]" value="<?php echo $user['disp_posts'] ?>" maxlength="3" />
                        </div>
                    </div>
                    <hr  />
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Email setting info'] ?></label>
                        <div class="col-sm-10">
							<div class="radio">
                                <label>
                                    <input type="radio" name="form[email_setting]" value="0"<?php if ($user['email_setting'] == '0') echo ' checked="checked"' ?> />
                                    <?php echo $lang['Email setting 1'] ?>
                                </label>
							</div>
							<div class="radio">
                                <label>
                                    <input type="radio" name="form[email_setting]" value="1"<?php if ($user['email_setting'] == '1') echo ' checked="checked"' ?> />
                                    <?php echo $lang['Email setting 2'] ?>
                                </label>
							</div>
							<div class="radio">
                                <label>
                                    <input type="radio" name="form[email_setting]" value="2"<?php if ($user['email_setting'] == '2') echo ' checked="checked"' ?> />
                                    <?php echo $lang['Email setting 3'] ?>
                                </label>
							</div>
                        </div>
                    </div>
<?php if ($luna_config['o_forum_subscriptions'] == '1' || $luna_config['o_topic_subscriptions'] == '1'): ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Subscriptions head'] ?></label>
                        <div class="col-sm-10">
							<div class="checkbox">
                                <label>
                                    <input type="checkbox" name="form[notify_with_post]" value="1"<?php if ($user['notify_with_post'] == '1') echo ' checked="checked"' ?> />
                                    <?php echo $lang['Notify full'] ?>
                                </label>
							</div>
<?php if ($luna_config['o_topic_subscriptions'] == '1'): ?>
							<div class="checkbox">
                                <label>
                                    <input type="checkbox" name="form[auto_notify]" value="1"<?php if ($user['auto_notify'] == '1') echo ' checked="checked"' ?> />
                                    <?php echo $lang['Auto notify full'] ?>
                                </label>
							</div>
<?php endif; ?>
                        </div>
                    </div>
<?php endif; ?>
                </fieldset>
            </div>
        </div>
    </form>
<?php

	}
	else if ($section == 'admin')
	{
		if (!$luna_user['is_admmod'] || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '0'))
			message($lang['Bad request'], false, '403 Forbidden');

		$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Section admin']);
		define('FORUM_ACTIVE_PAGE', 'profile');
		require FORUM_ROOT.'header.php';

		generate_profile_menu('admin');

?>
<div class="col-sm-10 col-profile">
    <h2 class="profile-h2"><?php echo $lang['Section admin'] ?></h2>
    <form id="profile7" method="post" action="profile.php?section=admin&amp;id=<?php echo $id ?>">
<?php

		if ($luna_user['g_moderator'] == '1')
		{

?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $lang['Delete ban legend'] ?></h3>
            </div>
            <div class="panel-body">
                <input type="hidden" name="form_sent" value="1" />
                <fieldset>
                    <p><input class="btn btn-primary" type="submit" name="ban" value="<?php echo $lang['Ban user'] ?>" /></p>
                </fieldset>
            </div>
        </div>
<?php

		}
		else
		{
			if ($luna_user['id'] != $id)
			{

?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $lang['Group membership legend'] ?></h3>
            </div>
            <div class="panel-body">
                <fieldset>
                    <select id="group_id" class="form-control" name="group_id">
<?php

				$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id!='.FORUM_GUEST.' ORDER BY g_title') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

				while ($cur_group = $db->fetch_assoc($result))
				{
					if ($cur_group['g_id'] == $user['g_id'] || ($cur_group['g_id'] == $luna_config['o_default_user_group'] && $user['g_id'] == ''))
						echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
					else
						echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
				}

?>
                    </select>
                    <input type="submit" class="btn btn-primary" name="update_group_membership" value="<?php echo $lang['Save'] ?>" />
                </fieldset>
            </div>
        </div>
<?php

			}

?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $lang['Delete ban legend'] ?></h3>
            </div>
            <div class="panel-body">
                <fieldset>
                    <input type="submit" class="btn btn-danger" name="delete_user" value="<?php echo $lang['Delete user'] ?>" /> <input type="submit" class="btn btn-danger" name="ban" value="<?php echo $lang['Ban user'] ?>" />
                </fieldset>
            </div>
        </div>
<?php

			if ($user['g_moderator'] == '1' || $user['g_id'] == FORUM_ADMIN)
			{

?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $lang['Set mods legend'] ?><span class="pull-right"><input type="submit" class="btn btn-primary" name="update_forums" value="<?php echo $lang['Update forums'] ?>" /></span></h3>
            </div>
            <div class="panel-body">
                <fieldset>
                    <p><?php echo $lang['Moderator in info'] ?></p>
<?php

				$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.moderators FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id WHERE f.redirect_url IS NULL ORDER BY c.disp_position, c.id, f.disp_position') or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

				$cur_category = 0;
				while ($cur_forum = $db->fetch_assoc($result))
				{
					if ($cur_forum['cid'] != $cur_category) // A new category since last iteration?
					{
						if ($cur_category)
							echo "\n\t\t\t\t\t\t\t\t".'</div>';

						if ($cur_category != 0)
							echo "\n\t\t\t\t\t\t\t".'</div>'."\n";

						echo "\t\t\t\t\t\t\t".'<div>'."\n\t\t\t\t\t\t\t\t".'<br /><strong>'.luna_htmlspecialchars($cur_forum['cat_name']).'</strong>'."\n\t\t\t\t\t\t\t\t".'<div>';
						$cur_category = $cur_forum['cid'];
					}

					$moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

					echo "\n\t\t\t\t\t\t\t\t\t".'<input type="checkbox" name="moderator_in['.$cur_forum['fid'].']" value="1"'.((in_array($id, $moderators)) ? ' checked="checked"' : '').' /> '.luna_htmlspecialchars($cur_forum['forum_name']).'<br />'."\n";
				}

?>
                </fieldset>
            </div>
        </div>
<?php

			}
		}

		if ($luna_user['g_id'] == FORUM_ADMIN)
			$posts_field = '<div class="form-group"><label class="col-sm-2 control-label">'.$lang['Posts table'].'</label><div class="col-sm-10"><input type="text" class="form-control" name="num_posts" value="'.$user['num_posts'].'" maxlength="8" /></div></div>';
		else
			$posts_field = '';


?>
    </form>
    <form id="profile1" class="form-horizontal" method="post" action="profile.php?section=admin&amp;id=<?php echo $id ?>" onsubmit="return process_form(this)">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $lang['User tools'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="update" value="<?php echo $lang['Submit'] ?>" /></span></h3>
            </div>
            <div class="panel-body">
                <input type="hidden" name="form_sent" value="1" />
                <fieldset>
                    <?php echo $posts_field ?>
					<?php if ($luna_user['is_admmod']): ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $lang['Admin note'] ?></label>
                        <div class="col-sm-10">
                            <input id="admin_note" type="text" class="form-control" name="admin_note" value="<?php echo luna_htmlspecialchars($user['admin_note']) ?>" maxlength="30" />
                        </div>
                    </div>
                    <?php endif; ?>
                </fieldset>
            </div>
        </div>
    </form>
<?php

	}
	else
		message($lang['Bad request'], false, '404 Not Found');

	require FORUM_ROOT.'footer.php';
}
