<?php

/*
 * Copyright (C) 2013-2018 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', '../');
define('LUNA_SECTION', 'content');
define('LUNA_PAGE', 'censoring');

require LUNA_ROOT.'include/common.php';

if (!$luna_user['is_admmod']) {
    header("Location: login.php");
    exit;
}

// Add a censor word
if (isset($_POST['add_word'])) {
    confirm_referrer('backstage/censoring.php');

    $search_for = luna_trim($_POST['new_search_for']);
    $replace_with = luna_trim($_POST['new_replace_with']);

    if ($search_for == '') {
        message_backstage(__('You must enter a word to censor.', 'luna'));
    }

    $db->query('INSERT INTO '.$db->prefix.'censoring (search_for, replace_with) VALUES (\''.$db->escape($search_for).'\', \''.$db->escape($replace_with).'\')') or error('Unable to add censor word', __FILE__, __LINE__, $db->error());

    // Regenerate the censoring cache
    if (!defined('LUNA_CACHE_FUNCTIONS_LOADED')) {
        require LUNA_ROOT.'include/cache.php';
    }

    generate_censoring_cache();

    redirect('backstage/censoring.php');
}

// Update a censor word
elseif (isset($_POST['update'])) {
    confirm_referrer('backstage/censoring.php');

    $censor_item = $_POST['item'];
    if (empty($censor_item)) {
        message_backstage(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');
    }

    foreach ($censor_item as $item_id => $cur_item) {
        $cur_item['search_for'] = luna_trim($cur_item['search_for']);
        $cur_item['replace_with'] = luna_trim($cur_item['replace_with']);

        if ($cur_item['search_for'] == '') {
            message_backstage(__('You must enter a word to censor.', 'luna'));
        } else {
            $db->query('UPDATE '.$db->prefix.'censoring SET search_for=\''.$db->escape($cur_item['search_for']).'\', replace_with=\''.$db->escape($cur_item['replace_with']).'\' WHERE id='.intval($item_id)) or error('Unable to update censor word', __FILE__, __LINE__, $db->error());
        }
    }

    // Regenerate the censoring cache
    if (!defined('LUNA_CACHE_FUNCTIONS_LOADED')) {
        require LUNA_ROOT.'include/cache.php';
    }

    generate_censoring_cache();

    redirect('backstage/censoring.php');
}

// Remove a censor word
elseif (isset($_POST['remove'])) {
    confirm_referrer('backstage/censoring.php');

    $id = intval(key($_POST['remove']));

    $db->query('DELETE FROM '.$db->prefix.'censoring WHERE id='.$id) or error('Unable to delete censor word', __FILE__, __LINE__, $db->error());

    // Regenerate the censoring cache
    if (!defined('LUNA_CACHE_FUNCTIONS_LOADED')) {
        require LUNA_ROOT.'include/cache.php';
    }

    generate_censoring_cache();

    redirect('backstage/censoring.php');
}

$focus_element = array('censoring', 'new_search_for');

require 'header.php';

?>
<div class="row">
    <div class="col-12">
        <?php if ($luna_config['o_censoring'] == 0) { ?>
        <div class="alert alert-danger">
            <i class="fas fa-fw fa-exclamation"></i> <?php echo sprintf(__('Censoring is disabled in %s.', 'luna'), '<a href="features.php">'.__('Features', 'luna').'</a>') ?>
        </div>
        <?php } ?>
    </div>
	<div class="col-md-4">
		<form id="censoring" method="post" action="censoring.php">
			<div class="card">
				<h5 class="card-header">
                    <?php _e('Add word', 'luna') ?>
                    <span class="float-right">
                        <button class="btn btn-link" type="submit" name="add_word" tabindex="3"><span class="fas fa-fw fa-plus"></span> <?php _e('Add', 'luna') ?></button>
                    </span>
                </h5>
                <div class="card-body">
                    <p><?php _e('Enter a word that you want to censor and the replacement text for this word. Wildcards are accepted.', 'luna' )?></p>
                    <hr />
                    <input type="text" class="form-control" placeholder="<?php _e('Censored word', 'luna') ?>" name="new_search_for" maxlength="60" tabindex="1" />
                    <hr />
                    <input type="text" class="form-control" placeholder="<?php _e('Replacement word', 'luna') ?>" name="new_replace_with" maxlength="60" tabindex="2" />
                </div>
			</div>
		</form>
	</div>
	<div class="col-md-8">
		<form class="card" id="censoring" method="post" action="censoring.php">
			<h5 class="card-header">
                <?php _e('Manage words', 'luna') ?>
                <span class="float-right">
                    <button class="btn btn-link" type="submit" name="update"><span class="fas fa-fw fa-check"></span> <?php _e('Save', 'luna')?></button>
                </span>
            </h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php _e('Censored word', 'luna') ?></th>
                            <th><?php _e('Replacement word', 'luna') ?></th>
                            <th><?php _e('Action', 'luna') ?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php
$result = $db->query('SELECT id, search_for, replace_with FROM '.$db->prefix.'censoring ORDER BY id') or error('Unable to fetch censor word list', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result)) {

    while ($cur_word = $db->fetch_assoc($result)) {
 ?>
                        <tr>
                            <td>
                                <input type="text" class="form-control" name="item[<?php echo $cur_word['id'] ?>][search_for]" value="<?php echo luna_htmlspecialchars($cur_word['search_for']) ?>" maxlength="60" />
                            </td>
                            <td>
                                <input type="text" class="form-control" name="item[<?php echo $cur_word['id'] ?>][replace_with]" value="<?php echo luna_htmlspecialchars($cur_word['replace_with']) ?>" maxlength="60" />
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-danger" type="submit" name="remove[<?php echo $cur_word['id'] ?>]"><span class="fas fa-fw fa-trash-alt"></span> <?php _e('Remove', 'luna') ?></button>
                                </div>
                            </td>
                        </tr>
<?php
    }
} else {
    echo '<tr><td colspan="3">'.__('No censor words in list.', 'luna').'</td></tr>';
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
