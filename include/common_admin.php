<?php

/**
 * Copyright (C) 2013-2014 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://opensource.org/licenses/MIT MIT
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

// Make sure we have a usable language pack for admin.
if (file_exists(FORUM_ROOT.'lang/'.$luna_user['language'].'/language.php'))
	$admin_language = $luna_user['language'];
else if (file_exists(FORUM_ROOT.'lang/'.$luna_config['o_default_lang'].'/language.php'))
	$admin_language = $luna_config['o_default_lang'];
else
	$admin_language = 'English';

//
// Display the admin navigation menu
//
function generate_admin_menu($page = '')
{
	global $luna_config, $luna_user, $lang;

	$is_admin = $luna_user['g_id'] == FORUM_ADMIN ? true : false;

?>
<nav class="navbar navbar-fixed-top navbar-default" role="navigation">
    <div class="nav-inner container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand visible-xs" href="../index.php">ModernBB</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
            	<li class="hidden-xs"><a href="../"><span class="glyphicon glyphicon-chevron-left"></span></a></li>
                <li class="<?php if ($page == 'index' || $page == 'update' || $page == 'stats') echo 'active'; ?>"><a href="index.php"><span class="glyphicon glyphicon-dashboard"></span> <?php echo $lang['Backstage'] ?></a></li>
                <li class="dropdown <?php if ($page == 'censoring' || $page == 'reports' || $page == 'board') echo 'active'; ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-file"></span> <?php echo $lang['Content'] ?> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if ($is_admin) { ?><li><a href="board.php"><?php echo $lang['Board structure'] ?></a></li>
                        <li class="divider"></li>
                        <li><a href="censoring.php"><?php echo $lang['Censoring'] ?></a></li><?php }; ?>
                        <li><a href="reports.php"><?php echo $lang['Reports'] ?></a></li>
                    </ul>
                </li>
                <li class="dropdown <?php if ($page == 'users' || $page == 'ranks' || $page == 'groups' || $page == 'permissions' || $page == 'bans') echo 'active'; ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-user"></span> <?php echo $lang['Users'] ?> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="users.php"><?php echo $lang['Users'] ?></a></li>
                        <?php if ($is_admin) { ?><li><a href="ranks.php"><?php echo $lang['Ranks'] ?></a></li>
                        <li><a href="groups.php"><?php echo $lang['Groups'] ?></a></li><?php }; ?>
                        <?php if (($luna_user['g_mod_ban_users'] == '1') || ($is_admin)) { ?><li class="divider"></li>
                        <?php if ($is_admin) { ?><li><a href="permissions.php"><?php echo $lang['Permissions'] ?></a></li><?php }; ?>
                        <li><a href="bans.php"><?php echo $lang['Bans'] ?></a></li><?php }; ?>
                    </ul>
                </li>
                <?php if ($is_admin) { ?><li class="dropdown <?php if ($page == 'global' || $page == 'display' || $page == 'features' || $page == 'registration' || $page == 'email' || $page == 'style' || $page == 'backstage' || $page == 'database' || $page == 'maintenance') echo 'active'; ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-cog"></span> <?php echo $lang['Settings'] ?> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="settings.php"><?php echo $lang['Global'] ?></a></li>
                        <li><a href="features.php"><?php echo $lang['Features'] ?></a></li>
                        <li><a href="registration.php"><?php echo $lang['Registration'] ?></a></li>
                        <li><a href="email.php"><?php echo $lang['Email'] ?></a></li>
                        <li class="divider"></li>
                        <li><a href="appearance.php"><?php echo $lang['Appearance'] ?></a></li>
                        <li><a href="style.php"><?php echo $lang['Style'] ?></a></li>
                        <li class="divider"></li>
                        <li><a href="maintenance.php"><?php echo $lang['Maintenance'] ?></a></li>
                        <li><a href="database.php"><?php echo $lang['Database'] ?></a></li>
                        <li class="divider"></li>
                        <li><a href="backstage.php"><?php echo $lang['Backstage settings'] ?></a></li>
                    </ul>
                </li><?php }; ?>
<?php

	// See if there are any plugins
	$plugins = forum_list_plugins($is_admin);

	// Did we find any plugins?
	if (!empty($plugins))
	{
?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-flash"></span> <?php echo $lang['Extensions'] ?> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
<?php
		foreach ($plugins as $plugin_name => $plugin)
			echo "\t\t\t\t\t".'<li class="'.(($page == $plugin_name) ? 'active' : '').'"><a href="loader.php?plugin='.$plugin_name.'">'.str_replace('_', ' ', $plugin).'</a></li>'."\n";
?>
                    </ul>
                </li>
<?php } ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown usermenu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php print(luna_htmlspecialchars($luna_user['username'])) ?> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo '<a href="../profile.php?id='.$luna_user['id'].'">' ?><?php echo $lang['Profile'] ?></a></li>
                        <li class="divider"></li>
                        <li><a href="http://modernbb.be"><?php echo $lang['Support'] ?></a></li>
                        <li class="divider"></li>
                        <li><?php echo '<a href="../login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_hash($luna_user['id'].luna_hash(get_remote_address())).'">' ?><?php echo $lang['Logout'] ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php

}


//
// Delete topics from $forum_id that are "older than" $prune_date (if $prune_sticky is 1, sticky topics will also be deleted)
//
function prune($forum_id, $prune_sticky, $prune_date)
{
	global $db;

	$extra_sql = ($prune_date != -1) ? ' AND last_post<'.$prune_date : '';

	if (!$prune_sticky)
		$extra_sql .= ' AND sticky=\'0\'';

	// Fetch topics to prune
	$result = $db->query('SELECT id FROM '.$db->prefix.'topics WHERE forum_id='.$forum_id.$extra_sql, true) or error('Unable to fetch topics', __FILE__, __LINE__, $db->error());

	$topic_ids = '';
	while ($row = $db->fetch_row($result))
		$topic_ids .= (($topic_ids != '') ? ',' : '').$row[0];

	if ($topic_ids != '')
	{
		// Fetch posts to prune
		$result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE topic_id IN('.$topic_ids.')', true) or error('Unable to fetch posts', __FILE__, __LINE__, $db->error());

		$post_ids = '';
		while ($row = $db->fetch_row($result))
			$post_ids .= (($post_ids != '') ? ',' : '').$row[0];

		if ($post_ids != '')
		{
			// Delete topics
			$db->query('DELETE FROM '.$db->prefix.'topics WHERE id IN('.$topic_ids.')') or error('Unable to prune topics', __FILE__, __LINE__, $db->error());
			// Delete subscriptions
			$db->query('DELETE FROM '.$db->prefix.'topic_subscriptions WHERE topic_id IN('.$topic_ids.')') or error('Unable to prune subscriptions', __FILE__, __LINE__, $db->error());
			// Delete posts
			$db->query('DELETE FROM '.$db->prefix.'posts WHERE id IN('.$post_ids.')') or error('Unable to prune posts', __FILE__, __LINE__, $db->error());

			// We removed a bunch of posts, so now we have to update the search index
			require_once FORUM_ROOT.'include/search_idx.php';
			strip_search_index($post_ids);
		}
	}
}
