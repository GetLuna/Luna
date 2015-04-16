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


// Show phpinfo() output
if ($action == 'phpinfo' && $luna_user['g_id'] == FORUM_ADMIN) {
	// Is phpinfo() a disabled function?
	if (strpos(strtolower((string) ini_get('disable_functions')), 'phpinfo') !== false)
		message_backstage($lang['PHPinfo disabled message']);

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
	$server_load = isset($load_averages[2]) ? $load_averages[0].' '.$load_averages[1].' '.$load_averages[2] : $lang['Not available'];
} elseif (!in_array(PHP_OS, array('WINNT', 'WIN32')) && preg_match('%averages?: ([0-9\.]+),?\s+([0-9\.]+),?\s+([0-9\.]+)%i', @exec('uptime'), $load_averages))
	$server_load = $load_averages[1].' '.$load_averages[2].' '.$load_averages[3];
else
	$server_load = $lang['Not available'];


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
	$php_accelerator = '<a href="http://'.$lang['Turck MMCache link'].'">'.$lang['Turck MMCache'].'</a>';
elseif (isset($_PHPA))
	$php_accelerator = '<a href="http://'.$lang['ionCube PHP Accelerator link'].'">'.$lang['ionCube PHP Accelerator'].'</a>';
elseif (ini_get('apc.enabled'))
	$php_accelerator ='<a href="http://'.$lang['Alternative PHP Cache (APC) link'].'">'.$lang['Alternative PHP Cache (APC)'].'</a>';
elseif (ini_get('zend_optimizer.optimization_level'))
	$php_accelerator = '<a href="http://'.$lang['Zend Optimizer link'].'">'.$lang['Zend Optimizer'].'</a>';
elseif (ini_get('eaccelerator.enable'))
	$php_accelerator = '<a href="http://'.$lang['eAccelerator link'].'">'.$lang['eAccelerator'].'</a>';
elseif (ini_get('xcache.cacher'))
	$php_accelerator = '<a href="http://'.$lang['XCache link'].'">'.$lang['XCache'].'</a>';
else
	$php_accelerator = $lang['NA'];


$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Server statistics']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('backstage', 'stats');

?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $lang['Version information'] ?></h3>
	</div>
	<table class="table">
		<thead>
			<tr>
				<th class="col-md-3"></th>
				<th class="col-md-3"><?php echo $lang['Version'] ?></th>
				<th class="col-md-3"></th>
				<th class="col-md-3"><?php echo $lang['Version'] ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo $lang['Software version'] ?></td>
				<td><?php echo $luna_config['o_cur_version']; ?></td>
				<td><?php echo $lang['Bootstrap version'] ?></td>
				<td>3.3.4</td>
			</tr>
			<tr>
				<td><?php echo $lang['Core version'] ?></td>
				<td><?php echo $luna_config['o_core_version']; ?></td>
				<td><?php echo $lang['Font Awesome version'] ?></td>
				<td>4.3.0</td>
			</tr>
			<tr>
				<td><?php echo $lang['Database version'] ?></td>
				<td><?php echo $luna_config['o_database_revision']; ?></td>
				<td><?php echo $lang['jQuery version'] ?></td>
				<td>2.1.3</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $lang['Server statistics head'] ?></h3>
	</div>
	<table class="table">
		<thead>
			<tr>
				<th class="col-md-4"><?php echo $lang['Server load label'] ?></th>
				<?php if ($luna_user['g_id'] == FORUM_ADMIN): ?>
				<th class="col-md-4"><?php echo $lang['Environment label'] ?></th>
				<th class="col-md-4"><?php echo $lang['Database'] ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php printf($lang['Server load data']."\n", $server_load, $num_online) ?></td>
				<?php if ($luna_user['g_id'] == FORUM_ADMIN): ?>
				<td>
					<?php printf($lang['Environment data OS'], PHP_OS) ?><br />
					<?php printf($lang['Environment data version'], phpversion(), '<a href="system.php?action=phpinfo">'.$lang['Show info'].'</a>') ?><br />
					<?php printf($lang['Environment data acc']."\n", $php_accelerator) ?>
				</td>
				<td>
					<?php echo implode(' ', $db->get_version())."\n" ?>
					<?php if (isset($total_records) && isset($total_size)): ?>
					<br /><?php printf($lang['Database data rows']."\n", forum_number_format($total_records)) ?>
					<br /><?php printf($lang['Database data size']."\n", $total_size) ?>
					<?php endif; ?>
				</td>
				<?php endif; ?>
			</tr>
		</tbody>
	</table>
</div>
<?php

require 'footer.php';
