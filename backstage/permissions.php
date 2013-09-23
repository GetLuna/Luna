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
    header("Location: ../login.php");
}

// Load the backstage.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/backstage.php';

if (isset($_POST['form_sent']))
{
	$form = array_map('intval', $_POST['form']);
	
	$form = array(
		'message_bbcode'		=> isset($_POST['form']['message_bbcode']) ? '1' : '0',
		'message_img_tag'		=> isset($_POST['form']['message_img_tag']) ? '1' : '0',
		'message_all_caps'		=> isset($_POST['form']['message_all_caps']) ? '1' : '0',
		'subject_all_caps'		=> isset($_POST['form']['subject_all_caps']) ? '1' : '0',
		'force_guest_email'		=> isset($_POST['form']['force_guest_email']) ? '1' : '0',
		'sig_bbcode'			=> isset($_POST['form']['sig_bbcode']) ? '1' : '0',
		'sig_img_tag'			=> isset($_POST['form']['sig_img_tag']) ? '1' : '0',
		'sig_all_caps'			=> isset($_POST['form']['sig_all_caps']) ? '1' : '0',
		'allow_banned_email'	=> isset($_POST['form']['allow_banned_email']) ? '1' : '0',
		'allow_dupe_email'		=> isset($_POST['form']['allow_dupe_email']) ? '1' : '0',
	);

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
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Posting subhead'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang_back['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
            	<h4>BBCode</h4>
                <input type="checkbox" name="form[message_bbcode]" value="1" <?php if ($pun_config['p_message_bbcode'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['BBCode help'] ?><br />
                <input type="checkbox" name="form[message_img_tag]" value="1" <?php if ($pun_config['p_message_img_tag'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Image tag help'] ?><br />
                <h4>All caps</h4>
                <input type="checkbox" name="form[message_all_caps]" value="1" <?php if ($pun_config['p_message_all_caps'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['All caps message help'] ?><br />
                <input type="checkbox" name="form[subject_all_caps]" value="1" <?php if ($pun_config['p_subject_all_caps'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['All caps subject help'] ?><br />
                <h4>Guests</h4>
                <input type="checkbox" name="form[force_guest_email]" value="1" <?php if ($pun_config['p_force_guest_email'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Require e-mail help'] ?>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Signatures subhead'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang_back['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <input type="checkbox" name="form[sig_bbcode]" value="1" <?php if ($pun_config['p_sig_bbcode'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['BBCode sigs help'] ?><br />
                <input type="checkbox" name="form[sig_img_tag]" value="1" <?php if ($pun_config['p_sig_img_tag'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Image tag sigs help'] ?><br />
                <input type="checkbox" name="form[sig_all_caps]" value="1" <?php if ($pun_config['p_sig_all_caps'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['All caps sigs help'] ?><br />
                <br /><b><?php echo $lang_back['Max sig length label'] ?></b><br />
                <input type="text" class="form-control"name="form[sig_length]" size="5" maxlength="5" value="<?php echo $pun_config['p_sig_length'] ?>" />
                <br /><span class="help-block"><?php echo $lang_back['Max sig length help'] ?></span><br />
                <br /><b><?php echo $lang_back['Max sig lines label'] ?></b><br />
                <input type="text" class="form-control"name="form[sig_lines]" size="3" maxlength="3" value="<?php echo $pun_config['p_sig_lines'] ?>" />
                <br /><span class="help-block"><?php echo $lang_back['Max sig lines help'] ?></span>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Registration subhead'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang_back['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <input type="checkbox" name="form[allow_banned_email]" value="1" <?php if ($pun_config['p_allow_banned_email'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Banned e-mail help'] ?><br /><br />
                <input type="checkbox" name="form[allow_dupe_email]" value="1" <?php if ($pun_config['p_allow_dupe_email'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Duplicate e-mail help'] ?>
            </fieldset>
        </div>
    </div>
</form>
<?php

require FORUM_ROOT.'backstage/footer.php';
