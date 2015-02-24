<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
	header("Location: ../login.php");

$action = isset($_GET['action']) ? $_GET['action'] : null;

if ($action == 'check_update') {
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

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

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Update']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('backstage', 'update');
	
	?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $lang['Luna updates'] ?><span class="pull-right"><a href="update.php?action=check_update" class="btn btn-primary"><span class="fa fa-fw fa-refresh"></span> <?php echo $lang['Check for updates'] ?></a></span></h3>
	</div>
	<div class="panel-body">
<?php
	if (version_compare(Version::FORUM_CORE_VERSION, $update_cache, 'lt')) {
?>
		<h3>A new version is available!</h3>
		<p>A new version, Luna <?php echo $update_cache ?> has been released. It's a good idea to update to the latest version of Luna, as it contains not only new features, improvements and bugfixes, but also the latest security updates.</p>
        <div class="btn-group">
            <a href="http://modernbb.be/cnt/get.php?id=1" class="btn btn-primary"><?php echo sprintf($lang['Download'], $update_cache) ?></a>
            <a href="http://modernbb.be/changelog.php" class="btn btn-primary"><?php echo $lang['Changelog'] ?></a>
        </div>
<?php
	} elseif (version_compare(Version::FORUM_CORE_VERSION, $update_cache, 'eq')) {
?>
		<h3>You're using the latest version of Luna!</h3>
		<p>You're on our latest release! Nothing to worry about.</p>
<?php
	} else {
?>
		<h3>You're using a development version of Luna. Be sure to stay up-to-date.</h3>
		<p>We release every now and then a new build for Luna, one more stable then the other, for you to check out. You can keep track of this at <a href="http://getluna.org/lunareleases.php">our website</a>. New builds can contain new features, improved features, and/or bugfixes.</p>
		<p>At this point, we can only tell you that a new you're beyond the latest release. We can't tell you if there is a new preview available. You'll have to find out for yourself.</p>
<?php
	}
?>
	</div>
</div>
<?php

require 'footer.php';
