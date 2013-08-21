<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$pun_user['is_admmod']) {
    header("Location: login.php");
}

// Load the backstage.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/backstage.php';

if (isset($_POST['form_sent']))
{
	$form = array_map('intval', $_POST['form']);

	foreach ($form as $key => $input)
	{
		// Make sure the input is never a negative value
		if($input < 0)
			$input = 0;

		// Only update values that have changed
		if (array_key_exists('p_'.$key, $pun_config) && $pun_config['p_'.$key] != $input)
			$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$input.' WHERE conf_name=\'p_'.$db->escape($key).'\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());
	}

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();

	redirect('backstage/permissions.php', $lang_back['Perms updated redirect']);
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['Permissions']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('permissions');

?>
<h2><?php echo $lang_back['Permissions head'] ?></h2>
<form method="post" action="permissions.php">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Posting subhead'] ?></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <table class="table" cellspacing="0">
                    <tr>
                        <th width="20%"><?php echo $lang_back['BBCode label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[message_bbcode]" value="1"<?php if ($pun_config['p_message_bbcode'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[message_bbcode]" value="0"<?php if ($pun_config['p_message_bbcode'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['BBCode help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Image tag label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[message_img_tag]" value="1"<?php if ($pun_config['p_message_img_tag'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[message_img_tag]" value="0"<?php if ($pun_config['p_message_img_tag'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Image tag help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['All caps message label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[message_all_caps]" value="1"<?php if ($pun_config['p_message_all_caps'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[message_all_caps]" value="0"<?php if ($pun_config['p_message_all_caps'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['All caps message help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['All caps subject label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[subject_all_caps]" value="1"<?php if ($pun_config['p_subject_all_caps'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[subject_all_caps]" value="0"<?php if ($pun_config['p_subject_all_caps'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['All caps subject help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Require e-mail label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[force_guest_email]" value="1"<?php if ($pun_config['p_force_guest_email'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[force_guest_email]" value="0"<?php if ($pun_config['p_force_guest_email'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Require e-mail help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Signatures subhead'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <table class="table" cellspacing="0">
                    <tr>
                        <th width="20%"><?php echo $lang_back['BBCode sigs label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[sig_bbcode]" value="1"<?php if ($pun_config['p_sig_bbcode'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[sig_bbcode]" value="0"<?php if ($pun_config['p_sig_bbcode'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['BBCode sigs help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Image tag sigs label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[sig_img_tag]" value="1"<?php if ($pun_config['p_sig_img_tag'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[sig_img_tag]" value="0"<?php if ($pun_config['p_sig_img_tag'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Image tag sigs help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['All caps sigs label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[sig_all_caps]" value="1"<?php if ($pun_config['p_sig_all_caps'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[sig_all_caps]" value="0"<?php if ($pun_config['p_sig_all_caps'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['All caps sigs help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Max sig length label'] ?></th>
                        <td>
                            <input type="text" class="form-control"name="form[sig_length]" size="5" maxlength="5" value="<?php echo $pun_config['p_sig_length'] ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Max sig length help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Max sig lines label'] ?></th>
                        <td>
                            <input type="text" class="form-control"name="form[sig_lines]" size="3" maxlength="3" value="<?php echo $pun_config['p_sig_lines'] ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Max sig lines help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Registration subhead'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <table class="table" cellspacing="0">
                    <tr>
                        <th width="20%"><?php echo $lang_back['Banned e-mail label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[allow_banned_email]" value="1"<?php if ($pun_config['p_allow_banned_email'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[allow_banned_email]" value="0"<?php if ($pun_config['p_allow_banned_email'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Banned e-mail help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Duplicate e-mail label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[allow_dupe_email]" value="1"<?php if ($pun_config['p_allow_dupe_email'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[allow_dupe_email]" value="0"<?php if ($pun_config['p_allow_dupe_email'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Duplicate e-mail help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
    <div class="alert alert-info"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang_back['Save changes'] ?>" /></div>
</form>
<?php

require FORUM_ROOT.'backstage/footer.php';
