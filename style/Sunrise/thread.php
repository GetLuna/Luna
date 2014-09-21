<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></h2>

<div class="row-nav">
	<div class="btn-breadcrumb">
		<a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><span class="fa fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
	</div>
	<ul class="pagination pull-right">
		<?php echo $paging_links ?>
	</ul>
</div>

<?php 

// Retrieve the posts (and their respective poster/online status)
$result = $db->query('SELECT u.email, u.title, u.url, u.location, u.signature, u.email_setting, u.num_posts, u.registered, u.admin_note, p.id, p.poster AS username, p.poster_id, p.poster_ip, p.poster_email, p.message, p.hide_smilies, p.posted, p.edited, p.edited_by, p.marked, g.g_id, g.g_user_title, o.user_id AS is_online FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'users AS u ON u.id=p.poster_id INNER JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id LEFT JOIN '.$db->prefix.'online AS o ON (o.user_id=u.id AND o.user_id!=1 AND o.idle=0) WHERE p.id IN ('.implode(',', $post_ids).') ORDER BY p.id', true) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
while ($cur_post = $db->fetch_assoc($result)) {
	$post_count++;
	$user_avatar = '';
	$user_info = array();
	$user_actions = array();
	$post_actions = array();
	$is_online = '';
	$signature = '';

	// If the poster is a registered user
	if ($cur_post['poster_id'] > 1) {
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
		if ($luna_config['o_show_user_info'] == '1') {
			if ($cur_post['location'] != '') {
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

			if ($cur_post['url'] != '') {
				if ($luna_config['o_censoring'] == '1')
					$cur_post['url'] = censor_words($cur_post['url']);

				$user_actions[] = '<a class="btn btn-primary btn-xs" href="'.luna_htmlspecialchars($cur_post['url']).'" rel="nofollow">'.$lang['Website'].'</a>';
			}


			if ($luna_user['is_admmod']) {
				$user_actions[] = '<a class="btn btn-primary btn-xs" href="moderate.php?get_host='.$cur_post['id'].'" title="'.luna_htmlspecialchars($cur_post['poster_ip']).'">'.$lang['IP address logged'].'</a>';
			}
		}


		if ($luna_user['is_admmod']) {
			if ($cur_post['admin_note'] != '')
				$user_info[] = '<dd><span>'.$lang['Note'].' <strong>'.luna_htmlspecialchars($cur_post['admin_note']).'</strong></span></dd>';
		}
	}
	// If the poster is a guest (or a user that has been deleted)
	else {
		$username = luna_htmlspecialchars($cur_post['username']);
		$user_title = get_title($cur_post);

		if ($luna_user['is_admmod'])
			$user_info[] = '<dd><span><a href="moderate.php?get_host='.$cur_post['id'].'" title="'.luna_htmlspecialchars($cur_post['poster_ip']).'">'.$lang['IP address logged'].'</a></span></dd>';

		if ($luna_config['o_show_user_info'] == '1' && $cur_post['poster_email'] != '' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
			$user_actions[] = '<span class="email"><a href="mailto:'.luna_htmlspecialchars($cur_post['poster_email']).'">'.$lang['Email'].'</a></span>';
	}

	// Get us the avatar
	if ($luna_config['o_avatars'] == '1' && $luna_user['show_avatars'] != '0') {
		if (isset($user_avatar_cache[$cur_post['poster_id']]))
			$user_avatar = $user_avatar_cache[$cur_post['poster_id']];
		else
			$user_avatar = generate_avatar_markup($cur_post['poster_id']);
	}

	// Generation post action array (quote, edit, delete etc.)
	if (!$is_admmod) {
		if (!$luna_user['is_guest']) {
			if ($cur_post['marked'] == false) {
				$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="misc.php?report='.$cur_post['id'].'">'.$lang['Report'].'</a>';
			} else {
				$post_actions[] = '<a class="btn btn-danger btn-actions btn-xs" disabled="disabled" href="misc.php?report='.$cur_post['id'].'">'.$lang['Report'].'</a>';
			}
		}

		if ($cur_topic['closed'] == '0') {
			if ($cur_post['poster_id'] == $luna_user['id']) {
				if ((($start_from + $post_count) == 1 && $luna_user['g_delete_topics'] == '1') || (($start_from + $post_count) > 1 && $luna_user['g_delete_posts'] == '1'))
					$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="delete.php?id='.$cur_post['id'].'">'.$lang['Delete'].'</a>';
				if ($luna_user['g_edit_posts'] == '1')
					$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="edit.php?id='.$cur_post['id'].'">'.$lang['Edit'].'</a>';
			}

			if (($cur_topic['post_replies'] == '' && $luna_user['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1')
				$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="post.php?tid='.$id.'&amp;qid='.$cur_post['id'].'">'.$lang['Quote'].'</a>';
		}
	} else {
		if ($cur_post['marked'] == false) {
			$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="misc.php?report='.$cur_post['id'].'">'.$lang['Report'].'</a>';
		} else {
			$post_actions[] = '<a class="btn btn-danger btn-actions btn-xs" disabled="disabled" href="misc.php?report='.$cur_post['id'].'">'.$lang['Report'].'</a>';
		}
		if ($luna_user['g_id'] == FORUM_ADMIN || !in_array($cur_post['poster_id'], $admin_ids)) {
			$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="delete.php?id='.$cur_post['id'].'">'.$lang['Delete'].'</a>';
			$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="edit.php?id='.$cur_post['id'].'">'.$lang['Edit'].'</a>';
		}
		$post_actions[] = '<a class="btn btn-default btn-actions btn-xs" href="post.php?tid='.$id.'&amp;qid='.$cur_post['id'].'">'.$lang['Quote'].'</a>';
	}

	// Perform the main parsing of the message (BBCode, smilies, censor words etc)
	$cur_post['message'] = parse_message($cur_post['message'], $cur_post['hide_smilies']);

	// Do signature parsing/caching
	if ($luna_config['o_signatures'] == '1' && $cur_post['signature'] != '' && $luna_user['show_sig'] != '0') {
		if (isset($signature_cache[$cur_post['poster_id']]))
			$signature = $signature_cache[$cur_post['poster_id']];
		else {
			$signature = parse_signature($cur_post['signature']);
			$signature_cache[$cur_post['poster_id']] = $signature;
		}
	}

	require get_view_path('comment.php'); 
}
?>

<div class="row-nav">
	<div class="btn-breadcrumb">
		<a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><span class="fa fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
	</div>
	<ul class="pagination pull-right">
		<?php echo $paging_links ?>
	</ul>
</div>