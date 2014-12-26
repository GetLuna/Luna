<?php

/*
 * Copyright (C) 2013-2015 Luna
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

if ($action == 'newnoti') {
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
	} else {
		message($lang['Bad request'], false, '404 Not Found');
	}
	
	require load_page('footer.php');
}