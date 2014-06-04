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

$action = isset($_GET['action']) ? $_GET['action'] : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
if ($id < 1 && $pid < 1)
	message($lang['Bad request'], false, '404 Not Found');

// If a post ID is specified we determine topic ID and page number so we can redirect to the correct message
if ($pid)
{
	$result = $db->query('SELECT topic_id, posted FROM '.$db->prefix.'posts WHERE id='.$pid) or error('Unable to fetch topic ID', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message($lang['Bad request'], false, '404 Not Found');

	list($id, $posted) = $db->fetch_row($result);

	// Determine on which page the post is located (depending on $forum_user['disp_posts'])
	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'posts WHERE topic_id='.$id.' AND posted<'.$posted) or error('Unable to count previous posts', __FILE__, __LINE__, $db->error());
	$num_posts = $db->result($result) + 1;

	$_GET['p'] = ceil($num_posts / $luna_user['disp_posts']);
}
else
{
	// If action=new, we redirect to the first new post (if any)
	if ($action == 'new')
	{
		if (!$luna_user['is_guest'])
		{
			// We need to check if this topic has been viewed recently by the user
			$tracked_topics = get_tracked_topics();
			$last_viewed = isset($tracked_topics['topics'][$id]) ? $tracked_topics['topics'][$id] : $luna_user['last_visit'];

			$result = $db->query('SELECT MIN(id) FROM '.$db->prefix.'posts WHERE topic_id='.$id.' AND posted>'.$last_viewed) or error('Unable to fetch first new post info', __FILE__, __LINE__, $db->error());
			$first_new_post_id = $db->result($result);

			if ($first_new_post_id)
			{
				header('Location: viewtopic.php?pid='.$first_new_post_id.'#p'.$first_new_post_id);
				exit;
			}
		}

		// If there is no new post, we go to the last post
		$action = 'last';
	}

	// If action=last, we redirect to the last post
	if ($action == 'last')
	{
		$result = $db->query('SELECT MAX(id) FROM '.$db->prefix.'posts WHERE topic_id='.$id) or error('Unable to fetch last post info', __FILE__, __LINE__, $db->error());
		$last_post_id = $db->result($result);


		if ($last_post_id)
		{
			header('Location: viewtopic.php?pid='.$last_post_id.'#p'.$last_post_id);
			exit;
		}
	}
}


// Fetch some info about the topic
if (!$luna_user['is_guest'])
	$result = $db->query('SELECT t.subject, t.closed, t.num_replies, t.sticky, t.first_post_id, f.id AS forum_id, f.forum_name, f.moderators, fp.post_replies, s.user_id AS is_subscribed FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'topic_subscriptions AS s ON (t.id=s.topic_id AND s.user_id='.$luna_user['id'].') LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());
else
	$result = $db->query('SELECT t.subject, t.closed, t.num_replies, t.sticky, t.first_post_id, f.id AS forum_id, f.forum_name, f.moderators, fp.post_replies, 0 AS is_subscribed FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());

if (!$db->num_rows($result))
	message($lang['Bad request'], false, '404 Not Found');

$cur_topic = $db->fetch_assoc($result);

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_topic['moderators'] != '') ? unserialize($cur_topic['moderators']) : array();
$is_admmod = ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && array_key_exists($luna_user['username'], $mods_array))) ? true : false;
if ($is_admmod)
$admin_ids = get_admin_ids();

// Can we or can we not post replies?
if ($cur_topic['closed'] == '0')
{
	if (($cur_topic['post_replies'] == '' && $luna_user['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1' || $is_admmod)
		$post_link = "\t\t\t".'<a class="btn btn-primary btn-post pull-right" href="post.php?tid='.$id.'">'.$lang['Post reply'].'</a>'."\n";
	else
		$post_link = '';
}
else
{
	$post_link = '<a class="btn disabled btn-warning btn-post pull-right">'.$lang['Topic closed'].'</a>';

	if ($is_admmod)
		$post_link .= '<a class="btn btn-primary btn-post pull-right" href="post.php?tid='.$id.'">'.$lang['Post reply'].'</a>';

	$post_link = $post_link."\n";
}


// Add/update this topic in our list of tracked topics
if (!$luna_user['is_guest'])
{
	$tracked_topics = get_tracked_topics();
	$tracked_topics['topics'][$id] = time();
	set_tracked_topics($tracked_topics);
}


// Determine the post offset (based on $_GET['p'])
$num_pages = ceil(($cur_topic['num_replies'] + 1) / $luna_user['disp_posts']);

$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : intval($_GET['p']);
$start_from = $luna_user['disp_posts'] * ($p - 1);

// Generate paging links
$paging_links = paginate($num_pages, $p, 'viewtopic.php?id='.$id);


if ($luna_config['o_censoring'] == '1')
	$cur_topic['subject'] = censor_words($cur_topic['subject']);


$quickpost = false;
if ($luna_config['o_quickpost'] == '1' &&
	($cur_topic['post_replies'] == '1' || ($cur_topic['post_replies'] == '' && $luna_user['g_post_replies'] == '1')) &&
	($cur_topic['closed'] == '0' || $is_admmod))
{
	$required_fields = array('req_message' => $lang['Message']);
	if ($luna_user['is_guest'])
	{
		$required_fields['req_username'] = $lang['Guest name'];
		if ($luna_config['p_force_guest_email'] == '1')
			$required_fields['req_email'] = $lang['Email'];
	}
	$quickpost = true;
}

if ($luna_config['o_feed_type'] == '1')
	$page_head = array('feed' => '<link rel="alternate" type="application/rss+xml" href="extern.php?action=feed&amp;tid='.$id.'&amp;type=rss" title="'.$lang['RSS topic feed'].'" />');
else if ($luna_config['o_feed_type'] == '2')
	$page_head = array('feed' => '<link rel="alternate" type="application/atom+xml" href="extern.php?action=feed&amp;tid='.$id.'&amp;type=atom" title="'.$lang['Atom topic feed'].'" />');

$topic_actions = array();

if (!$luna_user['is_guest'] && $luna_config['o_topic_subscriptions'] == '1')
{
	if ($cur_topic['is_subscribed'])
		$topic_actions[] = '<a href="misc.php?action=unsubscribe&amp;tid='.$id.'">'.$lang['Unsubscribe'].'</a>';
	else
		$topic_actions[] = '<a href="misc.php?action=subscribe&amp;tid='.$id.'">'.$lang['Subscribe'].'</a>';
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), luna_htmlspecialchars($cur_topic['forum_name']), luna_htmlspecialchars($cur_topic['subject']));
define('FORUM_ALLOW_INDEX', 1);
define('FORUM_ACTIVE_PAGE', 'index');
require FORUM_ROOT.'header.php';

require get_view_path('viewtopic-header.tpl.php');

require FORUM_ROOT.'include/parser.php';

$post_count = 0; // Keep track of post numbers

// Retrieve a list of post IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
$result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE topic_id='.$id.' ORDER BY id LIMIT '.$start_from.','.$luna_user['disp_posts']) or error('Unable to fetch post IDs', __FILE__, __LINE__, $db->error());

$post_ids = array();
for ($i = 0;$cur_post_id = $db->result($result, $i);$i++)
	$post_ids[] = $cur_post_id;

if (empty($post_ids))
	error('The post table and topic table seem to be out of sync!', __FILE__, __LINE__);

// Retrieve the posts (and their respective poster/online status)
$result = $db->query('SELECT u.email, u.title, u.url, u.location, u.signature, u.email_setting, u.num_posts, u.registered, u.admin_note, p.id, p.poster AS username, p.poster_id, p.poster_ip, p.poster_email, p.message, p.hide_smilies, p.posted, p.edited, p.edited_by, p.marked, g.g_id, g.g_user_title, o.user_id AS is_online FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'users AS u ON u.id=p.poster_id INNER JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id LEFT JOIN '.$db->prefix.'online AS o ON (o.user_id=u.id AND o.user_id!=1 AND o.idle=0) WHERE p.id IN ('.implode(',', $post_ids).') ORDER BY p.id', true) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
while ($cur_post = $db->fetch_assoc($result))
{
	$post_count++;
	$user_avatar = '';
	$user_info = array();
	$user_actions = array();
	$post_actions = array();
	$is_online = '';
	$signature = '';

	// If the poster is a registered user
	if ($cur_post['poster_id'] > 1)
	{
		if ($luna_user['g_view_users'] == '1')
			$username = '<a href="profile.php?id='.$cur_post['poster_id'].'">'.luna_htmlspecialchars($cur_post['username']).'</a>';
		else
			$username = luna_htmlspecialchars($cur_post['username']);

		$user_title = get_title($cur_post);

		if ($luna_config['o_censoring'] == '1')
			$user_title = censor_words($user_title);

		// Format the online indicator, those are ment as CSS classes
		$is_online = ($cur_post['is_online'] == $cur_post['poster_id']) ? 'is-online' : 'is-offline';

		// We only show location, register date, post count and the contact links if "Show user info" is enabled
		if ($luna_config['o_show_user_info'] == '1')
		{
			if ($cur_post['location'] != '')
			{
				if ($luna_config['o_censoring'] == '1')
					$cur_post['location'] = censor_words($cur_post['location']);

				$user_info[] = '<dd><span>'.$lang['From'].' '.luna_htmlspecialchars($cur_post['location']).'</span></dd>';
			}

			if ($luna_config['o_show_post_count'] == '1' || $luna_user['is_admmod'])
				$user_info[] = '<dd><span>'.$lang['Posts'].' '.forum_number_format($cur_post['num_posts']).'</span></dd>';

			// Now let's deal with the contact links (Email and URL)
			if ((($cur_post['email_setting'] == '0' && !$luna_user['is_guest']) || $luna_user['is_admmod']) && $luna_user['g_send_email'] == '1')
				$user_actions[] = '<a class="btn btn-primary btn-xs" href="mailto:'.luna_htmlspecialchars($cur_post['email']).'">'.$lang['Email'].'</a>';
			else if ($cur_post['email_setting'] == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
				$user_actions[] = '<a class="btn btn-primary btn-xs" href="misc.php?email='.$cur_post['poster_id'].'">'.$lang['Email'].'</a>';

			if ($cur_post['url'] != '')
			{
				if ($luna_config['o_censoring'] == '1')
					$cur_post['url'] = censor_words($cur_post['url']);

				$user_actions[] = '<a class="btn btn-primary btn-xs" href="'.luna_htmlspecialchars($cur_post['url']).'" rel="nofollow">'.$lang['Website'].'</a>';
			}


			if ($luna_user['is_admmod'])
			{
				$user_actions[] = '<a class="btn btn-primary btn-xs" href="moderate.php?get_host='.$cur_post['id'].'" title="'.luna_htmlspecialchars($cur_post['poster_ip']).'">'.$lang['IP address logged'].'</a>';
			}
		}


		if ($luna_user['is_admmod'])
		{
			if ($cur_post['admin_note'] != '')
				$user_info[] = '<dd><span>'.$lang['Note'].' <strong>'.luna_htmlspecialchars($cur_post['admin_note']).'</strong></span></dd>';
		}
	}
	// If the poster is a guest (or a user that has been deleted)
	else
	{
		$username = luna_htmlspecialchars($cur_post['username']);
		$user_title = get_title($cur_post);

		if ($luna_user['is_admmod'])
			$user_info[] = '<dd><span><a href="moderate.php?get_host='.$cur_post['id'].'" title="'.luna_htmlspecialchars($cur_post['poster_ip']).'">'.$lang['IP address logged'].'</a></span></dd>';

		if ($luna_config['o_show_user_info'] == '1' && $cur_post['poster_email'] != '' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
			$user_actions[] = '<span class="email"><a href="mailto:'.luna_htmlspecialchars($cur_post['poster_email']).'">'.$lang['Email'].'</a></span>';
	}

	// Get us the avatar
	if ($luna_config['o_avatars'] == '1' && $luna_user['show_avatars'] != '0')
	{
		if (isset($user_avatar_cache[$cur_post['poster_id']]))
			$user_avatar = $user_avatar_cache[$cur_post['poster_id']];
		else
			$user_avatar = generate_avatar_markup($cur_post['poster_id']);
	}

	// Generation post action array (quote, edit, delete etc.)
	if (!$is_admmod)
	{
		if (!$luna_user['is_guest']) {
			if ($cur_post['marked'] == false) {
				$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="misc.php?report='.$cur_post['id'].'">'.$lang['Report'].'</a>';
			} else {
				$post_actions[] = '<a class="btn btn-danger btn-actions btn-xs" disabled="disabled" href="misc.php?report='.$cur_post['id'].'">'.$lang['Report'].'</a>';
			}
		}

		if ($cur_topic['closed'] == '0')
		{
			if ($cur_post['poster_id'] == $luna_user['id'])
			{
				if ((($start_from + $post_count) == 1 && $luna_user['g_delete_topics'] == '1') || (($start_from + $post_count) > 1 && $luna_user['g_delete_posts'] == '1'))
					$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="delete.php?id='.$cur_post['id'].'">'.$lang['Delete'].'</a>';
				if ($luna_user['g_edit_posts'] == '1')
					$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="edit.php?id='.$cur_post['id'].'">'.$lang['Edit'].'</a>';
			}

			if (($cur_topic['post_replies'] == '' && $luna_user['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1')
				$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="post.php?tid='.$id.'&amp;qid='.$cur_post['id'].'">'.$lang['Quote'].'</a>';
		}
	}
	else
	{
		if ($cur_post['marked'] == false)
		{
			$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="misc.php?report='.$cur_post['id'].'">'.$lang['Report'].'</a>';
		} else {
			$post_actions[] = '<a class="btn btn-danger btn-actions btn-xs" disabled="disabled" href="misc.php?report='.$cur_post['id'].'">'.$lang['Report'].'</a>';
		}
		if ($luna_user['g_id'] == FORUM_ADMIN || !in_array($cur_post['poster_id'], $admin_ids))
		{
			$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="delete.php?id='.$cur_post['id'].'">'.$lang['Delete'].'</a>';
			$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="edit.php?id='.$cur_post['id'].'">'.$lang['Edit'].'</a>';
		}
		$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="post.php?tid='.$id.'&amp;qid='.$cur_post['id'].'">'.$lang['Quote'].'</a>';
	}

	// Perform the main parsing of the message (BBCode, smilies, censor words etc)
	$cur_post['message'] = parse_message($cur_post['message'], $cur_post['hide_smilies']);

	// Do signature parsing/caching
	if ($luna_config['o_signatures'] == '1' && $cur_post['signature'] != '' && $luna_user['show_sig'] != '0')
	{
		if (isset($signature_cache[$cur_post['poster_id']]))
			$signature = $signature_cache[$cur_post['poster_id']];
		else
		{
			$signature = parse_signature($cur_post['signature']);
			$signature_cache[$cur_post['poster_id']] = $signature;
		}
	}

	require get_view_path('viewtopic-posts.tpl.php');
}

require get_view_path('viewtopic-breadcrumbs.tpl.php');

// Display quick post if enabled
if ($quickpost)
{

	$cur_index = 1;

	require get_view_path('viewtopic-form_quickpost.tpl.php');
}

// Increment "num_views" for topic
if ($luna_config['o_topic_views'] == '1')
	$db->query('UPDATE '.$db->prefix.'topics SET num_views=num_views+1 WHERE id='.$id) or error('Unable to update topic', __FILE__, __LINE__, $db->error());

$forum_id = $cur_topic['forum_id'];
$footer_style = 'viewtopic';

require FORUM_ROOT.'footer.php';
