<?php

/*
 * Copyright (C) 2013-2018 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM')) {
    exit;
}

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
    if (!empty($forum_actions)) {
        $page_statusinfo[] = '<li>'.implode(' &middot; ', $forum_actions).'</li>';
    }

    if (!empty($thread_actions)) {
        $page_statusinfo[] = '<li>'.implode(' &middot; ', $thread_actions).'</li>';
    }

    if ($luna_user['is_admmod']) {
        if ($luna_config['o_report_method'] == '0' || $luna_config['o_report_method'] == '2') {
            $result_header = $db->query('SELECT 1 FROM '.$db->prefix.'reports WHERE zapped IS NULL') or error('Unable to fetch reports info', __FILE__, __LINE__, $db->error());

            if ($db->result($result_header)) {
                $page_statusinfo[] = '<li class="reportlink"><strong><a href="backstage/reports.php">'.__('New reports', 'luna').'</a></strong></li>';
            }
        }

        if ($luna_config['o_maintenance'] == '1') {
            $page_statusinfo[] = '<li class="maintenancelink"><strong><a href="backstage/settings.php#maintenance">'.__('Maintenance mode is enabled', 'luna').'</a></strong></li>';
        }
    }

    if ($luna_user['g_read_board'] == '1' && $luna_user['g_search'] == '1') {
        $page_threadsearches[] = '<a class="list-group-item" href="search.php?action=show_new" title="'.__('Show all new comments since your last visit', 'luna').'"><span class="fas fa-fw fa-newspaper"></span> '.__('New', 'luna').'</a>';
    }

    $page_threadsearches_inline[] = '<a href="search.php?action=show_new" title="'.__('Show all new comments since your last visit', 'luna').'"><span class="fas fa-fw fa-newspaper"></span> '.__('New', 'luna').'</a>';
}

// Quick searches
if ($luna_user['g_read_board'] == '1' && $luna_user['g_search'] == '1') {
    $page_threadsearches[] = '<a class="list-group-item" href="search.php?action=show_recent" title="'.__('Show all active threads', 'luna').'"><span class="fas fa-fw fa-clock"></span> '.__('Active', 'luna').'</a>';
    $page_threadsearches[] = '<a class="list-group-item" href="search.php?action=show_unanswered" title="'.__('Show all unanswered threads', 'luna').'"><span class="fas fa-fw fa-question"></span> '.__('Unanswered', 'luna').'</a>';
    $page_threadsearches_inline[] = '<a href="search.php?action=show_recent" title="'.__('Show all active threads', 'luna').'"><span class="fas fa-fw fa-clock"></span> '.__('Active', 'luna').'</a>';
    $page_threadsearches_inline[] = '<a href="search.php?action=show_unanswered" title="'.__('Show all unanswered threads', 'luna').'"><span class="fas fa-fw fa-question"></span> '.__('Unanswered', 'luna').'</a>';
}

// The status information
if (is_array($page_statusinfo)) {
    $tpl_temp .= '<ul class="conl">';
    $tpl_temp .= implode('', $page_statusinfo);
    $tpl_temp .= '</ul>';
} else {
    $tpl_temp .= $page_statusinfo;
}

// Generate quicklinks
if (!empty($page_threadsearches)) {
    $tpl_temp .= '<ul class="conr">';
    $tpl_temp .= '<li>'.implode(' &middot; ', $page_threadsearches_inline).'</li>';
    $tpl_temp .= '</ul>';
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

    if ($num_new_pm > 0) {
        $new_inbox = $num_new_pm.' ';
    } else {
        $new_inbox = '';
    }

    $inbox_menu_item = '<li class="nav-item"><a class="nav-link" href="inbox.php"><span'.(($num_new_pm > 0) ? ' class="flash"' : '').'>'.$new_inbox.'<i class="fas fa-fw fa-paper-plane"></i><span class="d-inline d-md-none"> '.__('Inbox', 'luna').'</span></span></a></li>';
}

if (!$luna_user['is_guest']) {
    // Check for new notifications
    $result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'notifications WHERE viewed = 0 AND user_id = '.$luna_user['id']) or error('Unable to load notifications', __FILE__, __LINE__, $db->error());
    $num_notifications = $db->result($result);

    if ($luna_config['o_notification_flyout'] == 1) {
        if ($num_notifications == '0') {
            $notificon = '<span class="far fa-fw fa-circle"></span>';
            $ind_notification[] = '<a class="dropdown-item" href="notifications.php">'.__('No new notifications', 'luna').'</a>';
        } else {
            $notificon = $num_notifications.' <span class="fas fa-fw fa-circle"></span>';

            $notification_result = $db->query('SELECT * FROM '.$db->prefix.'notifications WHERE user_id = '.$luna_user['id'].' AND viewed = 0 ORDER BY time DESC LIMIT 10') or error('Unable to load notifications', __FILE__, __LINE__, $db->error());
            while ($cur_notifi = $db->fetch_assoc($notification_result)) {
                $notifitime = format_time($cur_notifi['time'], false, null, $luna_config['o_time_format'], true, true);
                $ind_notification[] = '<a class="dropdown-item" href="notifications.php?notification='.$cur_notifi['id'].'"><span class="timestamp">'.$notifitime.'</span> <span class="fas fa-fw '.$cur_notifi['icon'].'"></span> '.$cur_notifi['message'].'</a>';
            }
        }

        $notifications = implode('', $ind_notification);
        $notification_menu_item = '
            <li class="nav-item dropdown dropdown-notifications">
                <a class="nav-link dropdown-toggle" href="#" id="notificationMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="'.(($num_notifications != 0) ? 'flash' : '').'">'.$notificon.'<span class="d-inline d-md-none"> '.__('Notifications', 'luna').'</span></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationMenu">
                    <h6 class="dropdown-header">'.__('Notifications', 'luna').'</h6>
                    <div class="dropdown-divider"></div>
                    '.$notifications.'
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item float-right" href="notifications.php">'.__('More', 'luna').' <i class="fas fa-fw fa-arrow-right"></i></a>
                </div>
            </li>';
    } else {
        if ($num_notifications == '0') {
            $notificon = '<span class="far fa-fw fa-circle"></span>';
        } else {
            $notificon = $num_notifications.' <span class="fas fa-fw fa-circle"></span>';
        }
    
        $notification_menu_item ='
            <li class="nav-item active">
                <a class="nav-link'.(($num_notifications != 0) ? ' flash' : '').'" href="https://getluna.org/docs" class="'.$notificon.'"><span class="d-inline d-sm-none"> '.__('Notifications', 'luna').'</span></a>
            </li>';
    }
}

// Generate navigation items
if (!$luna_user['is_admmod']) {
    $backstage = '';
} else {
    $backstage = '<li class="nav-item"><a class="nav-link" href="backstage/"><span class="fas fa-fw fa-tachometer-alt"></span><span class="d-inline d-md-none"> '.__('Backstage', 'luna').'</span></a></li>';
}

$result = $db->query('SELECT id, url, name, disp_position, visible FROM '.$db->prefix.'menu ORDER BY disp_position') or error('Unable to fetch menu items', __FILE__, __LINE__, $db->error());

if ($luna_user['is_guest']) {
    $usermenu = '<li class="nav-item"><a class="nav-link" href="register.php">'.__('Register', 'luna').'</a></li>
				 <li class="nav-item"><a class="nav-link" href="#" data-toggle="modal" data-target="#login-form">'.__('Login', 'luna').'</a></li>';
} else {
    $usermenu = $backstage.$inbox_menu_item.$notification_menu_item.'
        <li class="nav-item dropdown dropdown-user">
            <a class="nav-link dropdown-toggle" href="profile.php?id='.$luna_user['id'].'" id="profileMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                '.draw_user_avatar($luna_user['id'], true, 'avatar').'
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileMenu">
                <a class="dropdown-item" href="profile.php?id='.$luna_user['id'].'"><i class="fas fa-fw fa-user"></i>'.__('Profile', 'luna').'</a>
                <a class="dropdown-item" href="settings.php?id='.$luna_user['id'].'"><i class="fas fa-fw fa-cogs"></i>'.__('Settings', 'luna').'</a>
                <a class="dropdown-item" href="help.php"><i class="fas fa-fw fa-info-circle"></i>'.__('Help', 'luna').'</a>
                <a class="dropdown-item" href="login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_csrf_token().'"><i class="fas fa-fw fa-sign-out-alt"></i>'.__('Logout', 'luna').'</a>
            </div>
        </li>
	';
}

if ($db->num_rows($result) > 0) {
    while ($cur_item = $db->fetch_assoc($result)) {
        if ($cur_item['visible'] == '1') {
            $links[] = '<li class="nav-item"><a class="nav-link" href="'.$cur_item['url'].'">'.$cur_item['name'].'</a></li>';
        }
    }
}
