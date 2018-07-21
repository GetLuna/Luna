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

include(LUNA_ROOT.'/include/class/notification.class.php');
include(LUNA_ROOT.'/include/class/menu.class.php');

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

// Navbar data
$menu_title = $luna_config['o_board_title'];

$inbox_menu_item = '';

$num_new_pm = 0;
if ($luna_config['o_enable_inbox'] == '1' && $luna_user['g_inbox'] == '1' && $luna_user['use_inbox'] == '1') {
    // Check for new messages
    $result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'messages WHERE showed=0 AND show_message=1 AND owner='.$luna_user['id']) or error('Unable to check the availibility of new messages', __FILE__, __LINE__, $db->error());
    $num_new_pm = $db->result($result);

    if ($num_new_pm > 0) {
        $inbox_count = $num_new_pm.' ';
    } else {
        $inbox_count = '';
    }
}

if (!$luna_user['is_guest']) {
    $notifications = array();

    // Check for new notifications
    $result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'notifications WHERE viewed = 0 AND user_id = '.$luna_user['id']) or error('Unable to load notifications', __FILE__, __LINE__, $db->error());
    $notification_count = $db->result($result);
    
    $notification_result = $db->query('SELECT * FROM '.$db->prefix.'notifications WHERE user_id = '.$luna_user['id'].' AND viewed = 0 ORDER BY time DESC LIMIT 10') or error('Unable to load notifications', __FILE__, __LINE__, $db->error());

    $profile_url = 'profile.php?id='.$luna_user['id'];
    $settings_url = 'settings.php?id='.$luna_user['id'];
    $logout_url = 'login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_csrf_token();

    while ($row = $db->fetch_assoc($notification_result)) {
        $notifications[] = Notification::withRow($row);
    }

    $notification_count = count($notifications);

    if ($notification_count == '0') {
        $notificon = '<i class="far fa-fw fa-circle"></i>';
    } else {
        $notificon = $notification_count.' <i class="fas fa-fw fa-circle"></i>';
    }
}

$menu = new Menu( false );