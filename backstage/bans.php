<?php

/*
 * Copyright (C) 2013-2014 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if ($luna_user['g_id'] != FORUM_ADMIN && ($luna_user['g_moderator'] != '1' || $luna_user['g_mod_ban_users'] == '0')) {
    header("Location: ../login.php");
}

// Add/edit a ban (stage 1)
if (isset($_REQUEST['add_ban']) || isset($_GET['edit_ban'])) {
	if (isset($_GET['add_ban']) || isset($_POST['add_ban'])) {
		// If the ID of the user to ban was provided through GET (a link from ../profile.php)
		if (isset($_GET['add_ban'])) {
			$user_id = intval($_GET['add_ban']);
			if ($user_id < 2)
				message_backstage($lang['Bad request'], false, '404 Not Found');

			$result = $db->query('SELECT group_id, username, email FROM '.$db->prefix.'users WHERE id='.$user_id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result))
				list($group_id, $ban_user, $ban_email) = $db->fetch_row($result);
			else
				message_backstage($lang['No user ID message']);
		} else { // Otherwise the username is in POST
			$ban_user = luna_trim($_POST['new_ban_user']);

			if ($ban_user != '') {
				$result = $db->query('SELECT id, group_id, username, email FROM '.$db->prefix.'users WHERE username=\''.$db->escape($ban_user).'\' AND id>1') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
				if ($db->num_rows($result))
					list($user_id, $group_id, $ban_user, $ban_email) = $db->fetch_row($result);
				else
					message_backstage($lang['No user message']);
			}
		}

		// Make sure we're not banning an admin or moderator
		if (isset($group_id)) {
			if ($group_id == FORUM_ADMIN)
				message_backstage(sprintf($lang['User is admin message'], luna_htmlspecialchars($ban_user)));

			$result = $db->query('SELECT g_moderator FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to fetch group info', __FILE__, __LINE__, $db->error());
			$is_moderator_group = $db->result($result);

			if ($is_moderator_group)
				message_backstage(sprintf($lang['User is mod message'], luna_htmlspecialchars($ban_user)));
		}

		// If we have a $user_id, we can try to find the last known IP of that user
		if (isset($user_id)) {
			$result = $db->query('SELECT poster_ip FROM '.$db->prefix.'posts WHERE poster_id='.$user_id.' ORDER BY posted DESC LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
			$ban_ip = ($db->num_rows($result)) ? $db->result($result) : '';

			if ($ban_ip == '') {
				$result = $db->query('SELECT registration_ip FROM '.$db->prefix.'users WHERE id='.$user_id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
				$ban_ip = ($db->num_rows($result)) ? $db->result($result) : '';
			}
		}

		$mode = 'add';
	} else { // We are editing a ban
		$ban_id = intval($_GET['edit_ban']);
		if ($ban_id < 1)
			message_backstage($lang['Bad request'], false, '404 Not Found');

		$result = $db->query('SELECT username, ip, email, message, expire FROM '.$db->prefix.'bans WHERE id='.$ban_id) or error('Unable to fetch ban info', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result))
			list($ban_user, $ban_ip, $ban_email, $ban_message, $ban_expire) = $db->fetch_row($result);
		else
			message_backstage($lang['Bad request'], false, '404 Not Found');

		$diff = ($luna_user['timezone'] + $luna_user['dst']) * 3600;
		$ban_expire = ($ban_expire != '') ? gmdate('Y-m-d', $ban_expire + $diff) : '';

		$mode = 'edit';
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Bans']);
	$focus_element = array('bans2', 'ban_user');
	define('FORUM_ACTIVE_PAGE', 'admin');
	require 'header.php';
	load_admin_nav('users', 'bans');

?>
<form class="form-horizontal" id="bans2" method="post" action="bans.php">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Ban advanced subhead'] ?></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="mode" value="<?php echo $mode ?>" />
    <?php if ($mode == 'edit'): ?>				<input type="hidden" name="ban_id" value="<?php echo $ban_id ?>" />
    <?php endif; ?>				<fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Username'] ?><span class="help-block"><?php echo $lang['Username help'] ?></span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="ban_user" maxlength="25" value="<?php if (isset($ban_user)) echo luna_htmlspecialchars($ban_user); ?>" tabindex="1" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['IP label'] ?><span class="help-block"><?php echo $lang['IP help'] ?><?php if ($ban_user != '' && isset($user_id)) printf(' '.$lang['IP help link'], '<a href="users.php?ip_stats='.$user_id.'">'.$lang['here'].'</a>') ?></span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="ban_ip" maxlength="255" value="<?php if (isset($ban_ip)) echo luna_htmlspecialchars($ban_ip); ?>" tabindex="2" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Email'] ?><span class="help-block"><?php echo $lang['E-mail help'] ?></span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="ban_email" maxlength="80" value="<?php if (isset($ban_email)) echo luna_htmlspecialchars($ban_email); ?>" tabindex="3" />
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Message expiry subhead'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Ban message label'] ?><span class="help-block"><?php echo $lang['Ban message help'] ?></span></label>
                    <div class="col-sm-9">
						<input type="text" class="form-control" name="ban_message" maxlength="255" value="<?php if (isset($ban_message)) echo luna_htmlspecialchars($ban_message); ?>" tabindex="4" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Expire date label'] ?><span class="help-block"><?php echo $lang['Expire date help'] ?></span></label>
                    <div class="col-sm-9">
						<input type="text" class="form-control" name="ban_expire" maxlength="10" placeholder="<?php echo $lang['Date help'] ?>" value="<?php if (isset($ban_expire)) echo $ban_expire; ?>" tabindex="5" />
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
	<div class="alert alert-danger">
		<input class="btn btn-danger" type="submit" name="add_edit_ban" value="<?php echo $lang['Ban'] ?>" tabindex="6" />
	</div>
</form>
<?php

	require 'footer.php';
}

// Add/edit a ban (stage 2)
else if (isset($_POST['add_edit_ban'])) {
	confirm_referrer('backstage/bans.php');
	
	$ban_user = luna_trim($_POST['ban_user']);
	$ban_ip = luna_trim($_POST['ban_ip']);
	$ban_email = strtolower(luna_trim($_POST['ban_email']));
	$ban_message = luna_trim($_POST['ban_message']);
	$ban_expire = luna_trim($_POST['ban_expire']);

	if ($ban_user == '' && $ban_ip == '' && $ban_email == '')
		message_backstage($lang['Must enter message']);
	else if (strtolower($ban_user) == 'guest')
		message_backstage($lang['Cannot ban guest message']);

	// Make sure we're not banning an admin or moderator
	if (!empty($ban_user)) {
		$result = $db->query('SELECT group_id FROM '.$db->prefix.'users WHERE username=\''.$db->escape($ban_user).'\' AND id>1') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result)) {
			$group_id = $db->result($result);

			if ($group_id == FORUM_ADMIN)
				message_backstage(sprintf($lang['User is admin message'], luna_htmlspecialchars($ban_user)));

			$result = $db->query('SELECT g_moderator FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to fetch group info', __FILE__, __LINE__, $db->error());
			$is_moderator_group = $db->result($result);

			if ($is_moderator_group)
				message_backstage(sprintf($lang['User is mod message'], luna_htmlspecialchars($ban_user)));
		}
	}

	// Validate IP/IP range (it's overkill, I know)
	if ($ban_ip != '') {
		$ban_ip = preg_replace('%\s{2,}%S', ' ', $ban_ip);
		$addresses = explode(' ', $ban_ip);
		$addresses = array_map('luna_trim', $addresses);

		for ($i = 0; $i < count($addresses); ++$i) {
			if (strpos($addresses[$i], ':') !== false) {
				$octets = explode(':', $addresses[$i]);

				for ($c = 0; $c < count($octets); ++$c) {
					$octets[$c] = ltrim($octets[$c], "0");

					if ($c > 7 || (!empty($octets[$c]) && !ctype_xdigit($octets[$c])) || intval($octets[$c], 16) > 65535)
						message_backstage($lang['Invalid IP message']);
				}

				$cur_address = implode(':', $octets);
				$addresses[$i] = $cur_address;
			} else {
				$octets = explode('.', $addresses[$i]);

				for ($c = 0; $c < count($octets); ++$c) {
					$octets[$c] = (strlen($octets[$c]) > 1) ? ltrim($octets[$c], "0") : $octets[$c];

					if ($c > 3 || preg_match('%[^0-9]%', $octets[$c]) || intval($octets[$c]) > 255)
						message_backstage($lang['Invalid IP message']);
				}

				$cur_address = implode('.', $octets);
				$addresses[$i] = $cur_address;
			}
		}

		$ban_ip = implode(' ', $addresses);
	}

	require FORUM_ROOT.'include/email.php';
	if ($ban_email != '' && !is_valid_email($ban_email)) {
		if (!preg_match('%^[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$%', $ban_email))
			message_backstage($lang['Invalid e-mail message']);
	}

	if ($ban_expire != '' && $ban_expire != 'Never') {
		$ban_expire = strtotime($ban_expire.' GMT');

		if ($ban_expire == -1 || !$ban_expire)
			message_backstage($lang['Invalid date message'].' '.$lang['Invalid date reasons']);

		$diff = ($luna_user['timezone'] + $luna_user['dst']) * 3600;
		$ban_expire -= $diff;

		if ($ban_expire <= time())
			message_backstage($lang['Invalid date message'].' '.$lang['Invalid date reasons']);
	} else
		$ban_expire = 'NULL';

	$ban_user = ($ban_user != '') ? '\''.$db->escape($ban_user).'\'' : 'NULL';
	$ban_ip = ($ban_ip != '') ? '\''.$db->escape($ban_ip).'\'' : 'NULL';
	$ban_email = ($ban_email != '') ? '\''.$db->escape($ban_email).'\'' : 'NULL';
	$ban_message = ($ban_message != '') ? '\''.$db->escape($ban_message).'\'' : 'NULL';

	if ($_POST['mode'] == 'add')
		$db->query('INSERT INTO '.$db->prefix.'bans (username, ip, email, message, expire, ban_creator) VALUES('.$ban_user.', '.$ban_ip.', '.$ban_email.', '.$ban_message.', '.$ban_expire.', '.$luna_user['id'].')') or error('Unable to add ban', __FILE__, __LINE__, $db->error());
	else
		$db->query('UPDATE '.$db->prefix.'bans SET username='.$ban_user.', ip='.$ban_ip.', email='.$ban_email.', message='.$ban_message.', expire='.$ban_expire.' WHERE id='.intval($_POST['ban_id'])) or error('Unable to update ban', __FILE__, __LINE__, $db->error());

	// Regenerate the bans cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_bans_cache();

	redirect('backstage/bans.php');
}

// Remove a ban
else if (isset($_GET['del_ban'])) {
	confirm_referrer('backstage/bans.php');
	
	$ban_id = intval($_GET['del_ban']);
	if ($ban_id < 1)
		message_backstage($lang['Bad request'], false, '404 Not Found');

	$db->query('DELETE FROM '.$db->prefix.'bans WHERE id='.$ban_id) or error('Unable to delete ban', __FILE__, __LINE__, $db->error());

	// Regenerate the bans cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_bans_cache();

	redirect('backstage/bans.php');
}

// Find bans
else if (isset($_GET['find_ban'])) {
	$form = isset($_GET['form']) ? $_GET['form'] : array();

	// trim() all elements in $form
	$form = array_map('luna_trim', $form);
	$conditions = $query_str = array();

	$expire_after = isset($_GET['expire_after']) ? luna_trim($_GET['expire_after']) : '';
	$expire_before = isset($_GET['expire_before']) ? luna_trim($_GET['expire_before']) : '';
	$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], array('username', 'ip', 'email', 'expire')) ? 'b.'.$_GET['order_by'] : 'b.username';
	$direction = isset($_GET['direction']) && $_GET['direction'] == 'DESC' ? 'DESC' : 'ASC';

	$query_str[] = 'order_by='.$order_by;
	$query_str[] = 'direction='.$direction;

	// Try to convert date/time to timestamps
	if ($expire_after != '') {
		$query_str[] = 'expire_after='.$expire_after;

		$expire_after = strtotime($expire_after);
		if ($expire_after === false || $expire_after == -1)
			message_backstage($lang['Invalid date message']);

		$conditions[] = 'b.expire>'.$expire_after;
	}
	if ($expire_before != '') {
		$query_str[] = 'expire_before='.$expire_before;

		$expire_before = strtotime($expire_before);
		if ($expire_before === false || $expire_before == -1)
			message_backstage($lang['Invalid date message']);

		$conditions[] = 'b.expire<'.$expire_before;
	}

	$like_command = ($db_type == 'pgsql') ? 'ILIKE' : 'LIKE';
	foreach ($form as $key => $input) {
		if ($input != '' && in_array($key, array('username', 'ip', 'email', 'message'))) {
			$conditions[] = 'b.'.$db->escape($key).' '.$like_command.' \''.$db->escape(str_replace('*', '%', $input)).'\'';
			$query_str[] = 'form%5B'.$key.'%5D='.urlencode($input);
		}
	}

	// Fetch ban count
	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'bans as b WHERE b.id>0'.(!empty($conditions) ? ' AND '.implode(' AND ', $conditions) : '')) or error('Unable to fetch ban list', __FILE__, __LINE__, $db->error());
	$num_bans = $db->result($result);

	// Determine the ban offset (based on $_GET['p'])
	$num_pages = ceil($num_bans / 50);

	$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : intval($_GET['p']);
	$start_from = 50 * ($p - 1);

	// Generate paging links
	$paging_links = paginate($num_pages, $p, 'bans.php?find_ban=&amp;'.implode('&amp;', $query_str));

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Bans'], $lang['Results head']);
	define('FORUM_ACTIVE_PAGE', 'admin');
	require 'header.php';
	load_admin_nav('users', 'bans');

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Results head'] ?></h3>
    </div>
    <div class="panel-body">
		<ul class="pagination">
			<?php echo $paging_links ?>
		</ul>
	</div>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th><?php echo $lang['Username'] ?></th>
				<th><?php echo $lang['Email'] ?></th>
				<th><?php echo $lang['Results IP address head'] ?></th>
				<th><?php echo $lang['Results expire head'] ?></th>
				<th><?php echo $lang['Message'] ?></th>
				<th><?php echo $lang['Results banned by head'] ?></th>
				<th><?php echo $lang['Actions'] ?></th>
			</tr>
		</thead>
		<tbody>
    <?php

	$result = $db->query('SELECT b.id, b.username, b.ip, b.email, b.message, b.expire, b.ban_creator, u.username AS ban_creator_username FROM '.$db->prefix.'bans AS b LEFT JOIN '.$db->prefix.'users AS u ON b.ban_creator=u.id WHERE b.id>0'.(!empty($conditions) ? ' AND '.implode(' AND ', $conditions) : '').' ORDER BY '.$db->escape($order_by).' '.$db->escape($direction).' LIMIT '.$start_from.', 50') or error('Unable to fetch ban list', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		while ($ban_data = $db->fetch_assoc($result)) {

			$actions = '<div class="btn-group"><a class="btn btn-primary" href="bans.php?edit_ban='.$ban_data['id'].'">'.$lang['Edit'].'</a><a class="btn btn-danger" href="bans.php?del_ban='.$ban_data['id'].'">'.$lang['Remove'].'</a></div>';
			$expire = format_time($ban_data['expire'], true);

?>
			<tr>
				<td class="tcl"><?php echo ($ban_data['username'] != '') ? luna_htmlspecialchars($ban_data['username']) : '&#160;' ?></td>
				<td class="tc2"><?php echo ($ban_data['email'] != '') ? luna_htmlspecialchars($ban_data['email']) : '&#160;' ?></td>
				<td class="tc3"><?php echo ($ban_data['ip'] != '') ? luna_htmlspecialchars($ban_data['ip']) : '&#160;' ?></td>
				<td class="tc4"><?php echo $expire ?></td>
				<td class="tc5"><?php echo ($ban_data['message'] != '') ? luna_htmlspecialchars($ban_data['message']) : '&#160;' ?></td>
				<td class="tc6"><?php echo ($ban_data['ban_creator_username'] != '') ? '<a href="../profile.php?id='.$ban_data['ban_creator'].'">'.luna_htmlspecialchars($ban_data['ban_creator_username']).'</a>' : $lang['Unknown'] ?></td>
				<td class="tcr"><?php echo $actions ?></td>
			</tr>
<?php

		}
	} else
		echo "\t\t\t\t".'<tr><td class="tcl" colspan="7">'.$lang['No match'].'</td></tr>'."\n";

?>
		</tbody>
	</table>
	<div class="panel-body">
		<ul class="pagination">
			<?php echo $paging_links ?>
		</ul>
    </div>
</div>
<?php

	require 'footer.php';
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Bans']);
$focus_element = array('bans', 'new_ban_user');
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('users', 'bans');

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['New ban head'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="bans" method="post" action="bans.php?action=more">
            <fieldset>
				<div class="input-group">
					<input type="text" class="form-control" name="new_ban_user" maxlength="25" tabindex="1" />
					<span class="input-group-btn">
						<input class="btn btn-danger" type="submit" name="add_ban" value="<?php echo $lang['Add'] ?>" tabindex="2" />
					</span>
				</div>
                <span class="help-block"><?php echo $lang['Username advanced help'] ?></span>
            </fieldset>
        </form>
    </div>
</div>
<form id="find_bans" method="get" action="bans.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Ban search head'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="find_ban" value="<?php echo $lang['Submit search'] ?>" tabindex="12" /></span></h3>
		</div>
		<fieldset>
			<div class="panel-body">
				<p><?php echo $lang['Ban search info'] ?></p>
			</div>
			<table class="table">
				<tr>
					<th><?php echo $lang['Username'] ?></th>
					<td><input type="text" class="form-control" name="form[username]" maxlength="25" tabindex="4" /></td>
					<th><?php echo $lang['IP label'] ?></th>
					<td><input type="text" class="form-control" name="form[ip]" maxlength="255" tabindex="5" /></td>
				</tr>
				<tr>
					<th><?php echo $lang['Email'] ?></th>
					<td><input type="text" class="form-control" name="form[email]" maxlength="80" tabindex="6" /></td>
					<th><?php echo $lang['Message'] ?></th>
					<td><input type="text" class="form-control" name="form[message]" maxlength="255" tabindex="7" /></td>
				</tr>
				<tr>
					<th><?php echo $lang['Expire after label'] ?></th>
					<td><input type="text" class="form-control" name="expire_after" maxlength="10" tabindex="8" placeholder="<?php echo $lang['Date help'] ?>" /></td>
					<th><?php echo $lang['Expire before label'] ?></th>
					<td><input type="text" class="form-control" name="expire_before" maxlength="10" tabindex="9" placeholder="<?php echo $lang['Date help'] ?>" /></td>
				</tr>
				<tr>
					<th><?php echo $lang['Order by label'] ?></th>
					<td colspan="3">
						<select class="form-control" name="order_by" tabindex="10">
							<option value="username" selected="selected"><?php echo $lang['Username'] ?></option>
							<option value="ip"><?php echo $lang['Order by ip'] ?></option>
							<option value="email"><?php echo $lang['Email'] ?></option>
							<option value="expire"><?php echo $lang['Order by expire'] ?></option>
						</select>&#160;&#160;&#160;<select class="form-control" name="direction" tabindex="11">
							<option value="ASC" selected="selected"><?php echo $lang['Ascending'] ?></option>
							<option value="DESC"><?php echo $lang['Descending'] ?></option>
						</select>
					</td>
				</tr>
			</table>
		</fieldset>
	</div>
</form>
<?php

require 'footer.php';
