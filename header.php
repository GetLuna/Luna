<?php

/*
 * Copyright (C) 2013-2016 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

// Send no-cache headers
header('Expires: Thu, 21 Jul 1977 07:30:00 GMT'); // When yours truly first set eyes on this world! :)
header('Last-Modified: '.date('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache'); // For HTTP/1.0 compatibility

// Send the Content-type header in case the web server is setup to send something else
header('Content-type: text/html; charset=utf-8');

// Prevent site from being embedded in a frame
$frame_options = defined('LUNA_FRAME_OPTIONS') ? LUNA_FRAME_OPTIONS : 'deny';
header('X-Frame-Options: '.$frame_options);

// Define $p if it's not set to avoid a PHP notice
$p = isset($p) ? $p : null;

// Generate user avatar
$user_avatar = draw_user_avatar($luna_user['id'], true, 'avatar');

// Generate quick links
$page_statusinfo = $page_threadsearches = array();

if (!$luna_user['is_guest']) {
	if (!empty($forum_actions))
		$page_statusinfo[] = '<li>'.implode(' &middot; ', $forum_actions).'</li>';

	if (!empty($thread_actions))
		$page_statusinfo[] = '<li>'.implode(' &middot; ', $thread_actions).'</li>';

	if ($luna_user['is_admmod']) 	{
		if ($luna_config['o_report_method'] == '0' || $luna_config['o_report_method'] == '2') 		{
			$result_header = $db->query('SELECT 1 FROM '.$db->prefix.'reports WHERE zapped IS NULL') or error('Unable to fetch reports info', __FILE__, __LINE__, $db->error());

			if ($db->result($result_header))
				$page_statusinfo[] = '<li class="reportlink"><strong><a href="backstage/reports.php">'.__('New reports', 'luna').'</a></strong></li>';
		}

		if ($luna_config['o_maintenance'] == '1')
			$page_statusinfo[] = '<li class="maintenancelink"><strong><a href="backstage/settings.php#maintenance">'.__('Maintenance mode is enabled', 'luna').'</a></strong></li>';
	}

	if ($luna_user['g_read_board'] == '1' && $luna_user['g_search'] == '1')
		$page_threadsearches[] = '<a class="list-group-item" href="search.php?action=show_new" title="'.__('Show all new comments since your last visit', 'luna').'"><span class="fa fa-fw fa-newspaper-o"></span> '.__('New', 'luna').'</a>';
		$page_threadsearches_inline[] = '<a href="search.php?action=show_new" title="'.__('Show all new comments since your last visit', 'luna').'"><span class="fa fa-fw fa-newspaper-o"></span> '.__('New', 'luna').'</a>';
}

// Quick searches
if ($luna_user['g_read_board'] == '1' && $luna_user['g_search'] == '1') {
	$page_threadsearches[] = '<a class="list-group-item" href="search.php?action=show_recent" title="'.__('Show all active threads', 'luna').'"><span class="fa fa-fw fa-clock-o"></span> '.__('Active', 'luna').'</a>';
	$page_threadsearches[] = '<a class="list-group-item" href="search.php?action=show_unanswered" title="'.__('Show all unanswered threads', 'luna').'"><span class="fa fa-fw fa-question"></span> '.__('Unanswered', 'luna').'</a>';
	$page_threadsearches_inline[] = '<a href="search.php?action=show_recent" title="'.__('Show all active threads', 'luna').'"><span class="fa fa-fw fa-clock-o"></span> '.__('Active', 'luna').'</a>';
	$page_threadsearches_inline[] = '<a href="search.php?action=show_unanswered" title="'.__('Show all unanswered threads', 'luna').'"><span class="fa fa-fw fa-question"></span> '.__('Unanswered', 'luna').'</a>';
}

// Generate all that jazz
$tpl_temp = '<div id="brdwelcome">';

// The status information
if (is_array($page_statusinfo)) {
	$tpl_temp .= "\n\t\t\t".'<ul class="conl">';
	$tpl_temp .= "\n\t\t\t\t".implode("\n\t\t\t\t", $page_statusinfo);
	$tpl_temp .= "\n\t\t\t".'</ul>';
} else
	$tpl_temp .= "\n\t\t\t".$page_statusinfo;

// Generate quicklinks
if (!empty($page_threadsearches)) {
	$tpl_temp .= "\n\t\t\t".'<ul class="conr">';
	$tpl_temp .= "\n\t\t\t\t".'<li>'.implode(' &middot; ', $page_threadsearches_inline).'</li>';
	$tpl_temp .= "\n\t\t\t".'</ul>';
}

$tpl_temp .= '</div>';

// Navbar data
$links = array();
$menu_title = $luna_config['o_board_title'];

$inbox_menu_item = '';  
  
$num_new_pm = 0;
if ($luna_config['o_enable_inbox'] == '1' && $luna_user['g_inbox'] == '1' && $luna_user['use_inbox'] == '1') {
	// Check for new messages
	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'messages WHERE showed=0 AND show_message=1 AND owner='.$luna_user['id']) or error('Unable to check the availibility of new messages', __FILE__, __LINE__, $db->error());
	$num_new_pm = $db->result($result);

	if ($num_new_pm > 0)
		$new_inbox = $num_new_pm.' ';
	else
		$new_inbox = '';

	$inbox_menu_item = '<li><a href="inbox.php"><span class="'.(($num_new_pm > 0)? ' flash' : '').'">'.$new_inbox.'<span class="fa fa-fw fa-paper-plane-o"></span><span class="visible-xs-inline"> '.__( 'Inbox', 'luna' ).'</span></span></a></li>';
}

if (!$luna_user['is_guest']) {
    // Check for new notifications
    $result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'notifications WHERE viewed = 0 AND user_id = '.$luna_user['id']) or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
    $num_notifications = $db->result($result);

    if ($luna_config['o_notification_flyout'] == 1) {
        if ($num_notifications == '0') {
            $notificon = '<span class="fa fa-fw fa-circle-o"></span>';
            $ind_notification[] = '<li><a href="notifications.php">'.__( 'No new notifications', 'luna' ).'</a></li>';
        } else {
            $notificon = $num_notifications.' <span class="fa fa-fw fa-circle"></span>';

            $notification_result = $db->query('SELECT * FROM '.$db->prefix.'notifications WHERE user_id = '.$luna_user['id'].' AND viewed = 0 ORDER BY time DESC LIMIT 10') or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
            while ($cur_notifi = $db->fetch_assoc($notification_result)) {
                $notifitime = format_time($cur_notifi['time'], false, null, $luna_config['o_time_format'], true, true);
                $ind_notification[] = '<li class="overflow"><a href="notifications.php?notification='.$cur_notifi['id'].'"><span class="timestamp">'.$notifitime.'</span> <span class="fa fa-fw '.$cur_notifi['icon'].'"></span> '.$cur_notifi['message'].'</a></li>';
            }
        }

        $notifications = implode('<li class="divider"></li>', $ind_notification);
        $notification_menu_item = '
                        <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="'.(($num_notifications != 0)? ' flash' : '').'">'.$notificon.'<span class="visible-xs-inline"> '.__( 'Notifications', 'luna' ).'</span></span></a>
                        <ul class="dropdown-menu notification-menu">
                            <li role="presentation" class="dropdown-header">'.__( 'Notifications', 'luna' ).'</li>
                            <li class="divider"></li>
                            '.$notifications.'
                            <li class="divider"></li>
                            <li class="dropdown-footer hidden-xs"><a class="pull-right" href="notifications.php">'.__('More', 'luna').' <i class="fa fa-fw fa-arrow-right"></i></a></li>
                            <li class="dropdown-footer hidden-lg hidden-md hidden-sm"><a href="notifications.php">'.__('More', 'luna').' <i class="fa fa-fw fa-arrow-right"></i></a></li>
                        </ul>
                    </li>';
    } else {
        if ($num_notifications == '0')
            $notificon = '<span class="fa fa-fw fa-circle-o"></span>';
        else
            $notificon = $num_notifications.' <span class="fa fa-fw fa-circle"></span>';

        $notification_menu_item = '<li><a href="notifications.php" class="'.(($num_notifications != 0)? ' flash' : '').'">'.$notificon.'<span class="visible-xs-inline"> '.__( 'Notifications', 'luna' ).'</span></a></li>';
    }
}

// Generate navigation items
if (!$luna_user['is_admmod'])
	$backstage = '';
else
	$backstage = '<li><a href="backstage/"><span class="fa fa-fw fa-tachometer"></span><span class="visible-xs-inline"> '.__( 'Backstage', 'luna' ).'</span></a></li>';

$result = $db->query('SELECT id, url, name, disp_position, visible FROM '.$db->prefix.'menu ORDER BY disp_position') or error('Unable to fetch menu items', __FILE__, __LINE__, $db->error());

if ($luna_user['is_guest'])
	$usermenu = '<li id="navregister"'.((LUNA_ACTIVE_PAGE == 'register') ? ' class="active"' : '').'><a href="register.php">'.__( 'Register', 'luna' ).'</a></li>  
				 <li><a href="#" data-toggle="modal" data-target="#login-form">'.__( 'Login', 'luna' ).'</a></li>';
else
	$usermenu = $backstage.$inbox_menu_item.$notification_menu_item.'
				<li class="dropdown">
					<a href="#" class="dropdown-toggle avatar-item" data-toggle="dropdown"><i class="fa fa-fw fa-user"></i><span class="visible-xs-inline"> '.luna_htmlspecialchars($luna_user['username']).'</span> <i class="fa fa-fw fa-angle-down"></i></a>
					<ul class="dropdown-menu">
						<li><a href="profile.php?id='.$luna_user['id'].'"><i class="fa fa-fw fa-user"></i> '.__( 'Profile', 'luna' ).'</a></li>
						<li><a href="settings.php"><i class="fa fa-fw fa-cogs"></i> '.__( 'Settings', 'luna' ).'</a></li>
						<li class="divider"></li>
						<li><a href="help.php"><i class="fa fa-fw fa-info-circle"></i> '.__( 'Help', 'luna' ).'</a></li>
						<li class="divider"></li>
						<li><a href="login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_csrf_token().'"><i class="fa fa-fw fa-sign-out"></i> '.__( 'Logout', 'luna' ).'</a></li>
					</ul>
				</li>
	';

if ($db->num_rows($result) > 0)
	while ($cur_item = $db->fetch_assoc($result))
		if ($cur_item['visible'] == '1')
			$links[] = '<li><a href="'.$cur_item['url'].'">'.$cur_item['name'].'</a></li>';
