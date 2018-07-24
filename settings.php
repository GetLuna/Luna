<?php

/*
 * Copyright (C) 2013-2018 Luna
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', dirname(__FILE__).'/');
require LUNA_ROOT.'include/common.php';
require LUNA_ROOT.'include/parser.php';
require LUNA_ROOT.'include/utf8/substr_replace.php';
require LUNA_ROOT.'include/utf8/ucwords.php'; // utf8_ucwords needs utf8_substr_replace
require LUNA_ROOT.'include/utf8/strcasecmp.php';

// Load the me functions script
require LUNA_ROOT.'include/me_functions.php';
require LUNA_ROOT.'include/email.php';
require LUNA_ROOT.'include/class/user.class.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : $luna_user['id'];

if ($id < 2)
	message(__('Bad request. The link you followed is incorrecy not allowed to hang around here.', 'luna'), false, '404 Not Found');

if ($action != 'change_pass' || !isset($_GET['key']))
{
	if ($luna_user['g_read_board'] == '0')
		message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');
	else if ($luna_user['g_view_users'] == '0' && ($luna_user['is_guest'] || $luna_user['id'] != $id))
		message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');
}

if ($action == 'change_pass') {
	if (isset($_GET['key'])) {
        $new_key_setting = true;
		// If the user is already logged in we shouldn't be here :)
		if (!$luna_user['is_guest']) {
			header('Location: index.php');
			exit;
		}

		$key = $_GET['key'];

		$result = $db->query('SELECT * FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch new password', __FILE__, __LINE__, $db->error());
		$cur_user = $db->fetch_assoc($result);

		if ($key == '' || $key != $cur_user['activate_key'])
			message(__('The specified password activation key was incorrect or has expired. Please re-request a new password. If that fails, contact the forum administrator at', 'luna').' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.');
		else {
            $salt = random_pass(8);
            $password_hash = luna_sha512($cur_user['activate_string'], $salt);
            
			$db->query('UPDATE '.$db->prefix.'users SET password=\''.$db->escape($password_hash).'\', activate_string=NULL, activate_key=NULL, salt=\''.$salt.'\' WHERE id='.$id) or error('Unable to update password', __FILE__, __LINE__, $db->error());

			message(__('Your password has been updated. You can now login with your new password.', 'luna'), true);
		}
	}

	// Make sure we are allowed to change this user's password
	if ($luna_user['id'] != $id) {
		if (!$luna_user['is_admmod']) // A regular user trying to change another user's password?
			message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');
		elseif ($luna_user['g_moderator'] == '1') { // A moderator trying to change a user's password?
			$result = $db->query('SELECT u.group_id, g.g_moderator FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'groups AS g ON (g.g_id=u.group_id) WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
			if (!$db->num_rows($result))
				message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

			list($group_id, $is_moderator) = $db->fetch_row($result);

			if ($luna_user['g_mod_edit_users'] == '0' || $luna_user['g_mod_change_passwords'] == '0' || $group_id == LUNA_ADMIN || $is_moderator == '1')
				message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');
		}
	}

	if (isset($_POST['form_sent'])) {
		// Make sure they got here from the site
		confirm_referrer('settings.php');

		$old_password = isset($_POST['req_old_password']) ? luna_trim($_POST['req_old_password']) : '';
		$new_password1 = luna_trim($_POST['req_new_password1']);
		$new_password2 = luna_trim($_POST['req_new_password2']);

		if ($new_password1 != $new_password2)
			message(__('Passwords do not match.', 'luna'));
		if (luna_strlen($new_password1) < 6)
			message(__('Passwords must be at least 6 characters long. Please choose another (longer) password.', 'luna'));

		$result = $db->query('SELECT * FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch password', __FILE__, __LINE__, $db->error());
		$cur_user = $db->fetch_assoc($result);

		$authorized = false;

		if (!empty($cur_user['password'])) {
			$old_password_hash = luna_sha512($old_password, $cur_user['salt']);

			if ($cur_user['password'] == $old_password_hash || $luna_user['is_admmod'])
				$authorized = true;
		}

		if (!$authorized)
			message(__('Wrong old password.', 'luna'));

        $new_salt = random_pass(8);
        
		$new_password_hash = luna_sha512($new_password1, $new_salt);

		$db->query('UPDATE '.$db->prefix.'users SET password=\''.$new_password_hash.'\', salt=\''.$new_salt.'\' WHERE id='.$id) or error('Unable to update password', __FILE__, __LINE__, $db->error());

		if ($luna_user['id'] == $id)
			luna_setcookie($luna_user['id'], $new_password_hash, time() + $luna_config['o_timeout_visit']);

		redirect('settings.php?id='.$id);
	}
}

if (($luna_user['id'] != $id &&																	// If we aren't the user (i.e. editing your own profile)
	(!$luna_user['is_admmod'] ||																	// and we are not an admin or mod
	($luna_user['g_id'] != LUNA_ADMIN &&														// or we aren't an admin and ...
	($luna_user['g_mod_edit_users'] == '0' ||													// mods aren't allowed to edit users
	$group_id == LUNA_ADMIN ||																	// or the user is an admin
	$is_moderator))))																			// or the user is another mod
	|| $id == '1') {																				// or the ID is 1, and thus a guest
	message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');
}

if (isset($_POST['update_group_membership'])) {
	if ($luna_user['g_id'] > LUNA_ADMIN)
		message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');

	confirm_referrer('settings.php');

	$new_group_id = intval($_POST['group_id']);

	$result = $db->query('SELECT group_id FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch user group', __FILE__, __LINE__, $db->error());
	$old_group_id = $db->result($result);

	$db->query('UPDATE '.$db->prefix.'users SET group_id='.$new_group_id.' WHERE id='.$id) or error('Unable to change user group', __FILE__, __LINE__, $db->error());

	// Regenerate the users info cache
	if (!defined('LUNA_CACHE_FUNCTIONS_LOADED'))
		require LUNA_ROOT.'include/cache.php';

	generate_users_info_cache();

	if ($old_group_id == LUNA_ADMIN || $new_group_id == LUNA_ADMIN)
		generate_admins_cache();

	$result = $db->query('SELECT g_moderator FROM '.$db->prefix.'groups WHERE g_id='.$new_group_id) or error('Unable to fetch group', __FILE__, __LINE__, $db->error());
	$new_group_mod = $db->result($result);

	// If the user was a moderator or an administrator, we remove him/her from the moderator list in all forums as well
	if ($new_group_id != LUNA_ADMIN && $new_group_mod != '1') {
		$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

		while ($cur_forum = $db->fetch_assoc($result)) {
			$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

			if (in_array($id, $cur_moderators)) {
				$username = array_search($id, $cur_moderators);
				unset($cur_moderators[$username]);
				$cur_moderators = (!empty($cur_moderators)) ? '\''.$db->escape(serialize($cur_moderators)).'\'' : 'NULL';

				$db->query('UPDATE '.$db->prefix.'forums SET moderators='.$cur_moderators.' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
			}
		}
	}

	redirect('settings.php?id='.$id);
} elseif (isset($_POST['ban'])) {
	if ($luna_user['g_id'] != LUNA_ADMIN && ($luna_user['g_moderator'] != '1' || $luna_user['g_mod_ban_users'] == '0'))
		message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');

	confirm_referrer('settings.php');

	// Get the username of the user we are banning
	$result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch username', __FILE__, __LINE__, $db->error());
	$username = $db->result($result);

	// Check whether user is already banned
	$result = $db->query('SELECT id FROM '.$db->prefix.'bans WHERE username = \''.$db->escape($username).'\' ORDER BY expire IS NULL DESC, expire DESC LIMIT 1') or error('Unable to fetch ban ID', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		$ban_id = $db->result($result);
		redirect('backstage/bans.php?edit_ban='.$ban_id.'&amp;exists');
	} else
		redirect('backstage/bans.php?add_ban='.$id);
} elseif (isset($_POST['delete_user']) || isset($_POST['delete_user_comply'])) {
	if ($luna_user['g_id'] > LUNA_ADMIN)
		message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');

	// Get the username and group of the user we are deleting
	$result = $db->query('SELECT group_id, username FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	list($group_id, $username) = $db->fetch_row($result);

	if ($group_id == LUNA_ADMIN)
		message(__('Administrators cannot be deleted. In order to delete this user, you must first move him/her to a different user group.', 'luna'));

	if (isset($_POST['delete_user_comply'])) {
		// If the user is a moderator or an administrator, we remove him/her from the moderator list in all forums as well
		$result = $db->query('SELECT g_moderator FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to fetch group', __FILE__, __LINE__, $db->error());
		$group_mod = $db->result($result);

		if ($group_id == LUNA_ADMIN || $group_mod == '1') {
			$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

			while ($cur_forum = $db->fetch_assoc($result)) {
				$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

				if (in_array($id, $cur_moderators)) {
					unset($cur_moderators[$username]);
					$cur_moderators = (!empty($cur_moderators)) ? '\''.$db->escape(serialize($cur_moderators)).'\'' : 'NULL';

					$db->query('UPDATE '.$db->prefix.'forums SET moderators='.$cur_moderators.' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
				}
			}
		}

		// Delete any subscriptions
		$db->query('DELETE FROM '.$db->prefix.'thread_subscriptions WHERE user_id='.$id) or error('Unable to delete thread subscriptions', __FILE__, __LINE__, $db->error());
		$db->query('DELETE FROM '.$db->prefix.'forum_subscriptions WHERE user_id='.$id) or error('Unable to delete forum subscriptions', __FILE__, __LINE__, $db->error());

		// Remove him/her from the online list (if they happen to be logged in)
		$db->query('DELETE FROM '.$db->prefix.'online WHERE user_id='.$id) or error('Unable to remove user from online list', __FILE__, __LINE__, $db->error());

		// Should we delete all comments made by this user?
		if (isset($_POST['delete_comments'])) {
			require LUNA_ROOT.'include/search_idx.php';
			@set_time_limit(0);

			// Find all comments made by this user
			$result = $db->query('SELECT p.id, p.thread_id, t.forum_id FROM '.$db->prefix.'comments AS p INNER JOIN '.$db->prefix.'threads AS t ON t.id=p.thread_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id WHERE p.commenter_id='.$id) or error('Unable to fetch comments', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result)) {
				while ($cur_comment = $db->fetch_assoc($result)) {
					// Determine whether this comment is the "thread comment" or not
					$result2 = $db->query('SELECT id FROM '.$db->prefix.'comments WHERE thread_id='.$cur_comment['thread_id'].' ORDER BY commented LIMIT 1') or error('Unable to fetch comment info', __FILE__, __LINE__, $db->error());

					if ($db->result($result2) == $cur_comment['id'])
						delete_thread($cur_comment['thread_id']);
					else
						delete_comment($cur_comment['id'], $cur_comment['thread_id'], $id);

					update_forum($cur_comment['forum_id']);
				}
			}
		} else
			// Set all his/her comments to guest
			$db->query('UPDATE '.$db->prefix.'comments SET commenter_id=1 WHERE commenter_id='.$id) or error('Unable to update comments', __FILE__, __LINE__, $db->error());

		// Delete the user
		$db->query('DELETE FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to delete user', __FILE__, __LINE__, $db->error());

		// Delete user avatar
		delete_avatar($id);

		// Regenerate the users info cache
		if (!defined('LUNA_CACHE_FUNCTIONS_LOADED'))
			require LUNA_ROOT.'include/cache.php';

		generate_users_info_cache();

		if ($group_id == LUNA_ADMIN)
			generate_admins_cache();

		redirect('index.php');
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Profile', 'luna'), __('Confirm delete user', 'luna'));
	define('LUNA_ACTIVE_PAGE', 'profile');
    include LUNA_ROOT.'header.php';
	require load_page('header.php');

	require load_page('me-delete.php');
} elseif (isset($_POST['update_forums'])) {
	if ($luna_user['g_id'] > LUNA_ADMIN)
		message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');

	confirm_referrer('settings.php');

	// Get the username of the user we are processing
	$result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	$username = $db->result($result);

	$moderator_in = (isset($_POST['moderator_in'])) ? array_keys($_POST['moderator_in']) : array();

	// Loop through all forums
	$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

	while ($cur_forum = $db->fetch_assoc($result)) {
		$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();
		// If the user should have moderator access (and he/she doesn't already have it)
		if (in_array($cur_forum['id'], $moderator_in) && !in_array($id, $cur_moderators)) {
			$cur_moderators[$username] = $id;
			uksort($cur_moderators, 'utf8_strcasecmp');

			$db->query('UPDATE '.$db->prefix.'forums SET moderators=\''.$db->escape(serialize($cur_moderators)).'\' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
		}
		// If the user shouldn't have moderator access (and he/she already has it)
		elseif (!in_array($cur_forum['id'], $moderator_in) && in_array($id, $cur_moderators)) {
			unset($cur_moderators[$username]);
			$cur_moderators = (!empty($cur_moderators)) ? '\''.$db->escape(serialize($cur_moderators)).'\'' : 'NULL';

			$db->query('UPDATE '.$db->prefix.'forums SET moderators='.$cur_moderators.' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
		}
	}

	redirect('settings.php?&amp;id='.$id);
} elseif ($action == 'change_email') {
	// Make sure we are allowed to change this user's email
	if ($luna_user['id'] != $id) {
		if (!$luna_user['is_admmod']) // A regular user trying to change another user's email?
			message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');
		elseif ($luna_user['g_moderator'] == '1') { // A moderator trying to change a user's email?
			$result = $db->query('SELECT u.group_id, g.g_moderator FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'groups AS g ON (g.g_id=u.group_id) WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
			if (!$db->num_rows($result))
				message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

			list($group_id, $is_moderator) = $db->fetch_row($result);

			if ($luna_user['g_mod_edit_users'] == '0' || $group_id == LUNA_ADMIN || $is_moderator == '1')
				message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');
		}
	}

	if (isset($_GET['key'])) {
		$key = $_GET['key'];

		$result = $db->query('SELECT activate_string, activate_key FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch activation data', __FILE__, __LINE__, $db->error());
		list($new_email, $new_email_key) = $db->fetch_row($result);

		if ($key == '' || $key != $new_email_key)
			message(__('The specified email activation key was incorrect or has expired. Please re-request change of email address. If that fails, contact the forum administrator at', 'luna').' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.');
		else {
			$db->query('UPDATE '.$db->prefix.'users SET email=activate_string, activate_string=NULL, activate_key=NULL WHERE id='.$id) or error('Unable to update email address', __FILE__, __LINE__, $db->error());

			message(__('Your email address has been updated.', 'luna'), true);
		}
	} elseif (isset($_POST['form_sent'])) {
		if (luna_hash($_POST['req_password']) !== $luna_user['password'])
			message(__('Wrong old password.', 'luna'));

		// Make sure they got here from the site
		confirm_referrer('settings.php');

		// Validate the email address
		$new_email = strtolower(luna_trim($_POST['req_new_email']));
		if (!is_valid_email($new_email))
			message(__('The email address you entered is invalid.', 'luna'));

		// Check if it's a banned email address
		if (is_banned_email($new_email)) {
			if ($luna_config['o_allow_banned_email'] == '0')
				message(__('The email address you entered is banned in this forum. Please choose another email address.', 'luna'));
			elseif ($luna_config['o_mailing_list'] != '') {
				// Load the "banned email change" template
				$mail_tpl = trim(__('Subject: Alert - Banned email detected

User "<username>" changed to banned email address: <email>

User profile: <profile_url>

--
<board_mailer> Mailer
(Do not reply to this message)', 'luna'));

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
		if ($db->num_rows($result)) {
			if ($luna_config['o_allow_dupe_email'] == '0')
				message(__('Someone else is already registered with that email address. Please choose another email address.', 'luna'));
			elseif ($luna_config['o_mailing_list'] != '') {
				while ($cur_dupe = $db->fetch_assoc($result))
					$dupe_list[] = $cur_dupe['username'];

				// Load the "dupe email change" template
				$mail_tpl = trim(__('Subject: Alert - Duplicate email detected

User "<username>" changed to an email address that also belongs to: <dupe_list>

User profile: <profile_url>

--
<board_mailer> Mailer
(Do not reply to this message)', 'luna'));

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
		$mail_tpl = trim(__('Subject: Change email address requested

Hello <username>,

You have requested to have a new email address assigned to your account in the discussion forum at <base_url>. If you did not request this or if you do not want to change your email address you should just ignore this message. Only if you visit the activation page below will your email address be changed. In order for the activation page to work, you must be logged in to the forum.

To change your email address, please visit the following page:
<activation_url>

--
<board_mailer> Mailer
(Do not reply to this message)', 'luna'));

		// The first row contains the subject
		$first_crlf = strpos($mail_tpl, "\n");
		$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
		$mail_message = trim(substr($mail_tpl, $first_crlf));

		$mail_message = str_replace('<username>', $luna_user['username'], $mail_message);
		$mail_message = str_replace('<base_url>', get_base_url(), $mail_message);
		$mail_message = str_replace('<activation_url>', get_base_url().'/settings.php?action=change_email&id='.$id.'&key='.$new_email_key, $mail_message);
		$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

		luna_mail($new_email, $mail_subject, $mail_message);

		message(__('An email has been sent to the specified address with instructions on how to activate the new email address. If it doesn\'t arrive you can contact the forum administrator at', 'luna').' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.', true);
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Profile', 'luna'), __('Change email address', 'luna'));
	$required_fields = array('req_new_email' => __('New email', 'luna'), 'req_password' => __('Password', 'luna'));
	$focus_element = array('change_email', 'req_new_email');
	define('LUNA_ACTIVE_PAGE', 'me');
    include LUNA_ROOT.'header.php';
	require load_page('header.php');

	require get_view_path('me-change_email.tpl.php');
} elseif ($action == 'upload_avatar' || $action == 'upload_avatar2') {
	if ($luna_config['o_avatars'] == '0')
		message(__('The administrator has disabled avatar support.', 'luna'));

	if ($luna_user['id'] != $id && !$luna_user['is_admmod'])
		message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');

	if (isset($_POST['form_sent'])) {
		if (!isset($_FILES['req_file']))
			message(__('You did not select a file for upload.', 'luna'));

		// Make sure they got here from the site
		confirm_referrer('settings.php');

		$uploaded_file = $_FILES['req_file'];

		// Make sure the upload went smooth
		if (isset($uploaded_file['error'])) {
			switch ($uploaded_file['error']) {
				case 1: // UPLOAD_ERR_INI_SIZE
				case 2: // UPLOAD_ERR_FORM_SIZE
					message(__('The selected file was too large to upload. The server didn\'t allow the upload.', 'luna'));
					break;

				case 3: // UPLOAD_ERR_PARTIAL
					message(__('The selected file was only partially uploaded. Please try again.', 'luna'));
					break;

				case 4: // UPLOAD_ERR_NO_FILE
					message(__('You did not select a file for upload.', 'luna'));
					break;

				case 6: // UPLOAD_ERR_NO_TMP_DIR
					message(__('PHP was unable to save the uploaded file to a temporary location.', 'luna'));
					break;

				default:
					// No error occured, but was something actually uploaded?
					if ($uploaded_file['size'] == 0)
						message(__('You did not select a file for upload.', 'luna'));
					break;
			}
		}

		if (is_uploaded_file($uploaded_file['tmp_name'])) {
			// Preliminary file check, adequate in most cases
			$allowed_types = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
			if (!in_array($uploaded_file['type'], $allowed_types))
				message(__('The file you tried to upload is not of an allowed type. Allowed types are gif, jpeg and png.', 'luna'));

			// Make sure the file isn't too big
			if ($uploaded_file['size'] > $luna_config['o_avatars_size'])
				message(__('The file you tried to upload is larger than the maximum allowed', 'luna').' '.forum_number_format($luna_config['o_avatars_size']).' '.__('bytes', 'luna').'.');

			// Move the file to the avatar directory. We do this before checking the width/height to circumvent open_basedir restrictions
			if (!@move_uploaded_file($uploaded_file['tmp_name'], LUNA_ROOT.$luna_config['o_avatars_dir'].'/'.$id.'.tmp'))
				message(__('The server was unable to save the uploaded file. Please contact the forum administrator at', 'luna').' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.');

			list($width, $height, $type,) = @getimagesize(LUNA_ROOT.$luna_config['o_avatars_dir'].'/'.$id.'.tmp');

			// Determine type
			if ($type == IMAGETYPE_GIF)
				$extension = '.gif';
			elseif ($type == IMAGETYPE_JPEG)
				$extension = '.jpg';
			elseif ($type == IMAGETYPE_PNG)
				$extension = '.png';
			else {
				// Invalid type
				@unlink(LUNA_ROOT.$luna_config['o_avatars_dir'].'/'.$id.'.tmp');
				message(__('The file you tried to upload is not of an allowed type. Allowed types are gif, jpeg and png.', 'luna'));
			}

			// Now check the width/height
			if (empty($width) || empty($height) || $width > $luna_config['o_avatars_width'] || $height > $luna_config['o_avatars_height']) {
				@unlink(LUNA_ROOT.$luna_config['o_avatars_dir'].'/'.$id.'.tmp');
				message(__('The file you tried to upload is wider and/or higher than the maximum allowed', 'luna').' '.$luna_config['o_avatars_width'].'x'.$luna_config['o_avatars_height'].' '.__('pixels', 'luna').'.');
			}

			// Delete any old avatars and put the new one in place
			delete_avatar($id);
			@rename(LUNA_ROOT.$luna_config['o_avatars_dir'].'/'.$id.'.tmp', LUNA_ROOT.$luna_config['o_avatars_dir'].'/'.$id.$extension);
			@chmod(LUNA_ROOT.$luna_config['o_avatars_dir'].'/'.$id.$extension, 0644);
		} else
			message(__('An unknown error occurred. Please try again.', 'luna'));

		redirect('settings.php?id='.$id);
	}

} elseif ($action == 'delete_avatar') {
	if ($luna_user['id'] != $id && !$luna_user['is_admmod'])
		message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');

	confirm_referrer('settings.php');

	check_csrf($_GET['csrf_token']);

	delete_avatar($id);

	redirect('settings.php?id='.$id);
} else {
	$result = $db->query('SELECT u.*, g.g_id, g.g_user_title, g.g_moderator FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

	$row = $db->fetch_assoc( $result );
	$user = $row;
	$nuser = User::withRow( $row );

	if ($luna_user['is_admmod']) {
		if ($luna_user['g_id'] == LUNA_ADMIN || $luna_user['g_mod_rename_users'] == '1')
			$username_field = '<input type="text" class="form-control" name="req_username" value="'.luna_htmlspecialchars($nuser->getUsername()).'" maxlength="25" />';
		else
			$username_field = luna_htmlspecialchars($nuser->getUsername());

		$email_field = '<input type="text" class="form-control" name="req_email" value="'.luna_htmlspecialchars($nuser->getEmail()).'" maxlength="80" />';
		$email_button = '<span class="input-group-append"><a class="btn btn-primary" href="misc.php?email='.$id.'">'.__('Send email', 'luna').'</a></span>';
	} else {
		$username_field = '<input class="form-control" type="text"  value="'.luna_htmlspecialchars($nuser->getUsername()).'" disabled="disabled" />';

		if ($luna_config['o_regs_verify'] == '1') {
			$email_field = '<input type="text" class="form-control" name="req_email" value="'.luna_htmlspecialchars($nuser->getEmail()).'" maxlength="80" disabled />';
			if ( $nuser->getActivateString() !== null && $nuser->getActivateKey() !== null )
				$email_button = '<span class="input-group-append"><a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newmail">'.__('Change email address', 'luna').'</a></span>';
			else
				$email_button = '<span class="input-group-append"><a class="btn btn-danger disabled" href="#" data-toggle="modal" data-target="#newmail">'.__('Unverified', 'luna').'</a></span>';
		} else {
			$email_field = '<input type="text" class="form-control" name="req_email" value="'.$nuser->getEmail().'" maxlength="80" />';
			$email_button = '<span class="input-group-append"><a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newmail">'.__('Change email address', 'luna').'</a></span>';
		}
	}

	if ($nuser->getSignature() != '') {
		$parsed_signature = parse_signature($nuser->getSignature());
	}

	if (isset($_POST['form_sent'])) {
		// Fetch the user group of the user we are editing
		$result = $db->query('SELECT u.username, u.group_id, g.g_moderator FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON (g.g_id=u.group_id) WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message(__('Bad r here.', 'luna'), false, '404 Not Found');

		list($old_username, $group_id, $is_moderator) = $db->fetch_row($result);

		if ($luna_user['id'] != $id &&																	// If we aren't the user (i.e. editing your own profile)
			(!$luna_user['is_admmod'] ||																	// and we are not an admin or mod
			($luna_user['g_id'] != LUNA_ADMIN &&														// or we aren't an admin and ...
			($luna_user['g_mod_edit_users'] == '0' ||													// mods aren't allowed to edit users
			$group_id == LUNA_ADMIN ||																	// or the user is an admin
			$is_moderator))))																			// or the user is another mod
			message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');

		// Make sure they got here from the site
		!empty($_GET['id']) ? confirm_referrer('settings.php?id='.$id) : confirm_referrer('settings.php');

		$username_updated = false;

		// Validate input depending on section
		$nuser->setRealname( $_POST['realname'] );
		$nuser->setUrl( $_POST['url'] );
		$nuser->setLocation( $_POST['location'] );
		$nuser->setFacebook( $_POST['facebook'] );
		$nuser->setMicrosoft( $_POST['msn'] );
		$nuser->setTwitter( $_POST['twitter'] );
		$nuser->setGoogle( $_POST['google'] );
		$nuser->setColorScheme( $_POST['color_scheme'] );
		$nuser->setAdaptTime( $_POST['adapt_time'] );
		$nuser->setAccent( $_POST['accent'] );
		$nuser->setPhpTimezone( $_POST['php_timezone'] );
		$nuser->setTimeFormat( $_POST['time_format'] );
		$nuser->setDateFormat( $_POST['date_format'] );
		$nuser->setShowImg( $_POST['show_img'] );
		$nuser->setFirstRun( $_POST['first_run'] );
		$nuser->setShowSig( $_POST['show_sig'] );
		$nuser->setUseInbox( $_POST['use_inbox'] );
		$nuser->setEmailSetting( $_POST['email_setting'] );
		$nuser->setNotifyWithComment( $_POST['notify_with_comment'] );
		$nuser->setAutoNotify( $_POST['auto_notify'] );
		$nuser->setLanguage( $_POST['language'] );

		if ($luna_user['is_admmod']) {
			$nuser->setAdminNote(  $_POST['admin_note'] );

			// We only allow administrators to update the comment count
			if ($luna_user['g_id'] == LUNA_ADMIN)
				$nuser->setNumComments( $_POST['num_comments'] );
		}

		if ($luna_user['is_admmod']) {
			// Are we allowed to change usernames?
			if ( $luna_user['g_id'] == LUNA_ADMIN || ( $luna_user['g_moderator'] == '1' && $luna_user['g_mod_rename_users'] == '1' ))  {
				$nuser->setUsername( $_POST['req_username'] );

				if ( $nuser->getUsername() != $old_username ) {
					// Check username
					$errors = array();
					check_username( $nuser->getUsername(), $id );
					if ( !empty( $errors ) )
						message( $errors[0] );

					$username_updated = true;
				}
			}
		}

		if ($luna_config['o_regs_verify'] == '0' || $luna_user['is_admmod']) {
			$nuser->setEmail( $_POST['req_email'] );
		}

		// Clean up signature from POST
		if ($luna_config['o_signatures'] == '1') {
			$nuser->setSignature( $_POST['signature'] );
		}

		// Single quotes around non-empty values and NULL for empty values
		$nuser->save();

		// If we changed the username we have to update some stuff
		if ($username_updated) {
			$db->query('UPDATE '.$db->prefix.'bans SET username=\''.$db->escape($_POST['req_username']).'\' WHERE username=\''.$db->escape($old_username).'\'') or error('Unable to update bans', __FILE__, __LINE__, $db->error());
			// If any bans were updated, we will need to know because the cache will need to be regenerated.
			if ($db->affected_rows() > 0)
				$bans_updated = true;
			$db->query('UPDATE '.$db->prefix.'comments SET commenter=\''.$db->escape($_POST['req_username']).'\' WHERE commenter_id='.$id) or error('Unable to update comments', __FILE__, __LINE__, $db->error());
			$db->query('UPDATE '.$db->prefix.'comments SET edited_by=\''.$db->escape($_POST['req_username']).'\' WHERE edited_by=\''.$db->escape($old_username).'\'') or error('Unable to update comments', __FILE__, __LINE__, $db->error());
			$db->query('UPDATE '.$db->prefix.'threads SET commenter=\''.$db->escape($_POST['req_username']).'\' WHERE commenter=\''.$db->escape($old_username).'\'') or error('Unable to update threads', __FILE__, __LINE__, $db->error());
			$db->query('UPDATE '.$db->prefix.'threads SET last_commenter=\''.$db->escape($_POST['req_username']).'\' WHERE last_commenter=\''.$db->escape($old_username).'\'') or error('Unable to update threads', __FILE__, __LINE__, $db->error());
			$db->query('UPDATE '.$db->prefix.'online SET ident=\''.$db->escape($_POST['req_username']).'\' WHERE ident=\''.$db->escape($old_username).'\'') or error('Unable to update online list', __FILE__, __LINE__, $db->error());

			// If the user is a moderator or an administrator we have to update the moderator lists
			$result = $db->query('SELECT group_id FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
			$group_id = $db->result($result);

			$result = $db->query('SELECT g_moderator FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to fetch group', __FILE__, __LINE__, $db->error());
			$group_mod = $db->result($result);

			if ($group_id == LUNA_ADMIN || $group_mod == '1') {
				$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

				while ($cur_forum = $db->fetch_assoc($result)) {
					$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

					if (in_array($id, $cur_moderators)) {
						unset($cur_moderators[$old_username]);
						$cur_moderators[$_POST['req_username']] = $id;
						uksort($cur_moderators, 'utf8_strcasecmp');

						$db->query('UPDATE '.$db->prefix.'forums SET moderators=\''.$db->escape(serialize($cur_moderators)).'\' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
					}
				}
			}

			// Regenerate the users info cache
			if (!defined('LUNA_CACHE_FUNCTIONS_LOADED'))
				require LUNA_ROOT.'include/cache.php';

			generate_users_info_cache();

			// Check if the bans table was updated and regenerate the bans cache when needed
			if (isset($bans_updated))
				generate_bans_cache();
		}

		!empty($_GET['id']) ? redirect('settings.php?id='.$id) : redirect('settings.php');
	}

	if ($nuser->getGSetTitle() == '1')
		$title_field = '<input type="text" class="form-control" name="title" value="'.luna_htmlspecialchars($nuser->getTitle()).'" maxlength="50" />';

	$avatar_field = '<a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newavatar">'.__('Change avatar', 'luna').'</a>';

	$avatar_user = get_avatar( $nuser->getId() );
	$avatar_set = check_avatar($id);
	if ($avatar_user && $avatar_set)
		$avatar_field .= ' <a class="btn btn-primary" href="settings.php?action=delete_avatar&amp;id='.$id.'&amp;csrf_token='.luna_csrf_token().'">'.__('Delete avatar', 'luna').'</a>';
	else
		$avatar_field = '<a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newavatar">'.__('Upload avatar', 'luna').'</a>';

	if ($nuser->getSignature() != '')
		$signature_preview = $parsed_signature;
	else
		$signature_preview = __('No signature currently stored in profile.', 'luna');

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Profile', 'luna'), __('Settings', 'luna'));
	define('LUNA_ACTIVE_PAGE', 'me');
    include LUNA_ROOT.'header.php';
	require load_page('header.php');
	require load_page('me-modals.php');
	require load_page('settings.php');
	require load_page('footer.php');
}
