<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';


if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view'], false, '403 Forbidden');


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1)
	message($lang_common['Bad request'], false, '404 Not Found');

// Load the frontend.php language file
require FORUM_ROOT.'lang/'.$pun_user['language'].'/frontend.php';

// Fetch some info about the forum
if (!$pun_user['is_guest'])
	$result = $db->query('SELECT pf.forum_name AS parent_forum, f.parent_forum_id, f.forum_name, f.redirect_url, f.moderators, f.num_topics, f.sort_by, fp.post_topics, s.user_id AS is_subscribed FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_subscriptions AS s ON (f.id=s.forum_id AND s.user_id='.$pun_user['id'].') LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') LEFT JOIN '.$db->prefix.'forums AS pf ON f.parent_forum_id=pf.id  WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$id) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());
else
	$result = $db->query('SELECT pf.forum_name AS parent_forum, f.parent_forum_id, f.forum_name, f.redirect_url, f.moderators, f.num_topics, f.sort_by, fp.post_topics, 0 AS is_subscribed FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') LEFT JOIN '.$db->prefix.'forums AS pf ON f.parent_forum_id=pf.id  WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$id) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());

if (!$db->num_rows($result))
	message($lang_common['Bad request'], false, '404 Not Found');

$cur_forum = $db->fetch_assoc($result);

// Is this a redirect forum? In that case, redirect!
if ($cur_forum['redirect_url'] != '')
{
	header('Location: '.$cur_forum['redirect_url']);
	exit;
}

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();
$is_admmod = ($pun_user['g_id'] == FORUM_ADMIN || ($pun_user['g_moderator'] == '1' && array_key_exists($pun_user['username'], $mods_array))) ? true : false;

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
if (($cur_forum['post_topics'] == '' && $pun_user['g_post_topics'] == '1') || $cur_forum['post_topics'] == '1' || $is_admmod)
	$post_link = "\t\t\t".'<p class="postlink conr"><a href="post.php?fid='.$id.'">'.$lang_front['Post topic'].'</a></p>'."\n";
else
	$post_link = '';

// Get topic/forum tracking data
if (!$pun_user['is_guest'])
{
	$result = $db->query('SELECT t.forum_id, t.id, t.last_post FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.last_post>'.$pun_user['last_visit'].' AND t.moved_to IS NULL') or error('Unable to fetch new topics', __FILE__, __LINE__, $db->error());

	$new_topics = array();
	while ($cur_topic = $db->fetch_assoc($result))
		$new_topics[$cur_topic['forum_id']][$cur_topic['id']] = $cur_topic['last_post'];

	$tracked_topics = get_tracked_topics();
}

// Determine the topic offset (based on $_GET['p'])
$num_pages = ceil($cur_forum['num_topics'] / $pun_user['disp_topics']);

$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : intval($_GET['p']);
$start_from = $pun_user['disp_topics'] * ($p - 1);

// Generate paging links
$paging_links = '<span class="pages-label">'.$lang_common['Pages'].' </span>'.paginate($num_pages, $p, 'viewforum.php?id='.$id);

if ($pun_config['o_feed_type'] == '1')
	$page_head = array('feed' => '<link rel="alternate" type="application/rss+xml" href="extern.php?action=feed&amp;fid='.$id.'&amp;type=rss" title="'.$lang_common['RSS forum feed'].'" />');
else if ($pun_config['o_feed_type'] == '2')
	$page_head = array('feed' => '<link rel="alternate" type="application/atom+xml" href="extern.php?action=feed&amp;fid='.$id.'&amp;type=atom" title="'.$lang_common['Atom forum feed'].'" />');

$forum_actions = array();

if (!$pun_user['is_guest'])
{
	if ($pun_config['o_forum_subscriptions'] == '1')
	{
		if ($cur_forum['is_subscribed'])
			$forum_actions[] = '<span>'.$lang_front['Is subscribed'].' - </span><a href="misc.php?action=unsubscribe&amp;fid='.$id.'">'.$lang_front['Unsubscribe'].'</a>';
		else
			$forum_actions[] = '<a href="misc.php?action=subscribe&amp;fid='.$id.'">'.$lang_front['Subscribe'].'</a>';
	}

	$forum_actions[] = '<a href="misc.php?action=markforumread&amp;fid='.$id.'">'.$lang_common['Mark forum read'].'</a>';
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), pun_htmlspecialchars($cur_forum['forum_name']));
define('FORUM_ALLOW_INDEX', 1);
define('FORUM_ACTIVE_PAGE', 'index');
require FORUM_ROOT.'header.php';

if (!isset($_GET['p']) || $_GET['p'] == 1)
{
	$subforum_result = $db->query('SELECT f.forum_desc, f.forum_name, f.id, f.last_post, f.last_post_id, f.last_poster, f.moderators, f.num_posts, f.num_topics, f.redirect_url FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND parent_forum_id='.$id.' ORDER BY disp_position') or error('Unable to fetch sub forum info',__FILE__,__LINE__,$db->error());
	if ($db->num_rows($subforum_result))
	{


?>
<div id="punindex" class="subforumlist">

<div id="vf1" class="blocktable">
    <table class="table">
        <thead>
            <tr class="active">
                <th class="col-md-7"><?php echo $lang_common['Forum'] ?></th>
                <th class="col-md-1"><?php echo $lang_index['Topics'] ?></th>
                <th class="col-md-1"><?php echo $lang_common['Posts'] ?></th>
                <th class="col-md-3"><?php echo $lang_common['Last post'] ?></th>
            </tr>
        </thead>
        <tbody>
<?php
		$subforum_count = 0;

		while ($cur_subforum = $db->fetch_assoc($subforum_result))
		{
			++$subforum_count;
			$item_status = '';
			$icon_type = 'icon';

			// Are there new posts?
			if (!$pun_user['is_guest'] && $cur_subforum['last_post'] > $pun_user['last_visit'] && (empty($tracked_topics['forums'][$cur_subforum['id']]) || $cur_subforum['last_post'] > $tracked_topics['forums'][$cur_subforum['id']]))
			{
				// There are new posts in this forum, but have we read all of them already?
				foreach ($new_topics[$cur_subforum['id']] as $check_topic_id => $check_last_post)
				{
					if ((empty($tracked_topics['topics'][$check_topic_id]) || $tracked_topics['topics'][$check_topic_id] < $check_last_post) && (empty($tracked_topics['forums'][$cur_subforum['id']]) || $tracked_topics['forums'][$cur_subforum['id']] < $check_last_post))
					{
						$item_status = 'inew';
						$icon_type = 'icon inew';

						break;
					}
				}
			}

			// Is this a redirect forum?
			if ($cur_forum['redirect_url'] != '')
			{
				$forum_field = '<a href="'.pun_htmlspecialchars($cur_subforum['redirect_url']).'" title="'.$lang_index['Link to'].' '.pun_htmlspecialchars($cur_subforum['redirect_url']).'">'.pun_htmlspecialchars($cur_subforum['forum_name']).'</a>';
				$num_topics = $num_posts = '&nbsp;';
				$item_status = 'iredirect';
				$icon_type = 'icon';
			}
			else
			{
				$forum_field = '<a href="viewforum.php?id='.$cur_subforum['id'].'">'.pun_htmlspecialchars($cur_subforum['forum_name']).'</a>';
				$num_topics = $cur_subforum['num_topics'];
				$num_posts = $cur_subforum['num_posts'];
			}

			if ($cur_subforum['forum_desc'] != '')
				$forum_field .= "\n\t\t\t\t\t\t\t\t".$cur_subforum['forum_desc'];

			// If there is a last_post/last_poster
			if ($cur_subforum['last_post'] != '')
				$last_post = '<a href="viewtopic.php?pid='.$cur_subforum['last_post_id'].'#p'.$cur_subforum['last_post_id'].'">'.format_time($cur_subforum['last_post']).'</a> <span class="byuser">'.$lang_common['by'].' '.pun_htmlspecialchars($cur_subforum['last_poster']).'</span>';
			else if ($cur_subforum['redirect_url'] != '')
				$last_post = '- - -';
			else
				$last_post = $lang_common['Never'];

			if ($cur_subforum['moderators'] != '')
			{
				$mods_array = unserialize($cur_subforum['moderators']);
				$moderators = array();

				foreach ($mods_array as $mod_username => $mod_id)
				{
					if ($pun_user['g_view_users'] == '1')
						$moderators[] = '<a href="profile.php?id='.$mod_id.'">'.pun_htmlspecialchars($mod_username).'</a>';
					else
						$moderators[] = pun_htmlspecialchars($mod_username);
				}

				$moderators = "\t\t\t\t\t\t\t\t".'<p class="modlist">(<em>'.$lang_common['Moderated by'].'</em> '.implode(', ', $moderators).')</p>'."\n";
			}
?>
            <tr<?php if ($item_status != '') echo ' class="'.$item_status.'"'; ?>>
                <td class="tcl">
                    <div class="intd">
                        <div class="<?php echo $icon_type ?>"><div class="nosize"><?php echo forum_number_format($subforum_count) ?></div></div>
                        <div class="tclcon">
                            <?php echo $forum_field;
                            if ($cur_subforum['moderators'] != '') {
                                echo "\n".$moderators;
                            }
                            ?>
                        </div>
                    </div>
                </td>
                <td class="tc2"><?php echo $num_topics ?></td>
                <td class="tc3"><?php echo $num_posts ?></td>
                <td class="tcr"><?php echo $last_post ?></td>
            </tr>
<?php
		}
?>
        </tbody>
    </table>
</div>

</div>
<?php
	}
}

?>
<ul class="breadcrumb">
    <li><a href="index.php"><?php echo $lang_common['Index'] ?></a></li>
    <?php if($cur_forum['parent_forum']) echo "\t\t".'<li><a href="viewforum.php?id='.$cur_forum['parent_forum_id'].'">'.pun_htmlspecialchars($cur_forum['parent_forum']).'</a></li> '; ?>
    <li class="active"><a href="viewforum.php?id=<?php echo $id ?>"><?php echo pun_htmlspecialchars($cur_forum['forum_name']) ?></a></li>
</ul>
<div class="pagepost">
    <p class="pagelink conl"><?php echo $paging_links ?></p>
<?php echo $post_link ?>
</div>
<div id="vf" class="blocktable">
	<h2><?php echo pun_htmlspecialchars($cur_forum['forum_name']) ?></h2>
    <table class="table">
        <thead>
            <tr class="active">
                <th class="col-md-7"><?php echo $lang_common['Topic'] ?></th>
                <th class="col-md-1"><?php echo $lang_common['Replies'] ?></th>
<?php if ($pun_config['o_topic_views'] == '1'): ?>					<th class="col-md-1"><?php echo $lang_front['Views'] ?></th>
<?php endif; ?>			<th class="col-md-3"><?php echo $lang_common['Last post'] ?></th>
            </tr>
        </thead>
        <tbody>
<?php

// Retrieve a list of topic IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
$result = $db->query('SELECT id FROM '.$db->prefix.'topics WHERE forum_id='.$id.' ORDER BY sticky DESC, '.$sort_by.', id DESC LIMIT '.$start_from.', '.$pun_user['disp_topics']) or error('Unable to fetch topic IDs', __FILE__, __LINE__, $db->error());

// If there are topics in this forum
if ($db->num_rows($result))
{
	$topic_ids = array();
	for ($i = 0;$cur_topic_id = $db->result($result, $i);$i++)
		$topic_ids[] = $cur_topic_id;

	if (empty($topic_ids))
		error('The topic table and forum table seem to be out of sync!', __FILE__, __LINE__);

	// Fetch list of topics to display on this page
	if ($pun_user['is_guest'] || $pun_config['o_show_dot'] == '0')
	{
		// Without "the dot"
		$sql = 'SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, num_views, num_replies, closed, sticky, moved_to FROM '.$db->prefix.'topics WHERE id IN('.implode(',', $topic_ids).') ORDER BY sticky DESC, '.$sort_by.', id DESC';
	}
	else
	{
		// With "the dot"
		$sql = 'SELECT p.poster_id AS has_posted, t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id AND p.poster_id='.$pun_user['id'].' WHERE t.id IN('.implode(',', $topic_ids).') GROUP BY t.id'.($db_type == 'pgsql' ? ', t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, p.poster_id' : '').' ORDER BY t.sticky DESC, t.'.$sort_by.', t.id DESC';
	}

	$result = $db->query($sql) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());

	$topic_count = 0;
	while ($cur_topic = $db->fetch_assoc($result))
	{
		++$topic_count;
		$status_text = array();
		$item_status = ($topic_count % 2 == 0) ? 'roweven' : 'rowodd';
		$icon_type = 'icon';

		if (is_null($cur_topic['moved_to']))
			$last_post = '<a href="viewtopic.php?pid='.$cur_topic['last_post_id'].'#p'.$cur_topic['last_post_id'].'">'.format_time($cur_topic['last_post']).'</a> <span class="byuser">'.$lang_common['by'].' '.pun_htmlspecialchars($cur_topic['last_poster']).'</span>';
		else
			$last_post = '- - -';

		if ($pun_config['o_censoring'] == '1')
			$cur_topic['subject'] = censor_words($cur_topic['subject']);

		if ($cur_topic['sticky'] == '1')
		{
			$item_status .= ' isticky';
			$status_text[] = '<span class="stickytext">'.$lang_front['Sticky'].'</span>';
		}

		if ($cur_topic['moved_to'] != 0)
		{
			$subject = '<a href="viewtopic.php?id='.$cur_topic['moved_to'].'">'.pun_htmlspecialchars($cur_topic['subject']).'</a> <span class="byuser">'.$lang_common['by'].' '.pun_htmlspecialchars($cur_topic['poster']).'</span>';
			$status_text[] = '<span class="movedtext">'.$lang_front['Moved'].'</span>';
			$item_status .= ' imoved';
		}
		else if ($cur_topic['closed'] == '0')
			$subject = '<a href="viewtopic.php?id='.$cur_topic['id'].'">'.pun_htmlspecialchars($cur_topic['subject']).'</a> <span class="byuser">'.$lang_common['by'].' '.pun_htmlspecialchars($cur_topic['poster']).'</span>';
		else
		{
			$subject = '<a href="viewtopic.php?id='.$cur_topic['id'].'">'.pun_htmlspecialchars($cur_topic['subject']).'</a> <span class="byuser">'.$lang_common['by'].' '.pun_htmlspecialchars($cur_topic['poster']).'</span>';
			$status_text[] = '<span class="closedtext">'.$lang_front['Closed'].'</span>';
			$item_status .= ' iclosed';
		}

		if (!$pun_user['is_guest'] && $cur_topic['last_post'] > $pun_user['last_visit'] && (!isset($tracked_topics['topics'][$cur_topic['id']]) || $tracked_topics['topics'][$cur_topic['id']] < $cur_topic['last_post']) && (!isset($tracked_topics['forums'][$id]) || $tracked_topics['forums'][$id] < $cur_topic['last_post']) && is_null($cur_topic['moved_to']))
		{
			$item_status .= ' inew';
			$icon_type = 'icon icon-new';
			$subject = '<strong>'.$subject.'</strong>';
			$subject_new_posts = '<span class="newtext">[ <a href="viewtopic.php?id='.$cur_topic['id'].'&amp;action=new" title="'.$lang_common['New posts info'].'">'.$lang_common['New posts'].'</a> ]</span>';
		}
		else
			$subject_new_posts = null;

		// Insert the status text before the subject
		$subject = implode(' ', $status_text).' '.$subject;

		// Should we display the dot or not? :)
		if (!$pun_user['is_guest'] && $pun_config['o_show_dot'] == '1')
		{
			if ($cur_topic['has_posted'] == $pun_user['id'])
			{
				$subject = '<strong class="ipost">·&#160;</strong>'.$subject;
				$item_status .= ' iposted';
			}
		}

		$num_pages_topic = ceil(($cur_topic['num_replies'] + 1) / $pun_user['disp_posts']);

		if ($num_pages_topic > 1)
			$subject_multipage = '<span class="pagestext">[ '.paginate($num_pages_topic, -1, 'viewtopic.php?id='.$cur_topic['id']).' ]</span>';
		else
			$subject_multipage = null;

		// Should we show the "New posts" and/or the multipage links?
		if (!empty($subject_new_posts) || !empty($subject_multipage))
		{
			$subject .= !empty($subject_new_posts) ? ' '.$subject_new_posts : '';
			$subject .= !empty($subject_multipage) ? ' '.$subject_multipage : '';
		}

?>
            <tr class="<?php echo $item_status ?>">
                <td class="tcl">
                    <div class="<?php echo $icon_type ?>"><div class="nosize"><?php echo forum_number_format($topic_count + $start_from) ?></div></div>
                    <div class="tclcon">
                        <div>
                            <?php echo $subject."\n" ?>
                        </div>
                    </div>
                </td>
                <td class="tc2"><?php echo (is_null($cur_topic['moved_to'])) ? forum_number_format($cur_topic['num_replies']) : '-' ?></td>
<?php if ($pun_config['o_topic_views'] == '1'): ?>					<td class="tc3"><?php echo (is_null($cur_topic['moved_to'])) ? forum_number_format($cur_topic['num_views']) : '-' ?></td>
<?php endif; ?>			<td class="tcr"><?php echo $last_post ?></td>
            </tr>
<?php

	}
}
else
{
	$colspan = ($pun_config['o_topic_views'] == '1') ? 4 : 3;

?>
            <tr class="rowodd inone">
                <td class="tcl" colspan="<?php echo $colspan ?>">
                    <div class="icon inone"><div class="nosize"><!-- --></div></div>
                    <div class="tclcon">
                        <div>
                            <strong><?php echo $lang_front['Empty forum'] ?></strong>
                        </div>
                    </div>
                </td>
            </tr>
<?php

}

?>
        </tbody>
    </table>
</div>

<div class="pagepost">
    <p class="pagelink conl"><?php echo $paging_links ?></p>
<?php echo $post_link ?>
</div>
<ul class="breadcrumb">
    <li><a href="index.php"><?php echo $lang_common['Index'] ?></a></li>
    <?php if($cur_forum['parent_forum']) echo "\t\t".'<li><a href="viewforum.php?id='.$cur_forum['parent_forum_id'].'">'.pun_htmlspecialchars($cur_forum['parent_forum']).'</a></li> '; ?>
    <li class="active"><a href="viewforum.php?id=<?php echo $id ?>"><?php echo pun_htmlspecialchars($cur_forum['forum_name']) ?></a></li>
</ul>
<?php

$forum_id = $id;
$footer_style = 'viewforum';
require FORUM_ROOT.'footer.php';
