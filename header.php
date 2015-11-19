<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
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
				$page_statusinfo[] = '<li class="reportlink"><strong><a href="backstage/reports.php">New reports</a></strong></li>';
		}

		if ($luna_config['o_maintenance'] == '1')
			$page_statusinfo[] = '<li class="maintenancelink"><strong><a href="backstage/settings.php#maintenance">Maintenance mode is enabled</a></strong></li>';
	}

	if ($luna_user['g_read_board'] == '1' && $luna_user['g_search'] == '1')
		$page_threadsearches[] = '<a class="list-group-item" href="search.php?action=show_new" title="'.__('New', 'luna').'"><span class="fa fa-fw fa-newspaper-o"></span> '.__('New', 'luna').'</a>';
		$page_threadsearches_inline[] = '<a href="search.php?action=show_new" title="'.__('New', 'luna').'"><span class="fa fa-fw fa-newspaper-o"></span> '.__('New', 'luna').'</a>';
}

// Quick searches
if ($luna_user['g_read_board'] == '1' && $luna_user['g_search'] == '1') {
	$page_threadsearches[] = '<a class="list-group-item" href="search.php?action=show_recent" title="'.__('Active', 'luna').'"><span class="fa fa-fw fa-clock-o"></span> '.__('Active', 'luna').'</a>';
	$page_threadsearches[] = '<a class="list-group-item" href="search.php?action=show_unanswered" title="'.__('Unanswered', 'luna').'"><span class="fa fa-fw fa-question"></span> '.__('Unanswered', 'luna').'</a>';
	$page_threadsearches_inline[] = '<a href="search.php?action=show_recent" title="'.__('Active', 'luna').'"><span class="fa fa-fw fa-clock-o"></span> '.__('Active', 'luna').'</a>';
	$page_threadsearches_inline[] = '<a href="search.php?action=show_unanswered" title="'.__('Unanswered', 'luna').'"><span class="fa fa-fw fa-question"></span> '.__('Unanswered', 'luna').'</a>';
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

$result = $db->query('SELECT id, url, name, disp_position, visible FROM '.$db->prefix.'menu ORDER BY disp_position') or error('Unable to fetch menu items', __FILE__, __LINE__, $db->error());

if ($db->num_rows($result) > 0)
	while ($cur_item = $db->fetch_assoc($result))
		if ($cur_item['visible'] == '1')
			$links[] = '<li><a href="'.$cur_item['url'].'">'.$cur_item['name'].'</a></li>';