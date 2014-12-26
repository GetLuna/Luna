<?php

/*
 * Copyright (C) 2014-2015 Luna
 * Based on work by Adaur (2010), Vincent Garnier, Connorhd and David 'Chacmool' Djurback
 * Licensed under GPLv3 (http://modernbb.be/license.php)
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
if (!$luna_config['o_pms_enabled'] =='1' || $luna_user['g_pm'] == '0')
	message($lang['No permission']);

// Load the additionals language files
require FORUM_ROOT.'lang/'.$luna_user['language'].'/language.php';
require FORUM_ROOT.'lang/'.$luna_user['language'].'/pms.php';

// Action ?
$action = ((isset($_POST['action']) && ($_POST['action'] == 'delete_multiple')) ? $_POST['action'] : '');


if ($action != '')
{
	if ($action == 'delete_multiple')
	{
		if (isset($_POST['delete_multiple_comply']))
		{
			$idlist = explode(',', $_POST['selected_lists']);
			$idlist = array_map('intval', $idlist);
			$idlist = implode(',', array_values($idlist));

			$db->query('DELETE FROM '.$db->prefix.'sending_lists WHERE id IN('.$idlist.') AND user_id='.$luna_user['id']) or error('Unable to delete sending lists', __FILE__, __LINE__, $db->error());
		}
		else
		{
			if (empty($_POST['selected_lists']))
				message($lang_pms['Must select lists']);
			
			$idlist = array_map('trim', $_POST['selected_lists']);
			$idlist = array_map('intval', $idlist);
			$idlist = implode(',', array_values($idlist));
			
			$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang_pms['Private Messages'], $lang_pms['Multidelete lists'], $lang_pms['Sending lists']);
			define('FORUM_ACTIVE_PAGE', 'pm');
			require load_page('header.php');

			load_inbox_nav('lists');
?>
<form method="post" action="sending_lists.php">
	<div class="panel panel-danger">
		<div class="panel-heading">
			<h3 class="panel-title">Confirm deletion<span class="pull-right"><input class="btn btn-danger" type="submit" name="delete" value="<?php echo $lang_pms['Delete'] ?>" /></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="action" value="delete_multiple" />
			<input type="hidden" name="selected_lists" value="<?php echo $idlist ?>" />
			<input type="hidden" name="delete_multiple_comply" value="1" />
			<p><?php echo $lang_pms['Delete lists comply'] ?></p>
		</div>
	</div>
</form>
	<?php
		}
	}
}

// Add a list
else if (isset($_POST['form_sent']))
{
	// Make sure they got here from the site
	confirm_referrer('sending_lists.php');
	
	// Build list
	$list_name = luna_trim($_POST['list_name']);
	$p_destinataire = isset($_POST['req_username']) ? luna_trim($_POST['req_username']) : '';
    $dest_list = explode(', ', $p_destinataire);
	
	$dest_list = array_map('luna_trim', $dest_list);
	$dest_list = array_unique($dest_list);
	
	if (in_array($luna_user['username'], $dest_list))
		message('yourself');
	
	foreach ($dest_list as $k=>$v)
	{
		if ($v == '') unset($dest_list[$k]);
	}

    if (count($dest_list) > $luna_config['o_pms_max_receiver'])
		$errors[] = sprintf($lang_pms['Too many receiver'], $luna_config['o_pms_max_receiver']-1);

	$destinataires = array();
	$i = 0;
	$list_ids = array();
	$list_usernames = array();
	foreach ($dest_list as $destinataire)
	{
		// Get receiver infos
		$result_username = $db->query('SELECT u.id, u.username, u.email, u.notify_pm, u.notify_pm_full, u.use_pm, u.num_pms, g.g_id, g.g_pm_limit FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'groups AS g ON u.group_id=g.g_id LEFT JOIN '.$db->prefix.'messages AS pm ON pm.owner=u.id WHERE u.id!=1 AND u.username=\''.$db->escape($destinataire).'\' GROUP BY u.username') or error('Unable to get user ID', __FILE__, __LINE__, $db->error());
		// List users infos
		if ($destinataires[$i] = $db->fetch_assoc($result_username))
		{
			// Begin to build the IDs' list - Thanks to Yacodo!
			$list_ids[] = $destinataires[$i]['id'];
			// Begin to build usernames' list
			$list_usernames[] = $destinataires[$i]['username'];
			// Receivers enable PM ?
			if (!$destinataires[$i]['use_pm'] == '1')
				$errors[] = sprintf($lang_pms['User disable PM'], luna_htmlspecialchars($destinataire));			
		}
		else
			$errors[] = sprintf($lang_pms['No user'], luna_htmlspecialchars($destinataire));
		$i++;
	}
	
	$ids_serialized = serialize($list_ids);
	$usernames_serialized = serialize($list_usernames);
	
	$db->query('INSERT INTO '.$db->prefix.'sending_lists (user_id, name, receivers, array_id) VALUES ('.$luna_user['id'].', \''.$db->escape($list_name).'\', \''.$db->escape($usernames_serialized).'\', \''.$db->escape($ids_serialized).'\')') or error('Unable to add the list', __FILE__, __LINE__, $db->error());
}

// Delete a list
else if (isset($_GET['delete']))
{
	$id = intval($_GET['delete']);
	
	$result = $db->query('SELECT user_id FROM '.$db->prefix.'sending_lists WHERE id='.$id) or error('Unable to find the list', __FILE__, __LINE__, $db->error());
	
	if ($db->result($result) != $luna_user['id'])
		message($lang['Bad request']);

	$result = $db->query('DELETE FROM '.$db->prefix.'sending_lists WHERE id= '.$id) or error('Unable to delete the list', __FILE__, __LINE__, $db->error());
	
	redirect('sending_lists.php', $lang_pms['Deleted list redirect']);
} else {

// Build page
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang_pms['Private Messages'], $lang_pms['Sending lists']);

define('FORUM_ACTIVE_PAGE', 'pm');
require load_page('header.php');

load_inbox_nav('lists');
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
<form class="form-horizontal" action="sending_lists.php" method="post">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Add a list<span class="pull-right"><input class="btn btn-primary" type="submit" name="add" value="<?php echo $lang_pms['Add'] ?>" accesskey="s" /></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<input type="hidden" name="form_sent" value="1" />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang_pms['List name'] ?></label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="list_name" size="25" maxlength="255" tabindex="1" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang_pms['List usernames comma'] ?><span class="help-block">Separate names with commas</span></label>
					<div class="col-sm-9">
						<textarea class="form-control" name="req_username" rows="2" cols="50" tabindex="1"></textarea>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
<form method="post" action="sending_lists.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Inbox lists</h3>
		</div>
		<table class="table">
			<thead>
				<tr>
					<th><?php echo $lang_pms['List name'] ?></th>
					<th><?php echo $lang_pms['List usernames'] ?></th>
					<th><?php echo $lang_pms['Delete'] ?></th>
					<th><?php echo $lang_pms['Quick message'] ?></th>
					<th><label style="display: inline; white-space: nowrap;"><?php echo $lang_pms['Select'] ?>&nbsp;<input type="checkbox" id="checkAllButon" value="1" onclick="javascript:checkAll('selected_lists[]','checkAllButon');" /></label></th>
				</tr>
			</thead>
			<tbody>
<?php
// Fetch lists
$result = $db->query('SELECT * FROM '.$db->prefix.'sending_lists WHERE user_id='.$luna_user['id'].' ORDER BY id DESC') or error('Unable to update the list of the lists', __FILE__, __LINE__, $db->error());

if ($db->num_rows($result))
{
	while ($cur_list = $db->fetch_assoc($result))
	{
		$usernames = '';
		$ids_list = unserialize($cur_list['array_id']);
		$usernames_list = unserialize($cur_list['receivers']);
		for($i = 0; $i < count($ids_list); $i++){
			if ($i > 0 && $i < count($ids_list))
					$usernames = $usernames.', ';
			$usernames = $usernames.'<a href="me.php?id='.$ids_list[$i].'">'.luna_htmlspecialchars($usernames_list[$i]).'</a>';
		} 
?>
				<tr>
					<td><?php echo luna_htmlspecialchars($cur_list['name']) ?></td>
					<td><?php echo $usernames ?></td>
					<td><a href="sending_lists.php?delete=<?php echo $cur_list['id'] ?>" title="<?php $usernames ?>" onclick="return window.confirm('<?php echo $lang_pms['Delete list confirm'] ?>')"><?php echo $lang_pms['Delete this list'] ?></a></td>
					<td><a href="new_inbox.php?lid=<?php echo $cur_list['id'] ?>" title="<?php echo $lang_pms['Quick message'] ?>"><?php echo $lang_pms['Quick message'] ?></a></td>
					<td><input type="checkbox" name="selected_lists[]" value="<?php echo $cur_list['id'] ?>" /></td>
				</tr>
<?php
	}
}
else
	echo "\t".'<tr><td colspan="5">'.$lang_pms['No sending lists'].'</td></tr>'."\n";
?>
			</tbody>
		</table>
	</div>
	<label>With selection</label>
	<div class="input-group">
		<select class="form-control" name="action">
			<option value="delete_multiple"><?php echo $lang_pms['Delete'] ?></option>
		</select>
		<div class="input-group-btn">
			<input class="btn btn-primary" type="submit" value="<?php echo $lang_pms['OK'] ?>" />
		</div>
	</div>
</form>

<?php
}

require load_page('footer.php');