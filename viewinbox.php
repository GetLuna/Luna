<?php

/*
 * Copyright (C) 2014-2015 Luna
 * Based on work by Adaur (2010), Vincent Garnier, Connorhd and David 'Chacmool' Djurback
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/parser.php';
require FORUM_ROOT.'include/inbox_functions.php';

// No guest here !
if ($luna_user['is_guest'])
	message($lang['No permission']);
	
// User enable PM ?
if (!$luna_user['use_pm'] == '1')
	message($lang['No permission']);

// Are we allowed to use this ?
if (!$luna_config['o_pms_enabled'] =='1' || $luna_user['g_pm'] == '0')
	message($lang['No permission']);

// Load the additionals language files
require FORUM_ROOT.'lang/'.$luna_user['language'].'/language.php';

// Get the message's and topic's id
$mid = isset($_REQUEST['mid']) ? intval($_REQUEST['mid']) : '0';
$tid = isset($_REQUEST['tid']) ? intval($_REQUEST['tid']) : '0';
$pid = isset($_REQUEST['pid']) ? intval($_REQUEST['pid']) : '0';

$delete_all = '0';

$topic_msg = isset($_REQUEST['all_topic']) ? intval($_REQUEST['all_topic']) : '0';
$delete_all = isset($_POST['delete_all']) ? '1' : '0';

if ($pid) {
	$result = $db->query('SELECT shared_id FROM '.$db->prefix.'messages WHERE id='.$mid) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message($lang['Bad request']);

	$id = $db->result($result);

	// Determine on what page the post is located (depending on $luna_user['disp_posts'])
	$result = $db->query('SELECT id FROM '.$db->prefix.'messages WHERE shared_id='.$id.' AND owner='.$luna_user['id'].' ORDER BY posted') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	$num_posts = $db->num_rows($result);

	for ($i = 0; $i < $num_posts; ++$i) {
		$cur_id = $db->result($result, $i);
		if ($cur_id == $pid)
			break;
	}
	++$i; // we started at 0

	$_REQUEST['p'] = ceil($i / $luna_user['disp_posts']);
}

// Replace num_replies' feature by a query :-)
$result = $db->query('SELECT COUNT(*) FROM '.$db->prefix.'messages WHERE shared_id='.$tid.' AND owner='.$luna_user['id']) or error('Unable to count the messages', __FILE__, __LINE__, $db->error());
list($num_replies) = $db->fetch_row($result);

// Determine the post offset (based on $_GET['p'])
$num_pages = ceil($num_replies / $luna_user['disp_posts']);

// Page ?
$page = (!isset($_REQUEST['p']) || $_REQUEST['p'] <= '1') ? '1' : intval($_REQUEST['p']);
$start_from = $luna_user['disp_posts'] * ($page - 1);
	
// Check that $mid looks good
if ($mid <= 0)
	message($lang['Bad request']);

// Action ?
$action = ((isset($_REQUEST['action']) && ($_REQUEST['action'] == 'delete')) ? $_REQUEST['action'] : '');

	// Delete a single message or a full topic
	if ($action == 'delete') {
		// Make sure they got here from the site
		confirm_referrer('viewinbox.php');
		
		if (isset($_POST['delete_comply'])) {
			if ($topic_msg > '1' || $topic_msg < '0')
				message($lang['Bad request']);
			
			if ($topic_msg == '0') {
				if ($luna_user['is_admmod']) {
					if ($delete_all == '1') {
						$result_msg = $db->query('SELECT message FROM '.$db->prefix.'messages WHERE id='.$mid) or error('Unable to get the informations of the message', __FILE__, __LINE__, $db->error());
				
						if (!$db->num_rows($result_msg))
							message($lang['Bad request']);
							
						$delete_msg = $db->fetch_assoc($result_msg);
							
						// To devs: maybe this query is unsafe? Maybe you know how to secure it? I'm open to your suggestions ;) !
						$result_ids = $db->query('SELECT id FROM '.$db->prefix.'messages WHERE message=\''.$db->escape($delete_msg).'\'') or error('Unable to get the informations of the message', __FILE__, __LINE__, $db->error());
						
						if (!$db->num_rows($result_ids))
							message($lang['Bad request']);
						
						$ids_msg[] = $db->result($result_ids);
						
						// Finally, delete the messages!
						$db->query('DELETE FROM '.$db->prefix.'messages WHERE id IN ('.$ids_msg.')') or error('Unable to delete the message', __FILE__, __LINE__, $db->error());
					} else
						$db->query('DELETE FROM '.$db->prefix.'messages WHERE id='.$mid) or error('Unable to delete the message', __FILE__, __LINE__, $db->error());
				} else {
					$result = $db->query('SELECT owner FROM '.$db->prefix.'messages WHERE id='.$mid) or error('Unable to delete the message', __FILE__, __LINE__, $db->error());
					$owner = $db->result($result);
					
					if($owner != $luna_user['id']) // Double check : hackers are everywhere =)
						message($lang['No permission']);
						
					$db->query('DELETE FROM '.$db->prefix.'messages WHERE id='.$mid) or error('Unable to delete the message', __FILE__, __LINE__, $db->error());
				}
			} else {
				if ($luna_user['is_admmod']) {
					if ($delete_all == '1') {
						$result_ids = $db->query('SELECT DISTINCT owner FROM '.$db->prefix.'messages WHERE shared_id='.$tid) or error('Unable to get the informations of the message', __FILE__, __LINE__, $db->error());
						
						if (!$db->num_rows($result_ids))
							message($lang['Bad request']);
						
						while ($user_ids = $db->fetch_assoc($result_ids)) {
							$ids_users[] = $user_ids['owner'];
						}
						
						$ids_users = implode(',', $ids_users);
						
						$db->query('UPDATE '.$db->prefix.'users SET num_pms=num_pms-1 WHERE id IN('.$ids_users.')') or error('Unable to update user', __FILE__, __LINE__, $db->error());
						$db->query('DELETE FROM '.$db->prefix.'messages WHERE shared_id='.$tid) or error('Unable to delete the message', __FILE__, __LINE__, $db->error());
					} else {
						$db->query('DELETE FROM '.$db->prefix.'messages WHERE shared_id='.$tid.' AND owner='.$luna_user['id']) or error('Unable to delete the message', __FILE__, __LINE__, $db->error());
						$db->query('UPDATE '.$db->prefix.'messages SET receiver=REPLACE(receiver,\''.$db->escape($luna_user['username']).'\',\''.$db->escape($luna_user['username'].' Deleted').'\') WHERE receiver LIKE \'%'.$db->escape($luna_user['username']).'%\' AND shared_id='.$tid) or error('Unable to update private messages', __FILE__, __LINE__, $db->error());
						$db->query('UPDATE '.$db->prefix.'users SET num_pms=num_pms-1 WHERE id='.$luna_user['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());
					}
				} else {
					$result = $db->query('SELECT owner FROM '.$db->prefix.'messages WHERE id='.$mid) or error('Unable to delete the message', __FILE__, __LINE__, $db->error());
					$owner = $db->result($result);
					
					if($owner != $luna_user['id']) // Double check : hackers are everywhere =)
						message($lang['No permission']);
						
					$db->query('DELETE FROM '.$db->prefix.'messages WHERE id='.$mid) or error('Unable to delete the message', __FILE__, __LINE__, $db->error());
					$db->query('UPDATE '.$db->prefix.'users SET num_pms=num_pms-1 WHERE id='.$luna_user['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());
				}
			}
			
			// Redirect
			redirect('inbox.php', $lang['Del redirect']);
		} else {
			$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Delete message']);
			
			define('FORUM_ACTIVE_PAGE', 'pm');
			require load_page('header.php');
			
			// If you're not the owner of the message, you can't delete it.
			$result = $db->query('SELECT owner, show_message, posted, sender, message, hide_smilies FROM '.$db->prefix.'messages WHERE id='.$mid) or error('Unable to delete the message', __FILE__, __LINE__, $db->error());
			$cur_delete = $db->fetch_assoc($result);
			
			if($cur_delete['owner'] != $luna_user['id'] && !$luna_user['is_admmod'])
				message($lang['No permission']);

			$cur_delete['message'] = parse_message($cur_delete['message']);
		?>
<div class="linkst">
	<div class="inbox crumbsplus">
		<ul class="crumbs">
			<li><a href="index.php"><?php echo $lang['Index'] ?></a></li>
			<li><span>»&#160;</span><a href="inbox.php"><?php echo $lang['Private Messages'] ?></a></li>
			<li><span>»&#160;</span><a href="inbox.php"><?php echo $lang['Inbox'] ?></a></li>
			<li><span>»&#160;</span><?php echo $lang['Delete message'] ?></li>
		</ul>
		<div class="pagepost"></div>
		<div class="clearer"></div>
	</div>
</div>
<div class="block2col">
	<div class="blockmenu">
		<h2><span><?php echo $lang['PM Menu'] ?></span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
					<li class="isactive"><a href="inbox.php"><?php echo $lang['Inbox'] ?></a></li>
					<li><a href="new_inbox.php"><?php echo $lang['Write message'] ?></a></li>
					<li><a href="sending_lists.php"><?php echo $lang['Sending lists'] ?></a></li>
					<li><a href="contacts.php"><?php echo $lang['Contacts'] ?></a></li>
				</ul>
			</div>
		</div>
	</div>
<br />
<div class="blockform">
	<div class="box">
		<form action="viewinbox.php" method="post">
					<input type="hidden" name="action" value="delete" />
					<input type="hidden" name="mid" value="<?php echo $mid ?>" />
					<input type="hidden" name="tid" value="<?php echo $tid ?>" />
					<input type="hidden" name="delete_comply" value="1" />
					<input type="hidden" name="all_topic" value="<?php echo $cur_delete['show_message'] ?>" />
			<div class="inform">
				<div class="forminfo">
					<h3><span><?php printf($cur_delete['show_message'] ? $lang_delete['Topic by'] : $lang_delete['Reply by'], '<strong>'.luna_htmlspecialchars($cur_delete['sender']).'</strong>', format_time($cur_delete['posted'])) ?></span></h3>
					<p><?php echo ($cur_delete['show_message']) ? '<strong>'.$lang_delete['Topic warning'].'<br /></strong>'.$lang['Topic warning info'].'' : '<strong>'.$lang_delete['Warning'].'</strong>' ?><br /><?php echo $lang_delete['Delete info'] ?></p>
					<?php if ($luna_user['is_admmod']) : ?>
					<label><input type="checkbox" name="delete_all" value="1" /><?php echo $lang['Delete for everybody'] ?></label>
					<?php endif; ?>
				</div>
			</div>
			<p class="buttons"><input type="submit" name="delete" value="<?php echo $lang['Delete'] ?>" /> <a href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a></p>
		</form>
	</div>
</div>

<div id="postreview">
	<div class="blockpost">
		<div class="box">
			<div class="inbox">
				<div class="postbody">
					<div class="postleft">
						<dl>
							<dt><strong><?php echo luna_htmlspecialchars($cur_delete['sender']) ?></strong></dt>
							<dd><span><?php echo format_time($cur_delete['posted']) ?></span></dd>
						</dl>
					</div>
					<div class="postright">
						<div class="postmsg">
							<?php echo $cur_delete['message']."\n" ?>
						</div>
					</div>
				</div>
				<div class="clearer"></div>
			</div>
		</div>
	</div>
</div>
</div>
		
		<?php
			require load_page('footer.php');
		}
	}
	
// Start building page
	
$result_receivers = $db->query('SELECT DISTINCT receiver, owner, sender_id FROM '.$db->prefix.'messages WHERE shared_id='.$tid) or error('Unable to get the informations of the message', __FILE__, __LINE__, $db->error());

if (!$db->num_rows($result_receivers))
		message($lang['Bad request']);
		
$owner = array();
		
while ($receiver = $db->fetch_assoc($result_receivers)) {	
	$r_usernames = $receiver['receiver'];
	$owner[] = $receiver['owner'];
	$uid = $receiver['sender_id'];
}

$r_usernames = str_replace('Deleted', $lang['Deleted'], $r_usernames);

$result = $db->query('SELECT subject FROM '.$db->prefix.'messages WHERE shared_id='.$tid.' AND show_message=1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());

if (!$db->num_rows($result))
	message($lang['Bad request']);

$p_subject = $db->result($result);

$messageh2 = luna_htmlspecialchars($p_subject).' '.$lang['With'].' '.luna_htmlspecialchars($r_usernames);

$quickpost = false;
	if ($luna_config['o_quickpost'] == '1') {
		$required_fields = array('req_message' => $lang['Message']);
		$quickpost = true;
	}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Private Messages'], $lang['View']);

define('FORUM_ACTIVE_PAGE', 'pm');
require load_page('header.php');

if(!in_array($luna_user['id'], $owner) && !$luna_user['is_admmod'])
	message($lang['No permission']);
	
	$post_count = '0'; // Keep track of post numbers
	
	if ($num_new_pm > '0')
		$db->query('UPDATE '.$db->prefix.'messages SET showed=1 WHERE shared_id='.$tid.' AND show_message=1 AND owner='.$luna_user['id']) or error('Unable to update the status of the message', __FILE__, __LINE__, $db->error());

$result = $db->query('SELECT m.id AS mid, m.shared_id, m.subject, m.sender_ip, m.message, m.hide_smilies, m.posted, m.showed, m.sender, m.sender_id, u.id, u.group_id AS g_id, g.g_user_title, u.username, u.registered, u.email, u.title, u.url, u.location, u.email_setting, u.num_posts, u.admin_note, u.signature, u.use_pm, o.user_id AS is_online FROM '.$db->prefix.'messages AS m, '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'online AS o ON (o.user_id=u.id AND o.idle=0) LEFT JOIN '.$db->prefix.'groups AS g ON (u.group_id=g.g_id) WHERE u.id=m.sender_id AND m.shared_id='.$tid.' AND m.owner='.$luna_user['id'].' ORDER BY m.posted LIMIT '.$start_from.','.$luna_user['disp_posts']) or error('Unable to get the message and the informations of the user', __FILE__, __LINE__, $db->error());

if (!$db->num_rows($result))
	message($lang['Bad request']);
	
$reply_link = '<a href="new_inbox.php?reply='.$tid.'">'.$lang['Reply'].'</a>';

load_inbox_nav('view');
?>
<p><?php echo $lang['Pages'].' '.paginate($num_pages, $page, 'viewinbox.php?tid='.$tid.'&amp;mid='.$mid)  ?></p>
<?php
while ($cur_post = $db->fetch_assoc($result))
{	
	$post_count++;
	$user_avatar = '';
	$user_info = array();
	$user_contacts = array();
	$post_actions = array();
	$is_online = '';
	$signature = '';
	
	// If the poster is a registered user
	if ($cur_post['id']) {
		if ($luna_user['g_view_users'] == '1')
			$username = '<a href="me.php?id='.$cur_post['sender_id'].'">'.luna_htmlspecialchars($cur_post['sender']).'</a>';
		else
			$username = luna_htmlspecialchars($cur_post['sender']);
			
		$user_title = get_title($cur_post);

		if ($luna_config['o_censoring'] == '1')
			$user_title = censor_words($user_title);

		// Format the online indicator
		$is_online = ($cur_post['is_online'] == $cur_post['sender_id']) ? '<strong>'.$lang_topic['Online'].'</strong>' : '<span>'.$lang_topic['Offline'].'</span>';

		if ($luna_config['o_avatars'] == '1' && $luna_user['show_avatars'] != '0') {
			if (isset($user_avatar_cache[$cur_post['sender_id']]))
				$user_avatar = $user_avatar_cache[$cur_post['sender_id']];
			else
				$user_avatar = $user_avatar_cache[$cur_post['sender_id']] = generate_avatar_markup($cur_post['sender_id']);
		}

		// We only show location, register date, post count and the contact links if "Show user info" is enabled
		if ($luna_config['o_show_user_info'] == '1') {
			if ($cur_post['location'] != '') {
				if ($luna_config['o_censoring'] == '1')
					$cur_post['location'] = censor_words($cur_post['location']);

				$user_info[] = '<dd><span>'.$lang_topic['From'].' '.luna_htmlspecialchars($cur_post['location']).'</span></dd>';
			}

			$user_info[] = '<dd><span>'.$lang_topic['Registered'].' '.format_time($cur_post['registered'], true).'</span></dd>';

			if ($luna_config['o_show_post_count'] == '1' || $luna_user['is_admmod'])
				$user_info[] = '<dd><span>'.$lang_topic['Posts'].' '.forum_number_format($cur_post['num_posts']).'</span></dd>';

			// Now let's deal with the contact links (Email and URL)
			if ((($cur_post['email_setting'] == '0' && !$luna_user['is_guest']) || $luna_user['is_admmod']) && $luna_user['g_send_email'] == '1')
				$user_contacts[] = '<span class="email"><a href="mailto:'.$cur_post['email'].'">'.$lang['Email'].'</a></span>';
			elseif ($cur_post['email_setting'] == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
				$user_contacts[] = '<span class="email"><a href="misc.php?email='.$cur_post['sender_id'].'">'.$lang['Email'].'</a></span>';
				
			if ($luna_config['o_pms_enabled'] == '1' && !$luna_user['is_guest'] && $luna_user['g_pm'] == '1' && $luna_user['use_pm'] == '1' && $cur_post['use_pm'] == '1') {
				$pid = isset($cur_post['sender_id']) ? $cur_post['sender_id'] : $cur_post['sender_id'];
				$user_contacts[] = '<span class="email"><a href="new_inbox.php?uid='.$pid.'">'.$lang['PM'].'</a></span>';
			}

			if ($cur_post['url'] != '')
				$user_contacts[] = '<span class="website"><a href="'.luna_htmlspecialchars($cur_post['url']).'">'.$lang_topic['Website'].'</a></span>';
				
		}

		if ($luna_user['is_admmod']) {
			$user_info[] = '<dd><span><a href="backstage/moderate.php?get_host='.$cur_post['sender_ip'].'" title="'.$cur_post['sender_ip'].'">'.$lang_topic['IP address logged'].'</a></span></dd>';

			if ($cur_post['admin_note'] != '')
				$user_info[] = '<dd><span>'.$lang_topic['Note'].' <strong>'.luna_htmlspecialchars($cur_post['admin_note']).'</strong></span></dd>';
		}
	} else { // If the poster is a guest (or a user that has been deleted)
		$username = luna_htmlspecialchars($cur_post['username']);
		$user_title = get_title($cur_post);

		if ($luna_user['is_admmod'])
			$user_info[] = '<dd><span><a href="backstage/moderate.php?get_host='.$cur_post['sender_id'].'" title="'.$cur_post['sender_ip'].'">'.$lang_topic['IP address logged'].'</a></span></dd>';

		if ($luna_config['o_show_user_info'] == '1' && $cur_post['poster_email'] != '' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
			$user_contacts[] = '<span class="email"><a href="mailto:'.$cur_post['poster_email'].'">'.$lang['Email'].'</a></span>';
	}
	
	$username_quickreply = luna_htmlspecialchars($cur_post['username']);

		// Generation post action array (reply, delete etc.)
		if ($luna_user['id'] == $cur_post['sender_id'] || $luna_user['is_admmod']) {
			$post_actions[] = '<li class="postdelete"><span><a href="viewinbox.php?action=delete&amp;mid='.$cur_post['mid'].'&amp;tid='.$cur_post['shared_id'].'">'.$lang_topic['Delete'].'</a></span></li>';
			$post_actions[] = '<li class="postedit"><span><a href="new_inbox.php?edit='.$cur_post['mid'].'&amp;tid='.$cur_post['shared_id'].'">'.$lang_topic['Edit'].'</a></span></li>';
		}
		$post_actions[] = '<li class="postquote"><span><a href="new_inbox.php?reply='.$cur_post['shared_id'].'&amp;quote='.$cur_post['mid'].'">'.$lang_topic['Quote'].'</a></span></li>';

	// Perform the main parsing of the message (BBCode, smilies, censor words etc)
	$cur_post['message'] = parse_message($cur_post['message']);

	// Do signature parsing/caching
	if ($luna_config['o_signatures'] == '1' && $cur_post['signature'] != '' && $luna_user['show_sig'] != '0') {
		if (isset($signature_cache[$cur_post['id']]))
			$signature = $signature_cache[$cur_post['id']];
		else {
			$signature = parse_signature($cur_post['signature']);
			$signature_cache[$cur_post['id']] = $signature;
		}
	}
?>
<div id="p<?php echo $cur_post['id'] ?>" class="row comment <?php echo ($post_count % 2 == 0) ? ' roweven' : ' rowodd' ?><?php if ($cur_post['id'] == $cur_topic['first_post_id']) echo ' firstpost'; ?><?php if ($post_count == 1) echo ' onlypost'; ?><?php if ($cur_post['marked'] == true) echo ' marked'; ?>">
	<div class="col-xs-12">
		<div class="panel panel-default level<?php echo $cur_post['level'] ?>">
			<div class="panel-body">
				<div class="media">
					<a class="pull-left <?php echo $is_online; ?>" href="#">
						<?php echo $user_avatar; ?>
					</a>
					<div class="media-body">
						<h2><?php echo $username ?> <small><?php echo $user_title ?></small></h2>
						<a class="posttime" href="viewtopic.php?pid=<?php echo $cur_post['id'].'#p'.$cur_post['id'] ?>"><?php echo format_time($cur_post['posted']) ?></a>
					</div>
				</div>
				<hr />
				<?php echo $cur_post['message']."\n" ?>
				<?php if (($signature != '') || (!$luna_user['is_guest'])) echo '<hr />'; ?>
				<?php if ($signature != '') echo "\t\t\t\t\t".'<div class="postsignature">'.$signature.'</div>'."\n"; ?>
				<div class="pull-right post-actions"><?php if (count($post_actions)) echo implode(" &middot; ", $post_actions) ?></div>
			</div>
		</div>
	</div>
</div>
<?php	
}
?>
<p class="pagelink conl"><span class="pages-label"><?php echo $lang['Pages'].' '.paginate($num_pages, $page, 'viewinbox.php?tid='.$tid.'&amp;mid='.$mid)  ?></span></p>	
<?php
// Display quick post if enabled
if (!empty($reply_link) && $quickpost) {
?>
<div id="quickpost" class="blockform">
	<h2><span><?php echo $lang_topic['Quick post'] ?></span></h2>
	<div class="box">
		<form method="post" id="post" action="new_inbox.php?reply=<?php echo $tid ?>" onsubmit="return process_form(this)">
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang['Write message legend'] ?></legend>
					<div class="infldset txtarea">
						<input type="hidden" name="reply" value="<?php echo $tid ?>" />
						<input type="hidden" name="form_sent" value="1" />
						<input type="hidden" name="form_user" value="<?php echo luna_htmlspecialchars($luna_user['username']) ?>" />
						<input type="hidden" name="req_subject" value="<?php echo luna_htmlspecialchars($p_subject) ?>" />
						<input type="hidden" name="p_username" value="<?php echo luna_htmlspecialchars($r_usernames) ?>" />
						<label><textarea name="req_message" rows="7" cols="75" tabindex="1"></textarea></label>
						<ul class="bblinks">
							<li><span><a href="help.php#bbcode" onclick="window.open(this.href); return false;"><?php echo $lang['BBCode'] ?></a> <?php echo ($luna_config['p_message_bbcode'] == '1') ? $lang['on'] : $lang['off']; ?></span></li>
							<li><span><a href="help.php#img" onclick="window.open(this.href); return false;"><?php echo $lang['img tag'] ?></a> <?php echo ($luna_config['p_message_img_tag'] == '1') ? $lang['on'] : $lang['off']; ?></span></li>
							<li><span><a href="help.php#smilies" onclick="window.open(this.href); return false;"><?php echo $lang['Smilies'] ?></a> <?php echo ($luna_config['o_smilies'] == '1') ? $lang['on'] : $lang['off']; ?></span></li>
						</ul>
					</div>
				</fieldset>
			</div>
			<p class="buttons"><input type="submit" name="submit" tabindex="2" value="<?php echo $lang['Submit'] ?>" accesskey="s" /><input type="submit" name="preview" tabindex="2" value="<?php echo $lang_topic['Preview'] ?>" accesskey="s" /></p>
		</form>
	</div>
</div>
<?php
}
?>
</div>
<?php
require load_page('footer.php');
?>