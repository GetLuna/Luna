<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', '../');
require LUNA_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
	header("Location: login.php");

$action = isset($_GET['action']) ? $_GET['action'] : null;

if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/update.php');

	$db->query('UPDATE '.$db->prefix.'config SET conf_value=\''.luna_htmlspecialchars($_POST['form']['update_ring']).'\' WHERE conf_name=\'o_update_ring\'') or error('Unable to update update ring config', __FILE__, __LINE__, $db->error());

	// Regenerate the config cache
	if (!defined('LUNA_CACHE_FUNCTIONS_LOADED'))
		require LUNA_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/update.php?saved=true');
}

if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/update.php', __('Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database (look for o_base_url in the config table) and then clear the cache by deleting all .php files in the /cache directory.', 'luna'));

	$db->query('UPDATE '.$db->prefix.'config SET conf_value='.floatval($_POST['form']['update_ring']).' WHERE conf_name=\'o_update_ring\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());

	// Regenerate the config cache
	if (!defined('LUNA_CACHE_FUNCTIONS_LOADED'))
		require LUNA_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/settings.php?saved=true');
}

if ($action == 'check_update') {
	if (!defined('LUNA_CACHE_FUNCTIONS_LOADED'))
		require LUNA_ROOT.'include/cache.php';

	// Regenerate the update cache		
	generate_update_cache();
	header("Location: update.php");
}

if (file_exists(LUNA_CACHE_DIR.'cache_update.php'))
	include LUNA_CACHE_DIR.'cache_update.php';
	
if ((!defined('LUNA_UPDATE_LOADED') || ($last_check_time > time() + (60 * 60 * 24)))) {
	if (!defined('LUNA_CACHE_FUNCTIONS_LOADED'))
		require LUNA_ROOT.'include/cache.php';

	generate_update_cache();
	require LUNA_CACHE_DIR.'cache_update.php';
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('Update', 'luna'));
define('LUNA_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('backstage', 'update');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success">'.__('Your settings have been saved.', 'luna').'</div>';
?>
<div class="row">
	<div class="col-sm-4 col-md-3">
		<form method="post" action="update.php">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><?php _e('Update ring', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
					</div>
					<table class="table">
						<tbody>
							<tr>
								<td>
									<select class="form-control" id="update_ring" name="form[update_ring]" tabindex="1">
										<option value="1" <?php if ($luna_config['o_update_ring'] == 1) { echo 'selected'; } ?>><?php _e('Normal', 'luna') ?></option>
										<option value="2" <?php if ($luna_config['o_update_ring'] == 2) { echo 'selected'; } ?>><?php _e('Preview', 'luna') ?></option>
										<option value="3" <?php if ($luna_config['o_update_ring'] == 3) { echo 'selected'; } ?>><?php _e('Nightly', 'luna') ?></option>
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
				<h3 class="panel-title"><?php _e('Luna software updates', 'luna') ?><?php if ($luna_config['o_update_ring'] != 3) { ?><span class="pull-right"><a href="update.php?action=check_update" class="btn btn-primary"><span class="fa fa-fw fa-refresh"></span> <?php _e('Check for updates', 'luna') ?></a></span><?php } ?></h3>
			</div>
			<div class="panel-body">
<?php 
if ($luna_config['o_update_ring'] != 3) {
	if (version_compare(Version::LUNA_CORE_VERSION, $update_cache, 'lt')) {
?>
				<h3><?php _e('A new version is available!', 'luna') ?></h3>
				<p><?php printf(__('A new version, Luna %s has been released. It\'s a good idea to update to the latest version of Luna, as it contains not only new features, improvements and bugfixes, but also the latest security updates.', 'luna'), $update_cache) ?></p>
				<div class="btn-group">
					<a href="http://modernbb.be/cnt/get.php?id=4" class="btn btn-primary"><?php echo sprintf(__('Download v%s', 'luna'), $update_cache) ?></a>
					<a href="http://getluna.org/releaste-notes.php" class="btn btn-primary"><?php _e('Changelog', 'luna') ?></a>
				</div>
<?php
	} elseif (version_compare(Version::LUNA_CORE_VERSION, $update_cache, 'eq')) {
?>
				<h3><?php _e('You\'re using the latest version of Luna!', 'luna') ?></h3>
				<p><?php _e('You\'re on our latest release! Nothing to worry about.', 'luna') ?></p>
<?php
	} else {
?>
				<h3><?php _e('You\'re using a development version of Luna. Be sure to stay up-to-date.', 'luna') ?></h3>
				<p><?php _e('We release every now and then a new build for Luna, one more stable then the other, for you to check out. You can keep track of this at <a href="http://getluna.org/releaste-notes-preview.php">our website</a>. New builds can contain new features, improved features, and/or bugfixes.', 'luna') ?></p>
				<p><?php _e('At this point, we can only tell you that you\'re beyond the latest release. We can\'t tell you if there is a new preview available. You\'ll have to find out for yourself.', 'luna') ?></p>
<?php
	}
} else {
?>
				<h3><?php _e('You\'re using a development version of Luna. Be sure to stay up-to-date.', 'luna') ?></h3>
				<p><?php _e('At this point, we can only tell you that you\'re beyond the latest release. We can\'t tell you if there is a new preview available. You\'ll have to find out for yourself.', 'luna') ?></p>
				<div class="btn-group">
					<a href="http://modernbb.be/cnt/get.php?id=4" class="btn btn-primary"><?php _e('Download', 'luna') ?></a>
				</div>
<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php

require 'footer.php';
