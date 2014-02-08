<?php

/**
 * Copyright (C) 2013-2014 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License under GPLv3
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$pun_user['is_admmod']) {
    header("Location: ../login.php");
}

$action = isset($_GET['action']) ? $_GET['action'] : null;

if ($action == 'update_check')
{
	// Regenerate the update cache		
	if (!defined('FORUM_UPDATE_LOADED'))
	{
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';
	
		generate_update_cache();
		require FORUM_CACHE_DIR.'cache_update.php';
	}
	header("Location: update.php");
}
elseif ($action == 'soft_reset')
{
	unlink(FORUM_ROOT.'config.php');
	header("Location: ../install.php?action=softreset");
}
elseif ($action == 'hard_reset')
{
	$db->drop_table('bans') or error('Unable to drop bans table', __FILE__, __LINE__, $db->error());
	$db->drop_table('categories') or error('Unable to drop categories table', __FILE__, __LINE__, $db->error());
	$db->drop_table('censoring') or error('Unable to drop censoring table', __FILE__, __LINE__, $db->error());
	$db->drop_table('config') or error('Unable to drop config table', __FILE__, __LINE__, $db->error());
	$db->drop_table('forums') or error('Unable to drop forums table', __FILE__, __LINE__, $db->error());
	$db->drop_table('forum_perms') or error('Unable to drop forum_perms table', __FILE__, __LINE__, $db->error());
	$db->drop_table('groups') or error('Unable to drop groups table', __FILE__, __LINE__, $db->error());
	$db->drop_table('online') or error('Unable to drop online table', __FILE__, __LINE__, $db->error());
	$db->drop_table('posts') or error('Unable to drop posts table', __FILE__, __LINE__, $db->error());
	$db->drop_table('posts') or error('Unable to drop posts table', __FILE__, __LINE__, $db->error());
	$db->drop_table('ranks') or error('Unable to drop ranks table', __FILE__, __LINE__, $db->error());
	$db->drop_table('reports') or error('Unable to drop reports table', __FILE__, __LINE__, $db->error());
	$db->drop_table('search_cache') or error('Unable to drop search_cache table', __FILE__, __LINE__, $db->error());
	$db->drop_table('search_matches') or error('Unable to drop search_matches table', __FILE__, __LINE__, $db->error());
	$db->drop_table('search_words') or error('Unable to drop search_words table', __FILE__, __LINE__, $db->error());
	$db->drop_table('topic_subscriptions') or error('Unable to drop topic_subscriptions table', __FILE__, __LINE__, $db->error());
	$db->drop_table('forum_subscriptions') or error('Unable to drop forum_subscriptions table', __FILE__, __LINE__, $db->error());
	$db->drop_table('topics') or error('Unable to drop topics table', __FILE__, __LINE__, $db->error());
	$db->drop_table('users') or error('Unable to drop users table', __FILE__, __LINE__, $db->error());
	
	unlink(FORUM_ROOT.'config.php');
	header("Location: ../install.php?action=hardreset");
}

if (file_exists(FORUM_CACHE_DIR.'cache_update.php'))
	include FORUM_CACHE_DIR.'cache_update.php';
	
if ((!defined('FORUM_UPDATE_LOADED') || ($last_check_time > time() + (60 * 60 * 24))))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_update_cache();
	require FORUM_CACHE_DIR.'cache_update.php';
}

$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang['Admin'], $lang['Update']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('update');
	
	?>
<h2>ModernBB software updates</h2>
<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title">ModernBB updates<span class="pull-right"><a href="update.php?action=check_update" class="btn btn-primary">Check for updates</a></span></h3>
    </div>
    <div class="panel-body">
<?php
	$latest_version = $update_cache;
	if (version_compare(FORUM_VERSION, $latest_version, 'lt')) {
?>
		<h3>It's time to update, a new version is available</h3>
        <p><?php echo sprintf($lang['Available'], $latest_version) ?></p>
        <div class="btn-group">
            <a href="http://modernbb.be/downloads/<?php echo $latest_version ?>.zip" class="btn btn-primary"><?php echo sprintf($lang['Download'], $latest_version) ?></a>
            <a href="http://modernbb.be/releases/modernbb<?php echo $latest_version ?>.php" class="btn btn-primary"><?php echo $lang['Changelog'] ?></a>
        </div>
        <a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>.zip" class="btn btn-default"><?php echo sprintf($lang['Download'], FORUM_VERSION) ?></a>
<?php } elseif (version_compare(FORUM_VERSION, $latest_version, 'eq')) { ?>
		<h3>Thanks for using the latest version of ModernBB</h3>
        <p><?php echo $lang['ModernBB intro'].' '.FORUM_VERSION ?></p>
        <div class="btn-group">
            <a href="http://modernbb.be/releases/modernbb<?php echo FORUM_VERSION ?>.php" class="btn btn-primary"><?php echo $lang['Changelog'] ?></a>
            <a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>.zip" class="btn btn-primary"><?php echo sprintf($lang['Download'], FORUM_VERSION) ?></a>
        </div>
<?php } else { ?>
        <h3>You're using a development release</h3>
        <p><?php echo sprintf($lang['Development'], FORUM_VERSION, $latest_version) ?></p>
        <div class="btn-group">
            <a href="http://modernbb.be/downloads/<?php echo $latest_version ?>.zip" class="btn btn-primary"><?php echo sprintf($lang['Download'], $latest_version) ?></a>
            <a href="http://modernbb.be/releases/modernbb<?php echo $latest_version ?>.php" class="btn btn-primary"><?php echo $lang['Changelog'] ?></a>
        </div>
        <div class="btn-group">
            <a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>.zip" class="btn btn-default"><?php echo sprintf($lang['Download'], FORUM_VERSION) ?></a>
            <a href="http://modernbb.be/releases/modernbb<?php echo FORUM_VERSION ?>.php" class="btn btn-default"><?php echo $lang['Changelog'] ?></a>
        </div>
<?php
	}
?>
    </div>
    <div class="panel-footer">
    	<p>ModernBB 3 is developed by the ModernBB Group. Copyright 2013-2014. Released under the MIT license. We would like to thank you for using ModernBB.</p>
    </div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title">ModernBB reset features</h3>
    </div>
    <div class="panel-body">
    	<h3>Soft reset</h3>
        <p>The button below will remove the config.php file, this will cause the install to start so you can install ModernBB again. This will not drop the current database. This might be effective if your config.php file is corrupt. This can't be undone. Be sure you made a back-up before doing this.</p>
        <a href="update.php?action=soft_reset" class="btn btn-danger">Reset config.php</a>
    	<h3><br />Hard reset</h3>
        <p>The button below will remove the config.php file and database, this will cause the install to start so you can install ModernBB again. You will lose all your data. This can't be undone. Be sure you made a back-up before doing this.</p>
        <a href="update.php?action=hard_reset" class="btn btn-danger">Reset</a>
    </div>
</div>
<?php

require FORUM_ROOT.'backstage/footer.php';
