<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
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
$id = $luna_user['id'];
if ($id < 2)
	message($lang['Bad request'], false, '404 Not Found');

$result = $db->query('SELECT u.username, u.email, u.title, u.realname, u.url, u.facebook, u.msn, u.twitter, u.google, u.location, u.signature, u.disp_topics, u.disp_posts, u.email_setting, u.notify_with_post, u.auto_notify, u.show_smilies, u.show_img, u.show_img_sig, u.show_avatars, u.show_sig, u.timezone, u.dst, u.language, u.style, u.num_posts, u.last_post, u.registered, u.registration_ip, u.admin_note, u.date_format, u.time_format, u.last_visit, u.color_scheme, g.g_id, g.g_user_title, g.g_moderator FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message($lang['Bad request'], false, '404 Not Found');

$user = $db->fetch_assoc($result);

$user_username = luna_htmlspecialchars($user['username']);
$avatar_field = generate_avatar_markup($id);
$avatar_user_card = draw_user_avatar($id, 'visible-lg-block');

if ($action == 'readnoti') {
	$db->query('UPDATE '.$db->prefix.'notifications SET viewed = 1 WHERE user_id = '.$id.' AND viewed = 0') or error('Unable to update the notification status', __FILE__, __LINE__, $db->error());
	confirm_referrer('notifications.php');

	redirect('notifications.php?id='.$id);
} elseif ($action == 'delnoti') {
	$db->query('DELETE FROM '.$db->prefix.'notifications WHERE viewed = 1') or error('Unable to remove notifications', __FILE__, __LINE__, $db->error());
	confirm_referrer('notifications.php');

	redirect('notifications.php?id='.$id);
}

// Show notifications
$result = $db->query('SELECT COUNT(id) FROM '.$db_prefix.'notifications WHERE viewed = 0 AND user_id = '.$id) or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
$num_not_unseen = $db->result($result);

if ($num_not_unseen == '0')
	$ind_not[] = '<a class="list-group-item disabled" href="notifications.php?id='.$id.'">'.$lang['No new notifications'].'</a>';
else {
	$result = $db->query('SELECT * FROM '.$db_prefix.'notifications WHERE viewed = 0 AND user_id = '.$id.' ORDER BY time DESC') or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
	while ($cur_notifi = $db->fetch_assoc($result)) {
		$notifitime = format_time($cur_notifi['time'], false, null, $luna_config['o_time_format'], true, true);
		$ind_not[] = '<a class="list-group-item" href="'.$cur_notifi['link'].'"><span class="fa fa-fw luni luni-fw '.$cur_notifi['icon'].'"></span> '.$cur_notifi['message'].' <span class="timestamp pull-right">'.$notifitime.'</span></a>';
	}
}

$result = $db->query('SELECT COUNT(id) FROM '.$db_prefix.'notifications WHERE viewed = 1 AND user_id = '.$id) or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
$num_not_seen = $db->result($result);

if ($num_not_seen == '0')
	$ind_not_seen[] = '<a class="list-group-item disabled" href="notifications.php?id='.$id.'">'.$lang['No new notifications'].'</a>';
else {
	$result = $db->query('SELECT * FROM '.$db_prefix.'notifications WHERE viewed = 1 AND user_id = '.$id.' ORDER BY time DESC') or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
	while ($cur_notifi = $db->fetch_assoc($result)) {
		$notifitime = format_time($cur_notifi['time'], false, null, $luna_config['o_time_format'], true, true);
		$ind_not_seen[] = '<a class="list-group-item" href="'.$cur_notifi['link'].'"><span class="fa fa-fw '.$cur_notifi['icon'].'"></span> '.$cur_notifi['message'].' <span class="timestamp pull-right">'.$notifitime.'</span></a>';
	}
}

$not = implode('', $ind_not);
$not_seen = implode('', $ind_not_seen);

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']).' / '.$lang['Profile']);
define('FORUM_ACTIVE_PAGE', 'me');
require load_page('header.php');

require load_page('notifications.php');

require load_page('footer.php');