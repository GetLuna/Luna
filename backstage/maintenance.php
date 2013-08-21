<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);
// Tell common.php that we don't want output buffering
define('FORUM_DISABLE_BUFFERING', 1);

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$pun_user['is_admmod']) {
    header("Location: login.php");
}

// Load the backstage.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/backstage.php';

$action = isset($_REQUEST['action']) ? pun_trim($_REQUEST['action']) : '';

if ($action == 'rebuild')
{
	$per_page = isset($_GET['i_per_page']) ? intval($_GET['i_per_page']) : 0;
	$start_at = isset($_GET['i_start_at']) ? intval($_GET['i_start_at']) : 0;

	// Check per page is > 0
	if ($per_page < 1)
		message($lang_back['Posts must be integer message']);

	@set_time_limit(0);

	// If this is the first cycle of posts we empty the search index before we proceed
	if (isset($_GET['i_empty_index']))
	{
		$db->truncate_table('search_matches') or error('Unable to empty search index match table', __FILE__, __LINE__, $db->error());
		$db->truncate_table('search_words') or error('Unable to empty search index words table', __FILE__, __LINE__, $db->error());

		// Reset the sequence for the search words (not needed for SQLite)
		switch ($db_type)
		{
			case 'mysql':
			case 'mysqli':
			case 'mysql_innodb':
			case 'mysqli_innodb':
				$result = $db->query('ALTER TABLE '.$db->prefix.'search_words auto_increment=1') or error('Unable to update table auto_increment', __FILE__, __LINE__, $db->error());
				break;

			case 'pgsql';
				$result = $db->query('SELECT setval(\''.$db->prefix.'search_words_id_seq\', 1, false)') or error('Unable to update sequence', __FILE__, __LINE__, $db->error());
		}
	}

	$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Rebuilding search index']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo generate_page_title($page_title) ?></title>
<style type="text/css">
body {
	font: 12px Verdana, Arial, Helvetica, sans-serif;
	color: #333333;
	background-color: #FFFFFF
}

h1 {
	font-size: 16px;
	font-weight: normal;
}
</style>
</head>
<body>

<h1><?php echo $lang_back['Rebuilding index info'] ?></h1>
<hr />

<?php

	$query_str = '';

	require FORUM_ROOT.'include/search_idx.php';

	// Fetch posts to process this cycle
	$result = $db->query('SELECT p.id, p.message, t.subject, t.first_post_id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id WHERE p.id >= '.$start_at.' ORDER BY p.id ASC LIMIT '.$per_page) or error('Unable to fetch posts', __FILE__, __LINE__, $db->error());

	$end_at = 0;
	while ($cur_item = $db->fetch_assoc($result))
	{
		echo '<p><span>'.sprintf($lang_back['Processing post'], $cur_item['id']).'</span></p>'."\n";

		if ($cur_item['id'] == $cur_item['first_post_id'])
			update_search_index('post', $cur_item['id'], $cur_item['message'], $cur_item['subject']);
		else
			update_search_index('post', $cur_item['id'], $cur_item['message']);

		$end_at = $cur_item['id'];
	}

	// Check if there is more work to do
	if ($end_at > 0)
	{
		$result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE id > '.$end_at.' ORDER BY id ASC LIMIT 1') or error('Unable to fetch next ID', __FILE__, __LINE__, $db->error());

		if ($db->num_rows($result) > 0)
			$query_str = '?action=rebuild&i_per_page='.$per_page.'&i_start_at='.$db->result($result);
	}

	$db->end_transaction();
	$db->close();

	exit('<script type="text/javascript">window.location="maintenance.php'.$query_str.'"</script><hr /><p>'.sprintf($lang_back['Javascript redirect failed'], '<a href="maintenance.php'.$query_str.'">'.$lang_back['Click here'].'</a>').'</p>');
}

if ($action == 'prune')
{
	$prune_from = pun_trim($_POST['prune_from']);
	$prune_sticky = intval($_POST['prune_sticky']);

	if (isset($_POST['prune_comply']))
	{
		$prune_days = intval($_POST['prune_days']);
		$prune_date = ($prune_days) ? time() - ($prune_days * 86400) : -1;

		@set_time_limit(0);

		if ($prune_from == 'all')
		{
			$result = $db->query('SELECT id FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());
			$num_forums = $db->num_rows($result);

			for ($i = 0; $i < $num_forums; ++$i)
			{
				$fid = $db->result($result, $i);

				prune($fid, $prune_sticky, $prune_date);
				update_forum($fid);
			}
		}
		else
		{
			$prune_from = intval($prune_from);
			prune($prune_from, $prune_sticky, $prune_date);
			update_forum($prune_from);
		}

		// Locate any "orphaned redirect topics" and delete them
		$result = $db->query('SELECT t1.id FROM '.$db->prefix.'topics AS t1 LEFT JOIN '.$db->prefix.'topics AS t2 ON t1.moved_to=t2.id WHERE t2.id IS NULL AND t1.moved_to IS NOT NULL') or error('Unable to fetch redirect topics', __FILE__, __LINE__, $db->error());
		$num_orphans = $db->num_rows($result);

		if ($num_orphans)
		{
			for ($i = 0; $i < $num_orphans; ++$i)
				$orphans[] = $db->result($result, $i);

			$db->query('DELETE FROM '.$db->prefix.'topics WHERE id IN('.implode(',', $orphans).')') or error('Unable to delete redirect topics', __FILE__, __LINE__, $db->error());
		}

		redirect('backstage/maintenance.php', $lang_back['Posts pruned redirect']);
	}

	$prune_days = pun_trim($_POST['req_prune_days']);
	if ($prune_days == '' || preg_match('%[^0-9]%', $prune_days))
		message($lang_back['Days must be integer message']);

	$prune_date = time() - ($prune_days * 86400);

	// Concatenate together the query for counting number of topics to prune
	$sql = 'SELECT COUNT(id) FROM '.$db->prefix.'topics WHERE last_post<'.$prune_date.' AND moved_to IS NULL';

	if ($prune_sticky == '0')
		$sql .= ' AND sticky=0';

	if ($prune_from != 'all')
	{
		$prune_from = intval($prune_from);
		$sql .= ' AND forum_id='.$prune_from;

		// Fetch the forum name (just for cosmetic reasons)
		$result = $db->query('SELECT forum_name FROM '.$db->prefix.'forums WHERE id='.$prune_from) or error('Unable to fetch forum name', __FILE__, __LINE__, $db->error());
		$forum = '"'.pun_htmlspecialchars($db->result($result)).'"';
	}
	else
		$forum = $lang_back['All forums'];

	$result = $db->query($sql) or error('Unable to fetch topic prune count', __FILE__, __LINE__, $db->error());
	$num_topics = $db->result($result);

	if (!$num_topics)
		message(sprintf($lang_back['No old topics message'], $prune_days));


	$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['Prune']);
	define('FORUM_ACTIVE_PAGE', 'admin');
	require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('maintenance');

?>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Prune head'] ?></h3>
    </div>
	<div class="panel-body">
        <form method="post" action="maintenance.php">
            <input type="hidden" name="action" value="prune" />
            <input type="hidden" name="prune_days" value="<?php echo $prune_days ?>" />
            <input type="hidden" name="prune_sticky" value="<?php echo $prune_sticky ?>" />
            <input type="hidden" name="prune_from" value="<?php echo $prune_from ?>" />
            <fieldset>
                <h3><?php echo $lang_back['Confirm prune subhead'] ?></h3>
                <p><?php printf($lang_back['Confirm prune info'], $prune_days, $forum, forum_number_format($num_topics)) ?></p>
                <p class="warntext"><?php echo $lang_back['Confirm prune warn'] ?></p>
            </fieldset>
            <div class="control-group"><input class="btn btn-primary" type="submit" name="prune_comply" value="<?php echo $lang_back['Prune'] ?>" />
            <a class="btn" href="javascript:history.go(-1)"><?php echo $lang_back['Go back'] ?></a></div>
        </form>
    </div>
</div>
<?php

	require FORUM_ROOT.'backstage/footer.php';
	exit;
}

if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
	require FORUM_ROOT.'include/cache.php';

if (isset($_POST['userprune']))
{
	// Make sure something something was entered
	if ((trim($_POST['days']) == '') || trim($_POST['posts']) == '')
		message('You need to set all settings!');
	if ($_POST['admods_delete']) {
		$admod_delete = 'group_id > 0';
	}
	else {
		$admod_delete = 'group_id > 3';
	}

	if ($_POST['verified'] == 1)
		$verified = '';
	elseif ($_POST['verified'] == 0)
		$verified = 'AND (group_id < 32000)';
	else
		$verified = 'AND (group_id = 32000)';

	$prune = ($_POST['prune_by'] == 1) ? 'registered' : 'last_visit';

	$user_time = time() - ($_POST['days'] * 86400);
	$result = $db->query('SELECT id FROM '.$db->prefix.'users WHERE (num_posts < '.intval($_POST['posts']).') AND ('.$prune.' < '.intval($user_time).') AND (id > 2) AND ('.$admod_delete.')'.$verified, true) or error('Unable to fetch users to prune', __FILE__, __LINE__, $db->error());
	
	$user_ids = array();
	while ($id = $db->result($result))
		$user_ids[] = $id;
	
	if (!empty($user_ids))
	{
		$db->query('DELETE FROM '.$db->prefix.'users WHERE id IN ('.implode(',', $user_ids).')') or error('Unable to delete users', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'posts SET poster_id=1 WHERE poster_id IN ('.implode(',', $user_ids).')') or error('Unable to mark posts as guest posts', __FILE__, __LINE__, $db->error());
	}
	
	// Regenerate the users info cache
	generate_users_info_cache();

	$users_pruned = count($user_ids);
	message('Pruning complete. Users pruned '.$users_pruned.'.');
}


// Get the first post ID from the db
$result = $db->query('SELECT id FROM '.$db->prefix.'posts ORDER BY id ASC LIMIT 1') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result))
	$first_id = $db->result($result);

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['Maintenance']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('maintenance');

?>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Rebuild index subhead'] ?></h3>
    </div>
	<div class="panel-body">
        <form method="get" action="maintenance.php">
            <input type="hidden" name="action" value="rebuild" />
            <fieldset>
                <p><?php printf($lang_back['Rebuild index info'], '<a href="options.php#maintenance">'.$lang_back['Maintenance mode'].'</a>') ?></p>
                <table class="table" cellspacing="0">
                    <tr>
                        <th width="16%"><?php echo $lang_back['Posts per cycle label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="i_per_page" size="7" maxlength="7" value="300" tabindex="1" />
                            <br /><span class="help-block"><?php echo $lang_back['Posts per cycle help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Starting post label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="i_start_at" size="7" maxlength="7" value="<?php echo (isset($first_id)) ? $first_id : 0 ?>" tabindex="2" />
                            <br /><span class="help-block"><?php echo $lang_back['Starting post help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Empty index label'] ?></th>
                        <td class="inputadmin">
                            <label><input type="checkbox" name="i_empty_index" value="1" tabindex="3" checked="checked" />&#160;&#160;<?php echo $lang_back['Empty index help'] ?></label>
                        </td>
                    </tr>
                </table>
                <p class="topspace"><?php echo $lang_back['Rebuild completed info'] ?></p>
                <div class="control-group"><input class="btn btn-primary" type="submit" name="rebuild_index" value="<?php echo $lang_back['Rebuild index'] ?>" tabindex="4" /></div>
            </fieldset>
        </form>
    </div>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Prune subhead'] ?></h3>
    </div>
	<div class="panel-body">
        <form method="post" action="maintenance.php" onsubmit="return process_form(this)">
            <input type="hidden" name="action" value="prune" />
            <fieldset>
                <p class="topspace"><?php printf($lang_back['Prune info'], '<a href="options.php#maintenance">'.$lang_back['Maintenance mode'].'</a>') ?></p>
                <table class="table" cellspacing="0">
                    <tr>
                        <th width="16%"><?php echo $lang_back['Days old label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="req_prune_days" size="3" maxlength="3" tabindex="5" />
                            <br /><span class="help-block"><?php echo $lang_back['Days old help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Prune sticky label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="prune_sticky" value="1" tabindex="6" checked="checked" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="prune_sticky" value="0" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="help-block"><?php echo $lang_back['Prune sticky help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Prune from label'] ?></th>
                        <td>
                            <select class="form-control" name="prune_from" tabindex="7">
                                <option value="all"><?php echo $lang_back['All forums'] ?></option>
<?php

	$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id WHERE f.redirect_url IS NULL ORDER BY c.disp_position, c.id, f.disp_position') or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

	$cur_category = 0;
	while ($forum = $db->fetch_assoc($result))
	{
		if ($forum['cid'] != $cur_category) // Are we still in the same category?
		{
			if ($cur_category)
				echo "\t\t\t\t\t\t\t\t\t\t\t".'</optgroup>'."\n";

			echo "\t\t\t\t\t\t\t\t\t\t\t".'<optgroup label="'.pun_htmlspecialchars($forum['cat_name']).'">'."\n";
			$cur_category = $forum['cid'];
		}

		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$forum['fid'].'">'.pun_htmlspecialchars($forum['forum_name']).'</option>'."\n";
	}

?>
                                </optgroup>
                            </select>
                            <span class="help-block"><?php echo $lang_back['Prune from help'] ?></span>
                        </td>
                    </tr>
                </table>
                <div class="control-group"><input class="btn btn-primary" type="submit" name="prune" value="<?php echo $lang_back['Prune'] ?>" tabindex="8" /></div>
            </fieldset>
        </form>
    </div>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Prune users</h3>
    </div>
	<div class="panel-body">
        <form id="userprune" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
            <fieldset>
                <p class="topspace"><?php printf($lang_back['Prune users info'], '<a href="options.php#maintenance">'.$lang_back['Maintenance mode'].'</a>') ?></p>
                <table class="table" cellspacing="0">
                    <tr>
                        <th class="span3">Prune by</th>
                        <td>
                            <input type="radio" name="prune_by" value="1" checked="checked" />&nbsp;<strong>Registed date</strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="prune_by" value="0" />&nbsp;<strong>Last Login</strong>
                            <span class="help-block">This decides if the minimum number of days is calculated since the last login or the registered date.</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Minimum days since registration/last login</th>
                        <td>
                            <input type="text" class="form-control" name="days" value="28" size="25" tabindex="1" />
                            <br /><span class="help-block">The minimum number of days before users are pruned by the setting specified above.</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Maximum number of posts</th>
                        <td>
                            <input type="text" class="form-control" name="posts" value="1"  size="25" tabindex="1" />
                            <br /><span class="help-block">Users with a postcount equal of higher than this won't be pruned. E.g. a value of 1 will remove users with no posts.</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Delete admins and mods?</th>
                        <td>
                            <input type="radio" name="admods_delete" value="1" />&nbsp;<strong>Yes</strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="admods_delete" value="0" checked="checked" />&nbsp;<strong>No</strong>
                            <span class="help-block">If Yes, any affected Moderators and Admins will also be pruned.</span>
                        </td>
                    </tr>
                    <tr>
                        <th>User status</th>
                        <td>
                            <input type="radio" name="verified" value="1" />&nbsp;<strong>Delete any</strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="verified" value="0" checked="checked" />&nbsp;<strong>Delete only verified</strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="verified" value="2" />&nbsp;<strong>Delete only unverified</strong>
                            <span class="help-block">Decides if (un)verified users should be deleted.</span>
                        </td>
                    </tr>
                </table>
                <p class="control-group"><input class="btn btn-primary" type="submit" name="userprune" value="<?php echo $lang_back['Prune'] ?>" tabindex="2" /></p>
            </fieldset>
        </form>
    </div>
</div>
<?php

require FORUM_ROOT.'backstage/footer.php';
