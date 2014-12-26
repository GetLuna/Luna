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
$action = ((isset($_POST['action']) && ($_POST['action'] == 'send' || $_POST['action'] == 'authorize' || $_POST['action'] == 'refuse' || $_POST['action'] == 'delete_multiple')) ? $_POST['action'] : '');


if ($action != '') {
	// Make sure they got here from the site
	confirm_referrer('contacts.php');
	
	// send a message
	if ($action == 'send')
	{
		if (empty($_POST['selected_contacts']))
			message($lang_pms['Must select contacts']);
			
		$idlist = array_map('trim', $_POST['selected_contacts']);
		$idlist = array_map('intval', $idlist);
		$idlist = implode(',', array_values($idlist));
			
		// Fetch contacts
$result = $db->query('SELECT contact_id FROM '.$db->prefix.'contacts WHERE id IN('.$idlist.') AND user_id='.$luna_user['id']) or error('Unable to update to find the list of the contacts', __FILE__, __LINE__, $db->error());
		
		if (!$db->num_rows($result))
			message($lang['Bad request']);
			
		$idlist = array();
		while ($cur_contact = $db->fetch_assoc($result))
			$idlist[] = $cur_contact['contact_id'];
			
		header('Location: new_inbox.php?uid='.implode('-', $idlist));
	}
	// authorize multiple contacts
	elseif ($action == 'authorize')
	{
		if (empty($_POST['selected_contacts']))
			message($lang_pms['Must select contacts']);
		
		$idlist = array_map('trim', $_POST['selected_contacts']);
		$idlist = array_map('intval', $idlist);
		$idlist = implode(',', array_values($idlist));
		
		$db->query('UPDATE '.$db->prefix.'contacts SET allow_msg=1 WHERE id IN('.$idlist.') AND user_id='.$luna_user['id']) or error('Unable to update the status of the contacts', __FILE__, __LINE__, $db->error());
		
		redirect('contacts.php', $lang_pms['Multiples status redirect']);
	}
	// refuse multiple contacts
	elseif ($action == 'refuse')
	{
		if (empty($_POST['selected_contacts']))
			message($lang_pms['Must select contacts']);
			
		$idlist = array_map('trim', $_POST['selected_contacts']);
		$idlist = array_map('intval', $idlist);
		$idlist = implode(',', array_values($idlist));
		
		$db->query('UPDATE '.$db->prefix.'contacts SET allow_msg=0 WHERE id IN('.$idlist.') AND user_id='.$luna_user['id']) or error('Unable to update the status of the contacts', __FILE__, __LINE__, $db->error());
		
		redirect('contacts.php', $lang_pms['Multiples status redirect']);
	}
	elseif ($action == 'delete_multiple')
	{
		if (isset($_POST['delete_multiple_comply']))
		{
			$idlist = explode(',', $_POST['contacts']);
			$idlist = array_map('intval', $idlist);
			$idlist = implode(',', array_values($idlist));

			$db->query('DELETE FROM '.$db->prefix.'contacts WHERE id IN('.$idlist.') AND user_id='.$luna_user['id']) or error('Impossible de supprimer les contacts.', __FILE__, __LINE__, $db->error());

			switch ($db_type)
			{
				case 'mysql':
				case 'mysqli':
					$db->query('OPTIMIZE TABLE '.$db->prefix.'contacts') or error('Unable to optimize the database', __FILE__, __LINE__, $db->error());
					break;

				case 'pgsql':
				case 'sqlite':
					$db->query('VACUUM '.$db->prefix.'contacts') or error('Unable to optimize the database', __FILE__, __LINE__, $db->error());
					break;

			}
		}
		else
		{
			if (empty($_POST['selected_contacts']))
				message($lang_pms['Must select contacts']);
			
			$idlist = array_map('trim', $_POST['selected_contacts']);
			$idlist = array_map('intval', $idlist);
			$idlist = implode(',', array_values($idlist));
			
			$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang_pms['Private Messages'], $lang_pms['Multidelete contacts'], $lang_pms['Contacts']);
			define('FORUM_ACTIVE_PAGE', 'pm');
			require load_page('header.php');

			load_inbox_nav('contacts');
?>
<form method="post" action="inbox.php">
	<div class="panel panel-danger">
		<div class="panel-heading">
			<h3 class="panel-title">Confirm deletion<span class="pull-right"><input class="btn btn-danger" type="submit" name="delete" value="<?php echo $lang_pms['Delete'] ?>" /></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="action" value="delete_multiple" />
			<input type="hidden" name="contacts" value="<?php echo $idlist ?>" />
			<input type="hidden" name="delete_multiple_comply" value="1" />
			<p><?php echo $lang_pms['Delete contacts comply'] ?></p>
		</div>
	</div>
</form>
<?php
		}
	}
}

// Add a contact
else if (isset($_POST['add']))
{
	// Make sure they got here from the site
	confirm_referrer('contacts.php');
	
	if (isset($_POST['req_username']))
	{
		$sql_where = 'u.username=\''.$db->escape($_POST['req_username']).'\'';
		$redirect = 'contacts.php';
		$authorized = (isset($_POST['req_refuse']) && intval($_POST['req_refuse']) == 1)  ? 0 : 1;
	}
	else
	{
		$sql_where = 'u.id='.intval($_POST['add']);
		$redirect = 'me.php?id='.intval($_POST['add']);
		$authorized = 1;
	}
	
	$result = $db->query("SELECT u.id, u.username, g.g_id, g.g_pm, COUNT(c.id) AS allready FROM ".$db->prefix."users AS u INNER JOIN ".$db->prefix."groups AS g ON u.group_id=g.g_id LEFT JOIN ".$db->prefix."contacts AS c ON (c.contact_id=u.id AND c.user_id=".$luna_user['id'].") WHERE u.id!=1 AND ".$sql_where." GROUP BY u.id, g.g_id") or error("Unable to find the informations of the user", __FILE__, __LINE__, $db->error());
	
	if ($contact = $db->fetch_assoc($result))
	{		
		if (!$contact['allready'])
		{
			if ($contact['g_pm'] == '1')
			{
				$result = $db->query('INSERT INTO '.$db->prefix.'contacts (user_id, contact_id, contact_name, allow_msg) VALUES ('.$luna_user['id'].', '.$contact['id'].', \''.$db->escape($contact['username']).'\', '.$authorized.')') or error('Unable to add the contact', __FILE__, __LINE__, $db->error());
				
				redirect($redirect,$lang_pms['Added contact redirect']);
			}
			else
				message($lang_pms['Authorize user']);
		}
		else
			message($lang_pms['User already contact']);
	}
	else
		message($lang_pms['User not exists']);
}

// Delete a contact
else if (isset($_GET['delete']))
{
	// Make sure they got here from the site
	confirm_referrer('contacts.php');
	
	$id = intval($_GET['delete']);
	
	$result = $db->query('SELECT user_id FROM '.$db->prefix.'contacts WHERE id='.$id) or error('Unable to find the contact', __FILE__, __LINE__, $db->error());
	
	if ($db->result($result) != $luna_user['id'])
		message($lang['Bad request']);

	$result = $db->query('DELETE FROM '.$db->prefix.'contacts WHERE id= '.$id) or error('Unable to delete the contact', __FILE__, __LINE__, $db->error());
	
	redirect('contacts.php',$lang_pms['Deleted contact redirect']);
}

// Switch contact status
else if (isset($_GET['switch']))
{
	// Make sure they got here from the site
	confirm_referrer('contacts.php');
	
	$id = intval($_GET['switch']);
	
	$result = $db->query('SELECT user_id FROM '.$db->prefix.'contacts WHERE id='.$id) or error('Unable to find the contact', __FILE__, __LINE__, $db->error());
	
	if ($db->result($result) != $luna_user['id'])
		message($lang['Bad request']);

	$result = $db->query('UPDATE '.$db->prefix.'contacts SET allow_msg = 1-allow_msg WHERE id= '.$id) or error('Unable to edit the status of the contact', __FILE__, __LINE__, $db->error());
	
	redirect('contacts.php',$lang_pms['Status redirect']);
} else {
// Build page
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang_pms['Private Messages'], $lang_pms['Contacts']);

define('FORUM_ACTIVE_PAGE', 'pm');
require load_page('header.php');

load_inbox_nav('contacts');
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
<form class="form-horizontal" action="contacts.php" method="post">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Add contact<span class="pull-right"><input class="btn btn-primary" type="submit" name="add" value="<?php echo $lang_pms['Add'] ?>" accesskey="s" /></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang_pms['Contact name'] ?></label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="req_username" size="25" maxlength="120" tabindex="1" />
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="req_refuse" value="1" tabindex="2" />
								<?php echo $lang_pms['Refuse user'] ?>
                            </label>
                        </div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
<form method="post" action="contacts.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Inbox contacts</h3>
		</div>
		<table class="table">
			<thead>
				<tr>
					<th><?php echo $lang_pms['Contact name'] ?></th>
					<th><?php echo $lang_pms['Rights contact'] ?></th>
					<th><?php echo $lang_pms['Delete'] ?></th>
					<th><?php echo $lang_pms['Quick message'] ?></th>
					<th><label style="display: inline; white-space: nowrap;"><?php echo $lang_pms['Select'] ?>&nbsp;<input type="checkbox" id="checkAllButon" value="1" onclick="javascript:checkAll('selected_contacts[]','checkAllButon');" /></label></th>
				</tr>
			</thead>
			<tbody>
<?php
// Fetch contacts
$result = $db->query('SELECT * FROM '.$db->prefix.'contacts WHERE user_id='.$luna_user['id'].' ORDER BY allow_msg DESC, contact_name ASC') or error('Unable to update the list of the contacts', __FILE__, __LINE__, $db->error());

if ($db->num_rows($result))
{
	while ($cur_contact = $db->fetch_assoc($result))
	{
		// authorized or refused
		if ($cur_contact['allow_msg'])
		{
			$status_text = $lang_pms['Authorized messages'].' - <a href="contacts.php?switch='.$cur_contact['id'].'" title="'.sprintf($lang_pms['Refuse from'], luna_htmlspecialchars($cur_contact['contact_name'])).'">'.$lang_pms['Refuse'].'</a>';
			$status_class = '';
		}
		else {
			$status_text = $lang_pms['Refused messages'].' - <a href="contacts.php?switch='.$cur_contact['id'].'" title="'.sprintf($lang_pms['Authorize from'], luna_htmlspecialchars($cur_contact['contact_name'])).'">'.$lang_pms['Authorize'].'</a>';
			$status_class =  ' class="iclosed"';
		}
?>
			<tr<?php echo $status_class ?>>
	<?php
		if ($luna_user['g_view_users'] == '1')
			echo '<td><a href="me.php?id='.$cur_contact['contact_id'].'">'.luna_htmlspecialchars($cur_contact['contact_name']).'</a></td>';
		else
			echo '<td>'.luna_htmlspecialchars($cur_contact['contact_name']).'</td>';
	?>
					<td><?php echo $status_text; ?></td>
					<td><a href="contacts.php?delete=<?php echo $cur_contact['id']?>" title="<?php printf($lang_pms['Delete x'], luna_htmlspecialchars($cur_contact['contact_name'])) ?>" onclick="return window.confirm('<?php echo $lang_pms['Delete contact confirm'] ?>')"><?php echo $lang_pms['Delete'] ?></a></td>
					<td><a href="new_inbox.php?uid=<?php echo $cur_contact['contact_id']?>" title="<?php printf($lang_pms['Quick message x'], luna_htmlspecialchars($cur_contact['contact_name'])) ?>"><?php echo $lang_pms['Quick message'] ?></a></td>
					<td class="tcmod"><input type="checkbox" name="selected_contacts[]" value="<?php echo $cur_contact['id']; ?>" /></td>
				</tr>
<?php
	}
}
else
	echo "\t".'<tr><td colspan="5">'.$lang_pms['No contacts'].'</td></tr>'."\n";
?>
			</tbody>
		</table>
	</div>
	<label>With selection</label>
	<div class="input-group">
		<select class="form-control" name="action">
			<option value="send"><?php echo $lang_pms['Quick message'] ?></option>
			<option value="authorize"><?php echo $lang_pms['Authorize'] ?></option>
			<option value="refuse"><?php echo $lang_pms['Refuse'] ?></option>
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