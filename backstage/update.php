<?php

/*
 * Copyright (C) 2013-2014 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod']) {
    header("Location: ../login.php");
}

$action = isset($_GET['action']) ? $_GET['action'] : null;

if ($action == 'update_check') {
	// Regenerate the update cache		
	generate_update_cache();
	header("Location: update.php");
}

if (file_exists(FORUM_CACHE_DIR.'cache_update.php'))
	include FORUM_CACHE_DIR.'cache_update.php';
	
if ((!defined('FORUM_UPDATE_LOADED') || ($last_check_time > time() + (60 * 60 * 24)))) {
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_update_cache();
	require FORUM_CACHE_DIR.'cache_update.php';
}

$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Update']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('backstage', 'update');
	
	?>
<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title"><?php echo $lang['Luna updates'] ?><span class="pull-right"><a href="update.php?action=check_update" class="btn btn-primary"><?php echo $lang['Check for updates'] ?></a></span></h3>
    </div>
    <div class="panel-body">
<?php
	$latest_version = $update_cache;
	if (version_compare(Version::FORUM_VERSION, $latest_version, 'lt')) {
?>
		<h3><?php echo $lang['New version'] ?></h3>
        <p><?php echo sprintf($lang['Available'], $latest_version, $lang['update now']) ?></p>
        <div class="btn-group">
            <a href="http://modernbb.be/cnt/get.php?id=1.zip" class="btn btn-primary">Update now</a>
            <a href="http://modernbb.be/releases/luna1.0.php" class="btn btn-primary"><?php echo $lang['Changelog'] ?></a>
        </div>
<?php } elseif (version_compare(Version::FORUM_VERSION, $latest_version, 'eq')) { ?>
		<h3><?php echo $lang['Latest version'] ?></h3>
        <p><?php echo $lang['Luna intro'].' '.Version::FORUM_VERSION ?></p>
        <div class="btn-group">
            <a href="http://modernbb.be/releases/luna1.0.php" class="btn btn-primary"><?php echo $lang['Changelog'] ?></a>
        </div>
<?php
	} else {
?>
		<h3>You're using a development version of Luna. Be sure to stay up-to-date.</h3>
<?php } ?>
    </div>
    <div class="panel-footer">
    	<p>Luna is developed by the <a href="http://modernbb.be/luna.php">Luna Group</a>. Copyright 2013-2014. Released under the GPLv3 license.</p>
    </div>
</div>
<?php

require 'footer.php';
