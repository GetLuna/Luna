<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
    header("Location: ../login.php");

if ($luna_user['g_id'] != FORUM_ADMIN)
	message_backstage($lang['No permission'], false, '403 Forbidden');

// This particular function doesn't require forum-based moderator access. It can be used
// by all moderators and admins
if (isset($_GET['get_host'])) {
	if (!$luna_user['is_admmod'])
		message($lang['No permission'], false, '403 Forbidden');

	// Is get_host an IP address or a post ID?
	if (@preg_match('%^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$%', $_GET['get_host']) || @preg_match('%^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$%', $_GET['get_host']))
		$ip = $_GET['get_host'];
	else {
		$get_host = intval($_GET['get_host']);
		if ($get_host < 1)
			message($lang['Bad request'], false, '404 Not Found');

		$result = $db->query('SELECT poster_ip FROM '.$db->prefix.'posts WHERE id='.$get_host) or error('Unable to fetch post IP address', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang['Bad request'], false, '404 Not Found');

		$ip = $db->result($result);
	}

	message(sprintf($lang['Host info 1'], $ip).'<br />'.sprintf($lang['Host info 2'], @gethostbyaddr($ip)).'<br /><br /><a class="btn btn-primary" href="backstage/users.php?show_users='.$ip.'">'.$lang['Show more users'].'</a>');
}


// All other functions require moderator/admin access
$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
if ($fid < 1)
	message($lang['Bad request'], false, '404 Not Found');

$result = $db->query('SELECT moderators FROM '.$db->prefix.'forums WHERE id='.$fid) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());

$moderators = $db->result($result);
$mods_array = ($moderators != '') ? unserialize($moderators) : array();

if ($luna_user['g_id'] != FORUM_ADMIN && ($luna_user['g_moderator'] == '0' || !array_key_exists($luna_user['username'], $mods_array)))
	message($lang['No permission'], false, '403 Forbidden');

// Get topic/forum tracking data
if (!$luna_user['is_guest'])
	$tracked_topics = get_tracked_topics();


	$tid = intval($_GET['tid']);
	if ($tid < 1)
		message($lang['Bad request'], false, '404 Not Found');

	// Fetch some info about the topic
	$result = $db->query('SELECT t.subject, t.num_replies, t.first_post_id, f.id AS forum_id, forum_name FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$fid.' AND t.id='.$tid.' AND t.moved_to IS NULL') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message($lang['Bad request'], false, '404 Not Found');

	$cur_topic = $db->fetch_assoc($result);

	// Delete one or more posts
	if (isset($_POST['delete_posts']) || isset($_POST['delete_posts_comply'])) {
		$posts = isset($_POST['posts']) ? $_POST['posts'] : array();
		if (empty($posts))
			message($lang['No posts selected']);

		if (isset($_POST['delete_posts_comply'])) {
			confirm_referrer('moderate.php');

			if (@preg_match('%[^0-9,]%', $posts))
				message($lang['Bad request'], false, '404 Not Found');

			// Verify that the post IDs are valid
			$admins_sql = ($luna_user['g_id'] != FORUM_ADMIN) ? ' AND poster_id NOT IN('.implode(',', get_admin_ids()).')' : '';
			$result = $db->query('SELECT 1 FROM '.$db->prefix.'posts WHERE id IN('.$posts.') AND topic_id='.$tid.$admins_sql) or error('Unable to check posts', __FILE__, __LINE__, $db->error());

			if ($db->num_rows($result) != substr_count($posts, ',') + 1)
				message($lang['Bad request'], false, '404 Not Found');

			// Delete the posts
			$db->query('DELETE FROM '.$db->prefix.'posts WHERE id IN('.$posts.')') or error('Unable to delete posts', __FILE__, __LINE__, $db->error());

			require FORUM_ROOT.'include/search_idx.php';
			strip_search_index($posts);

			// Get last_post, last_post_id, and last_poster for the topic after deletion
			$result = $db->query('SELECT id, poster, posted FROM '.$db->prefix.'posts WHERE topic_id='.$tid.' ORDER BY id DESC LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
			$last_post = $db->fetch_assoc($result);

			// How many posts did we just delete?
			$num_posts_deleted = substr_count($posts, ',') + 1;

			// Update the topic
			$db->query('UPDATE '.$db->prefix.'topics SET last_post='.$last_post['posted'].', last_post_id='.$last_post['id'].', last_poster=\''.$db->escape($last_post['poster']).'\', num_replies=num_replies-'.$num_posts_deleted.' WHERE id='.$tid) or error('Unable to update topic', __FILE__, __LINE__, $db->error());

			update_forum($fid);

			redirect('viewtopic.php?id='.$tid);
		}

		$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Moderate']);
		define('FORUM_ACTIVE_PAGE', 'moderate');
		require load_page('header.php');

		require get_view_path('moderate-delete_posts.tpl.php');

	} else if (isset($_POST['split_posts']) || isset($_POST['split_posts_comply'])) {
		$posts = isset($_POST['posts']) ? $_POST['posts'] : array();
		if (empty($posts))
			message($lang['No posts selected']);

		if (isset($_POST['split_posts_comply'])) {
			confirm_referrer('moderate.php');

			if (@preg_match('%[^0-9,]%', $posts))
				message($lang['Bad request'], false, '404 Not Found');

			$move_to_forum = isset($_POST['move_to_forum']) ? intval($_POST['move_to_forum']) : 0;
			if ($move_to_forum < 1)
				message($lang['Bad request'], false, '404 Not Found');

			// How many posts did we just split off?
			$num_posts_splitted = substr_count($posts, ',') + 1;

			// Verify that the post IDs are valid
			$result = $db->query('SELECT 1 FROM '.$db->prefix.'posts WHERE id IN('.$posts.') AND topic_id='.$tid) or error('Unable to check posts', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result) != $num_posts_splitted)
				message($lang['Bad request'], false, '404 Not Found');

			// Verify that the move to forum ID is valid
			$result = $db->query('SELECT 1 FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.group_id='.$luna_user['g_id'].' AND fp.forum_id='.$move_to_forum.') WHERE (fp.post_topics IS NULL OR fp.post_topics=1)') or error('Unable to fetch forum permissions', __FILE__, __LINE__, $db->error());
			if (!$db->num_rows($result))
				message($lang['Bad request'], false, '404 Not Found');

			// Check subject
			$new_subject = isset($_POST['new_subject']) ? luna_trim($_POST['new_subject']) : '';

			if ($new_subject == '')
				message($lang['No subject']);
			else if (luna_strlen($new_subject) > 70)
				message($lang['Too long subject']);

			// Get data from the new first post
			$result = $db->query('SELECT p.id, p.poster, p.posted FROM '.$db->prefix.'posts AS p WHERE id IN('.$posts.') ORDER BY p.id ASC LIMIT 1') or error('Unable to get first post', __FILE__, __LINE__, $db->error());
			$first_post_data = $db->fetch_assoc($result);

			// Create the new topic
			$db->query('INSERT INTO '.$db->prefix.'topics (poster, subject, posted, first_post_id, forum_id) VALUES (\''.$db->escape($first_post_data['poster']).'\', \''.$db->escape($new_subject).'\', '.$first_post_data['posted'].', '.$first_post_data['id'].', '.$move_to_forum.')') or error('Unable to create new topic', __FILE__, __LINE__, $db->error());
			$new_tid = $db->insert_id();

			// Move the posts to the new topic
			$db->query('UPDATE '.$db->prefix.'posts SET topic_id='.$new_tid.' WHERE id IN('.$posts.')') or error('Unable to move posts into new topic', __FILE__, __LINE__, $db->error());

			// Apply every subscription to both topics
			$db->query('INSERT INTO '.$db->prefix.'topic_subscriptions (user_id, topic_id) SELECT user_id, '.$new_tid.' FROM '.$db->prefix.'topic_subscriptions WHERE topic_id='.$tid) or error('Unable to copy existing subscriptions', __FILE__, __LINE__, $db->error());

			// Get last_post, last_post_id, and last_poster from the topic and update it
			$result = $db->query('SELECT id, poster, posted FROM '.$db->prefix.'posts WHERE topic_id='.$tid.' ORDER BY id DESC LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
			$last_post_data = $db->fetch_assoc($result);
			$db->query('UPDATE '.$db->prefix.'topics SET last_post='.$last_post_data['posted'].', last_post_id='.$last_post_data['id'].', last_poster=\''.$db->escape($last_post_data['poster']).'\', num_replies=num_replies-'.$num_posts_splitted.' WHERE id='.$tid) or error('Unable to update topic', __FILE__, __LINE__, $db->error());

			// Get last_post, last_post_id, and last_poster from the new topic and update it
			$result = $db->query('SELECT id, poster, posted FROM '.$db->prefix.'posts WHERE topic_id='.$new_tid.' ORDER BY id DESC LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
			$last_post_data = $db->fetch_assoc($result);
			$db->query('UPDATE '.$db->prefix.'topics SET last_post='.$last_post_data['posted'].', last_post_id='.$last_post_data['id'].', last_poster=\''.$db->escape($last_post_data['poster']).'\', num_replies='.($num_posts_splitted-1).' WHERE id='.$new_tid) or error('Unable to update topic', __FILE__, __LINE__, $db->error());

			update_forum($fid);
			update_forum($move_to_forum);

			redirect('viewtopic.php?id='.$new_tid);
		}

		$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Moderate']);
		$focus_element = array('subject','new_subject');
		define('FORUM_ACTIVE_PAGE', 'moderate');
		require load_page('header.php');

		$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.post_topics IS NULL OR fp.post_topics=1) ORDER BY c.disp_position, c.id, f.disp_position') or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

		require get_view_path('moderate-split_posts.tpl.php');
	}


	// Show the moderate posts view

	// Used to disable the Move and Delete buttons if there are no replies to this topic
	$button_status = ($cur_topic['num_replies'] == 0) ? ' disabled="disabled"' : '';

	if (isset($_GET['action']) && $_GET['action'] == 'all')
		$luna_user['disp_posts'] = $cur_topic['num_replies'] + 1;

	// Determine the post offset (based on $_GET['p'])
	$num_pages = ceil(($cur_topic['num_replies'] + 1) / $luna_user['disp_posts']);

	$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : intval($_GET['p']);
	$start_from = $luna_user['disp_posts'] * ($p - 1);

	// Generate paging links
	$paging_links = paginate($num_pages, $p, 'moderate.php?fid='.$fid.'&amp;tid='.$tid);


	if ($luna_config['o_censoring'] == '1')
		$cur_topic['subject'] = censor_words($cur_topic['subject']);
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Moderate']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('content', 'moderate');

?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Moderate content</h3>
	</div>
	<div class="panel-body">
	<div class="row row-nav-fix">
    <div class="col-sm-6">
        <div class="btn-group btn-breadcrumb">
            <a class="btn btn-primary" href="index.php"><span class="fa fa-home"></span></a>
            <a class="btn btn-primary" href="viewforum.php?id=<?php echo $fid ?>"><?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
            <a class="btn btn-primary" href="viewtopic.php?id=<?php echo $tid ?>"><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></a>
            <a class="btn btn-primary" href="#"><?php echo $lang['Moderate'] ?></a>
        </div>
    </div>
    <div class="col-sm-6">
		<?php echo $paging_links ?>
    </div>
</div>

<form method="post" action="moderate.php?fid=<?php echo $fid ?>&amp;tid=<?php echo $tid ?>">
<?php

    require FORUM_ROOT.'include/parser.php';

    $post_count = 0; // Keep track of post numbers

    // Retrieve a list of post IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
    $result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE topic_id='.$tid.' ORDER BY id LIMIT '.$start_from.','.$luna_user['disp_posts']) or error('Unable to fetch post IDs', __FILE__, __LINE__, $db->error());

    $post_ids = array();
    for ($i = 0;$cur_post_id = $db->result($result, $i);$i++)
        $post_ids[] = $cur_post_id;

    // Retrieve the posts (and their respective poster)
    $result = $db->query('SELECT u.title, u.num_posts, g.g_id, g.g_user_title, p.id, p.poster, p.poster_id, p.message, p.hide_smilies, p.posted, p.edited, p.edited_by, o.user_id AS is_online FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'users AS u ON u.id=p.poster_id INNER JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id LEFT JOIN '.$db->prefix.'online AS o ON (o.user_id=u.id AND o.user_id!=1 AND o.idle=0) WHERE p.id IN ('.implode(',', $post_ids).') ORDER BY p.id', true) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());

    while ($cur_post = $db->fetch_assoc($result)) {
        $post_count++;

        // If the poster is a registered user
        if ($cur_post['poster_id'] > 1) {
            if ($luna_user['g_view_users'] == '1')
                $poster = '<a href="me.php?id='.$cur_post['poster_id'].'">'.luna_htmlspecialchars($cur_post['poster']).'</a>';
            else
                $poster = luna_htmlspecialchars($cur_post['poster']);

            // get_title() requires that an element 'username' be present in the array
            $cur_post['username'] = $cur_post['poster'];
            $user_title = get_title($cur_post);

            if ($luna_config['o_censoring'] == '1')
                $user_title = censor_words($user_title);
        }
        // If the poster is a guest (or a user that has been deleted)
        else {
            $poster = luna_htmlspecialchars($cur_post['poster']);
            $user_title = $lang['Guest'];
        }

        // Format the online indicator, those are ment as CSS classes
        $is_online = ($cur_post['is_online'] == $cur_post['poster_id']) ? 'is-online' : 'is-offline';

        // Perform the main parsing of the message (BBCode, smilies, censor words etc)
        $cur_post['message'] = parse_message($cur_post['message']);

?>
<div id="p<?php echo $cur_post['id'] ?>" class="blockpost<?php if($cur_post['id'] == $cur_topic['first_post_id']) echo ' firstpost' ?><?php echo ($post_count % 2 == 0) ? ' roweven' : ' rowodd' ?><?php if ($post_count == 1) echo ' blockpost1' ?>">
    <table class="table postview">
        <tr>
            <td class="col-lg-2 user-data">
                <dd class="usertitle <?php echo $is_online; ?>"><strong><?php echo $poster ?></strong></dd><?php echo $user_title ?>
            </td>
            <td class="col-lg-10 post-content">
                <span class="time-nr pull-right">#<?php echo ($start_from + $post_count) ?> &middot; <a href="viewtopic.php?pid=<?php echo $cur_post['id'].'#p'.$cur_post['id'] ?>"><?php echo format_time($cur_post['posted']) ?></a></span>
                <div class="postmsg">
                    <?php echo $cur_post['message']."\n" ?>
                    <?php if ($cur_post['edited'] != '') echo "\t\t\t\t\t\t".'<p class="postedit"><em>'.$lang['Last edit'].' '.luna_htmlspecialchars($cur_post['edited_by']).' ('.format_time($cur_post['edited']).')</em></p>'."\n"; ?>
                </div>
            </td>
        </tr>
        <?php if (!$luna_user['is_guest']) { ?>
        <tr>
            <td colspan="2" class="postfooter" style="padding-bottom: 0;">
                <?php echo ($cur_post['id'] != $cur_topic['first_post_id']) ? '<div class="checkbox pull-right" style="margin-top: 0;"><label><input type="checkbox" name="posts['.$cur_post['id'].']" value="1" /> '.$lang['Select'].'</label></div>' : '<p>'.$lang['Cannot select first'].'</p>' ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php

    }

?>

<div class="row row-nav-fix">
    <div class="col-sm-6">
        <div class="btn-group btn-breadcrumb">
            <a class="btn btn-primary" href="index.php"><span class="fa fa-home"></span></a>
            <a class="btn btn-primary" href="viewforum.php?id=<?php echo $fid ?>"><?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
            <a class="btn btn-primary" href="viewtopic.php?id=<?php echo $tid ?>"><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></a>
            <a class="btn btn-primary" href="#"><?php echo $lang['Moderate'] ?></a>
        </div>
    </div>
    <div class="col-sm-6">
		<?php echo $paging_links ?>
		<div class="btn-group"><input type="submit" class="btn btn-primary" name="split_posts" value="<?php echo $lang['Split'] ?>"<?php echo $button_status ?> /><input type="submit" class="btn btn-primary" name="delete_posts" value="<?php echo $lang['Delete'] ?>"<?php echo $button_status ?> /></div>
    </div>
</div>
</form>
	</div>
</div>
<?php

require 'footer.php';