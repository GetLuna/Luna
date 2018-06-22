<?php

/*
 * Copyright (C) 2013-2018 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', '../');
define('LUNA_SECTION', 'settings');
define('LUNA_PAGE', 'emoji');

require LUNA_ROOT.'include/common.php';

if (!$luna_user['is_admmod']) {
    header("Location: login.php");
    exit;
}

// Add a emoji
if (isset($_POST['add_emoji'])) {
    confirm_referrer('backstage/emoji.php');

    $text = luna_trim($_POST['new_text']);
    $unicode = luna_trim($_POST['new_unicode']);

    if ($text == '') {
        message_backstage(__('You must enter a BBCode that will be replaced with the emoji.', 'luna'));
    }

    if ($unicode == '') {
        message_backstage(__('You must enter a unicode which represents the emoji.', 'luna'));
    }

    $db->query('INSERT INTO '.$db->prefix.'emoji (text, unicode) VALUES (\''.$db->escape($text).'\', \''.$db->escape(strtolower($unicode)).'\')') or error('Unable to add emoji', __FILE__, __LINE__, $db->error());

    // Regenerate the emoji cache
    if (!defined('LUNA_CACHE_FUNCTIONS_LOADED')) {
        require LUNA_ROOT.'include/cache.php';
    }

    generate_emoji_cache();

    redirect('backstage/emoji.php');
}

// Update a emoji
elseif (isset($_POST['update'])) {
    confirm_referrer('backstage/emoji.php');

    $id = intval(key($_POST['update']));

    $text = luna_trim($_POST['text'][$id]);
    $unicode = luna_trim($_POST['unicode'][$id]);

    if ($text == '') {
        message_backstage(__('You must enter a BBCode that will be replaced with the emoji.', 'luna'));
    }

    if ($unicode == '') {
        message_backstage(__('You must enter a unicode which represents the emoji.', 'luna'));
    }

    $db->query('UPDATE '.$db->prefix.'emoji SET text=\''.$db->escape($text).'\', unicode=\''.$db->escape(strtolower($unicode)).'\' WHERE id='.$id) or error('Unable to update emoji', __FILE__, __LINE__, $db->error());

    // Regenerate the emoji cache
    if (!defined('LUNA_CACHE_FUNCTIONS_LOADED')) {
        require LUNA_ROOT.'include/cache.php';
    }

    generate_emoji_cache();

    redirect('backstage/emoji.php');
}

// Remove a emoji
elseif (isset($_POST['remove'])) {
    confirm_referrer('backstage/emoji.php');

    $id = intval(key($_POST['remove']));

    $db->query('DELETE FROM '.$db->prefix.'emoji WHERE id='.$id) or error('Unable to delete emoji', __FILE__, __LINE__, $db->error());

    // Regenerate the emoji cache
    if (!defined('LUNA_CACHE_FUNCTIONS_LOADED')) {
        require LUNA_ROOT.'include/cache.php';
    }

    generate_emoji_cache();

    redirect('backstage/emoji.php');
}

$focus_element = array('emoji', 'new_text');

require 'header.php';

?>
<div class="row">
	<div class="col-md-4">
		<form id="emoji" class="card" method="post" action="emoji.php">
			<h5 class="card-header">
                <?php _e('Add emoji', 'luna')?>
                <span class="float-right">
                    <button class="btn btn-link" type="submit" name="add_emoji" tabindex="3"><span class="fas fa-fw fa-plus"></span> <?php _e('Add', 'luna')?></button>
                </span>
            </h5>
            <div class="card-body">
                <p><?php echo sprintf(__('Enter the BBCode that should replace the emoji and the unicode that represents the emoji. See a %s of supported emoji', 'luna'), '<a href="http://unicode.org/emoji/charts/full-emoji-list.html">' . __('full list', 'luna') . '</a>') ?></p>
                <hr />
                <input type="text" class="form-control" placeholder="<?php _e('BBCode', 'luna')?>" name="new_text" maxlength="60" tabindex="1" />
                <hr />
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">U+</span>
                    </div>
                    <input type="text" class="form-control" placeholder="<?php _e('Unicode', 'luna')?>" name="new_unicode" maxlength="60" tabindex="2" />
                </div>
            </div>
		</form>
	</div>
	<div class="col-md-8">
        <form id="emoji" class="card" method="post" action="emoji.php">
			<h5 class="card-header"><?php _e('Manage words', 'luna')?></h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th><?php _e('BBCode', 'luna')?></th>
                            <th><?php _e('Unicode', 'luna')?></th>
                            <th><?php _e('Action', 'luna')?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php
$result = $db->query('SELECT id, text, unicode FROM '.$db->prefix.'emoji ORDER BY unicode') or error('Unable to fetch emoji', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result)) {
    while ($cur_emoji = $db->fetch_assoc($result)) {
?>
                        <tr>
                            <td><span class="emoji emoji-table">&#x<?php echo $cur_emoji['unicode'] ?>;</span></span>
                            <td>
                                <input type="text" class="form-control" name="text[<?php echo $cur_emoji['id'] ?>]" value="<?php echo luna_htmlspecialchars($cur_emoji['text']) ?>" maxlength="60" />
                            </td>
                            <td>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">U+</span>
                                    </div>
                                    <input type="text" class="form-control" name="unicode[<?php echo $cur_emoji['id'] ?>]" value="<?php echo luna_htmlspecialchars($cur_emoji['unicode']) ?>" maxlength="60" />
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-primary" type="submit" name="update[<?php echo $cur_emoji['id'] ?>]"><span class="fas fa-fw fa-check"></span> <?php _e('Update', 'luna')?></button>
                                    <button class="btn btn-danger" type="submit" name="remove[<?php echo $cur_emoji['id'] ?>]"><span class="fas fa-fw fa-trash"></span> <?php _e('Remove', 'luna')?></button>
                                </div>
                            </td>
                            </tr>
<?php
    }
} else {
    echo '<tr><td colspan="4">'.__('No emoji available.', 'luna').'</td></tr>';
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