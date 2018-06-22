<?php

/*
 * Copyright (C) 2013-2018 Luna
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', '../');
require '../include/common.php';
define('LUNA_SECTION', 'settings');
define('LUNA_PAGE', 'menu');

if (!$luna_user['is_admmod']) {
    header("Location: login.php");
    exit;
}

// Add a new item
if (isset($_POST['add_item'])) {
    confirm_referrer('backstage/menu.php');

    $item_name = luna_trim($_POST['name']);
    $item_url = luna_trim($_POST['url']);
    $item_position = luna_trim($_POST['position']);
    $item_visible = isset($_POST['visible']) ? '1' : '0';

    if ($item_name == '') {
        message_backstage(__('You must give your menu item a title.', 'luna'));
    } elseif ($item_url == '') {
        message_backstage(__('You must give your menu item an URL.', 'luna'));
    }

    if ($item_position == '' || preg_match('%[^0-9]%', $item_position)) {
        message_backstage(__('The location must be a positive integer value.', 'luna'));
    }

    $db->query('INSERT INTO '.$db->prefix.'menu (url, name, disp_position, visible, sys_entry) VALUES(\''.$db->escape($item_url).'\', \''.$db->escape($item_name).'\', '.$item_position.', '.$item_visible.', 0)') or error('Unable to add new menu item', __FILE__, __LINE__, $db->error());

    redirect('backstage/menu.php');
} elseif (isset($_GET['del_item'])) {
    confirm_referrer('backstage/menu.php');

    $item_id = intval($_GET['del_item']);
    if ($item_id < 4) {
        message_backstage(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');
    }

    $db->query('DELETE FROM '.$db->prefix.'menu WHERE id='.$item_id) or error('Unable to delete menu item', __FILE__, __LINE__, $db->error());

    redirect('backstage/menu.php');
} elseif (isset($_POST['update'])) {
    confirm_referrer('backstage/menu.php');

    $menu_items = $_POST['item'];
    if (empty($menu_items)) {
        message_backstage(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');
    }

    foreach ($menu_items as $item_id => $cur_item) {
        $cur_item['url'] = luna_trim($cur_item['url']);
        $cur_item['name'] = luna_trim($cur_item['name']);
        $cur_item['order'] = luna_trim($cur_item['order']);
        if (!isset($cur_item['visible'])) {
            $cur_item['visible'] = 0;
        }

        if ($cur_item['name'] == '') {
            message_backstage(__('You must give your menu item a title.', 'luna'));
        } elseif ($cur_item['url'] == '') {
            message_backstage(__('You must give your menu item an URL.', 'luna'));
        } elseif ($cur_item['order'] == '' || preg_match('%[^0-9]%', $cur_item['order'])) {
            message_backstage(__('Position must be a positive integer value.', 'luna'));
        } else {
            $db->query('UPDATE '.$db->prefix.'menu SET url=\''.$db->escape($cur_item['url']).'\', name=\''.$db->escape($cur_item['name']).'\', disp_position='.$cur_item['order'].', visible=\''.$cur_item['visible'].'\' WHERE id='.intval($item_id)) or error('Unable to update menu', __FILE__, __LINE__, $db->error());
        }
    }

    redirect('backstage/menu.php');
}

$menus = $db->query('SELECT * FROM '.$db->prefix.'menu ORDER BY disp_position') or error('Unable to fetch menu items', __FILE__, __LINE__, $db->error());

require 'header.php';
?>
<div class="row">
	<div class="col-md-4">
		<form method="post" class="card" action="menu.php?action=add_item">
            <h5 class="card-header">
                <?php _e('New item', 'luna')?>
                <span class="float-right">
                    <button class="btn btn-link" type="submit" name="add_item"><span class="fas fa-fw fa-plus"></span> <?php _e('Add', 'luna')?></button>
                </span>
            </h5>
            <div class="card-body">
                <input type="text" class="form-control" name="name" placeholder="<?php _e('Name', 'luna')?>" />
                <hr />
                <input type="text" class="form-control" name="url" placeholder="<?php _e('URL', 'luna')?>" />
                <hr />
                <input type="number" class="form-control" name="position" placeholder="<?php _e('Position', 'luna')?>" />
                <hr />
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="visible" name="visible" value="1" checked>
                    <label class="custom-control-label" for="visible">
                        <?php _e('Make this item visible in the menu.', 'luna')?>
                    </label>
                </div>
            </div>
		</form>
	</div>
	<div class="col-md-8">
		<form method="post" class="card" action="menu.php">
			<h5 class="card-header">
                <?php _e('Menu', 'luna')?>
                <span class="float-right">
                    <button class="btn btn-link" type="submit" name="update"><span class="fas fa-fw fa-check"></span> <?php _e('Save', 'luna')?></button>
                </span>
            </h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php _e('Name', 'luna')?></th>
                            <th><?php _e('URL', 'luna')?></th>
                            <th><?php _e('Position', 'luna')?></th>
                            <th><?php _e('Show', 'luna')?></th>
                            <th><?php _e('Delete', 'luna')?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php
while ($cur_item = $db->fetch_assoc($menus)) {
    ?>
                        <tr>
                            <td>
                                <input type="text" class="form-control" name="item[<?php echo $cur_item['id'] ?>][name]" value="<?php echo $cur_item['name'] ?>" />
                            </td>
                            <td>
                                <input type="text" class="form-control" name="item[<?php echo $cur_item['id'] ?>][url]" value="<?php echo $cur_item['url'] ?>" <?php if ($cur_item['sys_entry'] == 1) { echo ' readonly'; } ?> />
                            </td>
                            <td>
                                <input type="number" class="form-control" name="item[<?php echo $cur_item['id'] ?>][order]" value="<?php echo $cur_item['disp_position'] ?>" />
                            </td>
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="item[<?php echo $cur_item['id'] ?>][visible]" id="item[<?php echo $cur_item['id'] ?>][visible]" value="1"<?php if ($cur_item['visible'] == 1) { echo ' checked'; } ?>>
                                    <label class="custom-control-label" for="item[<?php echo $cur_item['id'] ?>][visible]"></label>
                                </div>
                            </td>
                            <td>
                                <?php if ( $cur_item['sys_entry'] == 0 ) { ?>
                                <a href="menu.php?del_item=<?php echo $cur_item['id'] ?>" class="btn btn-danger"><span class="fas fa-fw fa-trash"></span> <?php _e('Delete', 'luna') ?></a>
                                <?php } ?>
                            </td>
                        </tr>
<?php
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
