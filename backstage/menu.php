<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', '../');
require '../include/common.php';

if (!$is_admin)
	header("Location: login.php");
// Add a new item
if (isset($_POST['add_item'])) {
	confirm_referrer('backstage/menu.php');
	
	$item_name = luna_trim($_POST['name']);
	$item_url = luna_trim($_POST['url']);

	if ($item_name == '')
		message_backstage(__('You must give your menu item a title.', 'luna'));
	elseif ($item_url == '')
		message_backstage(__('You must give your menu item an URL.', 'luna'));

	$db->query('INSERT INTO '.$db->prefix.'menu (url, name, disp_position, visible, sys_entry) VALUES(\''.$db->escape($item_url).'\', \''.$db->escape($item_name).'\', 0, 1, 0)') or error('Unable to add new menu item', __FILE__, __LINE__, $db->error());

	redirect('backstage/menu.php');
} elseif (isset($_GET['del_item'])) {
	confirm_referrer('backstage/menu.php');
	
	$item_id = intval($_GET['del_item']);
	if ($item_id < 4)
		message_backstage(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

	$db->query('DELETE FROM '.$db->prefix.'menu WHERE id='.$item_id) or error('Unable to delete menu item', __FILE__, __LINE__, $db->error());

	redirect('backstage/menu.php');
} elseif (isset($_POST['update'])) {
	confirm_referrer('backstage/menu.php');
	
	$menu_items = $_POST['item'];
	if (empty($menu_items))
		message_backstage(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');

	foreach ($menu_items as $item_id => $cur_item) {
		$cur_item['url'] = luna_trim($cur_item['url']);
		$cur_item['name'] = luna_trim($cur_item['name']);
		$cur_item['order'] = luna_trim($cur_item['order']);
		if (!isset($cur_item['visible']))
			$cur_item['visible'] = 0;
		
		if ($cur_item['name'] == '')
			message_backstage(__('You must give your menu item a title.', 'luna'));
		elseif ($cur_item['url'] == '')
			message_backstage(__('You must give your menu item an URL.', 'luna'));
		elseif ($cur_item['order'] == '' || preg_match('%[^0-9]%', $cur_item['order']))
			message_backstage(__('Position must be a positive integer value.', 'luna'));
		else
			$db->query('UPDATE '.$db->prefix.'menu SET url=\''.$db->escape($cur_item['url']).'\', name=\''.$db->escape($cur_item['name']).'\', disp_position='.$cur_item['order'].', visible=\''.$cur_item['visible'].'\' WHERE id='.intval($item_id)) or error('Unable to update menu', __FILE__, __LINE__, $db->error());
	}

	redirect('backstage/menu.php');
}
	
$result = $db->query('SELECT * FROM '.$db->prefix.'menu ORDER BY disp_position') or error('Unable to fetch menu items', __FILE__, __LINE__, $db->error());

require 'header.php';
load_admin_nav('settings', 'menu');

?>
<div class="row">
	<div class="col-sm-4 col-md-3">
		<form method="post" action="menu.php?action=add_item">
			<fieldset>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><?php _e('New menu item', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="add_item"><span class="fa fa-fw fa-plus"></span> <?php _e('Add', 'luna') ?></button></span></h3>
					</div>
					<table class="table">
						<tbody>
							<tr>
								<td>
									<input type="text" class="form-control" name="name" placeholder="<?php _e('Name', 'luna') ?>" value="" />
								</td>
							</tr>
							<tr>
								<td>
									<input type="text" class="form-control" name="url" placeholder="<?php _e('URL', 'luna') ?>" value="" />
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</fieldset>
		</form>
	</div>
	<div class="col-sm-8 col-md-9">
		<form method="post" action="menu.php">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php _e('Menu', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="update"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
				</div>
				<table class="table">
					<thead>
						<tr>
							<th><?php _e('Name', 'luna') ?></th>
							<th><?php _e('URL', 'luna') ?></th>
							<th class="col-xs-1"><?php _e('Position', 'luna') ?></th>
							<th class="col-xs-1"><?php _e('Show', 'luna') ?></th>
							<th class="col-xs-1"><?php _e('Delete', 'luna') ?></th>
						</tr>
					</thead>
					<tbody>
<?php
if ($db->num_rows($result)) {
	while ($cur_item = $db->fetch_assoc($result)) {
?>
						<tr>
							<td>
								<input type="text" class="form-control" name="item[<?php echo $cur_item['id'] ?>][name]" value="<?php echo $cur_item['name'] ?>" />
							</td>
							<td>
								<input type="text" class="form-control" name="item[<?php echo $cur_item['id'] ?>][url]" value="<?php echo $cur_item['url'] ?>" <?php if ($cur_item['sys_entry'] == 1) echo ' readonly' ?> />
							</td>
							<td>
								<input type="number" class="form-control" name="item[<?php echo $cur_item['id'] ?>][order]" value="<?php echo $cur_item['disp_position'] ?>" />
							</td>
							<td>
								<input type="checkbox" value="1" name="item[<?php echo $cur_item['id'] ?>][visible]" <?php if ($cur_item['visible'] == 1) echo ' checked' ?> />
							</td>
							<td>
<?php
if ($cur_item['sys_entry'] == 0)
	echo '<a href="menu.php?del_item='.$cur_item['id'].'" class="btn btn-danger"><span class="fa fa-fw fa-trash"></span> '.__('Delete', 'luna').'</a>';
else
	echo '<a class="btn btn-danger" disabled="disabled"><span class="fa fa-fw fa-trash"></span> '.__('Delete', 'luna').'</a>';
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
		</form>
	</div>
</div>
<?php

require 'footer.php';
