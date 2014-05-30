<?php

/**
 * Copyright (C) 2013-2014 ModernBB Group
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License under GPLv3
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';


if ($luna_user['g_read_board'] == '0')
	message($lang['No view'], false, '403 Forbidden');


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1)
	message($lang['Bad request'], false, '404 Not Found');

// Fetch some info about the forum
if (!$luna_user['is_guest'])
	$result = $db->query('SELECT f.forum_name, f.redirect_url, f.moderators, f.num_topics, f.sort_by, fp.post_topics, s.user_id AS is_subscribed FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_subscriptions AS s ON (f.id=s.forum_id AND s.user_id='.$luna_user['id'].') LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$id) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());
else
	$result = $db->query('SELECT f.forum_name, f.redirect_url, f.moderators, f.num_topics, f.sort_by, fp.post_topics, 0 AS is_subscribed FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$id) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());

if (!$db->num_rows($result))
	message($lang['Bad request'], false, '404 Not Found');

$cur_forum = $db->fetch_assoc($result);

// Is this a redirect forum? In that case, redirect!
if ($cur_forum['redirect_url'] != '')
{
	header('Location: '.$cur_forum['redirect_url']);
	exit;
}

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();
$is_admmod = ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && array_key_exists($luna_user['username'], $mods_array))) ? true : false;

switch ($cur_forum['sort_by'])
{
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
	$post_link = "\t\t\t".'<a class="btn btn-primary btn-post pull-right" href="post.php?fid='.$id.'">'.$lang['Post new topic'].'</a>'."\n";
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
$paging_links = paginate($num_pages, $p, 'viewforum.php?id='.$id);

if ($luna_config['o_feed_type'] == '1')
	$page_head = array('feed' => '<link rel="alternate" type="application/rss+xml" href="extern.php?action=feed&amp;fid='.$id.'&amp;type=rss" title="'.$lang['RSS forum feed'].'" />');
else if ($luna_config['o_feed_type'] == '2')
	$page_head = array('feed' => '<link rel="alternate" type="application/atom+xml" href="extern.php?action=feed&amp;fid='.$id.'&amp;type=atom" title="'.$lang['Atom forum feed'].'" />');

$forum_actions = array();

if (!$luna_user['is_guest'])
{
	if ($luna_config['o_forum_subscriptions'] == '1')
	{
		if ($cur_forum['is_subscribed'])
			$forum_actions[] = '<a href="misc.php?action=unsubscribe&amp;fid='.$id.'">'.$lang['Unsubscribe'].'</a>';
		else
			$forum_actions[] = '<a href="misc.php?action=subscribe&amp;fid='.$id.'">'.$lang['Subscribe'].'</a>';
	}

	$forum_actions[] = '<a href="misc.php?action=markforumread&amp;fid='.$id.'">'.$lang['Mark as read'].'</a>';
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), luna_htmlspecialchars($cur_forum['forum_name']));
define('FORUM_ALLOW_INDEX', 1);
define('FORUM_ACTIVE_PAGE', 'index');
require FORUM_ROOT.'header.php';

require get_view_path('viewforum-header.tpl.php');

// Retrieve a list of topic IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
$result = $db->query('SELECT id FROM '.$db->prefix.'topics WHERE forum_id='.$id.' ORDER BY sticky DESC, '.$sort_by.', id DESC LIMIT '.$start_from.', '.$luna_user['disp_topics']) or error('Unable to fetch topic IDs', __FILE__, __LINE__, $db->error());

// If there are topics in this forum
if ($db->num_rows($result))
{
	$topic_ids = array();
	for ($i = 0; $cur_topic_id = $db->result($result, $i); $i++)
		$topic_ids[] = $cur_topic_id;

	// Fetch list of topics to display on this page
	if ($luna_user['is_guest'] || $luna_config['o_has_posted'] == '0')
	{
		// When not showing a posted label
		$sql = 'SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, last_poster_id, num_views, num_replies, closed, sticky, moved_to FROM '.$db->prefix.'topics WHERE id IN('.implode(',', $topic_ids).') ORDER BY sticky DESC, '.$sort_by.', id DESC';
	}
	else
	{
		// When showing a posted label
		$sql = 'SELECT p.poster_id AS has_posted, t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.last_poster_id, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id AND p.poster_id='.$luna_user['id'].' WHERE t.id IN('.implode(',', $topic_ids).') GROUP BY t.id ORDER BY t.sticky DESC, t.'.$sort_by.', t.id DESC';
	}

	$result = $db->query($sql) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());

	$topic_count = 0;
	while ($cur_topic = $db->fetch_assoc($result)) {

		++$topic_count;
		$status_text = array();
		$item_status = ($topic_count % 2 == 0) ? 'roweven' : 'rowodd';
		$icon_type = 'icon';

		if (is_null($cur_topic['moved_to']))
			if ($luna_user['g_view_users'] == '1' && $cur_topic['last_poster_id'] > '1')
				$last_post = '<a href="viewtopic.php?pid='.$cur_topic['last_post_id'].'#p'.$cur_topic['last_post_id'].'">'.format_time($cur_topic['last_post']).'</a> <span class="byuser">'.$lang['by'].' <a href="profile.php?id='.$cur_topic['last_poster_id'].'">'.luna_htmlspecialchars($cur_topic['last_poster']).'</a></span>';
        	else
				$last_post = '<a href="viewtopic.php?pid='.$cur_topic['last_post_id'].'#p'.$cur_topic['last_post_id'].'">'.format_time($cur_topic['last_post']).'</a> <span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['last_poster']).'</span>';
		else
			$last_post = '';

		if ($luna_config['o_censoring'] == '1')
			$cur_topic['subject'] = censor_words($cur_topic['subject']);

		if ($cur_topic['sticky'] == '1')
		{
			$item_status .= ' isticky';
			$status_text[] = '<span class="label label-success">'.$lang['Sticky'].'</span>';
		}

		if ($cur_topic['moved_to'] != 0)
		{
			$subject = '<a href="viewtopic.php?id='.$cur_topic['moved_to'].'">'.luna_htmlspecialchars($cur_topic['subject']).'</a> <br /><span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
			$status_text[] = '<span class="label label-info">'.$lang['Moved'].'</span>';
			$item_status .= ' imoved';
		}
		else if ($cur_topic['closed'] == '0')
			$subject = '<a href="viewtopic.php?id='.$cur_topic['id'].'">'.luna_htmlspecialchars($cur_topic['subject']).'</a> <br /><span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
		else
		{
			$subject = '<a href="viewtopic.php?id='.$cur_topic['id'].'">'.luna_htmlspecialchars($cur_topic['subject']).'</a> <br /><span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
			$status_text[] = '<span class="label label-danger">'.$lang['Closed'].'</span>';
			$item_status .= ' iclosed';
		}

		if (!$luna_user['is_guest'] && $luna_config['o_has_posted'] == '1')
		{
			if ($cur_topic['has_posted'] == $luna_user['id'])
			{
				$status_text[] = '<span class="glyphicon glyphicon-asterisk"></span>';
				$item_status .= ' iposted';
			}
		}

		if (!$luna_user['is_guest'] && $cur_topic['last_post'] > $luna_user['last_visit'] && (!isset($tracked_topics['topics'][$cur_topic['id']]) || $tracked_topics['topics'][$cur_topic['id']] < $cur_topic['last_post']) && (!isset($tracked_topics['forums'][$id]) || $tracked_topics['forums'][$id] < $cur_topic['last_post']) && is_null($cur_topic['moved_to']))
		{
			$item_status .= ' inew';
			$icon_type = 'icon icon-new';
			$subject = '<strong>'.$subject.'</strong>';
			$subject_new_posts = '<span class="newtext">[ <a href="viewtopic.php?id='.$cur_topic['id'].'&amp;action=new" title="'.$lang['New posts info'].'">'.$lang['New posts'].'</a> ]</span>';
		}
		else
			$subject_new_posts = null;

		// Insert the status text before the subject
		$subject = implode(' ', $status_text).' '.$subject;

		$num_pages_topic = ceil(($cur_topic['num_replies'] + 1) / $luna_user['disp_posts']);

		if ($num_pages_topic > 1)
			$subject_multipage = '<span class="inline-pagination"> '.simple_paginate($num_pages_topic, -1, 'viewtopic.php?id='.$cur_topic['id']).'</span>';
		else
			$subject_multipage = null;

		// Should we show the "New posts" and/or the multipage links?
		if (!empty($subject_new_posts) || !empty($subject_multipage))
		{
			$subject .= !empty($subject_new_posts) ? ' '.$subject_new_posts : '';
			$subject .= !empty($subject_multipage) ? ' '.$subject_multipage : '';
		}

		if (forum_number_format($cur_topic['num_replies']) == '1') {
			$replies_label = $lang['reply'];
		} else {
			$replies_label = $lang['replies'];
		}

		if (forum_number_format($cur_topic['num_views']) == '1') {
			$views_label = $lang['view'];
		} else {
			$views_label = $lang['views'];
		}

		require get_view_path('viewforum-topics_list.tpl.php');

	}

} else {

	require get_view_path('viewforum-topics_list_empty.tpl.php');

}

$forum_id = $id;
$footer_style = 'viewforum';

require get_view_path('viewforum-footer.tpl.php');
