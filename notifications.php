<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', dirname(__FILE__).'/');
require LUNA_ROOT.'include/common.php';

// Load the me functions script
require LUNA_ROOT.'include/me_functions.php';

// Include UTF-8 function
require LUNA_ROOT.'include/utf8/substr_replace.php';
require LUNA_ROOT.'include/utf8/ucwords.php'; // utf8_ucwords needs utf8_substr_replace
require LUNA_ROOT.'include/utf8/strcasecmp.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;
$id = $luna_user['id'];
if ($id < 2)
	message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

$result = $db->query('SELECT u.username, u.email, u.title, u.realname, u.url, u.facebook, u.msn, u.twitter, u.google, u.location, u.signature, u.disp_threads, u.disp_comments, u.email_setting, u.notify_with_comment, u.auto_notify, u.show_smilies, u.show_img, u.show_img_sig, u.show_avatars, u.show_sig, u.php_timezone, u.language, u.style, u.num_comments, u.last_comment, u.registered, u.registration_ip, u.admin_note, u.date_format, u.time_format, u.last_visit, u.color_scheme, u.accent, g.g_id, g.g_user_title, g.g_moderator FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

$user = $db->fetch_assoc($result);

$user_username = luna_htmlspecialchars($user['username']);
$user_usertitle = get_title($user);
$avatar_field = generate_avatar_markup($id);
$avatar_user_card = draw_user_avatar($id);

if ($action == 'readnoti') {
	set_user_notifications_viewed($id);
	confirm_referrer('notifications.php');

	redirect('notifications.php?id='.$id);
} elseif ($action == 'delnoti') {
	delete_user_notifications($id, $viewed = 1);
	confirm_referrer('notifications.php');

	redirect('notifications.php?id='.$id);
}

$viewed_notifications   = array();
$unviewed_notifications = array();

$num_viewed   = has_viewed_notifications();
$num_unviewed = has_unviewed_notifications();

if ($num_viewed) {
	$viewed_notifications = get_user_viewed_notifications();
}

if ($num_unviewed) {
	$unviewed_notifications = get_user_unviewed_notifications();
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']).' / '.__('Profile', 'luna'));
define('LUNA_ACTIVE_PAGE', 'me');
require load_page('header.php');

require load_page('notifications.php');

require load_page('footer.php');