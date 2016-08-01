<?php

/*
 * Copyright (C) 2013-2016 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', '../');
define('LUNA_SECTION', 'backstage');
define('LUNA_PAGE', 'update');

require LUNA_ROOT.'include/common.php';

if (!$luna_user['is_admmod']) {
	header("Location: login.php");
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : null;

// Show phpinfo() output
if ($action == 'phpinfo' && $luna_user['g_id'] == LUNA_ADMIN) {
	// Is phpinfo() a disabled function?
	if (strpos(strtolower((string) ini_get('disable_functions')), 'phpinfo') !== false)
		message_backstage(__('The PHP function phpinfo() has been disabled on this server.', 'luna'));

	phpinfo();
	exit;
}

// Get the server load averages (if possible)
if (@file_exists('/proc/loadavg') && is_readable('/proc/loadavg')) {
	// We use @ just in case
	$fh = @fopen('/proc/loadavg', 'r');
	$load_averages = @fread($fh, 64);
	@fclose($fh);

	if (($fh = @fopen('/proc/loadavg', 'r'))) 	{
		$load_averages = fread($fh, 64);
		fclose($fh);
	} else
		$load_averages = '';

	$load_averages = @explode(' ', $load_averages);
	$server_load = isset($load_averages[2]) ? $load_averages[0].' '.$load_averages[1].' '.$load_averages[2] : __('Not available', 'luna');
} elseif (!in_array(PHP_OS, array('WINNT', 'WIN32')) && preg_match('%averages?: ([0-9\.]+),?\s+([0-9\.]+),?\s+([0-9\.]+)%i', @exec('uptime'), $load_averages))
	$server_load = $load_averages[1].' '.$load_averages[2].' '.$load_averages[3];
else
	$server_load = __('Not available', 'luna');

// Get number of current visitors
$result = $db->query('SELECT COUNT(user_id) FROM '.$db->prefix.'online WHERE idle=0') or error('Unable to fetch online count', __FILE__, __LINE__, $db->error());
$num_online = $db->result($result);

// Collect some additional info about MySQL
if ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb') {
	// Calculate total db size/row count
	$result = $db->query('SHOW TABLE STATUS LIKE \''.$db->prefix.'%\'') or error('Unable to fetch table status', __FILE__, __LINE__, $db->error());

	$total_records = $total_size = 0;
	while ($status = $db->fetch_assoc($result)) {
		$total_records += $status['Rows'];
		$total_size += $status['Data_length'] + $status['Index_length'];
	}

	$total_size = file_size($total_size);
}

// Check for the existence of various PHP opcode caches/optimizers
if (function_exists('mmcache'))
	$php_accelerator = '<a href="http://turck-mmcache.sourceforge.net/">'.__('Turck MMCache', 'luna').'</a>';
elseif (isset($_PHPA))
	$php_accelerator = '<a href="http://www.php-accelerator.co.uk/">'.__('ionCube PHP Accelerator', 'luna').'</a>';
elseif (ini_get('apc.enabled'))
	$php_accelerator ='<a href="http://www.php.net/apc/">'.__('Alternative PHP Cache (APC)', 'luna').'</a>';
elseif (ini_get('zend_optimizer.optimization_level'))
	$php_accelerator = '<a href="http://www.zend.com/products/guard/zend-optimizer/">'.__('Zend Optimizer', 'luna').'</a>';
elseif (ini_get('eaccelerator.enable'))
	$php_accelerator = '<a href="http://www.eaccelerator.net/">'.__('eAccelerator', 'luna').'</a>';
elseif (ini_get('xcache.cacher'))
	$php_accelerator = '<a href="http://xcache.lighttpd.net/">'.__('XCache', 'luna').'</a>';
else
	$php_accelerator = __('Non available', 'luna');

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
    exit;
}

if (file_exists(LUNA_CACHE_DIR.'cache_update.php'))
	include LUNA_CACHE_DIR.'cache_update.php';

if ((!defined('LUNA_UPDATE_LOADED') || ($last_check_time > time() + (60 * 60 * 24)))) {
	if (!defined('LUNA_CACHE_FUNCTIONS_LOADED'))
		require LUNA_ROOT.'include/cache.php';

	generate_update_cache();
	require LUNA_CACHE_DIR.'cache_update.php';
}

require 'header.php';
?>
<div class="row">
    <div class="col-sm-12">
<?php
if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><i class="fa fa-fw fa-check"></i> '.__('Your settings have been saved.', 'luna').'</div>';
?>
    </div>
	<div class="col-sm-4">
		<form method="post" action="update.php" class="panel panel-default">
			<input type="hidden" name="form_sent" value="1" />
            <div class="panel-heading">
                <h3 class="panel-title"><?php _e('Update ring', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
            </div>
            <div class="panel-body">
                <select class="form-control" id="update_ring" name="form[update_ring]" tabindex="1">
                    <option value="1" <?php if ($luna_config['o_update_ring'] == 1) { echo 'selected'; } ?>><?php _e('Normal', 'luna') ?></option>
                    <option value="2" <?php if ($luna_config['o_update_ring'] == 2) { echo 'selected'; } ?>><?php _e('Preview', 'luna') ?></option>
                    <option value="3" <?php if ($luna_config['o_update_ring'] == 3) { echo 'selected'; } ?>><?php _e('Nightly', 'luna') ?></option>
                </select>
            </div>
		</form>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php _e('Luna version information', 'luna') ?></h3>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th class="col-md-6"></th>
                        <th class="col-md-6"><?php _e('Version', 'luna') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Software version', 'luna') ?></td>
                        <td><?php echo $luna_config['o_cur_version']; ?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Core version', 'luna') ?></td>
                        <td><?php echo $luna_config['o_core_version']; ?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Database version', 'luna') ?></td>
                        <td><?php echo $luna_config['o_database_revision']; ?></td>
                    </tr>
                    <tr>
                        <td><?php _e('Bootstrap version', 'luna') ?></td>
                        <td>3.3.7</td>
                    </tr>
                    <tr>
                        <td><?php _e('Font Awesome version', 'luna') ?></td>
                        <td>4.6.3</td>
                    </tr>
                    <tr>
                        <td><?php _e('jQuery version', 'luna') ?></td>
                        <td>2.2.4</td>
                    </tr>
                </tbody>
            </table>
        </div>
	</div>
	<div class="col-sm-8">
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
					<a href="http://getluna.org/cnt/get.php?id=4" class="btn btn-primary"><i class="fa fa-fw fa-download"></i> <?php echo sprintf(__('Download v%s', 'luna'), $update_cache) ?></a>
					<a href="http://getluna.org/release-notes.php" class="btn btn-primary"><i class="fa fa-fw fa-refresh"></i> <?php _e('Changelog', 'luna') ?></a>
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
				<p><?php _e('We release every now and then a new build for Luna, one more stable then the other, for you to check out. You can keep track of this at <a href="http://getluna.org/release-notes-preview.php">our website</a>. New builds can contain new features, improved features, and/or bugfixes.', 'luna') ?></p>
				<p><?php _e('At this point, we can only tell you that you\'re beyond the latest release. We can\'t tell you if there is a new preview available. You\'ll have to find out for yourself.', 'luna') ?></p>
<?php
	}
} else {
?>
				<h3><?php _e('You\'re using a development version of Luna. Be sure to stay up-to-date.', 'luna') ?></h3>
				<p><?php _e('At this point, we can only tell you that you\'re beyond the latest release. We can\'t tell you if there is a new preview available. You\'ll have to find out for yourself.', 'luna') ?></p>
				<div class="btn-group">
					<a href="http://getluna.org/cnt/get.php?id=4" class="btn btn-primary"><i class="fa fa-fw fa-download"></i> <?php _e('Download', 'luna') ?></a>
				</div>
<?php } ?>
			</div>
		</div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php _e('Server statistics', 'luna') ?></h3>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th class="col-md-4"><?php _e('Server load', 'luna') ?></th>
                        <?php if ($luna_user['g_id'] == LUNA_ADMIN): ?>
                        <th class="col-md-4"><?php _e('Environment', 'luna') ?></th>
                        <th class="col-md-4"><?php _e('Database', 'luna') ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php printf(__('%s - %s user(s) online', 'luna')."\n", $server_load, $num_online) ?></td>
                        <?php if ($luna_user['g_id'] == LUNA_ADMIN): ?>
                        <td>
                            <?php printf(__('Operating system: %s', 'luna'), PHP_OS) ?><br />
                            <?php printf(__('PHP: %s - %s', 'luna'), phpversion(), '<a href="system.php?action=phpinfo">'.__('Show info', 'luna').'</a>') ?><br />
                            <?php printf(__('Accelerator: %s', 'luna')."\n", $php_accelerator) ?>
                        </td>
                        <td>
                            <?php echo implode(' ', $db->get_version())."\n" ?>
                            <?php if (isset($total_records) && isset($total_size)): ?>
                            <br /><?php printf(__('Rows: %s', 'luna')."\n", forum_number_format($total_records)) ?>
                            <br /><?php printf(__('Size: %s', 'luna')."\n", $total_size) ?>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                </tbody>
            </table>
        </div>
	</div>
</div>
<?php

require 'footer.php';
