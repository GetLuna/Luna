<?php

/*
 * Copyright (C) 2014-2015 Luna
 * Based on work by Adaur (2010), Vincent Garnier, Connorhd and David 'Chacmool' Djurback
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/inbox_functions.php';

// No guest here !
if ($luna_user['is_guest'])
	message($lang['No permission']);
	
// User enable PM ?
if (!$luna_user['use_pm'] == '1')
	message($lang['No permission']);

// Are we allowed to use this ?
if (!$luna_config['o_pms_enabled'] == '1' || $luna_user['g_pm'] == '0')
	message($lang['No permission']);

// Load the additionals language files
require FORUM_ROOT.'lang/'.$luna_user['language'].'/language.php';

$p_destinataire = '';
$p_contact = '';
$p_subject = '';
$p_message = '';

// Clean informations
$r = (isset($_REQUEST['reply']) ? intval($_REQUEST['reply']) : '0');
$q = (isset($_REQUEST['quote']) ? intval($_REQUEST['quote']) : '0');
$edit = isset($_REQUEST['edit']) ? intval($_REQUEST['edit']) : '0';
$tid = isset($_REQUEST['tid']) ? intval($_REQUEST['tid']) : '0';
$mid = isset($_REQUEST['mid']) ? intval($_REQUEST['mid']) : '0';

$errors = array();

if (!empty($r) && !isset($_POST['form_sent'])) { // It's a reply
	// Make sure they got here from the site
	confirm_referrer(array('new_inbox.php', 'viewinbox.php'));
	
	$result = $db->query('SELECT DISTINCT owner, receiver FROM '.$db->prefix.'messages WHERE shared_id='.$r) or error('Unable to get the informations of the message', __FILE__, __LINE__, $db->error());
	
	if (!$db->num_rows($result))
		message($lang['Bad request']);
		
	$p_ids = array();
		
	while ($arry_dests = $db->fetch_assoc($result)) {	
		if ($arry_dests['receiver'] == '0')
			message($lang['Bad request']);
			
		$p_ids[] = $arry_dests['owner'];
	}
	
	if (!in_array($luna_user['id'], $p_ids)) // Are we in the array? If not, we add ourselves
		$p_ids[] = $luna_user['id'];
	
	$p_ids = implode(', ', $p_ids);
	
	$result_subject = $db->query('SELECT subject FROM '.$db->prefix.'messages WHERE shared_id='.$r.' AND show_message=1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());

	if (!$db->num_rows($result_subject))
		message($lang['Bad request']);

	$p_subject = $db->result($result_subject);
	
	$result_username = $db->query('SELECT username FROM '.$db->prefix.'users WHERE id IN ('.$p_ids.')') or error('Unable to find the owners of the message', __FILE__, __LINE__, $db->error());
	
	if (!$db->num_rows($result_username))
		message($lang['Bad request']);
		
	$p_destinataire = array();
	
	while ($username_result = $db->fetch_assoc($result_username)) {
		$p_destinataire[] = $username_result['username'];
	}
	
	$p_destinataire = implode(', ', $p_destinataire);
	
	if (!empty($q) && $q > '0') { // It's a reply with a quote
		// Get message info
		$result = $db->query('SELECT sender, message FROM '.$db->prefix.'messages WHERE id='.$q.' AND owner='.$luna_user['id']) or error('Unable to find the informations of the message', __FILE__, __LINE__, $db->error());
			
		if (!$db->num_rows($result))
			message($lang['Bad request']);
			
		$re_message = $db->fetch_assoc($result);
		
		// Quote the message
		$p_message = '[quote='.$re_message['sender'].']'.$re_message['message'].'[/quote]';
	}
} if (!empty($edit) && !isset($_POST['form_sent'])) { // It's an edit
	// Make sure they got here from the site
	confirm_referrer(array('new_inbox.php', 'viewinbox.php'));
	
	// Check that $edit looks good
	if ($edit <= 0)
		message($lang['Bad request']);
	
	$result = $db->query('SELECT sender_id, message, receiver FROM '.$db->prefix.'messages WHERE id='.$edit) or error('Unable to get the informations of the message', __FILE__, __LINE__, $db->error());
	
	if (!$db->num_rows($result))
		message($lang['Bad request']);
		
	$edit_msg = $db->fetch_assoc($result);
	
	// If you're not the owner of this message, why do you want to edit it?
	if ($edit_msg['sender_id'] != $luna_user['id'] && !$luna_user['is_admmod'] || $edit_msg['receiver'] == '0' && !$luna_user['is_admmod'])
		message($lang['No permission']);

	// Insert the message
	$p_message = censor_words($edit_msg['message']);
} if (isset($_POST['form_sent'])) { // The post button has been pressed
	// Make sure they got here from the site
	confirm_referrer(array('new_inbox.php', 'viewinbox.php'));
	
	$hide_smilies = isset($_POST['hide_smilies']) ? '1' : '0';
	
	// Make sure form_user is correct
	if ($_POST['form_user'] != $luna_user['username'])
		message($lang['Bad request']);
	
	// Flood protection by Newman
	if (!isset($_SESSION))
		session_start();

	if(!$edit && !isset($_POST['preview']) && $_SESSION['last_session_request'] > time() - $luna_user['g_post_flood'])
		$errors[] = sprintf( $lang['Flood'], $luna_user['g_post_flood'] );
		
	// Check users boxes
	if ($luna_user['g_pm_limit'] != '0' && !$luna_user['is_admmod'] && $luna_user['num_pms'] >= $luna_user['g_pm_limit'])
		$errors[] = $lang['Sender full'];
	
	// Build receivers list
	$p_destinataire = isset($_POST['p_username']) ? luna_trim($_POST['p_username']) : '';
	$p_contact = isset($_POST['p_contact']) ? luna_trim($_POST['p_contact']) : '';
	$dest_list = explode(', ', $p_destinataire);
	
	if (!in_array($luna_user['username'], $dest_list))
		$dest_list[] = $luna_user['username'];
	
	if ($p_contact != '0')
		$dest_list[] = $p_contact;
	
	$dest_list = array_map('luna_trim', $dest_list);
	$dest_list = array_unique($dest_list);
	
	foreach ($dest_list as $k=>$v) {
		if ($v == '') unset($dest_list[$k]);
	}

	 if (count($dest_list) < '1' && $edit == '0')
		$errors[] = $lang['Must receiver'];
		elseif (count($dest_list) > $luna_config['o_pms_max_receiver'])
		$errors[] = sprintf($lang['Too many receiver'], $luna_config['o_pms_max_receiver']-1);

	$destinataires = array(); $i = '0';
	$list_ids = array();
	$list_usernames = array();
	foreach ($dest_list as $destinataire) {
		// Get receiver infos
		$result_username = $db->query("SELECT u.id, u.username, u.email, u.notify_pm, u.notify_pm_full, u.use_pm, u.num_pms, g.g_id, g.g_pm_limit, g.g_pm FROM ".$db->prefix."users AS u INNER JOIN ".$db->prefix."groups AS g ON (u.group_id=g.g_id) LEFT JOIN ".$db->prefix."messages AS pm ON (pm.owner=u.id) WHERE u.id!=1 AND u.username='".$db->escape($destinataire)."' GROUP BY u.username, u.id, g.g_id") or error("Unable to get user ID", __FILE__, __LINE__, $db->error());

		// List users infos
		if ($destinataires[$i] = $db->fetch_assoc($result_username)) {
			// Begin to build the IDs' list - Thanks to Yacodo!
			$list_ids[] = $destinataires[$i]['id'];
			// Did the user left?
			if (!empty($r)) {
				$result = $db->query('SELECT 1 FROM '.$db->prefix.'messages WHERE shared_id='.$r.' AND show_message=1 AND owner='.$destinataires[$i]['id']) or error('Unable to get the informations of the message', __FILE__, __LINE__, $db->error());
				if (!$db->num_rows($result))
					$errors[] = sprintf($lang['User left'], luna_htmlspecialchars($destinataire));
			}
			// Begin to build usernames' list
			$list_usernames[] = $destinataires[$i]['username'];
			// Receivers enable PM ?
			if (!$destinataires[$i]['use_pm'] == '1' || !$destinataires[$i]['g_pm'] == '1')
				$errors[] = sprintf($lang['User disable PM'], luna_htmlspecialchars($destinataire));			
			// Check receivers boxes
			elseif ($destinataires[$i]['g_id'] > FORUM_GUEST && $destinataires[$i]['g_pm_limit'] != '0' && $destinataires[$i]['num_pms'] >= $destinataires[$i]['g_pm_limit'])
				$errors[] = sprintf($lang['Dest full'], luna_htmlspecialchars($destinataire));	
			// Are we authorized?
			elseif (!$luna_user['is_admmod'] && $destinataires[$i]['allow_msg'] == '0')
				$errors[] = sprintf($lang['User blocked'], luna_htmlspecialchars($destinataire));
		} else
			$errors[] = sprintf($lang['No user'], luna_htmlspecialchars($destinataire));
		$i++;
	}
	// Build IDs' & usernames' list : the end
	$ids_list = implode(', ', $list_ids);
	$usernames_list = implode(', ', $list_usernames);
	
	// Check subject
	$p_subject = luna_trim($_POST['req_subject']);
	
	if ($p_subject == '' && $edit == '0')
		$errors[] = $lang['No subject'];
	elseif (luna_strlen($p_subject) > '70')
		$errors[] = $lang['Too long subject'];
	elseif ($luna_config['p_subject_all_caps'] == '0' && strtoupper($p_subject) == $p_subject && $luna_user['is_admmod'])
		$p_subject = ucwords(strtolower($p_subject));

	// Clean up message from POST
	$p_message = luna_linebreaks(luna_trim($_POST['req_message']));

	// Check message
	if ($p_message == '')
		$errors[] = $lang['No message'];

	// Here we use strlen() not luna_strlen() as we want to limit the post to FORUM_MAX_POSTSIZE bytes, not characters
	elseif (strlen($p_message) > FORUM_MAX_POSTSIZE)
		$errors[] = sprintf($lang['Too long message'], forum_number_format(FORUM_MAX_POSTSIZE));
	elseif ($luna_config['p_message_all_caps'] == '0' && strtoupper($p_message) == $p_message && $luna_user['is_admmod'])
		$p_message = ucwords(strtolower($p_message));

	// Validate BBCode syntax
	require FORUM_ROOT.'include/parser.php';
	$p_message = preparse_bbcode($p_message, $errors);
	
	if (empty($errors) && !isset($_POST['preview'])) { // Send message(s)	
		$_SESSION['last_session_request'] = $now = time();
		
		if (empty($r) && empty($edit)) { // It's a new message
			$result_shared = $db->query('SELECT last_shared_id FROM '.$db->prefix.'messages ORDER BY last_shared_id DESC LIMIT 1') or error('Unable to fetch last_shared_id', __FILE__, __LINE__, $db->error());
			if (!$db->num_rows($result_shared))
				$shared_id = '1';
			else {
				$shared_result = $db->result($result_shared);
				$shared_id = $shared_result + '1';
			}
				
			foreach ($destinataires as $dest) {
				$val_showed = '0';
				
				if ($dest['id'] == $luna_user['id'])
					$val_showed = '1';
				else
					$val_showed = '0';
					
				$db->query('INSERT INTO '.$db->prefix.'messages (shared_id, last_shared_id, owner, subject, message, sender, receiver, sender_id, receiver_id, sender_ip, hide_smilies, posted, show_message, showed) VALUES(\''.$shared_id.'\', \''.$shared_id.'\', \''.$dest['id'].'\', \''.$db->escape($p_subject).'\', \''.$db->escape($p_message).'\', \''.$db->escape($luna_user['username']).'\', \''.$db->escape($usernames_list).'\', \''.$luna_user['id'].'\', \''.$db->escape($ids_list).'\', \''.get_remote_address().'\', \''.$hide_smilies.'\',  \''.$now.'\', \'1\', \''.$val_showed.'\')') or error('Unable to send the message.', __FILE__, __LINE__, $db->error());
				$new_mp = $db->insert_id();
				$db->query('UPDATE '.$db->prefix.'messages SET last_post_id='.$new_mp.', last_post='.$now.', last_poster=\''.$db->escape($luna_user['username']).'\' WHERE shared_id='.$shared_id.' AND show_message=1 AND owner='.$dest['id']) or error('Unable to update the message.', __FILE__, __LINE__, $db->error());
				$db->query('UPDATE '.$db->prefix.'users SET num_pms=num_pms+1 WHERE id='.$dest['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());
				// E-mail notification
				if ($luna_config['o_pms_notification'] == '1' && $dest['notify_pm'] == '1' && $dest['id'] != $luna_user['id']) {
					$mail_message = str_replace('<pm_url>', $luna_config['o_base_url'].'/viewinbox.php?tid='.$shared_id.'&mid='.$new_mp.'&box=inbox', $mail_message);
					$mail_message_full = str_replace('<pm_url>', $luna_config['o_base_url'].'/viewinbox.php?tid='.$shared_id.'&mid='.$new_mp.'&box=inbox', $mail_message_full);
					
					if ($dest['notify_pm_full'] == '1')
						luna_mail($dest['email'], $mail_subject_full, $mail_message_full);
					else
						luna_mail($dest['email'], $mail_subject, $mail_message);
				}
			}
			$db->query('UPDATE '.$db->prefix.'users SET last_post='.$now.' WHERE id='.$luna_user['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());
		} if (!empty($r)) { // It's a reply or a reply with a quote
			// Check that $edit looks good
			if ($r <= '0')
				message($lang['Bad request']);
				
			foreach ($destinataires as $dest) {
				$val_showed = '0';
				
				if ($dest['id'] == $luna_user['id'])
					$val_showed = '1';
				else
					$val_showed = '0';
					
					$db->query('INSERT INTO '.$db->prefix.'messages (shared_id, owner, subject, message, sender, receiver, sender_id, receiver_id, sender_ip, hide_smilies, posted, show_message, showed) VALUES(\''.$r.'\', \''.$dest['id'].'\', \''.$db->escape($p_subject).'\', \''.$db->escape($p_message).'\', \''.$db->escape($luna_user['username']).'\', \''.$db->escape($usernames_list).'\', \''.$luna_user['id'].'\', \''.$db->escape($ids_list).'\', \''.get_remote_address().'\', \''.$hide_smilies.'\', \''.$now.'\', \'0\', \''.$val_showed.'\')') or error('Unable to send the message.', __FILE__, __LINE__, $db->error());
					$new_mp = $db->insert_id();
					$db->query('UPDATE '.$db->prefix.'messages SET last_post_id='.$new_mp.', last_post='.$now.', last_poster=\''.$db->escape($luna_user['username']).'\' WHERE shared_id='.$r.' AND show_message=1 AND owner='.$dest['id']) or error('Unable to update the message.', __FILE__, __LINE__, $db->error());
					if ($dest['id'] != $luna_user['id']) {
						$db->query('UPDATE '.$db->prefix.'messages SET showed = 0 WHERE shared_id='.$r.' AND show_message=1 AND owner='.$dest['id']) or error('Unable to update the message.', __FILE__, __LINE__, $db->error());
					} if ($luna_config['o_pms_notification'] == '1' && $dest['notify_pm'] == '1' && $dest['id'] != $luna_user['id']) { // E-mail notification
						$mail_message = str_replace('<pm_url>', $luna_config['o_base_url'].'/viewinbox.php?tid='.$r.'&mid='.$new_mp.'&box=inbox', $mail_message);
						$mail_message_full = str_replace('<pm_url>', $luna_config['o_base_url'].'/viewinbox.php?tid='.$r.'&mid='.$new_mp.'&box=inbox', $mail_message_full);
						
						if ($dest['notify_pm_full'] == '1')
							luna_mail($dest['email'], $mail_subject_full, $mail_message_full);
						else
							luna_mail($dest['email'], $mail_subject, $mail_message);
					}
			}
			$db->query('UPDATE '.$db->prefix.'users SET last_post='.$now.' WHERE id='.$luna_user['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());
		} if (!empty($edit) && !empty($tid)) { // It's an edit
			// Check that $edit looks good
			if ($edit <= '0')
				message($lang['Bad request']);
			
			$result = $db->query('SELECT shared_id, owner, message FROM '.$db->prefix.'messages WHERE id='.$edit) or error('Unable to get the informations of the message', __FILE__, __LINE__, $db->error());
			
			if (!$db->num_rows($result))
				message($lang['Bad request']);
				
			while($edit_msg = $db->fetch_assoc($result)) {
				// If you're not the owner of this message, why do you want to edit it?
				if ($edit_msg['owner'] != $luna_user['id'] && !$luna_user['is_admmod'])
					message($lang['No permission']);
					
				$message = $edit_msg['message'];
				$shared_id_msg = $edit_msg['shared_id'];
			}
			
			$result_msg = $db->query('SELECT id FROM '.$db->prefix.'messages WHERE message=\''.$db->escape($message).'\' AND shared_id='.$shared_id_msg) or error('Unable to get the informations of the message', __FILE__, __LINE__, $db->error());
			
			if (!$db->num_rows($result_msg))
				message($lang['Bad request']);
				
			while($list_ids = $db->fetch_assoc($result_msg)) {		
				$ids_edit[] = $list_ids['id'];
			}
			
			$ids_edit = implode(',', $ids_edit);
				
			// Finally, edit the message - maybe this query is unsafe?
			$db->query('UPDATE '.$db->prefix.'messages SET message=\''.$db->escape($p_message).'\' WHERE message=\''.$db->escape($message).'\' AND id IN ('.$ids_edit.')') or error('Unable to edit the message', __FILE__, __LINE__, $db->error());
		}
			redirect('inbox.php');
	}
} else {
	// To user(s)
	if (isset($_GET['uid'])) {
		$users_id = explode('-', $_GET['uid']);
		$users_id = array_map('intval', $users_id);
		foreach ($users_id as $k=>$v)
			if ($v <= 0) unset($users_id[$k]);
		
		$arry_dests = array();
		foreach ($users_id as $user_id) {
			$result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE id='.$user_id) or error('Unable to find the informations of the message', __FILE__, __LINE__, $db->error());
			
			if (!$db->num_rows($result))
				message($lang['Bad request']);
			
			$arry_dests[] = $db->result($result);
		}
			
		$p_destinataire = implode(', ', $arry_dests);
	} if (isset($_GET['lid'])) { // From list
		$id = intval($_GET['lid']);
		
		$arry_dests = array();
		$result = $db->query('SELECT receivers FROM '.$db->prefix.'sending_lists WHERE user_id='.$luna_user['id'].' AND id='.$id) or error('Unable to find the informations of the message', __FILE__, __LINE__, $db->error());
		
		if (!$db->num_rows($result))
			message($lang['Bad request']);
		
		$arry_dests = unserialize($db->result($result));
			
		$p_destinataire = implode(', ', $arry_dests);
	}
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Private Messages'], $lang['Send a message']);

$required_fields = array('req_message' => $lang['Message']);
$focus_element = array('post');

if ($r == '0' && $q == '0' && $edit == '0') {
	$required_fields['req_subject'] = $lang['Subject'];
	$focus_element[] = 'p_username';
} else
	$focus_element[] = 'req_message';

define('FORUM_ACTIVE_PAGE', 'pm');
require load_page('header.php');

require load_page('inbox-new.php');

require load_page('footer.php');
?>