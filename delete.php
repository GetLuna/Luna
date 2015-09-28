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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1)
	message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

$action = isset($_GET['action']) ? $_GET['action'] : 0;

// Fetch some info about the comment, the thread and the forum
$result = $db->query('SELECT f.id AS fid, f.forum_name, f.moderators, fp.post_replies, fp.post_topics, t.id AS tid, t.subject, t.first_post_id, t.closed, p.posted, p.poster, p.poster_id, p.message, p.hide_smilies FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND p.id='.$id) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

$cur_post = $db->fetch_assoc($result);

if ($luna_config['o_censoring'] == '1')
	$cur_post['subject'] = censor_words($cur_post['subject']);

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_post['moderators'] != '') ? unserialize($cur_post['moderators']) : array();
$is_admmod = ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && array_key_exists($luna_user['username'], $mods_array))) ? true : false;

$is_topic_post = ($id == $cur_post['first_post_id']) ? true : false;

// Do we have permission to edit this post?
if (($luna_user['g_delete_posts'] == '0' ||
	($luna_user['g_delete_topics'] == '0' && $is_topic_post) ||
	$cur_post['poster_id'] != $luna_user['id'] ||
	$cur_post['closed'] == '1') &&
	!$is_admmod)
	message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');

if ($is_admmod && $luna_user['g_id'] != FORUM_ADMIN && in_array($cur_post['poster_id'], get_admin_ids()))
	message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');

// Soft delete posts
if (isset($_POST['soft_delete'])) {
	// Make sure they got here from the site
	confirm_referrer('delete.php');

	require FORUM_ROOT.'include/search_idx.php';

	if ($is_topic_post) {
		// Delete the thread and all of its posts
		delete_topic($cur_post['tid'], "soft");
		update_forum($cur_post['fid']);

		redirect('viewforum.php?id='.$cur_post['fid']);
	} else {
		// Delete just this one post
		$db->query('UPDATE '.$db->prefix.'posts SET soft = 1 WHERE id='.$id) or error('Unable to soft delete post', __FILE__, __LINE__, $db->error());
		update_forum($cur_post['fid']);

		// Redirect towards the previous post
		$result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE topic_id='.$cur_post['tid'].' AND id < '.$id.' ORDER BY id DESC LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
		$post_id = $db->result($result);

		redirect('viewtopic.php?pid='.$post_id.'#p'.$post_id);
	}
}

// Soft delete reset
if (isset($_POST['reset'])) {
	// Make sure they got here from the site
	confirm_referrer('delete.php');

	require FORUM_ROOT.'include/search_idx.php';

	if ($is_topic_post) {
		// Reset the thread and all of its posts
		delete_topic($cur_post['tid'], "reset");
		update_forum($cur_post['fid']);

		redirect('viewforum.php?id='.$cur_post['fid']);
	} else {
		// Reset just this one post
		$db->query('UPDATE '.$db->prefix.'posts SET soft = 0 WHERE id='.$id) or error('Unable to soft delete post', __FILE__, __LINE__, $db->error());
		update_forum($cur_post['fid']);

		// Redirect towards the comment
		redirect('viewtopic.php?pid='.$id.'#p'.$id);
	}
}

if (isset($_POST['delete'])) {
	// Make sure they got here from the site
	confirm_referrer('delete.php');

	require FORUM_ROOT.'include/search_idx.php';

	if ($is_topic_post) {
		// Delete the thread and all of its posts
		delete_topic($cur_post['tid'], "hard");
		update_forum($cur_post['fid']);

		redirect('viewforum.php?id='.$cur_post['fid']);
	} else {
		// Delete just this one post
		delete_post($id, $cur_post['tid'], $cur_post['poster_id']);
		update_forum($cur_post['fid']);

		// Redirect towards the previous post
		$result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE topic_id='.$cur_post['tid'].' AND id < '.$id.' ORDER BY id DESC LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
		$post_id = $db->result($result);

		redirect('viewtopic.php?pid='.$post_id.'#p'.$post_id);
	}
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Delete comment', 'luna'));
define ('FORUM_ACTIVE_PAGE', 'delete');

require FORUM_ROOT.'include/parser.php';
$cur_post['message'] = parse_message($cur_post['message']);

require load_page('header.php');

if ($action == "reset")
	require load_page('reset.php');
if ($action == "soft")
	require load_page('soft.php');
if ($action == "delete")
	require load_page('delete.php');

require load_page('footer.php');