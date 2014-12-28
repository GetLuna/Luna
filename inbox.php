<?php

/*
 * Copyright (C) 2014-2015 Luna
 * Based on work by Adaur (2010), Vincent Garnier, Connorhd and David 'Chacmool' Djurback
 * Licensed under GPLv3 (http://modernbb.be/license.php)
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
require FORUM_ROOT.'lang/'.$luna_user['language'].'/pms.php';

// Page ?
$page = (!isset($_REQUEST['p']) || $_REQUEST['p'] <= '1') ? '1' : intval($_REQUEST['p']);

// Action ?
$action = ((isset($_REQUEST['action']) && ($_REQUEST['action'] == 'delete_multiple' || $_REQUEST['action'] == 'markread' || $_REQUEST['action'] == 'markunread')) ? $_REQUEST['action'] : '');


if ($action != '')
{	
	// Make sure they got here from the site
	confirm_referrer('inbox.php');
	
	// Mark as read multiple posts
	if ($action == 'markread')
	{
		if (empty($_POST['selected_messages']))
			message($lang_pms['Must select']);
			
		$idlist = array_values($_POST['selected_messages']);
		$idlist = array_map('intval', $idlist);
		$idlist = implode(',', array_values($idlist));
		
		$db->query('UPDATE '.$db->prefix.'messages SET showed=1 WHERE shared_id IN ('.$idlist.') AND owner=\''.$luna_user['id'].'\' AND show_message=1') or error('Unable to update the status of the messages', __FILE__, __LINE__, $db->error());
		redirect('inbox.php', $lang_pms['Read redirect']);
	}
	// Mark as unread multiple posts
	elseif ($action == 'markunread')
	{
		if (empty($_POST['selected_messages']))
			message($lang_pms['Must select']);
			
		$idlist = array_values($_POST['selected_messages']);
		$idlist = array_map('intval', $idlist);
		$idlist = implode(',', array_values($idlist));
		
		$db->query('UPDATE '.$db->prefix.'messages SET showed=0 WHERE shared_id IN ('.$idlist.') AND owner=\''.$luna_user['id'].'\' AND show_message=1') or error('Unable to update the status of the messages', __FILE__, __LINE__, $db->error());
		redirect('inbox.php', $lang_pms['Unread redirect']);
	}
	// Delete multiple posts
	elseif ($action == 'delete_multiple')
	{
		if (isset($_POST['delete_multiple_comply']))
		{
			$idlist = explode(',', $_POST['messages']);
			$idlist = array_map('intval', $idlist);
			$idlist = implode(',', array_values($idlist));
			$number = explode(',', $_POST['messages']);
			$number = array_map('intval', $number);

			$db->query('DELETE FROM '.$db->prefix.'messages WHERE shared_id IN ('.$idlist.') AND owner=\''.$luna_user['id'].'\'') or error('Unable to delete the messages', __FILE__, __LINE__, $db->error());
			$db->query('UPDATE '.$db->prefix.'users SET num_pms=num_pms-'.count($number).' WHERE id='.$luna_user['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());
		}
		else
		{
			if (empty($_POST['selected_messages']))
				message($lang_pms['Must select']);
			
			$idlist = array_values($_POST['selected_messages']);
			$idlist = array_map('intval', $idlist);
			$idlist = implode(',', array_values($idlist));
			
			$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang_pms['Multidelete'], $lang_pms['Private Messages']);
			define('FORUM_ACTIVE_PAGE', 'pm');
			require load_page('header.php');
			
			// If you're not the owner of the message, you can't delete it.
			$result = $db->query('SELECT DISTINCT owner FROM '.$db->prefix.'messages WHERE shared_id IN ('.$idlist.')') or error('Unable to delete the message', __FILE__, __LINE__, $db->error());
			$owner = array();
			while ($cur_mess_delete = $db->fetch_assoc($result))
				$owner[] = $cur_mess_delete['owner'];
			
			if(!in_array($luna_user['id'], $owner) && !$luna_user['is_admmod'])
				message($lang['No permission']);

			load_inbox_nav('inbox');
?>
<form method="post" action="inbox.php">
	<div class="panel panel-danger">
		<div class="panel-heading">
			<h3 class="panel-title">Confirm deletion<span class="pull-right"><input class="btn btn-danger" type="submit" name="delete" value="<?php echo $lang_pms['Delete'] ?>" /></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="action" value="delete_multiple" />
			<input type="hidden" name="messages" value="<?php echo $idlist ?>" />
			<input type="hidden" name="delete_multiple_comply" value="1" />
			<p><?php echo $lang_pms['Delete messages comply'] ?></p>
		</div>
	</div>
</form>
	<?php
		}
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
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang_pms['Private Messages'], $lang_pms['Inbox']);

define('FORUM_ACTIVE_PAGE', 'pm');
require load_page('header.php');

load_inbox_nav('inbox');
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
<p><span class="pages-label"><?php echo $lang['Pages'].' '.paginate($num_pages, $page, 'inbox.php?') ?></span></p>
<form method="post" action="inbox.php">
	<fieldset>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Inbox messages</h3>
			</div>
			<input type="hidden" name="box" value="0" />
			<table class="table">
				<thead>
					<tr>
						<th><?php echo $lang_pms['Messages'] ?></th>
						<th><?php echo $lang_pms['Sender'] ?></th>
						<th><?php echo $lang_pms['Receiver'] ?></th>
						<th><?php echo $lang['Last post'] ?></th>
						<th><label style="display: inline; white-space: nowrap;"><?php echo $lang_pms['Select'] ?> <input type="checkbox" id="checkAllButon" value="1" onclick="checkAll('selected_messages[]','checkAllButon');" /></label></th>
					</tr>
				</thead>
				<tbody>
<?php
// Fetch messages
$result = $db->query("SELECT * FROM ".$db->prefix."messages WHERE show_message=1 AND owner='".$luna_user['id']."' ORDER BY last_post DESC LIMIT ".$limit) or error("Unable to find the list of the pms.", __FILE__, __LINE__, $db->error()); 

// If there are messages in this folder.
if ($db->num_rows($result))
{
	while ($cur_mess = $db->fetch_assoc($result))
	{
		$item_status = 'roweven';
		if ($cur_mess['showed'] == '0')
		{
			$item_status .= ' inew';
			$icon_type = 'icon icon-new';
			$subject = '<a href="viewinbox.php?tid='.$cur_mess['shared_id'].'&amp;mid='.$cur_mess['id'].'">'.
					   '<strong>'.luna_htmlspecialchars($cur_mess['subject']).'</strong>'.
					   '</a>';
		}
		else
		{
			$icon_type = 'icon';
			$subject = '<a href="viewinbox.php?tid='.$cur_mess['shared_id'].'&amp;mid='.$cur_mess['id'].'">'.
					   luna_htmlspecialchars($cur_mess['subject']).
					   '</a>';
		}
		
		$last_post = '<a href="viewinbox.php?tid='.$cur_mess['shared_id'].'&amp;mid='.$cur_mess['id'].'&amp;pid='.$cur_mess['last_post_id'].'#p'.$cur_mess['last_post_id'].'">'.format_time($cur_mess['last_post']).'</a> <span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_mess['last_poster']).'</span>';
?>
					<tr class="<?php echo $item_status ?>">
						<td>
							<div class="<?php echo $icon_type ?>"></div>
							<div><?php echo $subject ?></div>
						</td>
						<td>
		<?php
		if ($luna_user['g_view_users'] == '1')
			echo '<a href="me.php?id='.$cur_mess['sender_id'].'">'.luna_htmlspecialchars($cur_mess['sender']).'</a>';
		else
			echo luna_htmlspecialchars($cur_mess['sender']);
		?>
						</td>
						<td>
		<?php
			if ($luna_user['g_view_users'] == '1')
			{
				$ids_list = explode(', ', $cur_mess['receiver_id']);
				$sender_list = explode(', ', $cur_mess['receiver']);
				$sender_list = str_replace('Deleted', $lang_pms['Deleted'], $sender_list);
				
				for($i = '0'; $i < count($ids_list); $i++){
				echo '<a href="me.php?id='.$ids_list[$i].'">'.luna_htmlspecialchars($sender_list[$i]).'</a>';
				
				if($ids_list[$i][count($ids_list[$i])-'1'])
					echo'<br />';
				} 
			}
			else
				echo luna_htmlspecialchars($cur_mess['receiver']);
		?>
						</td>
						<td><?php echo $last_post ?></td>
						<td><input type="checkbox" name="selected_messages[]" value="<?php echo $cur_mess['shared_id'] ?>" /></td>
					</tr>
<?php
	}
}
else
	echo "\t".'<tr><td colspan="4">'.$lang_pms['No messages'].'</td></tr>'."\n";
?>
				</tbody>
			</table>
		</div>
		<p><?php echo $lang['Pages'].' '.paginate($num_pages, $page, 'inbox.php?') ?></p>
		<label>With selection</label>
		<div class="input-group">
			<select class="form-control" name="action">
				<option value="markread"><?php echo $lang_pms['Mark as read select'] ?></option>
				<option value="markunread"><?php echo $lang_pms['Mark as unread select'] ?></option>
				<option value="delete_multiple"><?php echo $lang_pms['Delete'] ?></option>
			</select>
			<div class="input-group-btn">
				<input class="btn btn-primary" type="submit" value="<?php echo $lang_pms['OK'] ?>" />
			</div>
		</div>
	</fieldset>
</form>
<?php

}
require load_page('footer.php');