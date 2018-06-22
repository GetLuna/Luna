<?php

/*
 * Copyright (C) 2013-2018 Luna
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
        'default_accent' => intval($_POST['form']['default_accent']),
        'allow_accent_color' => isset($_POST['form']['allow_accent_color']) ? '1' : '0',
        'allow_night_mode' => isset($_POST['form']['allow_night_mode']) ? '1' : '0',
        'show_user_info' => isset($_POST['form']['show_user_info']) ? '1' : '0',
        'show_comment_count' => isset($_POST['form']['show_comment_count']) ? '1' : '0',
        'moderated_by' => isset($_POST['form']['moderated_by']) ? '1' : '0',
        'disp_threads' => intval($_POST['form']['disp_threads']),
        'disp_comments' => intval($_POST['form']['disp_comments']),
        'board_statistics' => isset($_POST['form']['board_statistics']) ? '1' : '0',
        'back_to_top' => isset($_POST['form']['back_to_top']) ? '1' : '0',
        'notification_flyout' => isset($_POST['form']['notification_flyout']) ? '1' : '0',
        'header_search' => isset($_POST['form']['header_search']) ? '1' : '0',
        'show_copyright' => isset($_POST['form']['show_copyright']) ? '1' : '0',
        'copyright_type' => intval($_POST['form']['copyright_type']),
        'custom_copyright' => luna_trim($_POST['form']['custom_copyright']),
        'use_custom_css' => isset($_POST['form']['use_custom_css']) ? '1' : '0',
        'custom_css' => luna_trim($_POST['form']['custom_css']),
        'fontawesomepro' => isset($_POST['form']['fontawesomepro']) ? '1' : '0',
    );

    // Make sure the number of displayed threads and comments is between 3 and 75
    if ($form['disp_threads'] < 3) {
        $form['disp_threads'] = 3;
    } elseif ($form['disp_threads'] > 75) {
        $form['disp_threads'] = 75;
    }

    if ($form['disp_comments'] < 3) {
        $form['disp_comments'] = 3;
    } elseif ($form['disp_comments'] > 75) {
        $form['disp_comments'] = 75;
    }

    foreach ($form as $key => $input) {
        // Only update values that have changed
        if (array_key_exists('o_'.$key, $luna_config) && $luna_config['o_'.$key] != $input) {
            if ($input != '' || is_int($input)) {
                $value = '\''.$db->escape($input).'\'';
            } else {
                $value = 'NULL';
            }

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
                    message_backstage(__('The selected file was too large to upload. The server didn\'t allow the upload.', 'luna'));
                    break;

                case 3: // UPLOAD_ERR_PARTIAL, skip 4, we already did that
                    message_backstage(__('The selected file was only partially uploaded. Please try again.', 'luna'));
                    break;

                case 6: // UPLOAD_ERR_NO_TMP_DIR
                    message_backstage(__('PHP was unable to save the uploaded file to a temporary location.', 'luna'));
                    break;

                default:
                    // No error occured, but was something actually uploaded?
                    if ($uploaded_file['size'] == 0) {
                        message_backstage(__('You did not select a file for upload.', 'luna'));
                    }

                    break;
            }
        }

        if (is_uploaded_file($uploaded_file['tmp_name'])) {
            // Preliminary file check, adequate in most cases
            $allowed_types = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
            if (!in_array($uploaded_file['type'], $allowed_types)) {
                message_backstage(__('The file you tried to upload is not of an allowed type. Allowed types are jpeg and png.', 'luna'));
            }

            // Move the file to the avatar directory. We do this before checking the width/height to circumvent open_basedir restrictions
            if (!@move_uploaded_file($uploaded_file['tmp_name'], LUNA_ROOT.'/img/header.tmp')) {
                message_backstage(__('The server was unable to save the uploaded file. Please contact the forum administrator at', 'luna').' <a href="mailto:'.luna_htmlspecialchars($luna_config['o_admin_email']).'">'.luna_htmlspecialchars($luna_config['o_admin_email']).'</a>.');
            }

            list($width, $height, $type) = @getimagesize(LUNA_ROOT.'/img/header.tmp');

            // Determine type
            if ($type == IMAGETYPE_JPEG) {
                $extension = '.jpg';
            } elseif ($type == IMAGETYPE_PNG) {
                $extension = '.png';
            } else {
                // Invalid type
                @unlink(LUNA_ROOT.'/img/header.tmp');
                message_backstage(__('The file you tried to upload is not of an allowed type. Allowed types are jpeg and png.', 'luna'));
            }

            // Clean up existing headers
            @unlink(LUNA_ROOT.'/img/header.png');
            @unlink(LUNA_ROOT.'/img/header.jpg');

            // Do the final rename
            @rename(LUNA_ROOT.'/img/header.tmp', LUNA_ROOT.'/img/header'.$extension);
            @chmod(LUNA_ROOT.'/img/header'.$extension, 0644);
        } else {
            message_backstage(__('An unknown error occurred. Please try again.', 'luna'));
        }
    }

    // Regenerate the config cache
    if (!defined('LUNA_CACHE_FUNCTIONS_LOADED')) {
        require LUNA_ROOT.'include/cache.php';
    }

    generate_config_cache();
    clear_feed_cache();

    redirect('backstage/appearance.php?saved=true');
}

$theme = forum_get_theme();

require 'header.php';
?>
<div class="row">
	<div class="col-12">
<?php
if (isset($_GET['saved'])) {
    echo '<div class="alert alert-success"><i class="fas fa-fw fa-check"></i> '.__('Your settings have been saved.', 'luna').'</div>';
}
?>
        <form  method="post" enctype="multipart/form-data" action="appearance.php">
            <input type="hidden" name="form_sent" value="1" />
            <div class="card">
                <h5 class="card-header">
                    <?php _e('General', 'luna')?>
                    <span class="float-right">
                        <button class="btn btn-link" type="submit" name="save"><span class="fas fa-fw fa-check"></span> <?php _e('Save', 'luna')?></button>
                    </span>
                </h5>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('Accents', 'luna')?><?php if (count($theme->features->accents) <= 1) { ?><span class="help-block text-danger"><?php _e('Your theme does not support accent colors', 'luna')?></span><?php } ?></label>
                        <div class="col-md-9">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="form[allow_accent_color]" name="form[allow_accent_color]" value="1"<?php echo ( $luna_config['o_allow_accent_color'] == '1' ) ? ' checked' : '' ?>>
                                <label class="custom-control-label" for="form[allow_accent_color]">
                                    <?php _e('Allow users to set their own accent color.', 'luna')?>
                                </label>
                            </div>
                            <div class="btn-group accent-group" data-toggle="buttons">
<?php
foreach ($theme->features->accents as $accent) {
    echo '<label class="btn btn-primary color-accent'.(($luna_config['o_default_accent'] == $accent->id) ? ' active' : '').'" style="background: '.$accent->color.'"> <input type="radio" name="form[default_accent]" id="'.$accent->id.'" value="'.$accent->id.'"></label>';
}
?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('Night mode', 'luna')?><?php if (!$theme->features->night_mode) { ?><span class="help-block text-danger"><?php _e('Your theme does not support night mode', 'luna')?></span><?php } ?></label>
                        <div class="col-md-9">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="form[allow_night_mode]" name="form[allow_night_mode]" value="1"<?php echo ( $luna_config['o_allow_night_mode'] == '1' ) ? ' checked' : '' ?>>
                                <label class="custom-control-label" for="form[allow_night_mode]">
                                    <?php _e('Allow users to change the night mode settings.', 'luna')?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('Custom CSS', 'luna')?><?php if (!$theme->features->custom_css) { ?><span class="help-block text-danger"><?php _e('Your theme does not support custom CSS', 'luna')?></span><?php } ?></label>
                        <div class="col-md-9">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="form[use_custom_css]" name="form[use_custom_css]" value="1"<?php echo ( $luna_config['o_use_custom_css'] == '1' ) ? ' checked' : '' ?>>
                                <label class="custom-control-label" for="form[use_custom_css]">
                                    <?php _e('Use the custom CSS as defined below.', 'luna')?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('CSS code', 'luna')?></label>
                        <div class="col-md-9">
                            <textarea class="form-control form-control-mono" name="form[custom_css]" placeholder="/* <?php _e('Custom CSS')?> */" rows="10"><?php echo luna_htmlspecialchars($luna_config['o_custom_css']) ?></textarea>
                        </div>
                    </div>
                    <hr />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">
                            <?php _e('Header background', 'luna')?><span class="help-block"><?php _e('You can upload a custom header here to show in the Mainstage and Backstage', 'luna')?></span><?php if (!$theme->features->custom_css) { ?><span class="help-block text-danger"><?php _e('Your theme does not support header background', 'luna')?></span><?php } ?>
                            <?php if (file_exists(LUNA_ROOT.'/img/header.png') || file_exists(LUNA_ROOT.'/img/header.jpg')) {?>
                                <a class="btn btn-danger" href="?remove-header"><span class="fas fa-fw fa-trash"></span> <?php _e('Delete header', 'luna')?></a>
                            <?php }?>
                        </label>
                        <div class="col-md-9">
                            <?php if (file_exists(LUNA_ROOT.'/img/header.png') || file_exists(LUNA_ROOT.'/img/header.jpg')) {?>
                                <div class="restrict-size"></div>
                            <?php }?>
                            <input type="hidden" name="MAX_FILE_SIZE" value="5120000" />
                            <input name="req_file" type="file" />
                        </div>
                    </div>
                    <hr />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('Font Awesome Pro', 'luna')?><span class="help-block"><?php printf('<a href="https://getluna.org/docs/fontawesome">'.__('Install Font Awesome Pro', 'luna').'</a>')?></span></label>
                        <div class="col-md-9">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="form[fontawesomepro]" name="form[fontawesomepro]" value="1"<?php echo ( $luna_config['o_fontawesomepro'] == '1' ) ? ' checked' : '' ?>>
                                <label class="custom-control-label" for="form[fontawesomepro]">
                                    <?php _e('Enable Font Awesome Pro features within Luna.', 'luna')?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <h5 class="card-header">
                    <?php _e('Display', 'luna')?>
                    <span class="float-right">
                        <button class="btn btn-link" type="submit" name="save"><span class="fas fa-fw fa-check"></span> <?php _e('Save', 'luna')?></button>
                    </span>
                </h5>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('User profile', 'luna')?></label>
                        <div class="col-md-9">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="form[show_user_info]" name="form[show_user_info]" value="1"<?php echo ( $luna_config['o_show_user_info'] == '1' ) ? ' checked' : '' ?>>
                                <label class="custom-control-label" for="form[show_user_info]">
                                    <?php _e('Show information about the commenter under the username in threads.', 'luna')?>
                                </label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="form[show_comment_count]" name="form[show_comment_count]" value="1"<?php echo ( $luna_config['o_show_comment_count'] == '1' ) ? ' checked' : '' ?>>
                                <label class="custom-control-label" for="form[show_comment_count]">
                                    <?php _e('Show the number of comments a user has made in threads, profile and the user list.', 'luna')?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('Index settings', 'luna')?><?php if (!$theme->features->moderated_by_list) { ?><span class="help-block text-danger"><?php _e('Your theme does not support moderated by list', 'luna')?></span><?php } ?></label>
                        <div class="col-md-9">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="form[moderated_by]" name="form[moderated_by]" value="1"<?php echo ( $luna_config['o_moderated_by'] == '1' ) ? ' checked' : '' ?>>
                                <label class="custom-control-label" for="form[moderated_by]">
                                    <?php _e('Show the "Moderated by" list when moderators are set on a per-forum base (requires theme support).', 'luna')?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('Threads per page', 'luna')?><span class="help-block"><?php _e('Default amount of threads per page', 'luna')?></span></label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" name="form[disp_threads]" maxlength="2" value="<?php echo $luna_config['o_disp_threads'] ?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('Comments per page', 'luna')?><span class="help-block"><?php _e('Default amount of comments per page', 'luna')?></span></label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" name="form[disp_comments]" maxlength="3" value="<?php echo $luna_config['o_disp_comments'] ?>" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <h5 class="card-header">
                    <?php _e('Header', 'luna')?>
                    <span class="float-right">
                        <button class="btn btn-link" type="submit" name="save"><span class="fas fa-fw fa-check"></span> <?php _e('Save', 'luna')?></button>
                    </span>
                </h5>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('Notifications', 'luna')?><?php if (!$theme->features->notification_flyout) { ?><span class="help-block text-danger"><?php _e('Your theme does not support the notification flyout', 'luna')?></span><?php } ?></label>
                        <div class="col-md-9">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="form[notification_flyout]" name="form[notification_flyout]" value="1"<?php echo ( $luna_config['o_notification_flyout'] == '1' ) ? ' checked' : '' ?>>
                                <label class="custom-control-label" for="form[notification_flyout]">
                                    <?php _e('Show a fly-out when clicking the notification icon instead of going to the notification page. Disabling this feature might improve performance.', 'luna')?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('Search', 'luna')?><?php if (!$theme->features->header_search) { ?><span class="help-block text-danger"><?php _e('Your theme does not support header search', 'luna')?></span><?php } ?></label>
                        <div class="col-md-9">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="form[header_search]" name="form[header_search]" value="1"<?php echo ( $luna_config['o_header_search'] == '1' ) ? ' checked' : '' ?>>
                                <label class="custom-control-label" for="form[header_search]">
                                    <?php _e('Show the search bar in the heading.', 'luna')?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <h5 class="card-header">
                    <?php _e('Footer', 'luna')?>
                    <span class="float-right">
                        <button class="btn btn-link" type="submit" name="save"><span class="fas fa-fw fa-check"></span> <?php _e('Save', 'luna')?></button>
                    </span>
                </h5>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('Statistics', 'luna')?><?php if (!$theme->features->statistics) { ?><span class="help-block text-danger"><?php _e('Your theme does not support statistics', 'luna')?></span><?php } ?></label>
                        <div class="col-md-9">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="form[board_statistics]" name="form[board_statistics]" value="1"<?php echo ( $luna_config['o_board_statistics'] == '1' ) ? ' checked' : '' ?>>
                                <label class="custom-control-label" for="form[board_statistics]">
                                    <?php _e('Show the board statistics.', 'luna')?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('Back to top', 'luna')?><?php if (!$theme->features->back_to_top) { ?><span class="help-block text-danger"><?php _e('Your theme does not support back to top', 'luna')?></span><?php } ?></label>
                        <div class="col-md-9">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="form[back_to_top]" name="form[back_to_top]" value="1"<?php echo ( $luna_config['o_back_to_top'] == '1' ) ? ' checked' : '' ?>>
                                <label class="custom-control-label" for="form[back_to_top]">
                                    <?php _e('Show a "Back to top" link in the footer.', 'luna')?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('Copyright', 'luna')?><?php if (!$theme->features->copyright) { ?><span class="help-block text-danger"><?php _e('Your theme does not support copyright visibility', 'luna')?></span><?php } ?></label>
                        <div class="col-md-9">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="form[show_copyright]" name="form[show_copyright]" value="1"<?php echo ( $luna_config['o_show_copyright'] == '1' ) ? ' checked' : '' ?>>
                                <label class="custom-control-label" for="form[show_copyright]">
                                    <?php _e('Show the copyright notice in the footer.', 'luna')?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php _e('Copyright content', 'luna')?><?php if (!$theme->features->custom_copyright) { ?><span class="help-block text-danger"><?php _e('Your theme does not support custom copyright', 'luna')?></span><?php } ?></label>
                        <div class="col-md-9">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="form[copyright_type1]" name="form[copyright_type]" class="custom-control-input" value="0" <?php if ($luna_config['o_copyright_type'] == '0') { echo ' checked'; } ?>>
                                <label class="custom-control-label" for="form[copyright_type1]">
                                    <?php _e('Show default copyright', 'luna')?>
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="form[copyright_type2]" name="form[copyright_type]" class="custom-control-input" value="1" <?php if ($luna_config['o_copyright_type'] == '1') { echo ' checked'; } ?>>
                                <label class="custom-control-label" for="form[copyright_type2]">
                                    <?php _e('Show personalized copyright notices:', 'luna')?>
                                </label>
                            </div>
                            <input type="text" class="form-control" name="form[custom_copyright]" placeholder="<?php _e('Your copyright', 'luna')?>" value="<?php echo luna_htmlspecialchars($luna_config['o_custom_copyright']) ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php

require 'footer.php';
