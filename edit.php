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

// Fetch some info about the comment, the thread and the forum
$result = $db->query('SELECT f.id AS fid, f.forum_name, f.moderators, f.color, fp.post_replies, fp.post_topics, t.id AS tid, t.subject, t.posted, t.first_post_id, t.sticky, t.closed, p.poster, p.poster_id, p.message, p.hide_smilies FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND p.id='.$id) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

$cur_post = $db->fetch_assoc($result);

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_post['moderators'] != '') ? unserialize($cur_post['moderators']) : array();
$is_admmod = ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && array_key_exists($luna_user['username'], $mods_array))) ? true : false;

$can_edit_subject = $id == $cur_post['first_post_id'];

if ($luna_config['o_censoring'] == '1') {
	$cur_post['subject'] = censor_words($cur_post['subject']);
	$cur_post['message'] = censor_words($cur_post['message']);
}

// Do we have permission to edit this post?
if (($luna_user['g_edit_posts'] == '0' ||
	$cur_post['poster_id'] != $luna_user['id'] ||
	$cur_post['closed'] == '1') &&
	!$is_admmod)
	message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');

if ($is_admmod && $luna_user['g_id'] != FORUM_ADMIN && in_array($cur_post['poster_id'], get_admin_ids()))
	message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');

// Start with a clean slate
$errors = array();


if (isset($_POST['form_sent'])) {
	// Make sure they got here from the site
	if (!isset($_POST['_luna_nonce_edit_post']) || !LunaNonces::verify($_POST['_luna_nonce_edit_post'],'edit-post'))
		message(__('Are you sure you want to do this?', 'luna'));

	// If it's a thread it must contain a subject
	if ($can_edit_subject) {
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

	// Clean up message from POST
	$message = luna_linebreaks(luna_trim($_POST['req_message']));

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
	$stick_topic = isset($_POST['stick_topic']) ? '1' : '0';
	if (!$is_admmod)
		$stick_topic = $cur_post['sticky'];

	// Replace four-byte characters (MySQL cannot handle them)
	$message = strip_bad_multibyte_chars($message);

	// Did everything go according to plan?
	if (empty($errors) && !isset($_POST['preview'])) {
		$edited_sql = (!isset($_POST['silent']) || !$is_admmod) ? ', edited='.time().', edited_by=\''.$db->escape($luna_user['username']).'\'' : '';

		require FORUM_ROOT.'include/search_idx.php';

		if ($can_edit_subject) {
			// Update the thread and any redirect topics
			$db->query('UPDATE '.$db->prefix.'topics SET subject=\''.$db->escape($subject).'\', sticky='.$stick_topic.' WHERE id='.$cur_post['tid'].' OR moved_to='.$cur_post['tid']) or error('Unable to update topic', __FILE__, __LINE__, $db->error());

			// We changed the subject, so we need to take that into account when we update the search words
			update_search_index('edit', $id, $message, $subject);
		} else
			update_search_index('edit', $id, $message);

		// Update the comment
		$db->query('UPDATE '.$db->prefix.'posts SET message=\''.$db->escape($message).'\', hide_smilies='.$hide_smilies.$edited_sql.' WHERE id='.$id) or error('Unable to update post', __FILE__, __LINE__, $db->error());

		redirect('viewtopic.php?pid='.$id.'#p'.$id);
	}
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Edit comment', 'luna'));
$required_fields = array('req_subject' => __('Subject', 'luna'), 'req_message' => __('Message', 'luna'));
$focus_element = array('edit', 'req_message');
define('FORUM_ACTIVE_PAGE', 'edit');
require load_page('header.php');

$cur_index = 1;

require load_page('edit.php');

require load_page('footer.php');
