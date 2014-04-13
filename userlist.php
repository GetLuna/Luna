<?php

/**
 * Copyright (C) 2013-2014 ModernBB Group
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License under GPLv3
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';


if ($luna_user['g_read_board'] == '0')
	message($lang['No view'], false, '403 Forbidden');
else if ($luna_user['g_view_users'] == '0')
	message($lang['No permission'], false, '403 Forbidden');

// Determine if we are allowed to view post counts
$show_post_count = ($luna_config['o_show_post_count'] == '1' || $luna_user['is_admmod']) ? true : false;

$username = isset($_GET['username']) && $luna_user['g_search_users'] == '1' ? luna_trim($_GET['username']) : '';
$show_group = isset($_GET['show_group']) ? intval($_GET['show_group']) : -1;
$sort_by = isset($_GET['sort_by']) && (in_array($_GET['sort_by'], array('username', 'registered')) || ($_GET['sort_by'] == 'num_posts' && $show_post_count)) ? $_GET['sort_by'] : 'username';
$sort_dir = isset($_GET['sort_dir']) && $_GET['sort_dir'] == 'DESC' ? 'DESC' : 'ASC';

// Create any SQL for the WHERE clause
$where_sql = array();
$like_command = ($db_type == 'pgsql') ? 'ILIKE' : 'LIKE';

if ($username != '')
	$where_sql[] = 'u.username '.$like_command.' \''.$db->escape(str_replace('*', '%', $username)).'\'';
if ($show_group > -1)
	$where_sql[] = 'u.group_id='.$show_group;

// Fetch user count
$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'users AS u WHERE u.id>1 AND u.group_id!='.FORUM_UNVERIFIED.(!empty($where_sql) ? ' AND '.implode(' AND ', $where_sql) : '')) or error('Unable to fetch user list count', __FILE__, __LINE__, $db->error());
$num_users = $db->result($result);

// Determine the user offset (based on $_GET['p'])
$num_pages = ceil($num_users / 50);

$p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : intval($_GET['p']);
$start_from = 50 * ($p - 1);

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['User list']);
if ($luna_user['g_search_users'] == '1')
	$focus_element = array('userlist', 'username');

// Generate paging links
$paging_links = paginate($num_pages, $p, 'userlist.php?username='.urlencode($username).'&amp;show_group='.$show_group.'&amp;sort_by='.$sort_by.'&amp;sort_dir='.$sort_dir);


define('FORUM_ALLOW_INDEX', 1);
define('FORUM_ACTIVE_PAGE', 'userlist');
require FORUM_ROOT.'header.php';

?>
<h2><?php echo $lang['User list'] ?></h2>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['User list'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="userlist" class="usersearch" method="get" action="userlist.php">
            <fieldset>
            	<div class="row">
                	<div class="col-sm-5">
						<?php if ($luna_user['g_search_users'] == '1'): ?>
                            <input class="form-control" type="text" name="username" value="<?php echo luna_htmlspecialchars($username) ?>" placeholder="<?php echo $lang['Username'] ?>" maxlength="25" />
                        <?php endif; ?>
                	</div>
                	<div class="col-sm-2">
                        <select class="form-control" name="show_group">
                            <option value="-1"<?php if ($show_group == -1) echo ' selected="selected"' ?>><?php echo $lang['All users'] ?></option>
<?php

$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id!='.FORUM_GUEST.' ORDER BY g_id') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

while ($cur_group = $db->fetch_assoc($result))
{
	if ($cur_group['g_id'] == $show_group)
		echo "\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
	else
		echo "\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
}

?>
					</select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control" name="sort_by">
                        <option value="username"<?php if ($sort_by == 'username') echo ' selected="selected"' ?>><?php echo $lang['Username'] ?></option>
                        <option value="registered"<?php if ($sort_by == 'registered') echo ' selected="selected"' ?>><?php echo $lang['Registered table'] ?></option>
                        <?php if ($show_post_count): ?>
                            <option value="num_posts"<?php if ($sort_by == 'num_posts') echo ' selected="selected"' ?>><?php echo $lang['No of posts'] ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control" name="sort_dir">
                        <option value="ASC"<?php if ($sort_dir == 'ASC') echo ' selected="selected"' ?>><?php echo $lang['Ascending'] ?></option>
                        <option value="DESC"<?php if ($sort_dir == 'DESC') echo ' selected="selected"' ?>><?php echo $lang['Descending'] ?></option>
                    </select>
                </div>
                <div class="col-sm-1">
                    <input class="btn btn-primary" type="submit" name="search" value="<?php echo $lang['Submit'] ?>" accesskey="s" />
                </div>
            </fieldset>
        </form>
    </div>
</div>
<div class="row">
	<div class="col-sm-12">
		<ul class="pagination pagination-user-top">
			<?php echo $paging_links ?>
		</ul>
	</div>
</div>
<div class="col-xs-12">
    <div class="row forum-header">
        <div class="col-sm-8 col-xs-9"><?php echo $lang['Username'] ?></div>
        <div class="col-sm-1 align-center hidden-xs"><p class="text-center"><?php echo $lang['Posts table'] ?></p></div>
        <div class="col-sm-3 col-xs-3"><?php echo $lang['Registered table'] ?></div>
    </div>
    <div class="userlist">
<?php

// Retrieve a list of user IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
$result = $db->query('SELECT u.id FROM '.$db->prefix.'users AS u WHERE u.id>1 AND u.group_id!='.FORUM_UNVERIFIED.(!empty($where_sql) ? ' AND '.implode(' AND ', $where_sql) : '').' ORDER BY '.$sort_by.' '.$sort_dir.', u.id ASC LIMIT '.$start_from.', 50') or error('Unable to fetch user IDs', __FILE__, __LINE__, $db->error());

if ($db->num_rows($result))
{
	$user_ids = array();
	for ($i = 0;$cur_user_id = $db->result($result, $i);$i++)
		$user_ids[] = $cur_user_id;

	// Grab the users
	$result = $db->query('SELECT u.id, u.username, u.title, u.num_posts, u.registered, g.g_id, g.g_user_title FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id IN('.implode(',', $user_ids).') ORDER BY '.$sort_by.' '.$sort_dir.', u.id ASC') or error('Unable to fetch user list', __FILE__, __LINE__, $db->error());

	while ($user_data = $db->fetch_assoc($result))
	{
		$user_title_field = get_title($user_data);
		$user_avatar = generate_avatar_markup($user_data['id']);

?>
        <div class="row user-row">
            <div class="col-sm-8 col-xs-9">
                <span class="user-avatar thumbnail">
                    <?php echo $user_avatar; ?>
                </span>
                <span class="userlist-name"><?php echo '<a href="profile.php?id='.$user_data['id'].'">'.luna_htmlspecialchars($user_data['username']).'</a>' ?> <small><?php echo $user_title_field ?></small></span>
            </div>
            <div class="col-sm-1 collum-count align-center hidden-xs"><p class="text-center"><?php echo forum_number_format($user_data['num_posts']) ?></p></div>
            <div class="col-sm-3 col-xs-3 collum-count"><?php echo format_time($user_data['registered'], true) ?></div>
        </div>
<?php

	}
}
else
	echo "\t\t\t".'<tr>'."\n\t\t\t\t\t".'<td class="tcl" colspan="'.(($show_post_count) ? 4 : 3).'">'.$lang['No hits'].'</td></tr>'."\n";

?>
    </div>
</div>
<div class="row">
	<div class="col-sm-12">
		<ul class="pagination pagination-user-bottom">
			<?php echo $paging_links ?>
		</ul>
	</div>
</div>
<?php

require FORUM_ROOT.'footer.php';
