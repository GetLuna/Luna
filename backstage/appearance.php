<?php

/*
 * Copyright (C) 2013-2016 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', '../');
require LUNA_ROOT.'include/common.php';
define('LUNA_SECTION', 'settings');
define('LUNA_PAGE', 'appearance');

if (!$luna_user['is_admmod']) {
	header("Location: login.php");
    exit;
}

if (isset($_GET['remove-header'])) {
	confirm_referrer('backstage/appearance.php', __('Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database (look for o_base_url in the config table) and then clear the cache by deleting all .php files in the /cache directory.', 'luna'));

    @unlink(LUNA_ROOT.'/img/header.png');
    @unlink(LUNA_ROOT.'/img/header.jpg');

	redirect('backstage/appearance.php?saved=true');
}

if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/appearance.php', __('Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database (look for o_base_url in the config table) and then clear the cache by deleting all .php files in the /cache directory.', 'luna'));

	$form = array(
		'default_style'         => luna_trim($_POST['form']['default_style']),
		'default_accent'		=> intval($_POST['form']['default_accent']),
		'allow_accent_color'	=> isset($_POST['form']['allow_accent_color']) ? '1' : '0',
		'allow_night_mode'		=> isset($_POST['form']['allow_night_mode']) ? '1' : '0',
		'show_user_info'		=> isset($_POST['form']['show_user_info']) ? '1' : '0',
		'show_comment_count'	=> isset($_POST['form']['show_comment_count']) ? '1' : '0',
		'moderated_by'			=> isset($_POST['form']['moderated_by']) ? '1' : '0',
		'disp_threads'			=> intval($_POST['form']['disp_threads']),
		'disp_comments'			=> intval($_POST['form']['disp_comments']),
		'board_statistics'		=> isset($_POST['form']['board_statistics']) ? '1' : '0',
		'back_to_top'			=> isset($_POST['form']['back_to_top']) ? '1' : '0',
		'notification_flyout'	=> isset($_POST['form']['notification_flyout']) ? '1' : '0',
		'header_search'			=> isset($_POST['form']['header_search']) ? '1' : '0',
		'show_copyright'		=> isset($_POST['form']['show_copyright']) ? '1' : '0',
		'copyright_type'		=> intval($_POST['form']['copyright_type']),
		'custom_copyright'		=> luna_trim($_POST['form']['custom_copyright']),
		'use_custom_css'		=> isset($_POST['form']['use_custom_css']) ? '1' : '0',
		'custom_css'			=> luna_trim($_POST['form']['custom_css']),
	);

	// Make sure the number of displayed threads and comments is between 3 and 75
	if ($form['disp_threads'] < 3)
		$form['disp_threads'] = 3;
	elseif ($form['disp_threads'] > 75)
		$form['disp_threads'] = 75;

	if ($form['disp_comments'] < 3)
		$form['disp_comments'] = 3;
	elseif ($form['disp_comments'] > 75)
		$form['disp_comments'] = 75;

	foreach ($form as $key => $input) {
		// Only update values that have changed
		if (array_key_exists('o_'.$key, $luna_config) && $luna_config['o_'.$key] != $input) {
			if ($input != '' || is_int($input))
				$value = '\''.$db->escape($input).'\'';
			else
				$value = 'NULL';

			$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$value.' WHERE conf_name=\'o_'.$db->escape($key).'\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());
		}
	}
    
    if (isset($_FILES['req_file']['error']) && $_FILES['req_file']['error'] != 4) {
        $uploaded_file = $_FILES['req_file'];

        // Make sure the upload went smooth
        if (isset($uploaded_file['error'])) {
            switch ($uploaded_file['error']) {
                case 1: // UPLOAD_ERR_INI_SIZE
                case 2: // UPLOAD_ERR_FORM_SIZE
                    message(__('The selected file was too large to upload. The server didn\'t allow the upload.', 'luna'));
                    break;

                case 3: // UPLOAD_ERR_PARTIAL, skip 4, we already did that
                    message(__('The selected file was only partially uploaded. Please try again.', 'luna'));
                    break;

                case 6: // UPLOAD_ERR_NO_TMP_DIR
                    message(__('PHP was unable to save the uploaded file to a temporary location.', 'luna'));
                    break;

                default:
                    // No error occured, but was something actually uploaded?
                    if ($uploaded_file['size'] == 0)
                        message(__('You did not select a file for upload.', 'luna'));
                    break;
            }
        }

        if (is_uploaded_file($uploaded_file['tmp_name'])) {
            // Preliminary file check, adequate in most cases
            $allowed_types = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
            if (!in_array($uploaded_file['type'], $allowed_types))
                message(__('The file you tried to upload is not of an allowed type. Allowed types are jpeg and png.', 'luna'));

            // Move the file to the avatar directory. We do this before checking the width/height to circumvent open_basedir restrictions
            if (!@move_uploaded_file($uploaded_file['tmp_name'], LUNA_ROOT.'/img/header.tmp'))
                message(__('The server was unable to save the uploaded file. Please contact the forum administrator at', 'luna').' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.');

            list($width, $height, $type,) = @getimagesize(LUNA_ROOT.'/img/header.tmp');

            // Determine type
            if ($type == IMAGETYPE_JPEG)
                $extension = '.jpg';
            elseif ($type == IMAGETYPE_PNG)
                $extension = '.png';
            else {
                // Invalid type
                @unlink(LUNA_ROOT.'/img/header.tmp');
                message(__('The file you tried to upload is not of an allowed type. Allowed types are jpeg and png.', 'luna'));
            }
    
            // Clean up existing headers
            @unlink(LUNA_ROOT.'/img/header.png');
            @unlink(LUNA_ROOT.'/img/header.jpg');
            
            // Do the final rename
            @rename(LUNA_ROOT.'/img/header.tmp', LUNA_ROOT.'/img/header'.$extension);
            @chmod(LUNA_ROOT.'/img/header'.$extension, 0644);
        } else
            message(__('An unknown error occurred. Please try again.', 'luna'));
    }

	// Regenerate the config cache
	if (!defined('LUNA_CACHE_FUNCTIONS_LOADED'))
		require LUNA_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/appearance.php?saved=true');
}

require 'header.php';
?>
<div class="row">
	<div class="col-sm-12">
<?php
if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><i class="fa fa-fw fa-check"></i> '.__('Your settings have been saved.', 'luna').'</div>';
?>
        <form class="form-horizontal" method="post" enctype="multipart/form-data" action="appearance.php">
            <input type="hidden" name="form_sent" value="1" />
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php _e('Theme', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
                </div>
                <div class="panel-body">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('Theme', 'luna') ?></label>
                            <div class="col-sm-9">
                                <select class="form-control" name="form[default_style]">
<?php
		$styles = forum_list_styles();

		foreach ($styles as $temp) {
			if ($luna_config['o_default_style'] == $temp)
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$temp.'" selected="selected">'.str_replace('_', ' ', $temp).'</option>'."\n";
			else
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$temp.'">'.str_replace('_', ' ', $temp).'</option>'."\n";
		}

?>
								</select>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('Accents', 'luna') ?></label>
                            <div class="col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="form[allow_accent_color]" value="1" <?php if ($luna_config['o_allow_accent_color'] == '1') echo ' checked' ?> />
                                        <?php _e('Allow users to set their own accent color.', 'luna') ?>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="form[allow_night_mode]" value="1" <?php if ($luna_config['o_allow_night_mode'] == '1') echo ' checked' ?> />
                                        <?php _e('Allow users to change the night mode settings.', 'luna') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('Default', 'luna') ?></label>
                            <div class="col-sm-9">
                                <div class="btn-group accent-group" data-toggle="buttons">
<?php
		$accents = forum_list_accents('main');

		foreach ($accents as $temp) {
			if ($luna_config['o_default_accent'] == $temp)
				echo '<label class="btn btn-primary color-accent accent-'.$temp.' active"><input type="radio" name="form[default_accent]" id="'.$temp.'" value="'.$temp.'" checked></label>';
			else
				echo '<label class="btn btn-primary color-accent accent-'.$temp.'"> <input type="radio" name="form[default_accent]" id="'.$temp.'" value="'.$temp.'"></label>';
		}
?>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('Custom CSS', 'luna') ?></label>
                            <div class="col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="form[use_custom_css]" value="1" <?php if ($luna_config['o_use_custom_css'] == '1') echo ' checked' ?> />
                                        <?php _e('Use the custom CSS as defined below.', 'luna') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('CSS code', 'luna') ?></label>
                            <div class="col-sm-9">
                                <textarea class="form-control form-control-mono" name="form[custom_css]" placeholder="/* <?php _e('Custom CSS') ?> */" rows="10"><?php echo luna_htmlspecialchars($luna_config['o_custom_css']) ?></textarea>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group">
                            <label class="col-sm-3 control-label">
                                <?php _e('Header background', 'luna') ?><span class="help-block"><?php _e('You can upload a custom header here to show in the Mainstage and Backstage', 'luna') ?></span>
                                <?php if (file_exists(LUNA_ROOT.'/img/header.png') || file_exists(LUNA_ROOT.'/img/header.jpg')) { ?>
                                    <a class="btn btn-danger" href="?remove-header"><span class="fa fa-fw fa-trash"></span> <?php _e('Delete header', 'luna') ?></a>
                                <?php } ?>
                            </label>
                            <div class="col-sm-9">
                                <?php if (file_exists(LUNA_ROOT.'/img/header.png') || file_exists(LUNA_ROOT.'/img/header.jpg')) { ?>
                                    <div class="restrict-size"></div>
                                <?php } ?>
                                <input type="hidden" name="MAX_FILE_SIZE" value="5120000" />
                                <input name="req_file" type="file" />
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php _e('Display', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
                </div>
                <div class="panel-body">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('User profile', 'luna') ?></label>
                            <div class="col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="form[show_user_info]" value="1" <?php if ($luna_config['o_show_user_info'] == '1') echo ' checked' ?> />
                                        <?php _e('Show information about the commenter under the username in threads.', 'luna') ?>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="form[show_comment_count]" value="1" <?php if ($luna_config['o_show_comment_count'] == '1') echo ' checked' ?> />
                                        <?php _e('Show the number of comments a user has made in threads, profile and the user list.', 'luna') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('Index settings', 'luna') ?></label>
                            <div class="col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="form[moderated_by]" value="1" <?php if ($luna_config['o_moderated_by'] == '1') echo ' checked' ?> />
                                        <?php _e('Show the "Moderated by" list when moderators are set on a per-forum base (requires theme support).', 'luna') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('Threads per page', 'luna') ?><span class="help-block"><?php _e('Default amount of threads per page', 'luna') ?></span></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="form[disp_threads]" maxlength="2" value="<?php echo $luna_config['o_disp_threads'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('Comments per page', 'luna') ?><span class="help-block"><?php _e('Default amount of comments per page', 'luna') ?></span></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="form[disp_comments]" maxlength="3" value="<?php echo $luna_config['o_disp_comments'] ?>" />
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php _e('Header', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
                </div>
                <div class="panel-body">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('Notifications', 'luna') ?></label>
                            <div class="col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="form[notification_flyout]" value="1" <?php if ($luna_config['o_notification_flyout'] == '1') echo ' checked' ?> />
                                        <?php _e('Show a fly-out when clicking the notification icon instead of going to the notification page. Disabling this feature might improve performance.', 'luna') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('Search', 'luna') ?></label>
                            <div class="col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="form[header_search]" value="1" <?php if ($luna_config['o_header_search'] == '1') echo ' checked' ?> />
                                        <?php _e('Show the search bar in the heading.', 'luna') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php _e('Footer', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
                </div>
                <div class="panel-body">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('Statistics', 'luna') ?></label>
                            <div class="col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="form[board_statistics]" value="1" <?php if ($luna_config['o_board_statistics'] == '1') echo ' checked' ?> />
                                        <?php _e('Show the board statistics.', 'luna') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('Back to top', 'luna') ?></label>
                            <div class="col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="form[back_to_top]" value="1" <?php if ($luna_config['o_back_to_top'] == '1') echo ' checked' ?> />
                                        <?php _e('Show a "Back to top" link in the footer.', 'luna') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('Copyright', 'luna') ?></label>
                            <div class="col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="form[show_copyright]" value="1" <?php if ($luna_config['o_show_copyright'] == '1') echo ' checked' ?> />
                                        <?php _e('Show the copyright notice in the footer.', 'luna') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php _e('Copyright content', 'luna') ?></label>
                            <div class="col-sm-9">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="form[copyright_type]" id="o_copyright_type_0" value="0"<?php if ($luna_config['o_copyright_type'] == '0') echo ' checked' ?> />
                                        <?php _e('Show default copyright', 'luna') ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="form[copyright_type]" id="o_copyright_type_1" value="1"<?php if ($luna_config['o_copyright_type'] == '1') echo ' checked' ?> />
                                        <?php _e('Show personalized copyright notices:', 'luna') ?>
                                    </label><br /><br />
                                    <input type="text" class="form-control" name="form[custom_copyright]" placeholder="<?php _e('Your copyright', 'luna') ?>" value="<?php echo luna_htmlspecialchars($luna_config['o_custom_copyright']) ?>" />
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </form>
    </div>
</div>
<?php

require 'footer.php';
