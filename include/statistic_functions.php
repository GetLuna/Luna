<?php

/*
 * Copyright (C) 2014 Luna
 * Based on work by PunBB (2002-2009), FluxBB (2009-2012)
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

if (file_exists(FORUM_CACHE_DIR.'cache_users_info.php'))
	include FORUM_CACHE_DIR.'cache_users_info.php';

if (!defined('FORUM_USERS_INFO_LOADED')) {
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_users_info_cache();
	require FORUM_CACHE_DIR.'cache_users_info.php';
}

// Collect some statistics from the database
$result = $db->query('SELECT SUM(num_topics), SUM(num_posts) FROM '.$db->prefix.'forums') or error('Unable to fetch topic/post count', __FILE__, __LINE__, $db->error());
list($stats['total_topics'], $stats['total_posts']) = array_map('intval', $db->fetch_row($result));

function total_users() {
	global $lang, $stats;

	printf($lang['No of users'], '<strong>'.forum_number_format($stats['total_users']).'</strong>');
}

function total_topics() {
	global $lang, $stats;

	printf($lang['No of topics'], '<strong>'.forum_number_format($stats['total_topics']).'</strong>');
}

function total_posts() {
	global $lang, $stats;

	printf($lang['No of post'], '<strong>'.forum_number_format($stats['total_posts']).'</strong>');
}

function newest_user() {
	global $lang, $stats, $luna_user;

	if ($luna_user['g_view_users'] == '1')
		$stats['newest_user'] = '<a href="profile.php?id='.$stats['last_user']['id'].'">'.luna_htmlspecialchars($stats['last_user']['username']).'</a>';
	else
		$stats['newest_user'] = luna_htmlspecialchars($stats['last_user']['username']);

	printf($lang['Newest user'], $stats['newest_user']);
}

function users_online() {
	global $lang;

	printf($lang['Users online'], '<strong>'.forum_number_format(num_users_online()).'</strong>');
}

function guests_online() {
	global $lang;

	printf($lang['Guests online'], '<strong>'.forum_number_format(num_guests_online()).'</strong>');
}

function online_list() {
	global $lang, $luna_config, $db, $luna_user;

	if ($luna_config['o_users_online'] == '1') {
	
		// Fetch users online info and generate strings for output
		$result = $db->query('SELECT user_id, ident FROM '.$db->prefix.'online WHERE idle=0 AND user_id>1 ORDER BY ident', true) or error('Unable to fetch online list', __FILE__, __LINE__, $db->error());
	
		if ($db->num_rows($result) > 0) {
			echo "\n\t\t\t\t".'<div class="row"><span class="users-online"><strong>'.$lang['Online'].' </strong>';
	
			$ctr = 1;
			while ($luna_user_online = $db->fetch_assoc($result)) {
				if ($luna_user['g_view_users'] == '1')
					echo "\n\t\t\t\t".'<a href="profile.php?id='.$luna_user_online['user_id'].'">'.luna_htmlspecialchars($luna_user_online['ident']).'</a>';
				else
					echo "\n\t\t\t\t".luna_htmlspecialchars($luna_user_online['ident']);
	
				if ($ctr < $num_users_online) echo ', '; $ctr++;
			}
		
			echo "\n\t\t\t\t".'</span></div>';
		}
	
	}
}