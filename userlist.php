<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';


if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view'], false, '403 Forbidden');
else if ($pun_user['g_view_users'] == '0')
	message($lang_common['No permission'], false, '403 Forbidden');

// Load the frontend.php language file
require FORUM_ROOT.'lang/'.$pun_user['language'].'/frontend.php';


// Determine if we are allowed to view post counts
$show_post_count = ($pun_config['o_show_post_count'] == '1' || $pun_user['is_admmod']) ? true : false;

$username = isset($_GET['username']) && $pun_user['g_search_users'] == '1' ? pun_trim($_GET['username']) : '';
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

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_common['User list']);
if ($pun_user['g_search_users'] == '1')
	$focus_element = array('userlist', 'username');

// Generate paging links
$paging_links = '<span class="pages-label">'.$lang_common['Pages'].' </span>'.paginate($num_pages, $p, 'userlist.php?username='.urlencode($username).'&amp;show_group='.$show_group.'&amp;sort_by='.$sort_by.'&amp;sort_dir='.$sort_dir);


define('FORUM_ALLOW_INDEX', 1);
define('FORUM_ACTIVE_PAGE', 'userlist');
require FORUM_ROOT.'header.php';

?>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_search['User search'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="userlist" method="get" action="userlist.php">
            <fieldset>
            	<table>
                	<thead>
                    	<tr>
                        	<?php if ($pun_user['g_search_users'] == '1'): ?>
                                <th>
                                    <?php echo $lang_common['Username'] ?>
                                </th>
                            <?php endif; ?>
                            <th>
								<?php echo $lang_front['User group']."\n" ?>
                    		</th>
                            <th>
                    			<?php echo $lang_search['Sort by']."\n" ?>
                    		</th>
                            <th>
                    			<?php echo $lang_search['Sort order']."\n" ?>
                    		</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
							<?php if ($pun_user['g_search_users'] == '1'): ?>
                                <td><input class="form-control" type="text" name="username" value="<?php echo pun_htmlspecialchars($username) ?>" size="25" maxlength="25" /></td>
                            <?php endif; ?>
                        	<td>
                            	<select class="form-control" name="show_group">
                                    <option value="-1"<?php if ($show_group == -1) echo ' selected="selected"' ?>><?php echo $lang_front['All users'] ?></option>
<?php

$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id!='.FORUM_GUEST.' ORDER BY g_id') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

while ($cur_group = $db->fetch_assoc($result))
{
	if ($cur_group['g_id'] == $show_group)
		echo "\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.pun_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
	else
		echo "\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.pun_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
}

?>
                                </select>
                            </td>
                        	<td>
                            	<select class="form-control" name="sort_by">
                                    <option value="username"<?php if ($sort_by == 'username') echo ' selected="selected"' ?>><?php echo $lang_common['Username'] ?></option>
                                    <option value="registered"<?php if ($sort_by == 'registered') echo ' selected="selected"' ?>><?php echo $lang_common['Registered'] ?></option>
									<?php if ($show_post_count): ?>
                                        <option value="num_posts"<?php if ($sort_by == 'num_posts') echo ' selected="selected"' ?>><?php echo $lang_front['No of posts'] ?></option>
									<?php endif; ?>
								</select>
                            </td>
                        	<td>
                            	<select class="form-control" name="sort_dir">
                                    <option value="ASC"<?php if ($sort_dir == 'ASC') echo ' selected="selected"' ?>><?php echo $lang_search['Ascending'] ?></option>
                                    <option value="DESC"<?php if ($sort_dir == 'DESC') echo ' selected="selected"' ?>><?php echo $lang_search['Descending'] ?></option>
                                </select>
                    		</td>
                            <td>
                            	<input class="btn btn-primary" type="submit" name="search" value="<?php echo $lang_common['Submit'] ?>" accesskey="s" />
                            </td>
                        </tr>
                     </tbody>
                 </table>
                <p class="help-block"><?php echo ($pun_user['g_search_users'] == '1' ? $lang_front['User search info'].' ' : '').$lang_front['User sort info']; ?></p>
            </fieldset>
        </form>
    </div>
</div>


<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_common['User list'] ?></h3>
    </div>
    <div class="panel-body">
    <p class="pagelink"><?php echo $paging_links ?></p>
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo $lang_common['Username'] ?></th>
                    <th><?php echo $lang_common['Title'] ?></th>
<?php if ($show_post_count): ?>                <th><?php echo $lang_common['Posts'] ?></th>
<?php endif; ?>                <th><?php echo $lang_common['Registered'] ?></th>
                </tr>
            </thead>
            <tbody>
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

?>
                <tr>
                    <td><?php echo '<a href="profile.php?id='.$user_data['id'].'">'.pun_htmlspecialchars($user_data['username']).'</a>' ?></td>
                    <td><?php echo $user_title_field ?></td>
<?php if ($show_post_count): ?>                <td><?php echo forum_number_format($user_data['num_posts']) ?></td>
<?php endif; ?>
                    <td><?php echo format_time($user_data['registered'], true) ?></td>
                </tr>
<?php

	}
}
else
	echo "\t\t\t".'<tr>'."\n\t\t\t\t\t".'<td class="tcl" colspan="'.(($show_post_count) ? 4 : 3).'">'.$lang_search['No hits'].'</td></tr>'."\n";

?>
            </tbody>
        </table>
    </div>
</div>
<?php

require FORUM_ROOT.'footer.php';
