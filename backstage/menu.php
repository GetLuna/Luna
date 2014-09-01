<?php

/**
 * Copyright (C) 2013-2014 Luna
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', '../');
require '../include/common.php';

if (!$luna_user['is_admmod'])
    header("Location: ../login.php");
	
$result = $db->query('SELECT id, url, name, disp_position, disp, sys_entry FROM '.$db->prefix.'menu ORDER BY disp_position') or error('Unable to fetch menu items', __FILE__, __LINE__, $db->error());

require 'header.php';
load_admin_nav('settings', 'menu');

?>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Menu</h3>
			</div>
			<table class="table">
				<thead>
					<tr>
						<th>Name</th>
						<th>URL</th>
						<th>Position</th>
						<th>Show</th>
						<th>Delete</th>
					</tr>
				</thead>
				<tbody>
<?php
if ($db->num_rows($result) > 0) {
	while ($cur_item = $db->fetch_assoc($result)) {
?>
					<tr>
						<td><?php echo $cur_item['name'] ?></td>
						<td><?php echo $cur_item['url'] ?></td>
						<td><?php echo $cur_item['disp_position'] ?></td>
						<td><?php echo $cur_item['disp'] ?></td>
						<td><?php echo $cur_item['sys_entry'] ?></td>
					</tr>
<?php
	}
}
?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php

require 'footer.php';
