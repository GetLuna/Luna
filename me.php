<?php

/*
 * Copyright (C) 2013-2014 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/parser.php';

// Load the me functions script
require FORUM_ROOT.'include/me_functions.php';

// Include UTF-8 function
require FORUM_ROOT.'include/utf8/substr_replace.php';
require FORUM_ROOT.'include/utf8/ucwords.php'; // utf8_ucwords needs utf8_substr_replace
require FORUM_ROOT.'include/utf8/strcasecmp.php';

require load_page('me-modals.php');

$action = isset($_GET['action']) ? $_GET['action'] : null;
$section = isset($_GET['section']) ? $_GET['section'] : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 2)
	message($lang['Bad request'], false, '404 Not Found');

if ($action != 'change_pass' || !isset($_GET['key'])) {
	if ($luna_user['g_read_board'] == '0')
		message($lang['No view'], false, '403 Forbidden');
	else if ($luna_user['g_view_users'] == '0' && ($luna_user['is_guest'] || $luna_user['id'] != $id))
		message($lang['No permission'], false, '403 Forbidden');
}

if ($action == 'change_pass') {
	if (isset($_GET['key'])) {
		// If the user is already logged in we shouldn't be here :)
		if (!$luna_user['is_guest']) {
			header('Location: index.php');
			exit;
		}

		$key = $_GET['key'];

		$result = $db->query('SELECT * FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch new password', __FILE__, __LINE__, $db->error());
		$cur_user = $db->fetch_assoc($result);

		if ($key == '' || $key != $cur_user['activate_key'])
			message($lang['Pass key bad'].' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.');
		else {
			$db->query('UPDATE '.$db->prefix.'users SET password=\''.$db->escape($cur_user['activate_string']).'\', activate_string=NULL, activate_key=NULL'.(!empty($cur_user['salt']) ? ', salt=NULL' : '').' WHERE id='.$id) or error('Unable to update password', __FILE__, __LINE__, $db->error());

			message($lang['Pass updated'], true);
		}
	}

	// Make sure we are allowed to change this user's password
	if ($luna_user['id'] != $id) {
		if (!$luna_user['is_admmod']) // A regular user trying to change another user's password?
			message($lang['No permission'], false, '403 Forbidden');
		else if ($luna_user['g_moderator'] == '1') { // A moderator trying to change a user's password?
			$result = $db->query('SELECT u.group_id, g.g_moderator FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'groups AS g ON (g.g_id=u.group_id) WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
			if (!$db->num_rows($result))
				message($lang['Bad request'], false, '404 Not Found');

			list($group_id, $is_moderator) = $db->fetch_row($result);

			if ($luna_user['g_mod_edit_users'] == '0' || $luna_user['g_mod_change_passwords'] == '0' || $group_id == FORUM_ADMIN || $is_moderator == '1')
				message($lang['No permission'], false, '403 Forbidden');
		}
	}

	if (isset($_POST['form_sent'])) {
		// Make sure they got here from the site
		confirm_referrer('me.php');

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

		if (!empty($cur_user['password'])) {
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

		redirect('me.php?section=personality&amp;id='.$id);
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Change pass']);
	$required_fields = array('req_old_password' => $lang['Old pass'], 'req_new_password1' => $lang['New pass'], 'req_new_password2' => $lang['Confirm new pass']);
	$focus_element = array('change_pass', ((!$luna_user['is_admmod']) ? 'req_old_password' : 'req_new_password1'));
	define('FORUM_ACTIVE_PAGE', 'me');
	require load_page('header.php');

	require get_view_path('me-change_pass.tpl.php');
} else if ($action == 'change_email') {
	// Make sure we are allowed to change this user's email
	if ($luna_user['id'] != $id) {
		if (!$luna_user['is_admmod']) // A regular user trying to change another user's email?
			message($lang['No permission'], false, '403 Forbidden');
		else if ($luna_user['g_moderator'] == '1') { // A moderator trying to change a user's email?
			$result = $db->query('SELECT u.group_id, g.g_moderator FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'groups AS g ON (g.g_id=u.group_id) WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
			if (!$db->num_rows($result))
				message($lang['Bad request'], false, '404 Not Found');

			list($group_id, $is_moderator) = $db->fetch_row($result);

			if ($luna_user['g_mod_edit_users'] == '0' || $group_id == FORUM_ADMIN || $is_moderator == '1')
				message($lang['No permission'], false, '403 Forbidden');
		}
	}

	if (isset($_GET['key'])) {
		$key = $_GET['key'];

		$result = $db->query('SELECT activate_string, activate_key FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch activation data', __FILE__, __LINE__, $db->error());
		list($new_email, $new_email_key) = $db->fetch_row($result);

		if ($key == '' || $key != $new_email_key)
			message($lang['Email key bad'].' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.');
		else {
			$db->query('UPDATE '.$db->prefix.'users SET email=activate_string, activate_string=NULL, activate_key=NULL WHERE id='.$id) or error('Unable to update email address', __FILE__, __LINE__, $db->error());

			message($lang['Email updated'], true);
		}
	} else if (isset($_POST['form_sent'])) {
		if (luna_hash($_POST['req_password']) !== $luna_user['password'])
			message($lang['Wrong pass']);

		// Make sure they got here from the site
		confirm_referrer('me.php');

		require FORUM_ROOT.'include/email.php';

		// Validate the email address
		$new_email = strtolower(luna_trim($_POST['req_new_email']));
		if (!is_valid_email($new_email))
			message($lang['Invalid email']);

		// Check if it's a banned email address
		if (is_banned_email($new_email)) {
			if ($luna_config['p_allow_banned_email'] == '0')
				message($lang['Banned email']);
			else if ($luna_config['o_mailing_list'] != '') {
				// Load the "banned email change" template
				$mail_tpl = trim($lang['banned_email_change.tpl']);

				// The first row contains the subject
				$first_crlf = strpos($mail_tpl, "\n");
				$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
				$mail_message = trim(substr($mail_tpl, $first_crlf));

				$mail_message = str_replace('<username>', $luna_user['username'], $mail_message);
				$mail_message = str_replace('<email>', $new_email, $mail_message);
				$mail_message = str_replace('<profile_url>', get_base_url().'/me.php?id='.$id, $mail_message);
				$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

				luna_mail($luna_config['o_mailing_list'], $mail_subject, $mail_message);
			}
		}

		// Check if someone else already has registered with that email address
		$result = $db->query('SELECT id, username FROM '.$db->prefix.'users WHERE email=\''.$db->escape($new_email).'\'') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result)) {
			if ($luna_config['p_allow_dupe_email'] == '0')
				message($lang['Dupe email']);
			else if ($luna_config['o_mailing_list'] != '') {
				while ($cur_dupe = $db->fetch_assoc($result))
					$dupe_list[] = $cur_dupe['username'];

				// Load the "dupe email change" template
				$mail_tpl = trim($lang['dupe_email_change.tpl']);

				// The first row contains the subject
				$first_crlf = strpos($mail_tpl, "\n");
				$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
				$mail_message = trim(substr($mail_tpl, $first_crlf));

				$mail_message = str_replace('<username>', $luna_user['username'], $mail_message);
				$mail_message = str_replace('<dupe_list>', implode(', ', $dupe_list), $mail_message);
				$mail_message = str_replace('<profile_url>', get_base_url().'/me.php?id='.$id, $mail_message);
				$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

				luna_mail($luna_config['o_mailing_list'], $mail_subject, $mail_message);
			}
		}


		$new_email_key = random_pass(8);

		$db->query('UPDATE '.$db->prefix.'users SET activate_string=\''.$db->escape($new_email).'\', activate_key=\''.$new_email_key.'\' WHERE id='.$id) or error('Unable to update activation data', __FILE__, __LINE__, $db->error());

		// Load the "activate email" template
		$mail_tpl = trim($lang['activate_email.tpl']);

		// The first row contains the subject
		$first_crlf = strpos($mail_tpl, "\n");
		$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
		$mail_message = trim(substr($mail_tpl, $first_crlf));

		$mail_message = str_replace('<username>', $luna_user['username'], $mail_message);
		$mail_message = str_replace('<base_url>', get_base_url(), $mail_message);
		$mail_message = str_replace('<activation_url>', get_base_url().'/me.php?action=change_email&id='.$id.'&key='.$new_email_key, $mail_message);
		$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

		luna_mail($new_email, $mail_subject, $mail_message);

		message($lang['Activate email sent'].' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.', true);
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Change email']);
	$required_fields = array('req_new_email' => $lang['New email'], 'req_password' => $lang['Password']);
	$focus_element = array('change_email', 'req_new_email');
	define('FORUM_ACTIVE_PAGE', 'me');
	require load_page('header.php');

	require get_view_path('me-change_email.tpl.php');
} else if ($action == 'upload_avatar' || $action == 'upload_avatar2') {
	if ($luna_config['o_avatars'] == '0')
		message($lang['Avatars disabled']);

	if ($luna_user['id'] != $id && !$luna_user['is_admmod'])
		message($lang['No permission'], false, '403 Forbidden');

	if (isset($_POST['form_sent'])) {
		if (!isset($_FILES['req_file']))
			message($lang['No file']);

		// Make sure they got here from the site
		confirm_referrer('me.php');

		$uploaded_file = $_FILES['req_file'];

		// Make sure the upload went smooth
		if (isset($uploaded_file['error'])) {
			switch ($uploaded_file['error']) {
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

		if (is_uploaded_file($uploaded_file['tmp_name'])) {
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
			else {
				// Invalid type
				@unlink(FORUM_ROOT.$luna_config['o_avatars_dir'].'/'.$id.'.tmp');
				message($lang['Bad type']);
			}

			// Now check the width/height
			if (empty($width) || empty($height) || $width > $luna_config['o_avatars_width'] || $height > $luna_config['o_avatars_height']) {
				@unlink(FORUM_ROOT.$luna_config['o_avatars_dir'].'/'.$id.'.tmp');
				message($lang['Too wide or high'].' '.$luna_config['o_avatars_width'].'x'.$luna_config['o_avatars_height'].' '.$lang['pixels'].'.');
			}


			// Delete any old avatars and put the new one in place
			delete_avatar($id);
			@rename(FORUM_ROOT.$luna_config['o_avatars_dir'].'/'.$id.'.tmp', FORUM_ROOT.$luna_config['o_avatars_dir'].'/'.$id.$extension);
			@chmod(FORUM_ROOT.$luna_config['o_avatars_dir'].'/'.$id.$extension, 0644);
		} else
			message($lang['Unknown failure']);

		redirect('me.php?section=personality&amp;id='.$id);
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Upload avatar']);
	$required_fields = array('req_file' => $lang['File']);
	$focus_element = array('upload_avatar', 'req_file');
	define('FORUM_ACTIVE_PAGE', 'me');
	require load_page('header.php');

	require get_view_path('me-upload_avatar.tpl.php');
} else if ($action == 'delete_avatar') {
	if ($luna_user['id'] != $id && !$luna_user['is_admmod'])
		message($lang['No permission'], false, '403 Forbidden');

	confirm_referrer('me.php');

	delete_avatar($id);

	redirect('me.php?section=personality&amp;id='.$id);
} else if (isset($_POST['form_sent'])) {
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
	confirm_referrer('me.php');

	$username_updated = false;

	// Validate input depending on section
	switch ($section) {
		case 'admin': {
			$form = array();

			if ($luna_user['is_admmod']) {
				$form['admin_note'] = luna_trim($_POST['admin_note']);

				// We only allow administrators to update the post count
				if ($luna_user['g_id'] == FORUM_ADMIN)
					$form['num_posts'] = intval($_POST['num_posts']);
			}

			break;
		}

		case 'personality': {
			$form = array(
				'realname'		=> luna_trim($_POST['form']['realname']),
				'url'			=> luna_trim($_POST['form']['url']),
				'location'		=> luna_trim($_POST['form']['location']),
				'facebook'		=> luna_trim($_POST['form']['facebook']),
				'msn'			=> luna_trim($_POST['form']['msn']),
				'twitter'		=> luna_trim($_POST['form']['twitter']),
				'google'		=> luna_trim($_POST['form']['google']),
				'color'			=> luna_trim($_POST['form']['color'])
			);

			if ($luna_user['is_admmod']) {
				// Are we allowed to change usernames?
				if ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_rename_users'] == '1')) {
					$form['username'] = luna_trim($_POST['req_username']);

					if ($form['username'] != $old_username) {
						// Check username
						$errors = array();
						check_username($form['username'], $id);
						if (!empty($errors))
							message($errors[0]);

						$username_updated = true;
					}
				}
			}

			if ($luna_config['o_regs_verify'] == '0' || $luna_user['is_admmod']) {
				require FORUM_ROOT.'include/email.php';

				// Validate the email address
				$form['email'] = strtolower(luna_trim($_POST['req_email']));
				if (!is_valid_email($form['email']))
					message($lang['Invalid email']);
			}

			// Add http:// if the URL doesn't contain it already (while allowing https://, too)
			if ($form['url'] != '') {
				$url = url_valid($form['url']);

				if ($url === false)
					message($lang['Invalid website URL']);

				$form['url'] = $url['url'];
			}

			if ($luna_user['g_id'] == FORUM_ADMIN)
				$form['title'] = luna_trim($_POST['title']);
			else if ($luna_user['g_set_title'] == '1') {
				$form['title'] = luna_trim($_POST['title']);

				if ($form['title'] != '') {
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
			if ($luna_config['o_signatures'] == '1') {
				$form['signature'] = luna_linebreaks(luna_trim($_POST['signature']));

				// Validate signature
				if (luna_strlen($form['signature']) > $luna_config['p_sig_length'])
					message(sprintf($lang['Sig too long'], $luna_config['p_sig_length'], luna_strlen($form['signature']) - $luna_config['p_sig_length']));
				else if (substr_count($form['signature'], "\n") > ($luna_config['p_sig_lines']-1))
					message(sprintf($lang['Sig too many lines'], $luna_config['p_sig_lines']));
				else if ($form['signature'] && $luna_config['p_sig_all_caps'] == '0' && is_all_uppercase($form['signature']) && !$luna_user['is_admmod'])
					$form['signature'] = utf8_ucwords(utf8_strtolower($form['signature']));

				$errors = array();
				$form['signature'] = preparse_bbcode($form['signature'], $errors, true);

				if(count($errors) > 0)
					message('<ul><li>'.implode('</li><li>', $errors).'</li></ul>');
			}

			break;
		}

		case 'settings': {
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

			if ($form['disp_topics'] != '') {
				$form['disp_topics'] = intval($form['disp_topics']);
				if ($form['disp_topics'] < 3)
					$form['disp_topics'] = 3;
				else if ($form['disp_topics'] > 75)
					$form['disp_topics'] = 75;
			}

			if ($form['disp_posts'] != '') {
				$form['disp_posts'] = intval($form['disp_posts']);
				if ($form['disp_posts'] < 3)
					$form['disp_posts'] = 3;
				else if ($form['disp_posts'] > 75)
					$form['disp_posts'] = 75;
			}

			// Make sure we got a valid language string
			if (isset($_POST['form']['language'])) {
				$languages = forum_list_langs();
				$form['language'] = luna_trim($_POST['form']['language']);
				if (!in_array($form['language'], $languages))
					message($lang['Bad request'], false, '404 Not Found');
			}

			// Make sure we got a valid style string
			if (isset($_POST['form']['style'])) {
				$styles = forum_list_styles();
				$form['style'] = luna_trim($_POST['form']['style']);
				if (!in_array($form['style'], $styles))
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
	foreach ($form as $key => $input) {
		$value = ($input !== '') ? '\''.$db->escape($input).'\'' : 'NULL';

		$temp[] = $key.'='.$value;
	}

	if (empty($temp))
		message($lang['Bad request'], false, '404 Not Found');


	$db->query('UPDATE '.$db->prefix.'users SET '.implode(',', $temp).' WHERE id='.$id) or error('Unable to update profile', __FILE__, __LINE__, $db->error());

	// If we changed the username we have to update some stuff
	if ($username_updated) {
		$db->query('UPDATE '.$db->prefix.'bans SET username=\''.$db->escape($form['username']).'\' WHERE username=\''.$db->escape($old_username).'\'') or error('Unable to update bans', __FILE__, __LINE__, $db->error());
		// If any bans were updated, we will need to know because the cache will need to be regenerated.
		if ($db->affected_rows() > 0)
			$bans_updated = true;
		$db->query('UPDATE '.$db->prefix.'posts SET poster=\''.$db->escape($form['username']).'\' WHERE poster_id='.$id) or error('Unable to update posts', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'posts SET edited_by=\''.$db->escape($form['username']).'\' WHERE edited_by=\''.$db->escape($old_username).'\'') or error('Unable to update posts', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'topics SET poster=\''.$db->escape($form['username']).'\' WHERE poster=\''.$db->escape($old_username).'\'') or error('Unable to update topics', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'topics SET last_poster=\''.$db->escape($form['username']).'\' WHERE last_poster=\''.$db->escape($old_username).'\'') or error('Unable to update topics', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'online SET ident=\''.$db->escape($form['username']).'\' WHERE ident=\''.$db->escape($old_username).'\'') or error('Unable to update online list', __FILE__, __LINE__, $db->error());

		// If the user is a moderator or an administrator we have to update the moderator lists
		$result = $db->query('SELECT group_id FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
		$group_id = $db->result($result);

		$result = $db->query('SELECT g_moderator FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to fetch group', __FILE__, __LINE__, $db->error());
		$group_mod = $db->result($result);

		if ($group_id == FORUM_ADMIN || $group_mod == '1') {
			$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

			while ($cur_forum = $db->fetch_assoc($result)) {
				$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

				if (in_array($id, $cur_moderators)) {
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

	redirect('me.php?section='.$section.'&amp;id='.$id);
} else {

	$result = $db->query('SELECT u.id, u.username, u.email, u.title, u.realname, u.url, u.facebook, u.msn, u.twitter, u.google, u.location, u.signature, u.disp_topics, u.disp_posts, u.email_setting, u.notify_with_post, u.auto_notify, u.show_smilies, u.show_img, u.show_img_sig, u.show_avatars, u.show_sig, u.timezone, u.dst, u.language, u.style, u.color, u.num_posts, u.last_post, u.registered, u.registration_ip, u.admin_note, u.date_format, u.time_format, u.last_visit, g.g_id, g.g_user_title, g.g_moderator FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message($lang['Bad request'], false, '404 Not Found');
	
	$user = $db->fetch_assoc($result);
	
	$last_post = format_time($user['last_post']);
	
	if ($user['signature'] != '') {
		$parsed_signature = parse_signature($user['signature']);
	}
	
	// View or edit?
	if (!$section || $section == 'view') {
		$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']).' / '.$lang['Profile']);
		define('FORUM_ACTIVE_PAGE', 'me');
		require load_page('header.php');
		require load_page('activity.php');
	} else if ($section == 'personality') {
		$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Section personality']);
		define('FORUM_ACTIVE_PAGE', 'me');
		require load_page('header.php');
	
		require load_page('personality.php');
	} else if ($section == 'settings') {
		if ($luna_user['id'] != $id && (!$luna_user['is_admmod'] || ($luna_user['g_id'] != FORUM_ADMIN && ($luna_user['g_mod_edit_users'] == '0' || $user['g_id'] == FORUM_ADMIN || $user['g_moderator'] == '1'))))
			message($lang['Bad request'], false, '403 Forbidden');
	
		$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Settings']);
		define('FORUM_ACTIVE_PAGE', 'me');
		require load_page('header.php');
	
		require load_page('settings.php');
	} else if ($section == 'admin') {
		if (!$luna_user['is_admmod'] || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '0'))
			message($lang['Bad request'], false, '403 Forbidden');
	
		$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Section admin']);
		define('FORUM_ACTIVE_PAGE', 'me');
		require load_page('header.php');
	
		require load_page('me-admin.php');
	} else {
		message($lang['Bad request'], false, '404 Not Found');
	}
	
	require load_page('footer.php');
}