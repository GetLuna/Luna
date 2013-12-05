<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by Mpok copyright (C) 2010-2013 Mpok
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

if ($pun_user['g_id'] != FORUM_ADMIN)
	message($lang['No permission'], false, '403 Forbidden');

// Load the language file
require FORUM_ROOT.'lang/'.$admin_language.'/language.php';

// Retrieve configuration
$ftb_conf = array();
$result = $db->query('SELECT conf_name, conf_value FROM '.$db->prefix.'toolbar_conf') or error('Unable to retrieve toolbar configuration', __FILE__, __LINE__, $db->error());
while ($conf = $db->fetch_assoc($result))
	$ftb_conf[$conf['conf_name']] = $conf['conf_value'];

// Retrieve image files
$images = array();
$d = dir(FORUM_ROOT.'img/toolbar/'.$ftb_conf['img_pack']);
while (($entry = $d->read()) !== false)
{
	 if ($entry != '.' && $entry != '..' && $entry != 'index.html')
		$images[] = $entry;
}
$d->close();
@natsort($images);

// General errors
$errors = array();

// Default tag names
$def_tags = array('smilies', 'bold', 'italic', 'underline', 'strike', 'heading', 'color', 'code', 'quote', 'link', 'img', 'email', 'list', 'li');
$toolbar_tags = array('sup', 'sub', 'left', 'right', 'center', 'justify', 'q', 'acronym', 'video');

// Regenerate cache function
function re_generate($mode)
{
	require_once FORUM_ROOT.'include/cache.php';
	if ($mode == 'tags' || $mode == 'all')
		generate_ftb_cache('tags');
	if ($mode == 'forms' || $mode == 'all')
	{
		generate_ftb_cache('form');
		generate_ftb_cache('quickform');
	}
}

// Regenerate cache
if (isset($_POST['regenerate']))
{
	re_generate('all');
	redirect('backstage/toolbar.php', $lang['cache_updated']);
}

// General settings modification
else if (isset($_POST['form_conf']))
{
	$form = array_map('trim', $_POST['form']);

	$done = false;
	while (list($key, $input) = @each($form))
	{
		// Only update values that have changed
		if (array_key_exists($key, $ftb_conf) && $ftb_conf[$key] != $input)
		{
			// Checking input (basically for numeric values)
			if ($key != 'img_pack' && !is_numeric($input))
				message($lang['not_numeric'].$key);

			$db->query('UPDATE '.$db->prefix.'toolbar_conf SET conf_value=\''.$db->escape($input).'\' WHERE conf_name=\''.$db->escape($key).'\'') or error('Unable to update general settings', __FILE__, __LINE__, $db->error());
			$done = true;
		}
	}

	// End message
	if ($done)
	{
		re_generate('forms');
		redirect('backstage/toolbar.php', $lang['success']);
	}
	else
		redirect('backstage/toolbar.php', $lang['no_change']);
}

// Normal Display
else
{
	// Display the admin navigation menu


$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang['Admin'], $lang['Toolbar']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('toolbar');
?>
<h2><?php echo $lang['Toolbar'] ?></h2>
<form class="form-horizontal" action="toolbar.php" method="post">
<div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['glob_conf'] ?><span class="pull-right"><input type="submit" class="btn btn-primary" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_conf" value="1" />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['enable_form'] ?></label>
                    <div class="col-sm-10">
                        <label class="radio-inline">
                            <input type="radio" name="form[enable_form]" value="1"<?php if ($ftb_conf['enable_form'] == '1') echo ' checked="checked"' ?> />
                            <?php echo $lang['yes'] ?>
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="form[enable_form]" value="0"<?php if ($ftb_conf['enable_form'] == '0') echo ' checked="checked"' ?> />
							<?php echo $lang['no'] ?>
                        </label>
                        <span class="help-block"><?php echo $lang['enable_form_infos'] ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['enable_quickform'] ?></label>
                    <div class="col-sm-10">
                        <label class="radio-inline">
                            <input type="radio" name="form[enable_quickform]" value="1"<?php if ($ftb_conf['enable_quickform'] == '1') echo ' checked="checked"' ?> />
                            <?php echo $lang['yes'] ?>
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="form[enable_quickform]" value="0"<?php if ($ftb_conf['enable_quickform'] == '0') echo ' checked="checked"' ?> />
							<?php echo $lang['no'] ?>
                        </label>
                        <span class="help-block"><?php echo $lang['enable_quickform_infos'] ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['images_pack'] ?></label>
                    <div class="col-sm-10">
                        <select class="form-control" name="form[img_pack]">
<?php
	$packs = array();
	$d = dir(FORUM_ROOT.'img/toolbar');
	while (($entry = $d->read()) !== false)
	{
		 if ($entry != '.' && $entry != '..' && is_dir(FORUM_ROOT.'img/toolbar/'.$entry))
			$packs[] = $entry;
	}
	$d->close();
	@natsort($packs);

	while (list(, $temp) = @each($packs))
	{
		if ($ftb_conf['img_pack'] == $temp)
			echo "\t\t\t\t\t\t\t\t\t".'<option value="'.$temp.'" selected="selected">'.$temp.'</option>'."\n";
		else
			echo "\t\t\t\t\t\t\t\t\t".'<option value="'.$temp.'">'.$temp.'</option>'."\n";
	}
?>
						</select>
                        <span class="help-block"><?php echo $lang['images_pack_infos'] ?></span>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
<form action="toolbar.php" method="post">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['button_conf'] ?><span class="pull-right"><input type="submit" class="btn btn-primary" name="edit_pos" value="<?php echo $lang['Update positions'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_button" value="1" />
            <fieldset>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 6em"><?php echo $lang['position'] ?></th>
                            <th scope="col" style="width: 6em"><?php echo $lang['button'] ?></th>
                            <th scope="col"><?php echo $lang['classic_form'] ?></th>
                            <th scope="col"><?php echo $lang['quickreply_form'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php
	// Retrieve buttons
	$result = $db->query('SELECT position, name, enable_form, enable_quick, image FROM '.$db->prefix.'toolbar_tags ORDER by position') or error('Unable to retrieve toolbar buttons', __FILE__, __LINE__, $db->error());

	// Output each button
	while ($button = $db->fetch_assoc($result))
	{
		echo "\t\t\t\t\t\t\t\t".'<tr>'."\n";
		echo "\t\t\t\t\t\t\t\t\t".'<td>';
		if ($button['position'] != 0)
			echo '<input type="text" class="form-control" name="pos['.pun_htmlspecialchars($button['name']).']" value="'.$button['position'].'" size="3" maxlength="3" /></td>'."\n";
		else
			echo '&nbsp;</td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t".'<td><img src="../img/toolbar/'.$ftb_conf['img_pack'].'/'.pun_htmlspecialchars($button['image']).'" title="'.pun_htmlspecialchars($lang['bt_'.$button['name']]).'" alt="" style="vertical-align: -8px" /></td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t".'<td><input type="radio" name="c_form['.pun_htmlspecialchars($button['name']).']" value="1"';
		if ($button['enable_form'] == 1)
			echo ' checked="checked"';
		echo ' />&nbsp;'.$lang['yes'].'&nbsp;&nbsp;&nbsp;<input type="radio" name="c_form['.pun_htmlspecialchars($button['name']).']" value="0"';
		if ($button['enable_form'] == 0)
			echo ' checked="checked"';
		echo ' />&nbsp;'.$lang['no'].'</td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t".'<td><input type="radio" name="q_form['.pun_htmlspecialchars($button['name']).']" value="1"';
		if ($button['enable_quick'] == 1)
			echo ' checked="checked"';
		echo ' />&nbsp;'.$lang['yes'].'&nbsp;&nbsp;&nbsp;<input type="radio" name="q_form['.pun_htmlspecialchars($button['name']).']" value="0"';
		if ($button['enable_quick'] == 0)
			echo ' checked="checked"';
		echo ' />&nbsp;'.$lang['no'].'</td>'."\n";
		echo "\t\t\t\t\t\t\t\t".'</tr>'."\n";
	}
?>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
</form>
<?php
}

require FORUM_ROOT.'backstage/footer.php';