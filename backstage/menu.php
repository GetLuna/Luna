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
				<h3 class="panel-title">Menu<span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save'] ?>" /></span></h3>
			</div>
			<table class="table">
				<thead>
					<tr>
						<th>Name</th>
						<th>URL</th>
						<th class="col-xs-1">Position</th>
						<th class="col-xs-1">Show</th>
						<th class="col-xs-1">Delete</th>
					</tr>
				</thead>
				<tbody>
<?php
if ($db->num_rows($result) > 0) {
	while ($cur_item = $db->fetch_assoc($result)) {
?>
					<tr>
						<td>
							<input type="text" class="form-control" value="<?php echo $cur_item['name'] ?>" />
						</td>
						<td>
							<input type="text" class="form-control" value="<?php echo $cur_item['url'] ?>" <?php if ($cur_item['sys_entry'] == 1) echo ' disabled="disabled"' ?> />
						</td>
						<td>
							<input type="text" class="form-control" value="<?php echo $cur_item['disp_position'] ?>" />
						</td>
						<td>
							<input type="checkbox" value="1" <?php if ($cur_item['disp'] == 1) echo ' checked="checked"' ?> />
						</td>
						<td>
<?php
if ($cur_item['sys_entry'] == 0)
	echo '<button class="btn btn-danger">Delete</button>';
else
	echo '<button class="btn btn-danger" disabled="disabled">Delete</button>';
?>
						</td>
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
