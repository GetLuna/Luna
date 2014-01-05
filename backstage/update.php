<?php

/**
 * Copyright (C) 2013-2014 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://opensource.org/licenses/MIT MIT
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

if ($action == 'softreset')
{
	$config = FORUM_ROOT.'config.php';
	unlink(FORUM_ROOT.'config.php');
	header("Location: ../install.php?action=softreset");
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
    	<h3 class="panel-title">ModernBB updates</h3>
    </div>
    <div class="panel-body">
<?php
	$latest_version = trim(@file_get_contents('https://raw.github.com/ModernBB/ModernBB/master/version.txt'));
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
        <p>The button below will remove the config.php file, this will cause the install to start so you can install ModernBB again. This will not drop the current database. This might be effective if your config.php file is corrupt.</p>
        <a href="update.php?action=softreset" class="btn btn-danger">Reset config.php</a>
    	<h3><br />Hard reset</h3>
        <p>The button below will remove the config.php file and database, this will cause the install to start so you can install ModernBB again. You will lose all your data. A hard reset can't be undone. Be sure you made a back-up before doing this.</p>
        <a href="#" class="btn btn-danger">Reset</a>
    	<h3><br />Re-install</h3>
        <p>This button will download the most recent ModernBB package from the servers and launch the update screen if required. This will undo changes you made to the ModernBB core files.</p>
        <a href="#" class="btn btn-danger">Re-install</a>
    	<h3><br />Clean install</h3>
        <p>The button below will remove the config.php file and database, and will download the most recent ModernBB package from the servers and launch the installer. This will undo changes you made to the ModernBB core files. This is the same as an update, but you will also lose your data, both database as config.php.</p>
        <a href="#" class="btn btn-danger">Clean install</a>
    </div>
</div>
<?php

require FORUM_ROOT.'backstage/footer.php';
