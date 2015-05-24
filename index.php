<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';

if ($luna_user['g_read_board'] == '0')
	message(__('You do not have permission to view this page.', 'luna'), false, '403 Forbidden');

// Get list of forums and topics with new posts since last visit
if (!$luna_user['is_guest']) {
	$result = $db->query('SELECT f.id, f.last_post FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.last_post>'.$luna_user['last_visit']) or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

	if ($db->num_rows($result)) {
		$forums = $new_topics = array();
		$tracked_topics = get_tracked_topics();

		while ($cur_forum = $db->fetch_assoc($result)) {
			if (!isset($tracked_topics['forums'][$cur_forum['id']]) || $tracked_topics['forums'][$cur_forum['id']] < $cur_forum['last_post'])
				$forums[$cur_forum['id']] = $cur_forum['last_post'];
		}

		if (!empty($forums)) {
			if (empty($tracked_topics['topics']))
				$new_topics = $forums;
			else {
				$result = $db->query('SELECT forum_id, id, last_post FROM '.$db->prefix.'topics WHERE forum_id IN('.implode(',', array_keys($forums)).') AND last_post>'.$luna_user['last_visit'].' AND moved_to IS NULL') or error('Unable to fetch new topics', __FILE__, __LINE__, $db->error());

				while ($cur_topic = $db->fetch_assoc($result)) {
					if (!isset($new_topics[$cur_topic['forum_id']]) && (!isset($tracked_topics['forums'][$cur_topic['forum_id']]) || $tracked_topics['forums'][$cur_topic['forum_id']] < $forums[$cur_topic['forum_id']]) && (!isset($tracked_topics['topics'][$cur_topic['id']]) || $tracked_topics['topics'][$cur_topic['id']] < $cur_topic['last_post']))
						$new_topics[$cur_topic['forum_id']] = $forums[$cur_topic['forum_id']];
				}
			}
		}
	}
}

if ($luna_config['o_feed_type'] == '1')
	$page_head = array('feed' => '<link rel="alternate" type="application/rss+xml" href="extern.php?action=feed&amp;type=rss" title="'.__('RSS active topics feed', 'luna').'" />');
elseif ($luna_config['o_feed_type'] == '2')
	$page_head = array('feed' => '<link rel="alternate" type="application/atom+xml" href="extern.php?action=feed&amp;type=atom" title="'.__('Atom active topics feed', 'luna').'" />');

$forum_actions = array();

// Someone clicked "Do not show again"
$action = isset($_GET['action']) ? $_GET['action'] : null;

if ($action == 'do_not_show') {
	confirm_referrer(array('index.php', ''));

	$db->query('UPDATE '.$db->prefix.'users SET first_run = 1 WHERE id='.$luna_user['id']) or error('Unable to disable first run', __FILE__, __LINE__, $db->error());

	redirect('index.php');
}

// Or want to disable the cookiebar
if ($action == 'disable_cookiebar') {
	luna_cookiebarcookie();

	redirect('index.php');
}

if ($id != 0) {
	// Fetch some info about the forum
	if (!$luna_user['is_guest'])
		$result = $db->query('SELECT f.forum_name, f.moderators, f.num_topics, f.sort_by, f.color, fp.post_topics, s.user_id AS is_subscribed FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_subscriptions AS s ON (f.id=s.forum_id AND s.user_id='.$luna_user['id'].') LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$id) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());
	else
		$result = $db->query('SELECT f.forum_name, f.moderators, f.num_topics, f.sort_by, f.color, fp.post_topics, 0 AS is_subscribed FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$id) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());
	
	if (!$db->num_rows($result))
		message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');
	
	$cur_forum = $db->fetch_assoc($result);
	
	// Sort out who the moderators are and if we are currently a moderator (or an admin)
	$mods_array = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();
	$is_admmod = ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && array_key_exists($luna_user['username'], $mods_array))) ? true : false;
	
	switch ($cur_forum['sort_by']) {
		case 0:
			$sort_by = 'last_post DESC';
			break;
		case 1:
			$sort_by = 'posted DESC';
			break;
		case 2:
			$sort_by = 'subject ASC';
			break;
		default:
			$sort_by = 'last_post DESC';
			break;
	}
	
	// Can we or can we not post new topics?
	if (($cur_forum['post_topics'] == '' && $luna_user['g_post_topics'] == '1') || $cur_forum['post_topics'] == '1' || $is_admmod)
		$post_link = "\t\t\t".'<a class="btn btn-primary btn-post pull-right" href="post.php?fid='.$id.'">'.__('Post topic', 'luna').'</a>'."\n";
	else
		$post_link = '';
	
	// Get topic/forum tracking data
	if (!$luna_user['is_guest'])
		$tracked_topics = get_tracked_topics();
	
	// Determine the topic offset (based on $_GET['p'])
	$num_pages = ceil($cur_forum['num_topics'] / $luna_user['disp_topics']);
	
	$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : intval($_GET['p']);
	$start_from = $luna_user['disp_topics'] * ($p - 1);
	
	// Generate paging links
	$paging_links = paginate($num_pages, $p, 'index.php?id='.$id);
	
	if ($luna_config['o_feed_type'] == '1')
		$page_head = array('feed' => '<link rel="alternate" type="application/rss+xml" href="extern.php?action=feed&amp;fid='.$id.'&amp;type=rss" title="'.__('RSS forum feed', 'luna').'" />');
	elseif ($luna_config['o_feed_type'] == '2')
		$page_head = array('feed' => '<link rel="alternate" type="application/atom+xml" href="extern.php?action=feed&amp;fid='.$id.'&amp;type=atom" title="'.__('Atom forum feed', 'luna').'" />');
	
	$forum_actions = array();
	
	if (!$luna_user['is_guest']) {
		if ($luna_config['o_forum_subscriptions'] == '1') {
			if ($cur_forum['is_subscribed'])
				$forum_actions[] = '<a href="misc.php?action=unsubscribe&amp;fid='.$id.'">'.__('Unsubscribe', 'luna').'</a>';
			else
				$forum_actions[] = '<a href="misc.php?action=subscribe&amp;fid='.$id.'">'.__('Subscribe', 'luna').'</a>';
		}
	
		$forum_actions[] = '<a href="misc.php?action=markforumread&amp;fid='.$id.'">'.__('Mark as read', 'luna').'</a>';
	}
	
	$forum_id = $id;
	$footer_style = 'viewforum';
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']));
define('FORUM_ALLOW_INDEX', 1);
define('FORUM_ACTIVE_PAGE', 'index');
$footer_style = 'index';

require load_page('header.php');

require load_page('index.php');

require load_page('footer.php');
