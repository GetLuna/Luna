<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

// Make sure we have a usable language pack for admin.
if (file_exists(FORUM_ROOT.'lang/'.$pun_user['language'].'/admin_common.php'))
	$admin_language = $pun_user['language'];
else if (file_exists(FORUM_ROOT.'lang/'.$pun_config['o_default_lang'].'/admin_common.php'))
	$admin_language = $pun_config['o_default_lang'];
else
	$admin_language = 'English';

//
// Display the admin navigation menu
//
function generate_admin_menu($page = '')
{
	global $pun_config, $pun_user, $lang_back;

	$is_admin = $pun_user['g_id'] == FORUM_ADMIN ? true : false;

?>
<div class="navbar navbar-fixed-top">
    <div class="nav-inner">
        <a class="navbar-brand" href="../index.php">ModernBB</a>
        <ul class="nav navbar-nav">
            <li class="<?php if ($page == 'index' || $page == 'about' || $page == 'stats') echo 'active'; ?>"><a href="index.php"><?php echo $lang_back['Dashboard'] ?></a></li>
            <li class="dropdown <?php if ($page == 'forums' || $page == 'categories' || $page == 'censoring' || $page == 'reports') echo 'active'; ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php echo $lang_back['Content'] ?> <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <?php if ($is_admin) { ?><li><a href="forums.php"><?php echo $lang_back['Forums'] ?></a></li><?php }; ?>
                    <?php if ($is_admin) { ?><li><a href="categories.php"><?php echo $lang_back['Categories'] ?></a></li><?php }; ?>
                    <?php if ($is_admin) { ?><li><a href="censoring.php"><?php echo $lang_back['Censoring'] ?></a></li><?php }; ?>
                    <li><a href="reports.php"><?php echo $lang_back['Reports'] ?></a></li>
                </ul>
            </li>
            <li class="dropdown <?php if ($page == 'users' || $page == 'ranks' || $page == 'groups' || $page == 'permissions' || $page == 'bans') echo 'active'; ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php echo $lang_back['Users'] ?> <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="users.php"><?php echo $lang_back['Users'] ?></a></li>
                    <?php if ($is_admin) { ?><li><a href="ranks.php"><?php echo $lang_back['Ranks'] ?></a></li><?php }; ?>
                    <?php if ($is_admin) { ?><li><a href="groups.php"><?php echo $lang_back['Groups'] ?></a></li><?php }; ?>
                    <?php if ($is_admin) { ?><li><a href="permissions.php"><?php echo $lang_back['Permissions'] ?></a></li><?php }; ?>
                    <li><a href="bans.php"><?php echo $lang_back['Bans'] ?></a></li>
                </ul>
            </li>
            <?php if ($is_admin) { ?><li class="dropdown <?php if ($page == 'global' || $page == 'display' || $page == 'features' || $page == 'email' || $page == 'maintenance') echo 'active'; ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php echo $lang_back['Settings'] ?> <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="options.php"><?php echo $lang_back['Global'] ?></a></li>
                    <li><a href="display.php"><?php echo $lang_back['Display'] ?></a></li>
                    <li><a href="features.php"><?php echo $lang_back['Features'] ?></a></li>
                    <li><a href="email.php"><?php echo $lang_back['Email'] ?></a></li>
                    <li><a href="maintenance.php"><?php echo $lang_back['Maintenance'] ?></a></li>
                    <li><a href="database.php"><?php echo $lang_back['Database'] ?></a></li>
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
                    <?php echo $lang_back['Extensions'] ?> <b class="caret"></b>
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
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php echo $lang_back['Welcome'] ?>, <?php print(pun_htmlspecialchars($pun_user['username'])) ?> <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><?php echo '<a href="../profile.php?id='.$pun_user['id'].'">' ?><?php echo $lang_back['Profile'] ?></a></li>
                    <li><a href="../index.php"><?php echo $lang_back['Forum'] ?></a></li>
                    <li class="divider"></li>
                    <li><a href="http://modernbb.be"><?php echo $lang_back['Support'] ?></a></li>
                    <li><a href="about.php"><?php echo $lang_back['About'] ?></a></li>
                    <li class="divider"></li>
                    <li><?php echo '<a href="../login.php?action=out&amp;id='.$pun_user['id'].'&amp;csrf_token='.pun_hash($pun_user['id'].pun_hash(get_remote_address())).'">' ?><?php echo $lang_back['Logout'] ?></a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>

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
