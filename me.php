<?php

/*
 * Copyright (C) 2013-2014 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';

// Load the me functions script
require FORUM_ROOT.'include/me_functions.php';

// Include UTF-8 function
require FORUM_ROOT.'include/utf8/substr_replace.php';
require FORUM_ROOT.'include/utf8/ucwords.php'; // utf8_ucwords needs utf8_substr_replace
require FORUM_ROOT.'include/utf8/strcasecmp.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;
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

$result = $db->query('SELECT u.username, u.email, u.title, u.realname, u.url, u.facebook, u.msn, u.twitter, u.google, u.location, u.signature, u.disp_topics, u.disp_posts, u.email_setting, u.notify_with_post, u.auto_notify, u.show_smilies, u.show_img, u.show_img_sig, u.show_avatars, u.show_sig, u.timezone, u.dst, u.language, u.style, u.num_posts, u.last_post, u.registered, u.registration_ip, u.admin_note, u.date_format, u.time_format, u.last_visit, u.color, g.g_id, g.g_user_title, g.g_moderator FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message($lang['Bad request'], false, '404 Not Found');

$user = $db->fetch_assoc($result);

$last_post = format_time($user['last_post']);

$user_personality = array();

$user_username = luna_htmlspecialchars($user['username']);
$user_usertitle = get_title($user);
$avatar_field = generate_avatar_markup($id);
$avatar_user_card = draw_user_avatar($id, 'visible-lg-block');

$user_title_field = get_title($user);
$user_personality[] = '<b>'.$lang['Title'].':</b> '.(($luna_config['o_censoring'] == '1') ? censor_words($user_title_field) : $user_title_field);

$user_personality[] = '<b>'.$lang['Posts table'].':</b> '.$posts_field = forum_number_format($user['num_posts']);

if ($user['num_posts'] > 0)
	$user_personality[] = '<b>'.$lang['Last post'].':</b> '.$last_post;

$user_activity[] = '<b>'.$lang['Registered table'].':</b> '.format_time($user['registered'], true);

$user_personality[] = '<b>'.$lang['Registered'].':</b> '.format_time($user['registered'], true);

$user_personality[] = '<b>'.$lang['Last visit info'].':</b> '.format_time($user['last_visit'], true);

if ($user['realname'] != '')
	$user_personality[] = '<b>'.$lang['Realname'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['realname']) : $user['realname']);

if ($user['location'] != '')
	$user_personality[] = '<b>'.$lang['Location'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['location']) : $user['location']);

$posts_field = '';
if ($luna_user['g_search'] == '1') {
	$quick_searches = array();
	if ($user['num_posts'] > 0) {
		$quick_searches[] = '<a class="btn btn-primary btn-sm" href="search.php?action=show_user_topics&amp;user_id='.$id.'">'.$lang['Show topics'].'</a>';
		$quick_searches[] = '<a class="btn btn-primary btn-sm" href="search.php?action=show_user_posts&amp;user_id='.$id.'">'.$lang['Show posts'].'</a>';
	}
	if ($luna_user['is_admmod'] && $luna_config['o_topic_subscriptions'] == '1')
		$quick_searches[] = '<a class="btn btn-primary btn-sm" href="search.php?action=show_subscriptions&amp;user_id='.$id.'">'.$lang['Show subscriptions'].'</a>';

	if (!empty($quick_searches))
		$posts_field .= implode('', $quick_searches);
}

if ($posts_field != '')
	$user_personality[] = '<br /><div class="btn-group">'.$posts_field.'</div>';

if ($user['url'] != '') {
	$user_website = '<a class="btn btn-default btn-block" href="'.luna_htmlspecialchars($user['url']).'" rel="nofollow"><span class="fa fa-globe"></span> '.$lang['Website'].'</a>';
} else {
	$user_website = '';
}

if ($user['email_setting'] == '0' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
	$email_field = '<a class="btn btn-default btn-block" href="mailto:'.luna_htmlspecialchars($user['email']).'"><span class="fa fa-send-o"></span> '.luna_htmlspecialchars($user['email']).'</a>';
else if ($user['email_setting'] == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
	$email_field = '<a class="btn btn-default btn-block" href="misc.php?email='.$id.'"><span class="fa fa-send-o"></span> '.$lang['Send email'].'</a>';
else
	$email_field = '';
if ($email_field != '') {
	$email_field;
}

$user_messaging = array();

if ($user['facebook'] != '')
	$user_messaging[] = '<b>'.$lang['Facebook'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['facebook']) : $user['facebook']);

if ($user['msn'] != '')
	$user_messaging[] = '<b>'.$lang['Microsoft'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['msn']) : $user['msn']);

if ($user['twitter'] != '')
	$user_messaging[] = '<b>'.$lang['Twitter'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['twitter']) : $user['twitter']);

if ($user['google'] != '')
	$user_messaging[] = '<b>'.$lang['Google+'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['google']) : $user['google']);

if (($luna_config['o_signatures'] == '1') && (isset($parsed_signature)))
	$user_signature = $parsed_signature;

$user_activity = array();

if ($user['signature'] != '') {
	require FORUM_ROOT.'include/parser.php';
	$parsed_signature = parse_signature($user['signature']);
}

if (isset($_POST['update_group_membership'])) {
	if ($luna_user['g_id'] > FORUM_ADMIN)
		message($lang['No permission'], false, '403 Forbidden');

	confirm_referrer('me.php');

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
	if ($new_group_id != FORUM_ADMIN && $new_group_mod != '1') {
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

	redirect('me.php?section=admin&amp;id='.$id);
} else if (isset($_POST['ban'])) {
	if ($luna_user['g_id'] != FORUM_ADMIN && ($luna_user['g_moderator'] != '1' || $luna_user['g_mod_ban_users'] == '0'))
		message($lang['No permission'], false, '403 Forbidden');

	confirm_referrer('me.php');

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
} else if (isset($_POST['delete_user']) || isset($_POST['delete_user_comply'])) {
	if ($luna_user['g_id'] > FORUM_ADMIN)
		message($lang['No permission'], false, '403 Forbidden');

	// Get the username and group of the user we are deleting
	$result = $db->query('SELECT group_id, username FROM '.$db->prefix.'users WHERE id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	list($group_id, $username) = $db->fetch_row($result);

	if ($group_id == FORUM_ADMIN)
		message($lang['No delete admin message']);

	if (isset($_POST['delete_user_comply'])) {
		// If the user is a moderator or an administrator, we remove him/her from the moderator list in all forums as well
		$result = $db->query('SELECT g_moderator FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to fetch group', __FILE__, __LINE__, $db->error());
		$group_mod = $db->result($result);

		if ($group_id == FORUM_ADMIN || $group_mod == '1') {
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
		$db->query('DELETE FROM '.$db->prefix.'topic_subscriptions WHERE user_id='.$id) or error('Unable to delete topic subscriptions', __FILE__, __LINE__, $db->error());
		$db->query('DELETE FROM '.$db->prefix.'forum_subscriptions WHERE user_id='.$id) or error('Unable to delete forum subscriptions', __FILE__, __LINE__, $db->error());

		// Remove him/her from the online list (if they happen to be logged in)
		$db->query('DELETE FROM '.$db->prefix.'online WHERE user_id='.$id) or error('Unable to remove user from online list', __FILE__, __LINE__, $db->error());

		// Should we delete all posts made by this user?
		if (isset($_POST['delete_posts'])) {
			require FORUM_ROOT.'include/search_idx.php';
			@set_time_limit(0);

			// Find all posts made by this user
			$result = $db->query('SELECT p.id, p.topic_id, t.forum_id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id WHERE p.poster_id='.$id) or error('Unable to fetch posts', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result)) {
				while ($cur_post = $db->fetch_assoc($result)) {
					// Determine whether this post is the "topic post" or not
					$result2 = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE topic_id='.$cur_post['topic_id'].' ORDER BY posted LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());

					if ($db->result($result2) == $cur_post['id'])
						delete_topic($cur_post['topic_id']);
					else
						delete_post($cur_post['id'], $cur_post['topic_id']);

					update_forum($cur_post['forum_id']);
				}
			}
		} else
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

		redirect('index.php');
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Confirm delete user']);
	define('FORUM_ACTIVE_PAGE', 'profile');
	require load_page('header.php');

	require get_view_path('me-delete_user.tpl.php');
} else if ($action == 'change_pass') {
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
} else if ($action == 'newnoti') {
	if ($type == 'windows') {
		new_notification('2', 'index.php', 'Windows 8.1 is recent', 'fa-windows');
	} elseif ($type == 'comment') {
		new_notification('2', 'index.php', 'Someone made a comment on your topic', 'fa-comment');
	} elseif ($type == 'check') {
		new_notification('2', 'index.php', 'Check this out', 'fa-check');
	} elseif ($type == 'version') {
		new_notification('2', 'index.php', 'You are using Luna '.$luna_config['o_core_version'].'! Awesome!', 'fa-moon-o');
	} elseif ($type == 'cogs') {
		new_notification('2', 'index.php', 'This icon usualy indicates settings, not now through...', 'fa-cogs');
	}

	redirect('me.php?section=notifications&amp;id='.$id);
} else if ($action == 'readnoti') {
	$db->query('UPDATE '.$db->prefix.'notifications SET viewed = 1 WHERE user_id = '.$id.' AND viewed = 0') or error('Unable to update the notification status', __FILE__, __LINE__, $db->error());
	confirm_referrer('me.php');

	redirect('me.php?section=notifications&amp;id='.$id);
} else if ($action == 'delnoti') {
	$db->query('DELETE FROM '.$db->prefix.'notifications WHERE viewed = 1') or error('Unable to remove notifications', __FILE__, __LINE__, $db->error());
	confirm_referrer('me.php');

	redirect('me.php?section=notifications&amp;id='.$id);
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
		require load_page('profile.php');
	} else if ($section == 'notifications') {

		$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']).' / '.$lang['Profile']);
		define('FORUM_ACTIVE_PAGE', 'me');
		require load_page('header.php');
		require load_page('notifications.php');
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