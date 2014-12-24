<?php

/*
 * Copyright (C) 2013-2014 Luna
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/parser.php';

// Load the me functions script
require FORUM_ROOT.'include/me_functions.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : $luna_user['id'];

if (($luna_user['id'] != $id &&																	// If we aren't the user (i.e. editing your own profile)
	(!$luna_user['is_admmod'] ||																	// and we are not an admin or mod
	($luna_user['g_id'] != FORUM_ADMIN &&														// or we aren't an admin and ...
	($luna_user['g_mod_edit_users'] == '0' ||													// mods aren't allowed to edit users
	$group_id == FORUM_ADMIN ||																	// or the user is an admin
	$is_moderator))))																			// or the user is another mod
	|| $id == '1') {																				// or the ID is 1, and thus a guest
	message($lang['No permission'], false, '403 Forbidden');
} else {
	
	$result = $db->query('SELECT u.username, u.email, u.title, u.realname, u.url, u.facebook, u.msn, u.twitter, u.google, u.location, u.signature, u.disp_topics, u.disp_posts, u.email_setting, u.notify_with_post, u.auto_notify, u.show_smilies, u.show_img, u.show_img_sig, u.show_avatars, u.show_sig, u.timezone, u.dst, u.language, u.style, u.num_posts, u.last_post, u.registered, u.registration_ip, u.admin_note, u.date_format, u.time_format, u.last_visit, u.color, g.g_id, g.g_user_title, g.g_moderator FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message($lang['Bad request'], false, '404 Not Found');
	
	$user = $db->fetch_assoc($result);
	
	if ($luna_user['is_admmod']) {
		if ($luna_user['g_id'] == FORUM_ADMIN || $luna_user['g_mod_rename_users'] == '1')
			$username_field = '<input type="text" class="form-control" name="req_username" value="'.luna_htmlspecialchars($user['username']).'" maxlength="25" />';
		else
			$username_field = luna_htmlspecialchars($user['username']);
	
		$email_field = '<input type="text" class="form-control" name="req_email" value="'.luna_htmlspecialchars($user['email']).'" maxlength="80" />';
		$email_button = '<span class="input-group-btn"><a class="btn btn-primary" href="misc.php?email='.$id.'">'.$lang['Send email'].'</a></span>';
	} else {
		$username_field = '<input class="form-control" type="text"  value="'.luna_htmlspecialchars($user['username']).'" disabled="disabled" />';
	
		if ($luna_config['o_regs_verify'] == '1') {
			$email_field = '<input type="text" class="form-control" name="req_email" value="'.luna_htmlspecialchars($user['email']).'" maxlength="80" disabled />';
			$email_button = '<span class="input-group-btn"><a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newmail">'.$lang['Change email'].'</a></span>';
		} else {
			$email_field = '<input type="text" class="form-control" name="req_email" value="'.$user['email'].'" maxlength="80" />';
			$email_button = '<span class="input-group-btn"><a class="btn btn-danger disabled" href="#" data-toggle="modal" data-target="#newmail">Unverified</a></span>';
		}
	}
	
	if ($user['signature'] != '') {
		$parsed_signature = parse_signature($user['signature']);
	}
	
	if (isset($_POST['form_sent'])) {
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
		!empty($_GET['id']) ? confirm_referrer('settings.php?id='.$id) : confirm_referrer('settings.php');
	
		$username_updated = false;
	
		// Validate input depending on section
		$form = array(
			'realname'			=> luna_trim($_POST['form']['realname']),
			'url'				=> luna_trim($_POST['form']['url']),
			'location'			=> luna_trim($_POST['form']['location']),
			'facebook'			=> luna_trim($_POST['form']['facebook']),
			'msn'				=> luna_trim($_POST['form']['msn']),
			'twitter'			=> luna_trim($_POST['form']['twitter']),
			'google'			=> luna_trim($_POST['form']['google']),
			'color'				=> luna_trim($_POST['form']['color']),
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
			'auto_notify'		=> isset($_POST['form']['auto_notify']) ? '1' : '0'
		);
	
		if ($luna_user['is_admmod']) {
			$form['admin_note'] = luna_trim($_POST['admin_note']);
	
			// We only allow administrators to update the post count
			if ($luna_user['g_id'] == FORUM_ADMIN)
				$form['num_posts'] = intval($_POST['num_posts']);
		}
	
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
	
		// Single quotes around non-empty values and NULL for empty values
		$temp = array();
		foreach ($form as $key => $input) {
			$value = ($input !== '') ? '\''.$db->escape($input).'\'' : 'NULL';
	
			$temp[] = $key.'='.$value;
		}
	
		if (empty($temp))
			message($lang['Bad request'], false, '404 Not Found');
	
		$db->query('UPDATE '.$db->prefix.'users SET '.implode(', ', $temp).' WHERE id='.$id) or error('Unable to update profile', __FILE__, __LINE__, $db->error());
	
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
	
		!empty($_GET['id']) ? redirect('settings.php?id='.$id) : redirect('settings.php');
	}
	
	if ($luna_user['g_set_title'] == '1')
		$title_field = '<input class="form-control" type="text" class="form-control" name="title" value="'.luna_htmlspecialchars($user['title']).'" maxlength="50" />';
	
	$avatar_user = draw_user_avatar($id, 'visible-lg-inline');
	$avatar_set = check_avatar($id);
	if ($user_avatar && $avatar_set)
		$avatar_field .= ' <a class="btn btn-primary" href="me.php?action=delete_avatar&amp;id='.$id.'">'.$lang['Delete avatar'].'</a>';
	else
		$avatar_field = '<a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newavatar">'.$lang['Upload avatar'].'</a>';
	
	if ($user['signature'] != '')
		$signature_preview = $parsed_signature;
	else
		$signature_preview = $lang['No sig'];
	
	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Settings']);
	define('FORUM_ACTIVE_PAGE', 'me');
	require load_page('header.php');
	require load_page('settings2.php');
	require load_page('footer.php');
}