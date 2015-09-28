<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';
define('FORUM_CANONICAL_TAG_TOPIC', 1);

if ($luna_user['g_read_board'] == '0')
	message(__('You do not have permission to view this page.', 'luna'), false, '403 Forbidden');

$action = isset($_GET['action']) ? $_GET['action'] : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
if ($id < 1 && $pid < 1)
	message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

// If a comment ID is specified we determine topic ID and page number so we can redirect to the correct message
if ($pid) {
	$result = $db->query('SELECT topic_id, posted FROM '.$db->prefix.'posts WHERE id='.$pid) or error('Unable to fetch topic ID', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

	list($id, $posted) = $db->fetch_row($result);

	// Determine on which page the comment is located (depending on $forum_user['disp_posts'])
	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'posts WHERE topic_id='.$id.' AND posted<'.$posted) or error('Unable to count previous posts', __FILE__, __LINE__, $db->error());
	$num_posts = $db->result($result) + 1;

	$_GET['p'] = ceil($num_posts / $luna_user['disp_posts']);
} else {
	// If action=new, we redirect to the first new comment (if any)
	if ($action == 'new') {
		if (!$luna_user['is_guest']) {
			// We need to check if this thread has been viewed recently by the user
			$tracked_topics = get_tracked_topics();
			$last_viewed = isset($tracked_topics['topics'][$id]) ? $tracked_topics['topics'][$id] : $luna_user['last_visit'];

			$result = $db->query('SELECT MIN(id) FROM '.$db->prefix.'posts WHERE topic_id='.$id.' AND posted>'.$last_viewed) or error('Unable to fetch first new comment info', __FILE__, __LINE__, $db->error());
			$first_new_post_id = $db->result($result);

			if ($first_new_post_id) {
				header('Location: viewtopic.php?pid='.$first_new_post_id.'#p'.$first_new_post_id);
				exit;
			}
		}

		// If there is no new comment, we go to the last comment
		$action = 'last';
	}

	// If action=last, we redirect to the last comment
	if ($action == 'last') {
		$result = $db->query('SELECT MAX(id) FROM '.$db->prefix.'posts WHERE topic_id='.$id) or error('Unable to fetch last comment info', __FILE__, __LINE__, $db->error());
		$last_post_id = $db->result($result);


		if ($last_post_id) {
			header('Location: viewtopic.php?pid='.$last_post_id.'#p'.$last_post_id);
			exit;
		}
	}
}


// Fetch some info about the thread
if ($luna_user['is_guest'])
	$result = $db->query('SELECT t.subject, t.poster, t.closed, t.num_replies, t.sticky, t.solved AS answer, t.first_post_id, f.id AS forum_id, f.forum_name, f.moderators, fp.post_replies FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());
else
	$result = $db->query('SELECT t.subject, t.poster, t.closed, t.num_replies, t.sticky, t.solved AS answer, t.first_post_id, f.id AS forum_id, f.forum_name, f.moderators, fp.post_replies, s.user_id AS is_subscribed FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'topic_subscriptions AS s ON (t.id=s.topic_id AND s.user_id='.$luna_user['id'].') LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());

if (!$db->num_rows($result))
	message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

$cur_topic = $db->fetch_assoc($result);
$started_by = $cur_topic['poster'];

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_topic['moderators'] != '') ? unserialize($cur_topic['moderators']) : array();
$is_admmod = ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && array_key_exists($luna_user['username'], $mods_array))) ? true : false;
if ($is_admmod)
$admin_ids = get_admin_ids();

if ($cur_topic['closed'] == '0') {
	if (($cur_topic['post_replies'] == '' && $luna_user['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1' || $is_admmod)
		$post_link = "\t\t\t".'<a class="btn btn-primary btn-post" href="post.php?tid='.$id.'">'.__('Comment', 'luna').'</a>'."\n";
	else
		$post_link = '';
} else {
	$post_link = '<a class="btn disabled btn-danger btn-post"><span class="fa fa-fw fa-lock"></span></a>';

	if ($is_admmod)
		$post_link .= '<a class="btn btn-primary btn-post" href="post.php?tid='.$id.'">'.__('Comment', 'luna').'</a>';

	$post_link = $post_link."\n";
}


// Add/update this thread in our list of tracked topics
if (!$luna_user['is_guest']) {
	$tracked_topics = get_tracked_topics();
	$tracked_topics['topics'][$id] = time();
	set_tracked_topics($tracked_topics);
}


// Determine the comment offset (based on $_GET['p'])
$num_pages = ceil(($cur_topic['num_replies'] + 1) / $luna_user['disp_posts']);

$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : intval($_GET['p']);
$start_from = $luna_user['disp_posts'] * ($p - 1);

// Generate paging links
$paging_links = paginate($num_pages, $p, 'viewtopic.php?id='.$id);

$quickpost = false;
if (($cur_topic['post_replies'] == '1' || ($cur_topic['post_replies'] == '' && $luna_user['g_post_replies'] == '1')) && ($cur_topic['closed'] == '0' || $is_admmod)) {
	$required_fields = array('req_message' => __('Message', 'luna'));
	if ($luna_user['is_guest']) {
		$required_fields['req_username'] = __('Name', 'luna');
		if ($luna_config['p_force_guest_email'] == '1')
			$required_fields['req_email'] = __('Email', 'luna');
	}

	$quickpost = true;
}

if ($luna_config['o_censoring'] == '1')
	$cur_topic['subject'] = censor_words($cur_topic['subject']);

if ($luna_config['o_feed_type'] == '1')
	$page_head = array('feed' => '<link rel="alternate" type="application/rss+xml" href="extern.php?action=feed&amp;tid='.$id.'&amp;type=rss" title="'.__('RSS thread feed', 'luna').'" />');
elseif ($luna_config['o_feed_type'] == '2')
	$page_head = array('feed' => '<link rel="alternate" type="application/atom+xml" href="extern.php?action=feed&amp;tid='.$id.'&amp;type=atom" title="'.__('Atom thread feed', 'luna').'" />');

$topic_actions = array();

if (!$luna_user['is_guest'] && $luna_config['o_topic_subscriptions'] == '1') {
	if ($cur_topic['is_subscribed'])
		$topic_actions[] = '<a href="misc.php?action=unsubscribe&amp;tid='.$id.'">'.__('Unsubscribe', 'luna').'</a>';
	else
		$topic_actions[] = '<a href="misc.php?action=subscribe&amp;tid='.$id.'">'.__('Subscribe', 'luna').'</a>';
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), luna_htmlspecialchars($cur_topic['forum_name']), luna_htmlspecialchars($cur_topic['subject']));
if (!$pid)
	define('FORUM_ALLOW_INDEX', 1);
define('FORUM_ACTIVE_PAGE', 'viewtopic');
require load_page('header.php');

require FORUM_ROOT.'include/parser.php';

$post_count = 0; // Keep track of comment numbers

// Retrieve a list of comment IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
if (!$luna_user['is_admmod'])
	$result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE soft = 0 AND topic_id='.$id.' ORDER BY id LIMIT '.$start_from.','.$luna_user['disp_posts']) or error('Unable to fetch post IDs', __FILE__, __LINE__, $db->error());
else
	$result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE topic_id='.$id.' ORDER BY id LIMIT '.$start_from.','.$luna_user['disp_posts']) or error('Unable to fetch post IDs', __FILE__, __LINE__, $db->error());

$post_ids = array();
for ($i = 0;$cur_post_id = $db->result($result, $i);$i++)
	$post_ids[] = $cur_post_id;

if (empty($post_ids))
	error('The comment table and topic table seem to be out of sync!', __FILE__, __LINE__);

$cur_index = 1;

require load_page('thread.php');

// Increment "num_views" for topic
if ($luna_config['o_topic_views'] == '1')
	$db->query('UPDATE '.$db->prefix.'topics SET num_views=num_views+1 WHERE id='.$id) or error('Unable to update topic', __FILE__, __LINE__, $db->error());

$forum_id = $cur_topic['forum_id'];
$footer_style = 'viewtopic';

require load_page('footer.php');
