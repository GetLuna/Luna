<?php

/*
 * Copyright (C) 2014-2015 Luna
 * Based on work by PunBB (2002-2009), FluxBB (2009-2012)
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

if (!defined('FORUM_CACHE_DIR'))
	define('FORUM_CACHE_DIR', FORUM_ROOT.'cache/');
	
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


function get_total_users() {
	global $stats;

	return sprintf(forum_number_format($stats['total_users']));
}

function get_total_topics() {
	global $stats;

	return sprintf(forum_number_format($stats['total_topics']));
}

function get_total_posts() {
	global $stats;

	return sprintf(forum_number_format($stats['total_posts']));
}

function total_users() {
	echo get_total_users();
}

function total_topics() {
	echo get_total_topics();
}

function total_posts() {
	echo get_total_posts();
}

function newest_user() {
	global $stats, $luna_user;

	if ($luna_user['g_view_users'] == '1')
		$stats['newest_user'] = '<a href="profile.php?id='.$stats['last_user']['id'].'">'.luna_htmlspecialchars($stats['last_user']['username']).'</a>';
	else
		$stats['newest_user'] = luna_htmlspecialchars($stats['last_user']['username']);

	printf($stats['newest_user']);
}

function users_online() {

	printf(forum_number_format(num_users_online()));
}

function guests_online() {

	printf(forum_number_format(num_guests_online()));
}

function online_list() {
	global $luna_config, $db, $luna_user;

	if ($luna_config['o_users_online'] == '1') {
	
		// Fetch users online info and generate strings for output
		$result = $db->query('SELECT user_id, ident FROM '.$db->prefix.'online WHERE idle=0 AND user_id>1 ORDER BY ident', true) or error('Unable to fetch online list', __FILE__, __LINE__, $db->error());
	
		if ($db->num_rows($result) > 0) {
			$ctr = 1;
			while ($luna_user_online = $db->fetch_assoc($result)) {
				if ($luna_user['g_view_users'] == '1')
					echo "\n\t\t\t\t".'<li><a href="profile.php?id='.$luna_user_online['user_id'].'">'.luna_htmlspecialchars($luna_user_online['ident']).'</a></li>';
				else
					echo "\n\t\t\t\t".'<li>'.luna_htmlspecialchars($luna_user_online['ident']).'</li>';
			}
		} else
			echo '<li><a>'.__('No users online', 'luna').'</a></li>';
	}
}