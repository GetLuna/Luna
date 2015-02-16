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
require FORUM_ROOT.'include/me_functions.php';

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

// User block
$avatar_user_card = draw_user_avatar($luna_user['id'], 'visible-lg-block');

// Page ?
$page = (!isset($_REQUEST['p']) || $_REQUEST['p'] <= '1') ? '1' : intval($_REQUEST['p']);

// Action ?
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$id = $luna_user['id'];
	
// Mark as read multiple posts
if (isset($_REQUEST['markread'])) {
	confirm_referrer('inbox.php');

	if (empty($_POST['selected_messages']))
		message($lang['Must select']);
		
	$idlist = array_values($_POST['selected_messages']);
	$idlist = array_map('intval', $idlist);
	$idlist = implode(',', array_values($idlist));
	
	$db->query('UPDATE '.$db->prefix.'messages SET showed=1 WHERE shared_id IN ('.$idlist.') AND owner=\''.$luna_user['id'].'\' AND show_message=1') or error('Unable to update the status of the messages', __FILE__, __LINE__, $db->error());
	redirect('inbox.php', $lang['Read redirect']);
} elseif (isset($_REQUEST['markunread'])) { // Mark as unread multiple posts
	confirm_referrer('inbox.php');

	if (empty($_POST['selected_messages']))
		message($lang['Must select']);
		
	$idlist = array_values($_POST['selected_messages']);
	$idlist = array_map('intval', $idlist);
	$idlist = implode(',', array_values($idlist));
	
	$db->query('UPDATE '.$db->prefix.'messages SET showed=0 WHERE shared_id IN ('.$idlist.') AND owner=\''.$luna_user['id'].'\' AND show_message=1') or error('Unable to update the status of the messages', __FILE__, __LINE__, $db->error());
	redirect('inbox.php', $lang['Unread redirect']);
} elseif (isset($_REQUEST['delete_multiple'])) { // Delete multiple posts
	confirm_referrer('inbox.php');

	if (isset($_POST['delete_multiple_comply'])) {
		$idlist = explode(',', $_POST['messages']);
		$idlist = array_map('intval', $idlist);
		$idlist = implode(',', array_values($idlist));
		$number = explode(',', $_POST['messages']);
		$number = array_map('intval', $number);

		$db->query('DELETE FROM '.$db->prefix.'messages WHERE shared_id IN ('.$idlist.') AND owner=\''.$luna_user['id'].'\'') or error('Unable to delete the messages', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'users SET num_pms=num_pms-'.count($number).' WHERE id='.$luna_user['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());
	} else {
		if (empty($_POST['selected_messages']))
			message($lang['Must select']);
		
		$idlist = array_values($_POST['selected_messages']);
		$idlist = array_map('intval', $idlist);
		$idlist = implode(',', array_values($idlist));
		
		$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Multidelete'], $lang['Private Messages']);
		define('FORUM_ACTIVE_PAGE', 'pm');
		require load_page('header.php');
		
		// If you're not the owner of the message, you can't delete it.
		$result = $db->query('SELECT DISTINCT owner FROM '.$db->prefix.'messages WHERE shared_id IN ('.$idlist.')') or error('Unable to delete the message', __FILE__, __LINE__, $db->error());
		$owner = array();
		while ($cur_mess_delete = $db->fetch_assoc($result))
			$owner[] = $cur_mess_delete['owner'];
		
		if(!in_array($luna_user['id'], $owner) && !$luna_user['is_admmod'])
			message($lang['No permission']);
?>
<form method="post" action="inbox.php">
	<div class="panel panel-danger">
		<div class="panel-heading">
			<h3 class="panel-title">Confirm deletion<span class="pull-right"><input class="btn btn-danger" type="submit" name="delete" value="<?php echo $lang['Delete'] ?>" /></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="action" value="delete_multiple" />
			<input type="hidden" name="messages" value="<?php echo $idlist ?>" />
			<input type="hidden" name="delete_multiple_comply" value="1" />
			<p><?php echo $lang['Delete messages comply'] ?></p>
		</div>
	</div>
</form>
<?php
	}
} else {

// Get message count for this box
$result = $db->query("SELECT COUNT(*) FROM ".$db->prefix."messages WHERE show_message=1 AND owner='".$luna_user['id']."'") or error("Unable to count the messages", __FILE__, __LINE__, $db->error());
list($num_messages) = $db->fetch_row($result);

// What page are we on ?
$num_pages = ceil($num_messages/$luna_config['o_pms_mess_per_page']);
if ($page > $num_pages) $page = 1;
$start_from = intval($luna_config['o_pms_mess_per_page'])*($page-1);
$limit = $start_from.','.$luna_config['o_pms_mess_per_page'];

// Start building page
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Private Messages'], $lang['Inbox']);

define('FORUM_ACTIVE_PAGE', 'pm');
require load_page('header.php');

?>
<script type="text/javascript">
/* <![CDATA[ */
function checkAll(checkWhat,command){
	var inputs = document.getElementsByTagName('input');
   
	for(index = 0; index < inputs.length; index++){
		if(inputs[index].name == checkWhat){
			inputs[index].checked=document.getElementById(command).checked;
		}
	}
}
/* ]]> */
</script>
<?php
	require load_page('inbox.php');
}
require load_page('footer.php');