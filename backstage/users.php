<?php

/**
 * Copyright (C) 2013-2014 ModernBB Group
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License under GPLv3
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$luna_user['is_admmod']) {
    header("Location: ../login.php");
}

// Create new user
if (isset($_POST['add_user']))
{
	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Users'], $lang['Results head']);
	define('FORUM_ACTIVE_PAGE', 'admin');
	require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('users');

	$username = luna_trim($_POST['username']);
	$email1 = strtolower(trim($_POST['email']));
	$email2 = strtolower(trim($_POST['email']));

	if (isset($_POST['random_pass']) == '1')
		$password = random_pass(8);
	else
		$password = trim($_POST['password']);

	$errors = array();

	// Convert multiple whitespace characters into one (to prevent people from registering with indistinguishable usernames)
	$username = preg_replace('#\s+#s', ' ', $username);

	// Validate username and passwords
	if (strlen($username) < 2)
		message_backstage($lang['Username too short']);
	else if (luna_strlen($username) > 25)	// This usually doesn't happen since the form element only accepts 25 characters
	    message_backstage($lang['Bad request'], false, '404 Not Found');
	else if (strlen($password) < 6)
		message_backstage($lang['Pass too short']);
	else if (!strcasecmp($username, 'Guest') || !strcasecmp($username, $lang['Guest']))
		message_backstage($lang['Username guest']);
	else if (preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $username))
		message_backstage($lang['Username IP']);
	else if ((strpos($username, '[') !== false || strpos($username, ']') !== false) && strpos($username, '\'') !== false && strpos($username, '"') !== false)
		message_backstage($lang['Username reserved chars']);
	else if (preg_match('#\[b\]|\[/b\]|\[u\]|\[/u\]|\[i\]|\[/i\]|\[color|\[/color\]|\[quote\]|\[quote=|\[/quote\]|\[code\]|\[/code\]|\[img\]|\[/img\]|\[url|\[/url\]|\[email|\[/email\]#i', $username))
		message_backstage($lang['Username BBCode']);

	// Check username for any censored words
	if ($luna_config['o_censoring'] == '1')
	{
		// If the censored username differs from the username
		if (censor_words($username) != $username)
			message_backstage($lang['Username censor']);
	}

	// Check that the username (or a too similar username) is not already registered
	$result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE username=\''.$db->escape($username).'\' OR username=\''.$db->escape(preg_replace('/[^\w]/', '', $username)).'\'') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());

	if ($db->num_rows($result))
	{
		$busy = $db->result($result);
		message_backstage($lang['Username dupe 1'].' '.luna_htmlspecialchars($busy).'. '.$lang['Username dupe 2']);
	}

	// Validate e-mail
	require FORUM_ROOT.'include/email.php';

	if (!is_valid_email($email1))
		message_backstage($lang['Invalid e-mail']);

	// Check if someone else already has registered with that e-mail address
	$dupe_list = array();

	$result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE email=\''.$email1.'\'') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result))
	{
		while ($cur_dupe = $db->fetch_assoc($result))
			$dupe_list[] = $cur_dupe['username'];
	}

	$timezone = '0';
	$language = isset($_POST['language']) ? $_POST['language'] : $luna_config['o_default_lang'];

	$email_setting = intval(1);

	// Insert the new user into the database. We do this now to get the last inserted id for later use.
	$now = time();

	$intial_group_id = ($_POST['random_pass'] == '0') ? $luna_config['o_default_user_group'] : FORUM_UNVERIFIED;
	$password_hash = luna_hash($password);

	// Add the user
	$db->query('INSERT INTO '.$db->prefix.'users (username, group_id, password, email, email_setting, timezone, language, style, registered, registration_ip, last_visit) VALUES(\''.$db->escape($username).'\', '.$intial_group_id.', \''.$password_hash.'\', \''.$email1.'\', '.$email_setting.', '.$timezone.' , \''.$language.'\', \''.$luna_config['o_default_style'].'\', '.$now.', \''.get_remote_address().'\', '.$now.')') or error('Unable to create user', __FILE__, __LINE__, $db->error());
	$new_uid = $db->insert_id();

	// Should we alert people on the admin mailing list that a new user has registered?
	if ($luna_config['o_regs_report'] == '1')
	{
		$mail_subject = 'Alert - New registration';
		$mail_message = 'User \''.$username.'\' registered in the forums at '.$luna_config['o_base_url']."\n\n".'User profile: '.$luna_config['o_base_url'].'/profile.php?id='.$new_uid."\n\n".'-- '."\n".'Forum Mailer'."\n".'(Do not reply to this message)';

		luna_mail($luna_config['o_mailing_list'], $mail_subject, $mail_message);
	}

	// Must the user verify the registration or do we log him/her in right now?
	if ($_POST['random_pass'] == '1')
	{
		// Load the "welcome" template
		$mail_tpl = trim($lang['welcome.tpl']);

		// The first row contains the subject
		$first_crlf = strpos($mail_tpl, "\n");
		$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
		$mail_message = trim(substr($mail_tpl, $first_crlf));
		$mail_subject = str_replace('<board_title>', $luna_config['o_board_title'], $mail_subject);
		$mail_message = str_replace('<base_url>', $luna_config['o_base_url'].'/', $mail_message);
		$mail_message = str_replace('<username>', $username, $mail_message);
		$mail_message = str_replace('<password>', $password, $mail_message);
		$mail_message = str_replace('<login_url>', $luna_config['o_base_url'].'/login.php', $mail_message);
		$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);
		luna_mail($email1, $mail_subject, $mail_message);
	}

	// Regenerate the users info cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_users_info_cache();

	message_backstage('User Created');
}

// Show IP statistics for a certain user ID
if (isset($_GET['ip_stats']))
{
	$ip_stats = intval($_GET['ip_stats']);
	if ($ip_stats < 1)
		message_backstage($lang['Bad request'], false, '404 Not Found');

	// Fetch ip count
	$result = $db->query('SELECT poster_ip, MAX(posted) AS last_used FROM '.$db->prefix.'posts WHERE poster_id='.$ip_stats.' GROUP BY poster_ip') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	$num_ips = $db->num_rows($result);

	// Determine the ip offset (based on $_GET['p'])
	$num_pages = ceil($num_ips / 50);

	$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : intval($_GET['p']);
	$start_from = 50 * ($p - 1);

	// Generate paging links
	$paging_links = paginate($num_pages, $p, 'users.php?ip_stats='.$ip_stats );

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Users'], $lang['Results head']);
	define('FORUM_ACTIVE_PAGE', 'admin');
	require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('users');

?>
<h2><?php echo $lang['Users'] ?></h2>
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
				<th><?php echo $lang['Results IP address head'] ?></th>
				<th><?php echo $lang['Results last used head'] ?></th>
				<th><?php echo $lang['Results times found head'] ?></th>
				<th><?php echo $lang['Action'] ?></th>
			</tr>
		</thead>
		<tbody>
<?php

	$result = $db->query('SELECT poster_ip, MAX(posted) AS last_used, COUNT(id) AS used_times FROM '.$db->prefix.'posts WHERE poster_id='.$ip_stats.' GROUP BY poster_ip ORDER BY last_used DESC LIMIT '.$start_from.', 50') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result))
	{
		while ($cur_ip = $db->fetch_assoc($result))
		{

?>
			<tr>
				<td><a href="../moderate.php?get_host=<?php echo $cur_ip['poster_ip'] ?>"><?php echo luna_htmlspecialchars($cur_ip['poster_ip']) ?></a></td>
				<td><?php echo format_time($cur_ip['last_used']) ?></td>
				<td><?php echo $cur_ip['used_times'] ?></td>
				<td><a href="users.php?show_users=<?php echo luna_htmlspecialchars($cur_ip['poster_ip']) ?>"><?php echo $lang['Results find more link'] ?></a></td>
			</tr>
<?php

		}
	}
	else
		echo "\t\t\t\t".'<tr><td colspan="4">'.$lang['Results no posts found'].'</td></tr>'."\n";

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

	require FORUM_ROOT.'backstage/footer.php';
}


if (isset($_GET['show_users']))
{
	$ip = luna_trim($_GET['show_users']);

	if (!@preg_match('%^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$%', $ip) && !@preg_match('%^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$%', $ip))
		message_backstage($lang['Bad IP message']);

	// Fetch user count
	$result = $db->query('SELECT DISTINCT poster_id, poster FROM '.$db->prefix.'posts WHERE poster_ip=\''.$db->escape($ip).'\'') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	$num_users = $db->num_rows($result);

	// Determine the user offset (based on $_GET['p'])
	$num_pages = ceil($num_users / 50);

	$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : intval($_GET['p']);
	$start_from = 50 * ($p - 1);

	// Generate paging links
	$paging_links = paginate($num_pages, $p, 'users.php?show_users='.$ip);

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Users'], $lang['Results head']);
	define('FORUM_ACTIVE_PAGE', 'admin');
	require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('users');

?>
<h2><?php echo $lang['Users'] ?></h2>
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
				<th><?php echo $lang['Results title head'] ?></th>
				<th class="text-center"><?php echo $lang['Results posts head'] ?></th>
				<th><?php echo $lang['Admin note'] ?></th>
				<th><?php echo $lang['Actions'] ?></th>
			</tr>
		</thead>
		<tbody>
<?php

	$result = $db->query('SELECT DISTINCT poster_id, poster FROM '.$db->prefix.'posts WHERE poster_ip=\''.$db->escape($ip).'\' ORDER BY poster ASC LIMIT '.$start_from.', 50') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	$num_posts = $db->num_rows($result);

	if ($num_posts)
	{
		$posters = $poster_ids = array();
		while ($cur_poster = $db->fetch_assoc($result))
		{
			$posters[] = $cur_poster;
			$poster_ids[] = $cur_poster['poster_id'];
		}

		$result = $db->query('SELECT u.id, u.username, u.email, u.title, u.num_posts, u.admin_note, g.g_id, g.g_user_title FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id>1 AND u.id IN('.implode(',', $poster_ids).')') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
		$user_data = array();
		while ($cur_user = $db->fetch_assoc($result))
			$user_data[$cur_user['id']] = $cur_user;

		// Loop through users and print out some info
		foreach ($posters as $cur_poster)
		{
			if (isset($user_data[$cur_poster['poster_id']]))
			{
				$user_title = get_title($user_data[$cur_poster['poster_id']]);

			$actions = '<a href="users.php?ip_stats='.$user_data[$cur_poster['poster_id']]['id'].'">'.$lang['Results view IP link'].'</a> &middot; <a href="../search.php?action=show_user_posts&amp;user_id='.$user_data[$cur_poster['poster_id']]['id'].'">'.$lang['Posts table'].'</a>';
?>
			<tr>
				<td><?php echo '<a href="../profile.php?id='.$user_data[$cur_poster['poster_id']]['id'].'">'.luna_htmlspecialchars($user_data[$cur_poster['poster_id']]['username']).'</a>' ?></td>
				<td><a href="mailto:<?php echo luna_htmlspecialchars($user_data[$cur_poster['poster_id']]['email']) ?>"><?php echo luna_htmlspecialchars($user_data[$cur_poster['poster_id']]['email']) ?></a></td>
				<td><?php echo $user_title ?></td>
				<td class="text-center"><?php echo forum_number_format($user_data[$cur_poster['poster_id']]['num_posts']) ?></td>
				<td><?php echo ($user_data[$cur_poster['poster_id']]['admin_note'] != '') ? luna_htmlspecialchars($user_data[$cur_poster['poster_id']]['admin_note']) : '&#160;' ?></td>
				<td><?php echo $actions ?></td>
			</tr>
<?php

			}
			else
			{

?>
			<tr>
				<td><?php echo luna_htmlspecialchars($cur_poster['poster']) ?></td>
				<td>&#160;</td>
				<td><?php echo $lang['Guest'] ?></td>
				<td>&#160;</td>
				<td>&#160;</td>
				<td>&#160;</td>
			</tr>
<?php

			}
		}
	}
	else
		echo "\t\t\t\t".'<tr><td colspan="6">'.$lang['Results no IP found'].'</td></tr>'."\n";

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
	require FORUM_ROOT.'backstage/footer.php';
}


// Move multiple users to other user groups
else if (isset($_POST['move_users']) || isset($_POST['move_users_comply']))
{
	if ($luna_user['g_id'] > FORUM_ADMIN)
		message_backstage($lang['No permission'], false, '403 Forbidden');

	confirm_referrer('backstage/users.php');

	if (isset($_POST['users']))
	{
		$user_ids = is_array($_POST['users']) ? array_keys($_POST['users']) : explode(',', $_POST['users']);
		$user_ids = array_map('intval', $user_ids);

		// Delete invalid IDs
		$user_ids = array_diff($user_ids, array(0, 1));
	}
	else
		$user_ids = array();

	if (empty($user_ids))
		message_backstage($lang['No users selected']);

	// Are we trying to batch move any admins?
	$result = $db->query('SELECT COUNT(*) FROM '.$db->prefix.'users WHERE id IN ('.implode(',', $user_ids).') AND group_id='.FORUM_ADMIN) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	if ($db->result($result) > 0)
		message_backstage($lang['No move admins message']);

	// Fetch all user groups
	$all_groups = array();
	$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id NOT IN ('.FORUM_GUEST.','.FORUM_ADMIN.') ORDER BY g_title ASC') or error('Unable to fetch groups', __FILE__, __LINE__, $db->error());
	while ($row = $db->fetch_row($result))
		$all_groups[$row[0]] = $row[1];

	if (isset($_POST['move_users_comply']))
	{
		$new_group = isset($_POST['new_group']) && isset($all_groups[$_POST['new_group']]) ? $_POST['new_group'] : message_backstage($lang['Invalid group message']);

		// Is the new group a moderator group?
		$result = $db->query('SELECT g_moderator FROM '.$db->prefix.'groups WHERE g_id='.$new_group) or error('Unable to fetch group info', __FILE__, __LINE__, $db->error());
		$new_group_mod = $db->result($result);

		// Fetch user groups
		$user_groups = array();
		$result = $db->query('SELECT id, group_id FROM '.$db->prefix.'users WHERE id IN ('.implode(',', $user_ids).')') or error('Unable to fetch user groups', __FILE__, __LINE__, $db->error());
		while ($cur_user = $db->fetch_assoc($result))
		{
			if (!isset($user_groups[$cur_user['group_id']]))
				$user_groups[$cur_user['group_id']] = array();

			$user_groups[$cur_user['group_id']][] = $cur_user['id'];
		}

		// Are any users moderators?
		$group_ids = array_keys($user_groups);
		$result = $db->query('SELECT g_id, g_moderator FROM '.$db->prefix.'groups WHERE g_id IN ('.implode(',', $group_ids).')') or error('Unable to fetch group moderators', __FILE__, __LINE__, $db->error());
		while ($cur_group = $db->fetch_assoc($result))
		{
			if ($cur_group['g_moderator'] == '0')
				unset($user_groups[$cur_group['g_id']]);
		}

		if (!empty($user_groups) && $new_group != FORUM_ADMIN && $new_group_mod != '1')
		{
			// Fetch forum list and clean up their moderator list
			$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());
			while ($cur_forum = $db->fetch_assoc($result))
			{
				$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

				foreach ($user_groups as $group_users)
					$cur_moderators = array_diff($cur_moderators, $group_users);

				$cur_moderators = (!empty($cur_moderators)) ? '\''.$db->escape(serialize($cur_moderators)).'\'' : 'NULL';
				$db->query('UPDATE '.$db->prefix.'forums SET moderators='.$cur_moderators.' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
			}
		}

		// Change user group
		$db->query('UPDATE '.$db->prefix.'users SET group_id='.$new_group.' WHERE id IN ('.implode(',', $user_ids).')') or error('Unable to change user group', __FILE__, __LINE__, $db->error());

		redirect('backstage/users.php');
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Users'], $lang['Move users']);
	define('FORUM_ACTIVE_PAGE', 'admin');
	require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('users');

?>
<h2><?php echo $lang['Move users'] ?></h2>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Move users'] ?></h3>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" name="confirm_move_users" method="post" action="users.php">
            <input type="hidden" name="users" value="<?php echo implode(',', $user_ids) ?>" />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['New group label'] ?><span class="help-block"><?php echo $lang['New group help'] ?></span></label>
                    <div class="col-sm-9">
						<div class="input-group">
							<select class="form-control" name="new_group" tabindex="1">
	<?php foreach ($all_groups as $gid => $group) : ?>											<option value="<?php echo $gid ?>"><?php echo luna_htmlspecialchars($group) ?></option>
	<?php endforeach; ?>
							</select>
							<span class="input-group-btn"><input class="btn btn-primary" type="submit" name="move_users_comply" value="<?php echo $lang['Save'] ?>" tabindex="2" /></span>
						</div>
                    </div>
                </div>
            </fieldset>
        </form>
	</div>
</div>
<?php

	require FORUM_ROOT.'backstage/footer.php';
}


// Delete multiple users
else if (isset($_POST['delete_users']) || isset($_POST['delete_users_comply']))
{
	if ($luna_user['g_id'] > FORUM_ADMIN)
		message_backstage($lang['No permission'], false, '403 Forbidden');

	confirm_referrer('backstage/users.php');

	if (isset($_POST['users']))
	{
		$user_ids = is_array($_POST['users']) ? array_keys($_POST['users']) : explode(',', $_POST['users']);
		$user_ids = array_map('intval', $user_ids);

		// Delete invalid IDs
		$user_ids = array_diff($user_ids, array(0, 1));
	}
	else
		$user_ids = array();

	if (empty($user_ids))
		message_backstage($lang['No users selected']);

	// Are we trying to delete any admins?
	$result = $db->query('SELECT COUNT(*) FROM '.$db->prefix.'users WHERE id IN ('.implode(',', $user_ids).') AND group_id='.FORUM_ADMIN) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	if ($db->result($result) > 0)
		message_backstage($lang['No delete admins message']);

	if (isset($_POST['delete_users_comply']))
	{
		// Fetch user groups
		$user_groups = array();
		$result = $db->query('SELECT id, group_id FROM '.$db->prefix.'users WHERE id IN ('.implode(',', $user_ids).')') or error('Unable to fetch user groups', __FILE__, __LINE__, $db->error());
		while ($cur_user = $db->fetch_assoc($result))
		{
			if (!isset($user_groups[$cur_user['group_id']]))
				$user_groups[$cur_user['group_id']] = array();

			$user_groups[$cur_user['group_id']][] = $cur_user['id'];
		}

		// Are any users moderators?
		$group_ids = array_keys($user_groups);
		$result = $db->query('SELECT g_id, g_moderator FROM '.$db->prefix.'groups WHERE g_id IN ('.implode(',', $group_ids).')') or error('Unable to fetch group moderators', __FILE__, __LINE__, $db->error());
		while ($cur_group = $db->fetch_assoc($result))
		{
			if ($cur_group['g_moderator'] == '0')
				unset($user_groups[$cur_group['g_id']]);
		}

		// Fetch forum list and clean up their moderator list
		$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());
		while ($cur_forum = $db->fetch_assoc($result))
		{
			$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

			foreach ($user_groups as $group_users)
				$cur_moderators = array_diff($cur_moderators, $group_users);

			$cur_moderators = (!empty($cur_moderators)) ? '\''.$db->escape(serialize($cur_moderators)).'\'' : 'NULL';
			$db->query('UPDATE '.$db->prefix.'forums SET moderators='.$cur_moderators.' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
		}

		// Delete any subscriptions
		$db->query('DELETE FROM '.$db->prefix.'topic_subscriptions WHERE user_id IN ('.implode(',', $user_ids).')') or error('Unable to delete topic subscriptions', __FILE__, __LINE__, $db->error());
		$db->query('DELETE FROM '.$db->prefix.'forum_subscriptions WHERE user_id IN ('.implode(',', $user_ids).')') or error('Unable to delete forum subscriptions', __FILE__, __LINE__, $db->error());

		// Remove them from the online list (if they happen to be logged in)
		$db->query('DELETE FROM '.$db->prefix.'online WHERE user_id IN ('.implode(',', $user_ids).')') or error('Unable to remove users from online list', __FILE__, __LINE__, $db->error());

		// Should we delete all posts made by these users?
		if (isset($_POST['delete_posts']))
		{
			require FORUM_ROOT.'include/search_idx.php';
			@set_time_limit(0);

			// Find all posts made by this user
			$result = $db->query('SELECT p.id, p.topic_id, t.forum_id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id WHERE p.poster_id IN ('.implode(',', $user_ids).')') or error('Unable to fetch posts', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result))
			{
				while ($cur_post = $db->fetch_assoc($result))
				{
					// Determine whether this post is the "topic post" or not
					$result2 = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE topic_id='.$cur_post['topic_id'].' ORDER BY posted LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());

					if ($db->result($result2) == $cur_post['id'])
						delete_topic($cur_post['topic_id']);
					else
						delete_post($cur_post['id'], $cur_post['topic_id']);

					update_forum($cur_post['forum_id']);
				}
			}
		}
		else
			// Set all their posts to guest
			$db->query('UPDATE '.$db->prefix.'posts SET poster_id=1 WHERE poster_id IN ('.implode(',', $user_ids).')') or error('Unable to update posts', __FILE__, __LINE__, $db->error());

		// Delete the users
		$db->query('DELETE FROM '.$db->prefix.'users WHERE id IN ('.implode(',', $user_ids).')') or error('Unable to delete users', __FILE__, __LINE__, $db->error());

		// Delete user avatars
		foreach ($user_ids as $user_id)
			delete_avatar($user_id);

		// Regenerate the users info cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_users_info_cache();

		redirect('backstage/users.php?deleted=true');
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Users'], $lang['Delete users']);
	define('FORUM_ACTIVE_PAGE', 'admin');
	require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('users');

?>
<h2><?php echo $lang['Users'] ?></h2>
<form name="confirm_del_users" method="post" action="users.php">
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Delete users'] ?></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="users" value="<?php echo implode(',', $user_ids) ?>" />
			<fieldset>
            	<p><?php echo $lang['Delete warning'] ?></p>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="delete_posts" value="1" checked="checked" />
						<?php echo $lang['Delete all posts'] ?>
					</label>
				</div>
			</fieldset>
        </div>
        <div class="panel-footer">
            <input class="btn btn-danger" type="submit" name="delete_users_comply" value="<?php echo $lang['Delete'] ?>" />
            <a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a>
        </div>
    </div>
</form>
<?php

	require FORUM_ROOT.'backstage/footer.php';
}


// Ban multiple users
else if (isset($_POST['ban_users']) || isset($_POST['ban_users_comply']))
{
	if ($luna_user['g_id'] != FORUM_ADMIN && ($luna_user['g_moderator'] != '1' || $luna_user['g_mod_ban_users'] == '0'))
		message_backstage($lang['No permission'], false, '403 Forbidden');

	confirm_referrer('backstage/users.php');

	if (isset($_POST['users']))
	{
		$user_ids = is_array($_POST['users']) ? array_keys($_POST['users']) : explode(',', $_POST['users']);
		$user_ids = array_map('intval', $user_ids);

		// Delete invalid IDs
		$user_ids = array_diff($user_ids, array(0, 1));
	}
	else
		$user_ids = array();

	if (empty($user_ids))
		message_backstage($lang['No users selected']);

	// Are we trying to ban any admins?
	$result = $db->query('SELECT COUNT(*) FROM '.$db->prefix.'users WHERE id IN ('.implode(',', $user_ids).') AND group_id='.FORUM_ADMIN) or error('Unable to fetch group info', __FILE__, __LINE__, $db->error());
	if ($db->result($result) > 0)
		message_backstage($lang['No ban admins message']);

	// Also, we cannot ban moderators
	$result = $db->query('SELECT COUNT(*) FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'groups AS g ON u.group_id=g.g_id WHERE g.g_moderator=1 AND u.id IN ('.implode(',', $user_ids).')') or error('Unable to fetch moderator group info', __FILE__, __LINE__, $db->error());
	if ($db->result($result) > 0)
		message_backstage($lang['No ban mods message']);

	if (isset($_POST['ban_users_comply']))
	{
		$ban_message = luna_trim($_POST['ban_message']);
		$ban_expire = luna_trim($_POST['ban_expire']);
		$ban_the_ip = isset($_POST['ban_the_ip']) ? intval($_POST['ban_the_ip']) : 0;

		if ($ban_expire != '' && $ban_expire != 'Never')
		{
			$ban_expire = strtotime($ban_expire.' GMT');

			if ($ban_expire == -1 || !$ban_expire)
				message_backstage($lang['Invalid date message'].' '.$lang['Invalid date reasons']);

			$diff = ($luna_user['timezone'] + $luna_user['dst']) * 3600;
			$ban_expire -= $diff;

			if ($ban_expire <= time())
				message_backstage($lang['Invalid date message'].' '.$lang['Invalid date reasons']);
		}
		else
			$ban_expire = 'NULL';

		$ban_message = ($ban_message != '') ? '\''.$db->escape($ban_message).'\'' : 'NULL';

		// Fetch user information
		$user_info = array();
		$result = $db->query('SELECT id, username, email, registration_ip FROM '.$db->prefix.'users WHERE id IN ('.implode(',', $user_ids).')') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
		while ($cur_user = $db->fetch_assoc($result))
			$user_info[$cur_user['id']] = array('username' => $cur_user['username'], 'email' => $cur_user['email'], 'ip' => $cur_user['registration_ip']);

		// Overwrite the registration IP with one from the last post (if it exists)
		if ($ban_the_ip != 0)
		{
			$result = $db->query('SELECT p.poster_id, p.poster_ip FROM '.$db->prefix.'posts AS p INNER JOIN (SELECT MAX(id) AS id FROM '.$db->prefix.'posts WHERE poster_id IN ('.implode(',', $user_ids).') GROUP BY poster_id) AS i ON p.id=i.id') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
			while ($cur_address = $db->fetch_assoc($result))
				$user_info[$cur_address['poster_id']]['ip'] = $cur_address['poster_ip'];
		}

		// And insert the bans!
		foreach ($user_ids as $user_id)
		{
			$ban_username = '\''.$db->escape($user_info[$user_id]['username']).'\'';
			$ban_email = '\''.$db->escape($user_info[$user_id]['email']).'\'';
			$ban_ip = ($ban_the_ip != 0) ? '\''.$db->escape($user_info[$user_id]['ip']).'\'' : 'NULL';

			$db->query('INSERT INTO '.$db->prefix.'bans (username, ip, email, message, expire, ban_creator) VALUES('.$ban_username.', '.$ban_ip.', '.$ban_email.', '.$ban_message.', '.$ban_expire.', '.$luna_user['id'].')') or error('Unable to add ban', __FILE__, __LINE__, $db->error());
		}

		// Regenerate the bans cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_bans_cache();

		redirect('backstage/users.php');
	}

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Bans']);
	$focus_element = array('bans2', 'ban_message');
	define('FORUM_ACTIVE_PAGE', 'admin');
	require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('users');

?>
<h2><?php echo $lang['Users'] ?></h2>
<form id="bans2" class="form-horizontal" name="confirm_ban_users" method="post" action="users.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Ban users'] ?><span class="pull-right"><input class="btn btn-danger" type="submit" name="ban_users_comply" value="<?php echo $lang['Ban'] ?>" tabindex="3" /></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="users" value="<?php echo implode(',', $user_ids) ?>" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Ban message label'] ?></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="ban_message" maxlength="255" tabindex="1" />
						<span class="help-block"><?php echo $lang['Ban message help'] ?></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Expire date label'] ?></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="ban_expire" maxlength="10" tabindex="2" />
						<span class="help-block"><?php echo $lang['Expire date help'] ?></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Ban IP label'] ?></label>
					<div class="col-sm-9">
						<label class="radio-inline">
							<input type="radio" name="ban_the_ip" tabindex="3" value="1" checked="checked" />
							<?php echo $lang['Yes'] ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="ban_the_ip" tabindex="4" value="0" checked="checked" />
							<?php echo $lang['No'] ?>
						</label>
						<span class="help-block"><?php echo $lang['Ban IP help'] ?></span>
					</div>
				</div>
			</fieldset>
		 </div>
	</div>
</form>
<?php

	require FORUM_ROOT.'backstage/footer.php';
}


else if (isset($_GET['find_user']))
{
	$form = isset($_GET['form']) ? $_GET['form'] : array();

	// trim() all elements in $form
	$form = array_map('luna_trim', $form);
	$conditions = $query_str = array();

	$posts_greater = isset($_GET['posts_greater']) ? luna_trim($_GET['posts_greater']) : '';
	$posts_less = isset($_GET['posts_less']) ? luna_trim($_GET['posts_less']) : '';
	$last_post_after = isset($_GET['last_post_after']) ? luna_trim($_GET['last_post_after']) : '';
	$last_post_before = isset($_GET['last_post_before']) ? luna_trim($_GET['last_post_before']) : '';
	$last_visit_after = isset($_GET['last_visit_after']) ? luna_trim($_GET['last_visit_after']) : '';
	$last_visit_before = isset($_GET['last_visit_before']) ? luna_trim($_GET['last_visit_before']) : '';
	$registered_after = isset($_GET['registered_after']) ? luna_trim($_GET['registered_after']) : '';
	$registered_before = isset($_GET['registered_before']) ? luna_trim($_GET['registered_before']) : '';
	$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], array('username', 'email', 'num_posts', 'last_post', 'last_visit', 'registered')) ? $_GET['order_by'] : 'username';
	$direction = isset($_GET['direction']) && $_GET['direction'] == 'DESC' ? 'DESC' : 'ASC';
	$user_group = isset($_GET['user_group']) ? intval($_GET['user_group']) : -1;

	$query_str[] = 'order_by='.$order_by;
	$query_str[] = 'direction='.$direction;
	$query_str[] = 'user_group='.$user_group;

	if (preg_match('%[^0-9]%', $posts_greater.$posts_less))
		message_backstage($lang['Non numeric message']);

	// Try to convert date/time to timestamps
	if ($last_post_after != '')
	{
		$query_str[] = 'last_post_after='.$last_post_after;

		$last_post_after = strtotime($last_post_after);
		if ($last_post_after === false || $last_post_after == -1)
			message_backstage($lang['Invalid date time message']);

		$conditions[] = 'u.last_post>'.$last_post_after;
	}
	if ($last_post_before != '')
	{
		$query_str[] = 'last_post_before='.$last_post_before;

		$last_post_before = strtotime($last_post_before);
		if ($last_post_before === false || $last_post_before == -1)
			message_backstage($lang['Invalid date time message']);

		$conditions[] = 'u.last_post<'.$last_post_before;
	}
	if ($last_visit_after != '')
	{
		$query_str[] = 'last_visit_after='.$last_visit_after;

		$last_visit_after = strtotime($last_visit_after);
		if ($last_visit_after === false || $last_visit_after == -1)
			message_backstage($lang['Invalid date time message']);

		$conditions[] = 'u.last_visit>'.$last_visit_after;
	}
	if ($last_visit_before != '')
	{
		$query_str[] = 'last_visit_before='.$last_visit_before;

		$last_visit_before = strtotime($last_visit_before);
		if ($last_visit_before === false || $last_visit_before == -1)
			message_backstage($lang['Invalid date time message']);

		$conditions[] = 'u.last_visit<'.$last_visit_before;
	}
	if ($registered_after != '')
	{
		$query_str[] = 'registered_after='.$registered_after;

		$registered_after = strtotime($registered_after);
		if ($registered_after === false || $registered_after == -1)
			message_backstage($lang['Invalid date time message']);

		$conditions[] = 'u.registered>'.$registered_after;
	}
	if ($registered_before != '')
	{
		$query_str[] = 'registered_before='.$registered_before;

		$registered_before = strtotime($registered_before);
		if ($registered_before === false || $registered_before == -1)
			message_backstage($lang['Invalid date time message']);

		$conditions[] = 'u.registered<'.$registered_before;
	}

	$like_command = ($db_type == 'pgsql') ? 'ILIKE' : 'LIKE';
	foreach ($form as $key => $input)
	{
		if ($input != '' && in_array($key, array('username', 'email', 'title', 'realname', 'url', 'jabber', 'icq', 'msn', 'aim', 'yahoo', 'location', 'signature', 'admin_note')))
		{
			$conditions[] = 'u.'.$db->escape($key).' '.$like_command.' \''.$db->escape(str_replace('*', '%', $input)).'\'';
			$query_str[] = 'form%5B'.$key.'%5D='.urlencode($input);
		}
	}

	if ($posts_greater != '')
	{
		$query_str[] = 'posts_greater='.$posts_greater;
		$conditions[] = 'u.num_posts>'.$posts_greater;
	}
	if ($posts_less != '')
	{
		$query_str[] = 'posts_less='.$posts_less;
		$conditions[] = 'u.num_posts<'.$posts_less;
	}

	if ($user_group > -1)
		$conditions[] = 'u.group_id='.$user_group;

	// Fetch user count
	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id>1'.(!empty($conditions) ? ' AND '.implode(' AND ', $conditions) : '')) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	$num_users = $db->result($result);

	// Determine the user offset (based on $_GET['p'])
	$num_pages = ceil($num_users / 50);

	$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : intval($_GET['p']);
	$start_from = 50 * ($p - 1);

	// Generate paging links
	$paging_links = paginate($num_pages, $p, 'users.php?find_user=&amp;'.implode('&amp;', $query_str));

	// Some helper variables for permissions
	$can_delete = $can_move = $luna_user['g_id'] == FORUM_ADMIN;
	$can_ban = $luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1');
	$can_action = ($can_delete || $can_ban || $can_move) && $num_users > 0;

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Users'], $lang['Results head']);
	$page_head = array('js' => '<script type="text/javascript" src="common.js"></script>');
	define('FORUM_ACTIVE_PAGE', 'admin');
	require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('users');

?>
<h2><?php echo $lang['Users'] ?></h2>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Results head'] ?></h3>
    </div>
	<form id="search-users-form" action="users.php" method="post">
		<div class="panel-body">
			<ul class="pagination">
				<?php echo $paging_links ?>
			</ul>
			<?php if ($can_action): ?>
				<span class="btn-toolbar pull-right">
					<div class="btn-group">
						<?php if ($can_ban) : ?>
						<input class="btn btn-danger" type="submit" name="ban_users" value="<?php echo $lang['Ban'] ?>" />
						<?php endif; if ($can_delete) : ?>
						<input class="btn btn-danger" type="submit" name="delete_users" value="<?php echo $lang['Delete'] ?>" />
						<?php endif; if ($can_move) : ?>
						<input class="btn btn-primary" type="submit" name="move_users" value="<?php echo $lang['Change group'] ?>" />
						<?php endif; ?>
					</div>
				</span>
			<?php endif; ?>
		</div>
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th><?php echo $lang['Username'] ?></th>
					<th><?php echo $lang['Email'] ?></th>
					<th><?php echo $lang['Results title head'] ?></th>
					<th class="text-center"><?php echo $lang['Results posts head'] ?></th>
					<th><?php echo $lang['Admin note'] ?></th>
					<th><?php echo $lang['Actions'] ?></th>
		<?php if ($can_action): ?>					<th><?php echo $lang['Select'] ?></th>
		<?php endif; ?>
				</tr>
			</thead>
			<tbody>
<?php

	$result = $db->query('SELECT u.id, u.username, u.email, u.title, u.num_posts, u.admin_note, g.g_id, g.g_user_title FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id>1'.(!empty($conditions) ? ' AND '.implode(' AND ', $conditions) : '').' ORDER BY '.$db->escape($order_by).' '.$db->escape($direction).' LIMIT '.$start_from.', 50') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result))
	{
		while ($user_data = $db->fetch_assoc($result))
		{
			$user_title = get_title($user_data);

			// This script is a special case in that we want to display "Not verified" for non-verified users
			if (($user_data['g_id'] == '' || $user_data['g_id'] == FORUM_UNVERIFIED) && $user_title != $lang['Banned'])
				$user_title = '<span class="warntext">'.$lang['Not verified'].'</span>';

			$actions = '<a href="users.php?ip_stats='.$user_data['id'].'">'.$lang['Results view IP link'].'</a> &middot <a href="../search.php?action=show_user_posts&amp;user_id='.$user_data['id'].'">'.$lang['Posts table'].'</a>';

?>
				<tr>
					<td><?php echo '<a href="../profile.php?id='.$user_data['id'].'">'.luna_htmlspecialchars($user_data['username']).'</a>' ?></td>
					<td><a href="mailto:<?php echo luna_htmlspecialchars($user_data['email']) ?>"><?php echo luna_htmlspecialchars($user_data['email']) ?></a></td>                 <td><?php echo $user_title ?></td>
					<td class="text-center"><?php echo forum_number_format($user_data['num_posts']) ?></td>
					<td><?php echo ($user_data['admin_note'] != '') ? luna_htmlspecialchars($user_data['admin_note']) : '&#160;' ?></td>
					<td><?php echo $actions ?></td>
		<?php if ($can_action): ?>					<td><input type="checkbox" name="users[<?php echo $user_data['id'] ?>]" value="1" /></td>
		<?php endif; ?>
				</tr>
<?php

		}
	}
	else
		echo "\t\t\t\t".'<tr><td colspan="6">'.$lang['No match'].'</td></tr>'."\n";

?>
			</tbody>
		</table>
		<div class="panel-body">
			<ul class="pagination">
				<?php echo $paging_links ?>
			</ul>
			<?php if ($can_action): ?>
				<span class="btn-toolbar pull-right">
					<div class="btn-group">
						<?php if ($can_ban) : ?>
						<input class="btn btn-danger" type="submit" name="ban_users" value="<?php echo $lang['Ban'] ?>" />
						<?php endif; if ($can_delete) : ?>
						<input class="btn btn-danger" type="submit" name="delete_users" value="<?php echo $lang['Delete'] ?>" />
						<?php endif; if ($can_move) : ?>
						<input class="btn btn-primary" type="submit" name="move_users" value="<?php echo $lang['Change group'] ?>" />
						<?php endif; ?>
					</div>
				</span>
			<?php endif; ?>
		</div>
	</form>
</div>
<?php

	require FORUM_ROOT.'backstage/footer.php';
}


else
{
	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Users']);
	$focus_element = array('find_user', 'form[username]');
	define('FORUM_ACTIVE_PAGE', 'admin');
	require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('users');

?>
<h2><?php echo $lang['Users'] ?></h2>
<?php
if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>';
if (isset($_GET['deleted']))
	echo '<div class="alert alert-danger"><h4>'.$lang['User deleted'].'</h4></div>';
?>
<form id="find_user" method="get" action="users.php">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['User search'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="find_user" value="<?php echo $lang['Submit search'] ?>" tabindex="1" /></span></h3>
        </div>
		<fieldset>
			<div class="panel-body">
                <p><?php echo $lang['User search info'] ?></p>
			</div>
			<table class="table">
				<tr>
					<th><?php echo $lang['Username'] ?></th>
					<td><input type="text" class="form-control" name="form[username]" maxlength="25" tabindex="2" /></td>
					<th><?php echo $lang['E-mail address label'] ?></th>
					<td><input type="text" class="form-control" name="form[email]" maxlength="80" tabindex="3" /></td>
				</tr>
				<tr>
					<th><?php echo $lang['Title'] ?></th>
					<td><input type="text" class="form-control" name="form[title]" maxlength="50" tabindex="4" /></td>
					<th><?php echo $lang['Real name label'] ?></th>
					<td><input type="text" class="form-control" name="form[realname]" maxlength="40" tabindex="5" /></td>
				</tr>
				<tr>
					<th><?php echo $lang['Website'] ?></th>
					<td><input type="text" class="form-control" name="form[url]" maxlength="100" tabindex="6" /></td>
					<th><?php echo $lang['Jabber'] ?></th>
					<td><input type="text" class="form-control" name="form[jabber]" maxlength="75" tabindex="7" /></td>
				</tr>
				<tr>
					<th><?php echo $lang['ICQ'] ?></th>
					<td><input type="text" class="form-control" name="form[icq]" maxlength="12" tabindex="8" /></td>
					<th><?php echo $lang['MSN'] ?></th>
					<td><input type="text" class="form-control" name="form[msn]" maxlength="50" tabindex="9" /></td>
				</tr>
				<tr>
					<th><?php echo $lang['AOL'] ?></th>
					<td><input type="text" class="form-control" name="form[aim]" maxlength="20" tabindex="10" /></td>
					<th><?php echo $lang['Yahoo'] ?></th>
					<td><input type="text" class="form-control" name="form[yahoo]" maxlength="20" tabindex="11" /></td>
				</tr>
				<tr>
					<th><?php echo $lang['Location'] ?></th>
					<td><input type="text" class="form-control" name="form[location]" maxlength="30" tabindex="12" /></td>
					<th><?php echo $lang['Signature'] ?></th>
					<td><input type="text" class="form-control" name="form[signature]" maxlength="512" tabindex="13" /></td>
				</tr>
				<tr>
					<th><?php echo $lang['Admin note'] ?></th>
					<td><input type="text" class="form-control" name="form[admin_note]" maxlength="30" tabindex="14" /></td>
					<th><?php echo $lang['User group'] ?></th>
					<td>
						<select class="form-control" name="user_group" tabindex="23">
							<option value="-1" selected="selected"><?php echo $lang['All groups'] ?></option>
							<option value="0"><?php echo $lang['Unverified users'] ?></option>
<?php

	$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id!='.FORUM_GUEST.' ORDER BY g_title') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

	while ($cur_group = $db->fetch_assoc($result))
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";

?>
						</select>
					</td>
				</tr>
				<tr>
					<th><?php echo $lang['Posts less than label'] ?></th>
					<td><input type="text" class="form-control" name="posts_less" maxlength="8" tabindex="16" /></td>
					<th><?php echo $lang['Posts more than label'] ?></th>
					<td><input type="text" class="form-control" name="posts_greater" maxlength="8" tabindex="15" /></td>
				</tr>
				<tr>
					<th><?php echo $lang['Last post before label'] ?></th>
					<td><input type="text" class="form-control" name="last_post_before" placeholder="<?php echo $lang['Date help'] ?>" maxlength="19" tabindex="18" /></td>
					<th><?php echo $lang['Last post after label'] ?></th>
					<td><input type="text" class="form-control" name="last_post_after" placeholder="<?php echo $lang['Date help'] ?>" maxlength="19" tabindex="17" /></td>
				</tr>
				<tr>
					<th><?php echo $lang['Last visit before label'] ?></th>
					<td><input type="text" class="form-control" name="last_visit_before" placeholder="<?php echo $lang['Date help'] ?>" maxlength="19" tabindex="18" /></td>
					<th><?php echo $lang['Last visit after label'] ?></th>
					<td><input type="text" class="form-control" name="last_visit_after" placeholder="<?php echo $lang['Date help'] ?>" maxlength="19" tabindex="17" /></td>
				</tr>
				<tr>
					<th><?php echo $lang['Registered before label'] ?></th>
					<td><input type="text" class="form-control" name="registered_before" placeholder="<?php echo $lang['Date help'] ?>" maxlength="19" tabindex="20" /></td>
					<th><?php echo $lang['Registered after label'] ?></th>
					<td><input type="text" class="form-control" name="registered_after" placeholder="<?php echo $lang['Date help'] ?>" maxlength="19" tabindex="19" /></td>
				</tr>
				<tr>
					<th><?php echo $lang['Order by label'] ?></th>
					<td colspan="3">
						<select class="form-control" name="order_by" tabindex="21">
							<option value="username" selected="selected"><?php echo $lang['Username'] ?></option>
							<option value="email"><?php echo $lang['Email'] ?></option>
							<option value="num_posts"><?php echo $lang['Order by posts'] ?></option>
							<option value="last_post"><?php echo $lang['Last post'] ?></option>
							<option value="last_visit"><?php echo $lang['Order by last visit'] ?></option>
							<option value="registered"><?php echo $lang['Order by registered'] ?></option>
						</select>&#160;&#160;&#160;<select class="form-control" name="direction" tabindex="22">
							<option value="ASC" selected="selected"><?php echo $lang['Ascending'] ?></option>
							<option value="DESC"><?php echo $lang['Descending'] ?></option>
						</select>
					</td>
				</tr>
			</table>
		</fieldset>
    </div>
</form>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['IP search head'] ?></h3>
    </div>
    <div class="panel-body">
        <form method="get" action="users.php">
            <fieldset>
                <div class="input-group">
					<input type="text" class="form-control" name="show_users" maxlength="15" tabindex="24" />
					<span class="input-group-btn">
						<input class="btn btn-primary" type="submit" value="<?php echo $lang['Find IP address'] ?>" tabindex="26" />
					</span>
				</div>
                <span class="help-block"><?php echo $lang['IP address help'] ?></span>
            </fieldset>
        </form>
    </div>
</div>
<form class="form-horizontal" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Add user head'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="add_user" value="<?php echo $lang['Submit'] ?>" tabindex="4" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Username'] ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="username" tabindex="3" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Email'] ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="email" tabindex="3" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Password'] ?></label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" name="password" tabindex="3" />
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="random_pass" value="1" />
                                <?php echo $lang['Random password info'] ?>
                            </label>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
<?php

	require FORUM_ROOT.'backstage/footer.php';
}
