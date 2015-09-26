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

$tid = isset($_GET['tid']) ? intval($_GET['tid']) : 0;
$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
if ($tid < 1 && $fid < 1 || $tid > 0 && $fid > 0)
	message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

// Fetch some info about the thread and/or the forum
if ($tid)
	$result = $db->query('SELECT f.id AS fid, f.forum_name, f.moderators, f.color, fp.post_replies, fp.post_topics, t.subject, t.closed, s.user_id AS is_subscribed, t.id AS tid FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') LEFT JOIN '.$db->prefix.'topic_subscriptions AS s ON (t.id=s.topic_id AND s.user_id='.$luna_user['id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$tid) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());
else
	$result = $db->query('SELECT f.id AS fid, f.forum_name, f.moderators, f.color, fp.post_replies, fp.post_topics FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$fid) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());

if (!$db->num_rows($result))
	message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

$cur_posting = $db->fetch_assoc($result);
$is_subscribed = $tid && $cur_posting['is_subscribed'];

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_posting['moderators'] != '') ? unserialize($cur_posting['moderators']) : array();
$is_admmod = ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && array_key_exists($luna_user['username'], $mods_array))) ? true : false;

if ($tid && $luna_config['o_censoring'] == '1')
	$cur_posting['subject'] = censor_words($cur_posting['subject']);

// Do we have permission to post?
if ((($tid && (($cur_posting['post_replies'] == '' && $luna_user['g_post_replies'] == '0') || $cur_posting['post_replies'] == '0')) ||
	($fid && (($cur_posting['post_topics'] == '' && $luna_user['g_post_topics'] == '0') || $cur_posting['post_topics'] == '0')) ||
	(isset($cur_posting['closed']) && $cur_posting['closed'] == '1')) &&
	!$is_admmod)
	message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');

// Start with a clean slate
$errors = array();

// Did someone just hit "Submit" or "Preview"?
if (isset($_POST['form_sent'])) {
	// Flood protection
	if (!isset($_POST['preview']) && $luna_user['last_post'] != '' && (time() - $luna_user['last_post']) < $luna_user['g_post_flood'])
		$errors[] = sprintf(__('At least %s seconds have to pass between comments. Please wait %s seconds and try posting again.', 'luna'), $luna_user['g_post_flood'], $luna_user['g_post_flood'] - (time() - $luna_user['last_post']));

	// Make sure they got here from the site
	if (($fid && (!isset($_POST['_luna_nonce_post_topic']) || !LunaNonces::verify($_POST['_luna_nonce_post_topic'],'post-reply'))) ||
	   (!$fid && (!isset($_POST['_luna_nonce_post_reply']) || !LunaNonces::verify($_POST['_luna_nonce_post_reply'],'post-reply'))))
		message(__('Are you sure you want to do this?', 'luna'));

	// If it's a new thread
	if ($fid) {
		$subject = luna_trim($_POST['req_subject']);

		if ($luna_config['o_censoring'] == '1')
			$censored_subject = luna_trim(censor_words($subject));

		if ($subject == '')
			$errors[] = __('Threads must contain a subject.', 'luna');
		elseif ($luna_config['o_censoring'] == '1' && $censored_subject == '')
			$errors[] = __('Threads must contain a subject. After applying censoring filters, your subject was empty.', 'luna');
		elseif (luna_strlen($subject) > 70)
			$errors[] = __('Subjects cannot be longer than 70 characters.', 'luna');
		elseif ($luna_config['p_subject_all_caps'] == '0' && is_all_uppercase($subject) && !$luna_user['is_admmod'])
			$errors[] = __('Subjects cannot contain only capital letters.', 'luna');
	}

	// If the user is logged in we get the username and email from $luna_user
	if (!$luna_user['is_guest']) {
		$username = $luna_user['username'];
		$email = $luna_user['email'];
		$id = $luna_user['id'];
	}
	// Otherwise it should be in $_POST
	else {
		$username = luna_trim($_POST['req_username']);
		$email = strtolower(luna_trim(($luna_config['p_force_guest_email'] == '1') ? $_POST['req_email'] : $_POST['email']));
		$banned_email = false;

		// It's a guest, so we have to validate the username
		check_username($username);

		if ($luna_config['p_force_guest_email'] == '1' || $email != '') {
			require FORUM_ROOT.'include/email.php';
			if (!is_valid_email($email))
				$errors[] = __('The email address you entered is invalid.', 'luna');

			// Check if it's a banned email address
			// we should only check guests because members' addresses are already verified
			if ($luna_user['is_guest'] && is_banned_email($email)) {
				if ($luna_config['p_allow_banned_email'] == '0')
					$errors[] = __('The email address you entered is banned in this forum. Please choose another email address.', 'luna');

				$banned_email = true; // Used later when we send an alert email
			}
		}
	}

	// Clean up message from POST
	$orig_message = $message = luna_linebreaks(luna_trim($_POST['req_message']));

	// Here we use strlen() not luna_strlen() as we want to limit the comment to FORUM_MAX_POSTSIZE bytes, not characters
	if (strlen($message) > FORUM_MAX_POSTSIZE)
		$errors[] = sprintf(__('Comments cannot be longer than %s bytes.', 'luna'), forum_number_format(FORUM_MAX_POSTSIZE));
	elseif ($luna_config['p_message_all_caps'] == '0' && is_all_uppercase($message) && !$luna_user['is_admmod'])
		$errors[] = __('Comments cannot contain only capital letters.', 'luna');

	// Validate BBCode syntax
	require FORUM_ROOT.'include/parser.php';
	$message = preparse_bbcode($message, $errors);

	if (empty($errors)) {
		if ($message == '')
			$errors[] = __('You must enter a message.', 'luna');
		elseif ($luna_config['o_censoring'] == '1') {
			// Censor message to see if that causes problems
			$censored_message = luna_trim(censor_words($message));

			if ($censored_message == '')
				$errors[] = __('You must enter a message. After applying censoring filters, your message was empty.', 'luna');
		}
	}

	$hide_smilies = isset($_POST['hide_smilies']) ? '1' : '0';
	$subscribe = isset($_POST['subscribe']) ? '1' : '0';
	$stick_topic = isset($_POST['stick_topic']) && $is_admmod ? '1' : '0';

	// Replace four-byte characters (MySQL cannot handle them)
	$message = strip_bad_multibyte_chars($message);

	$now = time();

	// Did everything go according to plan?
	if (empty($errors) && !isset($_POST['preview'])) {
		require FORUM_ROOT.'include/search_idx.php';

		// If it's a reply
		if ($tid) {
			if (!$luna_user['is_guest']) {
				$new_tid = $tid;

				// Insert the new comment
				$db->query('INSERT INTO '.$db->prefix.'posts (poster, poster_id, poster_ip, message, hide_smilies, posted, topic_id) VALUES(\''.$db->escape($username).'\', '.$luna_user['id'].', \''.$db->escape(get_remote_address()).'\', \''.$db->escape($message).'\', '.$hide_smilies.', '.$now.', '.$tid.')') or error('Unable to create post', __FILE__, __LINE__, $db->error());
				$new_pid = $db->insert_id();

				// To subscribe or not to subscribe, that ...
				if ($luna_config['o_topic_subscriptions'] == '1') {
					if ($subscribe && !$is_subscribed)
						$db->query('INSERT INTO '.$db->prefix.'topic_subscriptions (user_id, topic_id) VALUES('.$luna_user['id'].' ,'.$tid.')') or error('Unable to add subscription', __FILE__, __LINE__, $db->error());
					elseif (!$subscribe && $is_subscribed)
						$db->query('DELETE FROM '.$db->prefix.'topic_subscriptions WHERE user_id='.$luna_user['id'].' AND topic_id='.$tid) or error('Unable to remove subscription', __FILE__, __LINE__, $db->error());
				}
			} else {
				// It's a guest. Insert the new comment
				$email_sql = ($luna_config['p_force_guest_email'] == '1' || $email != '') ? '\''.$db->escape($email).'\'' : 'NULL';
				$db->query('INSERT INTO '.$db->prefix.'posts (poster, poster_ip, poster_email, message, hide_smilies, posted, topic_id) VALUES(\''.$db->escape($username).'\', \''.$db->escape(get_remote_address()).'\', '.$email_sql.', \''.$db->escape($message).'\', '.$hide_smilies.', '.$now.', '.$tid.')') or error('Unable to create post', __FILE__, __LINE__, $db->error());
				$new_pid = $db->insert_id();
			}

			if (!$luna_user['is_guest'])
				$user_id_poster = $db->escape($id);
			else
				$user_id_poster = '1';

			// Update topic
			$db->query('UPDATE '.$db->prefix.'topics SET num_replies=num_replies+1, last_post='.$now.', last_post_id='.$new_pid.', last_poster=\''.$db->escape($username).'\', last_poster_id=\''.$user_id_poster.'\' WHERE id='.$tid) or error('Unable to update topic', __FILE__, __LINE__, $db->error());

			update_search_index('post', $new_pid, $message);

			update_forum($cur_posting['fid']);

			// Should we send out notifications?
			if ($luna_config['o_topic_subscriptions'] == '1') {
				// Get the comment time for the previous post in this thread
				$result = $db->query('SELECT posted FROM '.$db->prefix.'posts WHERE topic_id='.$tid.' ORDER BY id DESC LIMIT 1, 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
				$previous_post_time = $db->result($result);

				// Get any subscribed users that should be notified (banned users are excluded)
				$result = $db->query('SELECT u.id, u.email, u.notify_with_post, u.language FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'topic_subscriptions AS s ON u.id=s.user_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id='.$cur_posting['fid'].' AND fp.group_id=u.group_id) LEFT JOIN '.$db->prefix.'online AS o ON u.id=o.user_id LEFT JOIN '.$db->prefix.'bans AS b ON u.username=b.username WHERE b.username IS NULL AND COALESCE(o.logged, u.last_visit)>'.$previous_post_time.' AND (fp.read_forum IS NULL OR fp.read_forum=1) AND s.topic_id='.$tid.' AND u.id!='.$luna_user['id']) or error('Unable to fetch subscription info', __FILE__, __LINE__, $db->error());
				if ($db->num_rows($result)) {
					require_once FORUM_ROOT.'include/email.php';

					$notification_emails = array();

					if ($luna_config['o_censoring'] == '1')
						$cleaned_message = bbcode2email($censored_message, -1);
					else
						$cleaned_message = bbcode2email($message, -1);

					// Loop through subscribed users and send emails
					while ($cur_subscriber = $db->fetch_assoc($result)) {
						// First of all, add a new notification
						new_notification($cur_subscriber['id'], get_base_url().'/viewtopic.php?pid='.$new_pid.'#p'.$new_pid, $username.' replied to '.$cur_posting['subject'], 'fa-reply');

						// Is the subscription email for $cur_subscriber['language'] cached or not?
						if (!isset($notification_emails[$cur_subscriber['language']])) {
								// Load the "new reply" template
								$mail_tpl = trim(__('Subject: Reply to thread: "<thread_subject>"

<replier> has replied to the thread "<thread_subject>" to which you are subscribed. There may be more new replies, but this is the only notification you will receive until you visit the board again.

The comment is located at <comment_url>

You can unsubscribe by going to <unsubscribe_url>

--
<board_mailer> Mailer
(Do not reply to this message)', 'luna'));

								// Load the "new reply full" template (with post included)
								$mail_tpl_full = trim(__('Subject: Reply to thread: "<thread_subject>"

<replier> has replied to the thread "<thread_subject>" to which you are subscribed. There may be more new replies, but this is the only notification you will receive until you visit the board again.

The comment is located at <comment_url>

The message reads as follows:
-----------------------------------------------------------------------

<message>

-----------------------------------------------------------------------

You can unsubscribe by going to <unsubscribe_url>

--
<board_mailer> Mailer
(Do not reply to this message)', 'luna'));

								// The first row contains the subject (it also starts with "Subject:")
								$first_crlf = strpos($mail_tpl, "\n");
								$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
								$mail_message = trim(substr($mail_tpl, $first_crlf));

								$first_crlf = strpos($mail_tpl_full, "\n");
								$mail_subject_full = trim(substr($mail_tpl_full, 8, $first_crlf-8));
								$mail_message_full = trim(substr($mail_tpl_full, $first_crlf));

								$mail_subject = str_replace('<thread_subject>', $cur_posting['subject'], $mail_subject);
								$mail_message = str_replace('<thread_subject>', $cur_posting['subject'], $mail_message);
								$mail_message = str_replace('<replier>', $username, $mail_message);
								$mail_message = str_replace('<comment_url>', get_base_url().'/viewtopic.php?pid='.$new_pid.'#p'.$new_pid, $mail_message);
								$mail_message = str_replace('<unsubscribe_url>', get_base_url().'/misc.php?action=unsubscribe&tid='.$tid, $mail_message);
								$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

								$mail_subject_full = str_replace('<thread_subject>', $cur_posting['subject'], $mail_subject_full);
								$mail_message_full = str_replace('<thread_subject>', $cur_posting['subject'], $mail_message_full);
								$mail_message_full = str_replace('<replier>', $username, $mail_message_full);
								$mail_message_full = str_replace('<message>', $cleaned_message, $mail_message_full);
								$mail_message_full = str_replace('<comment_url>', get_base_url().'/viewtopic.php?pid='.$new_pid.'#p'.$new_pid, $mail_message_full);
								$mail_message_full = str_replace('<unsubscribe_url>', get_base_url().'/misc.php?action=unsubscribe&tid='.$tid, $mail_message_full);
								$mail_message_full = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message_full);

								$notification_emails[$cur_subscriber['language']][0] = $mail_subject;
								$notification_emails[$cur_subscriber['language']][1] = $mail_message;
								$notification_emails[$cur_subscriber['language']][2] = $mail_subject_full;
								$notification_emails[$cur_subscriber['language']][3] = $mail_message_full;

								$mail_subject = $mail_message = $mail_subject_full = $mail_message_full = null;
						}

						// We have to double check here because the templates could be missing
						if (isset($notification_emails[$cur_subscriber['language']])) {
							if ($cur_subscriber['notify_with_post'] == '0')
								luna_mail($cur_subscriber['email'], $notification_emails[$cur_subscriber['language']][0], $notification_emails[$cur_subscriber['language']][1]);
							else
								luna_mail($cur_subscriber['email'], $notification_emails[$cur_subscriber['language']][2], $notification_emails[$cur_subscriber['language']][3]);
						}
					}

					unset($cleaned_message);
				}
			}
		}
		// If it's a new thread
		elseif ($fid) {
			if (!$luna_user['is_guest'])
				$user_id_poster = $db->escape($id);
			else
				$user_id_poster = '1';

			// Create the thread
			$db->query('INSERT INTO '.$db->prefix.'topics (poster, subject, posted, last_post, last_poster, last_poster_id, sticky, forum_id) VALUES(\''.$db->escape($username).'\', \''.$db->escape($subject).'\', '.$now.', '.$now.', \''.$db->escape($username).'\', '.$user_id_poster.', '.$stick_topic.', '.$fid.')') or error('Unable to create topic', __FILE__, __LINE__, $db->error());
			$new_tid = $db->insert_id();

			if (!$luna_user['is_guest']) {
				// To subscribe or not to subscribe, that ...
				if ($luna_config['o_topic_subscriptions'] == '1' && $subscribe)
					$db->query('INSERT INTO '.$db->prefix.'topic_subscriptions (user_id, topic_id) VALUES('.$luna_user['id'].' ,'.$new_tid.')') or error('Unable to add subscription', __FILE__, __LINE__, $db->error());

				// Create the comment ("topic post")
				$db->query('INSERT INTO '.$db->prefix.'posts (poster, poster_id, poster_ip, message, hide_smilies, posted, topic_id) VALUES(\''.$db->escape($username).'\', '.$luna_user['id'].', \''.$db->escape(get_remote_address()).'\', \''.$db->escape($message).'\', '.$hide_smilies.', '.$now.', '.$new_tid.')') or error('Unable to create post', __FILE__, __LINE__, $db->error());
			} else {
				// Create the comment ("topic post")
				$email_sql = ($luna_config['p_force_guest_email'] == '1' || $email != '') ? '\''.$db->escape($email).'\'' : 'NULL';
				$db->query('INSERT INTO '.$db->prefix.'posts (poster, poster_ip, poster_email, message, hide_smilies, posted, topic_id) VALUES(\''.$db->escape($username).'\', \''.$db->escape(get_remote_address()).'\', '.$email_sql.', \''.$db->escape($message).'\', '.$hide_smilies.', '.$now.', '.$new_tid.')') or error('Unable to create post', __FILE__, __LINE__, $db->error());
			}
			$new_pid = $db->insert_id();

			// Update the thread with last_post_id
			$db->query('UPDATE '.$db->prefix.'topics SET last_post_id='.$new_pid.', first_post_id='.$new_pid.' WHERE id='.$new_tid) or error('Unable to update topic', __FILE__, __LINE__, $db->error());

			update_search_index('post', $new_pid, $message, $subject);

			update_forum($fid);

			// Should we send out notifications?
			if ($luna_config['o_forum_subscriptions'] == '1') {
				// Get any subscribed users that should be notified (banned users are excluded)
				$result = $db->query('SELECT u.id, u.email, u.notify_with_post, u.language FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'forum_subscriptions AS s ON u.id=s.user_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id='.$cur_posting['fid'].' AND fp.group_id=u.group_id) LEFT JOIN '.$db->prefix.'bans AS b ON u.username=b.username WHERE b.username IS NULL AND (fp.read_forum IS NULL OR fp.read_forum=1) AND s.forum_id='.$cur_posting['fid'].' AND u.id!='.$luna_user['id']) or error('Unable to fetch subscription info', __FILE__, __LINE__, $db->error());
				if ($db->num_rows($result)) {
					require_once FORUM_ROOT.'include/email.php';

					$notification_emails = array();

					if ($luna_config['o_censoring'] == '1')
						$cleaned_message = bbcode2email($censored_message, -1);
					else
						$cleaned_message = bbcode2email($message, -1);

					// Loop through subscribed users and send emails
					while ($cur_subscriber = $db->fetch_assoc($result)) {
						// Is the subscription email for $cur_subscriber['language'] cached or not?
						if (!isset($notification_emails[$cur_subscriber['language']])) {
								// Load the "new thread" template
								$mail_tpl = trim(__('Subject: New thread in forum: "<forum_name>"

<commenter> has commented a new thread "<thread_subject>" in the forum "<forum_name>", to which you are subscribed.

The thread is located at <thread_url>

You can unsubscribe by going to <unsubscribe_url>

--
<board_mailer> Mailer
(Do not reply to this message)', 'luna'));

								// Load the "new thread full" template (with post included)
								$mail_tpl_full = trim(__('Subject: New thread in forum: "<forum_name>"

<commenter> has commented a new thread "<thread_subject>" in the forum "<forum_name>", to which you are subscribed.

The thread is located at <thread_url>

The message reads as follows:
-----------------------------------------------------------------------

<message>

-----------------------------------------------------------------------

You can unsubscribe by going to <unsubscribe_url>

--
<board_mailer> Mailer
(Do not reply to this message)', 'luna'));

								// The first row contains the subject (it also starts with "Subject:")
								$first_crlf = strpos($mail_tpl, "\n");
								$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
								$mail_message = trim(substr($mail_tpl, $first_crlf));

								$first_crlf = strpos($mail_tpl_full, "\n");
								$mail_subject_full = trim(substr($mail_tpl_full, 8, $first_crlf-8));
								$mail_message_full = trim(substr($mail_tpl_full, $first_crlf));

								$mail_subject = str_replace('<forum_name>', $cur_posting['forum_name'], $mail_subject);
								$mail_message = str_replace('<thread_subject>', $luna_config['o_censoring'] == '1' ? $censored_subject : $subject, $mail_message);
								$mail_message = str_replace('<forum_name>', $cur_posting['forum_name'], $mail_message);
								$mail_message = str_replace('<commenter>', $username, $mail_message);
								$mail_message = str_replace('<thread_url>', get_base_url().'/viewtopic.php?id='.$new_tid, $mail_message);
								$mail_message = str_replace('<unsubscribe_url>', get_base_url().'/misc.php?action=unsubscribe&fid='.$cur_posting['fid'], $mail_message);
								$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

								$mail_subject_full = str_replace('<forum_name>', $cur_posting['forum_name'], $mail_subject_full);
								$mail_message_full = str_replace('<thread_subject>', $luna_config['o_censoring'] == '1' ? $censored_subject : $subject, $mail_message_full);
								$mail_message_full = str_replace('<forum_name>', $cur_posting['forum_name'], $mail_message_full);
								$mail_message_full = str_replace('<commenter>', $username, $mail_message_full);
								$mail_message_full = str_replace('<message>', $cleaned_message, $mail_message_full);
								$mail_message_full = str_replace('<thread_url>', get_base_url().'/viewtopic.php?id='.$new_tid, $mail_message_full);
								$mail_message_full = str_replace('<unsubscribe_url>', get_base_url().'/misc.php?action=unsubscribe&fid='.$cur_posting['fid'], $mail_message_full);
								$mail_message_full = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message_full);

								$notification_emails[$cur_subscriber['language']][0] = $mail_subject;
								$notification_emails[$cur_subscriber['language']][1] = $mail_message;
								$notification_emails[$cur_subscriber['language']][2] = $mail_subject_full;
								$notification_emails[$cur_subscriber['language']][3] = $mail_message_full;

								$mail_subject = $mail_message = $mail_subject_full = $mail_message_full = null;
						}

						// We have to double check here because the templates could be missing
						if (isset($notification_emails[$cur_subscriber['language']])) {
							if ($cur_subscriber['notify_with_post'] == '0')
								luna_mail($cur_subscriber['email'], $notification_emails[$cur_subscriber['language']][0], $notification_emails[$cur_subscriber['language']][1]);
							else
								luna_mail($cur_subscriber['email'], $notification_emails[$cur_subscriber['language']][2], $notification_emails[$cur_subscriber['language']][3]);
						}
					}

					unset($cleaned_message);
				}
			}
		}

		// If we previously found out that the email was banned
		if ($luna_user['is_guest'] && $banned_email && $luna_config['o_mailing_list'] != '') {
			// Load the "banned email post" template
			$mail_tpl = trim(__('Subject: Alert - Banned email detected

User "<username>" commented with banned email address: <email>

Comment URL: <comment_url>

--
<board_mailer> Mailer
(Do not reply to this message)', 'luna'));

			// The first row contains the subject
			$first_crlf = strpos($mail_tpl, "\n");
			$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
			$mail_message = trim(substr($mail_tpl, $first_crlf));

			$mail_message = str_replace('<username>', $username, $mail_message);
			$mail_message = str_replace('<email>', $email, $mail_message);
			$mail_message = str_replace('<comment_url>', get_base_url().'/viewtopic.php?pid='.$new_pid.'#p'.$new_pid, $mail_message);
			$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

			luna_mail($luna_config['o_mailing_list'], $mail_subject, $mail_message);
		}

		// If the commenting user is logged in, increment his/her post count
		if (!$luna_user['is_guest']) {
			$db->query('UPDATE '.$db->prefix.'users SET num_posts=num_posts+1, last_post='.$now.' WHERE id='.$luna_user['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());

			$tracked_topics = get_tracked_topics();
			$tracked_topics['topics'][$new_tid] = time();
			set_tracked_topics($tracked_topics);
		} else {
			$db->query('UPDATE '.$db->prefix.'online SET last_post='.$now.' WHERE ident=\''.$db->escape(get_remote_address()).'\'' ) or error('Unable to update user', __FILE__, __LINE__, $db->error());
		}

		redirect('viewtopic.php?pid='.$new_pid.'#p'.$new_pid);
	}
}


// If a thread ID was specified in the url (it's a reply)
if ($tid) {
	$action = __('Add comment', 'luna');
	$form = '<form id="post" method="post" action="post.php?action=post&amp;tid='.$tid.'" onsubmit="window.onbeforeunload=null;this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">';

	// If a quote ID was specified in the url
	if (isset($_GET['qid'])) {
		$qid = intval($_GET['qid']);
		if ($qid < 1)
			message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

		$result = $db->query('SELECT poster, message FROM '.$db->prefix.'posts WHERE id='.$qid.' AND topic_id='.$tid) or error('Unable to fetch quote info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

		list($q_poster, $q_message) = $db->fetch_row($result);

		// If the message contains a code tag we have to split it up (text within [code][/code] shouldn't be touched)
		if (strpos($q_message, '[code]') !== false && strpos($q_message, '[/code]') !== false) {
			list($inside, $outside) = split_text($q_message, '[code]', '[/code]');

			$q_message = implode("\1", $outside);
		}

		// Remove [img] tags from quoted message
		$q_message = preg_replace('%\[img(?:=(?:[^\[]*?))?\]((ht|f)tps?://)([^\s<"]*?)\[/img\]%U', '\1\3', $q_message);

		// If we split up the message before we have to concatenate it together again (code tags)
		if (isset($inside)) {
			$outside = explode("\1", $q_message);
			$q_message = '';

			$num_tokens = count($outside);
			for ($i = 0; $i < $num_tokens; ++$i) {
				$q_message .= $outside[$i];
				if (isset($inside[$i]))
					$q_message .= '[code]'.$inside[$i].'[/code]';
			}

			unset($inside);
		}

		if ($luna_config['o_censoring'] == '1')
			$q_message = censor_words($q_message);

		$q_message = luna_htmlspecialchars($q_message);

		// If username contains a square bracket, we add "" or '' around it (so we know when it starts and ends)
		if (strpos($q_poster, '[') !== false || strpos($q_poster, ']') !== false) {
			if (strpos($q_poster, '\'') !== false)
				$q_poster = '"'.$q_poster.'"';
			else
				$q_poster = '\''.$q_poster.'\'';
		} else {
			// Get the characters at the start and end of $q_poster
			$ends = substr($q_poster, 0, 1).substr($q_poster, -1, 1);

			// Deal with quoting "Username" or 'Username' (becomes '"Username"' or "'Username'")
			if ($ends == '\'\'')
				$q_poster = '"'.$q_poster.'"';
			elseif ($ends == '""')
				$q_poster = '\''.$q_poster.'\'';
		}

		$quote = '[quote='.$q_poster.']'.$q_message.'[/quote]'."\n";
	}
}
// If a forum ID was specified in the url (new thread)
elseif ($fid) {
	$action = __('Create thread', 'luna');
	$form = '<form id="post" method="post" action="post.php?action=post&amp;fid='.$fid.'" onsubmit="window.onbeforeunload=null;return process_form(this)">';
} else
	message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');


$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $action);
$required_fields = array('req_email' => __('Email', 'luna'), 'req_subject' => __('Subject', 'luna'), 'req_message' => __('Message', 'luna'));
$focus_element = array('post');

if (!$luna_user['is_guest'])
	$focus_element[] = ($fid) ? 'req_subject' : 'req_message';
else {
	$required_fields['req_username'] = __('Name', 'luna');
	$focus_element[] = 'req_username';
}

$cur_index = 1;
define('FORUM_ACTIVE_PAGE', 'post');
require load_page('header.php');

require load_page('post.php');

require load_page('footer.php');
