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

$action = isset($_GET['action']) ? $_GET['action'] : null;

if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/update.php');

	$db->query('UPDATE '.$db->prefix.'config SET conf_value=\''.luna_htmlspecialchars($_POST['form']['update_ring']).'\' WHERE conf_name=\'o_update_ring\'') or error('Unable to update update ring config', __FILE__, __LINE__, $db->error());

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/update.php?saved=true');
}

if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/update.php', $lang['Bad HTTP Referer message']);

	$db->query('UPDATE '.$db->prefix.'config SET conf_value='.floatval($_POST['form']['update_ring']).' WHERE conf_name=\'o_update_ring\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/settings.php?saved=true');
}

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

if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>';
?>
<div class="row">
	<div class="col-sm-4 col-md-3">
		<form method="post" action="update.php">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo $lang['Update ring'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
					</div>
					<table class="table">
						<tbody>
							<tr>
								<td>
									<select class="form-control" id="update_ring" name="form[update_ring]" tabindex="1">
										<option value="0" <?php if ($luna_config['o_update_ring'] == 0) { echo 'selected'; } ?>><?php echo $lang['Slow'] ?></option>
										<option value="1" <?php if ($luna_config['o_update_ring'] == 1) { echo 'selected'; } ?>><?php echo $lang['Normal'] ?></option>
										<option value="2" <?php if ($luna_config['o_update_ring'] == 2) { echo 'selected'; } ?>><?php echo $lang['Preview'] ?></option>
										<option value="3" <?php if ($luna_config['o_update_ring'] == 3) { echo 'selected'; } ?>><?php echo $lang['Nightly'] ?></option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</fieldset>
		</form>
	</div>
	<div class="col-sm-8 col-md-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $lang['Luna updates'] ?><span class="pull-right"><a href="update.php?action=check_update" class="btn btn-primary"><span class="fa fa-fw fa-refresh"></span> <?php echo $lang['Check for updates'] ?></a></span></h3>
			</div>
			<div class="panel-body">
<?php
	if (version_compare(Version::FORUM_CORE_VERSION, $update_cache, 'lt')) {
?>
				<h3><?php echo $lang['New version'] ?></h3>
				<p><?php printf($lang['New version info'], $update_cache) ?></p>
				<div class="btn-group">
					<a href="http://modernbb.be/cnt/get.php?id=4" class="btn btn-primary"><?php echo sprintf($lang['Download'], $update_cache) ?></a>
					<a href="http://getluna.org/changelog.php" class="btn btn-primary"><?php echo $lang['Changelog'] ?></a>
				</div>
<?php
	} elseif (version_compare(Version::FORUM_CORE_VERSION, $update_cache, 'eq')) {
?>
				<h3><?php echo $lang['Latest version'] ?></h3>
				<p><?php echo $lang['Latest version info'] ?></p>
<?php
	} else {
?>
				<h3><?php echo $lang['Preview version'] ?></h3>
				<p><?php echo $lang['Preview version info 1'] ?></p>
				<p><?php echo $lang['Preview version info 2'] ?></p>
<?php
	}
?>
			</div>
		</div>
	</div>
</div>
<?php

require 'footer.php';
