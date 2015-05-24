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
	header("Location: login.php");

require(FORUM_ROOT.'include/class/luna_update.php');

$update = new AutoUpdate(true);
$update->currentVersion = Version::LUNA_CORE_REVISION; //Must be an integer - you can't compare strings
$update->updateUrl = 'https://raw.githubusercontent.com/GetLuna/UpdateService/master'; //Replace with your server update directory

$action = isset($_GET['action']) ? $_GET['action'] : null;

if (file_exists(FORUM_CACHE_DIR.'cache_update.php'))
	include FORUM_CACHE_DIR.'cache_update.php';
	
if ((!defined('FORUM_UPDATE_LOADED') || ($last_check_time > time() + (60 * 60 * 24)))) {
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_update_cache();
	require FORUM_CACHE_DIR.'cache_update.php';
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('Update', 'luna'));
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('backstage', 'update');
?>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php _e('Luna one-click update', 'luna') ?></h3>
			</div>
			<div class="panel-body">
<?php
$latest = $update->checkUpdate();
if ($latest !== false) {
	if ($latest > $update->currentVersion) {
		//Install new update
		printf(__('We found version %s', 'luna'), $update->latestVersionName).'<br />';
		echo _e('Updating Luna...', 'luna').'<br />';
		if ($update->update())
			echo _e('Update successful!', 'luna');
		else
			echo _e('Update failed!', 'luna');
	} else
		echo _e('You\'re using the latest version of Luna!', 'luna');
} else
	echo $update->getLastError();
?>
			</div>
		</div>
	</div>
</div>
<?php

require 'footer.php';
