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
						<h3 class="panel-title">Update ring<span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
					</div>
					<table class="table">
						<tbody>
							<tr>
								<td>
									<select class="form-control" id="update_ring" name="form[update_ring]" tabindex="1">
										<option value="0" <?php if ($luna_config['o_update_ring'] == 0) { echo 'selected'; } ?>>Slow</option>
										<option value="1" <?php if ($luna_config['o_update_ring'] == 1) { echo 'selected'; } ?>>Normal</option>
										<option value="2" <?php if ($luna_config['o_update_ring'] == 2) { echo 'selected'; } ?>>Preview</option>
										<option value="3" <?php if ($luna_config['o_update_ring'] == 3) { echo 'selected'; } ?>>Nightly</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</fieldset>
		</form>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<?php
						if ($luna_config['o_update_ring'] == 0)
							echo 'About the Slow ring';
						elseif ($luna_config['o_update_ring'] == 1)
							echo 'About the Normal ring';
						elseif ($luna_config['o_update_ring'] == 2)
							echo 'About the Preview ring';
						elseif ($luna_config['o_update_ring'] == 3)
							echo 'About the Nightly ring';
					?>
				</h3>
			</div>
			<div class="panel-body">
				<?php if ($luna_config['o_update_ring'] == 0) { ?>
				<p>The slow ring will provide updates for your current branch. Luna will warn you for new updates, but only if they do not contain new features. Warnings will stop as soon as your branch is no longer supported, so be sure to stay up-to-date, the Normal ring might also have an update for you, with new features.</p>
				<?php } elseif ($luna_config['o_update_ring'] == 1) { ?>
				<p>The Normal ring is the default ring for updates, and the recommended one. You'll be receiving warnings for every stable version of Luna that gets released. These contain bug fixes, but also new features and more.</p>
				<?php } elseif ($luna_config['o_update_ring'] == 2) { ?>
				<p>The Preview ring will warn you for all stable updates, in addition to all preview releases, including alphas, betas and Release Candidates. This ring is a good idea for developers that want to test the latest and greatest features that are conciderd usable. It's not recommended to update according to this ring if you're using Luna in a productive environment.</p>
				<?php } elseif($luna_config['o_update_ring'] == 3) { ?>
				<p>The Nighly ring gets regular updates and contains experimental features. It's not recommended to update according to this ring if you're using Luna in a productive environment.</p>
				<?php } ?>
			</div>
		</div>
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
	</div>
</div>
<?php

require 'footer.php';
