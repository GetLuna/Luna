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
require FORUM_ROOT.'lang/'.$admin_language.'/common.php';

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
	require_once FORUM_ROOT.'include/cache_toolbar.php';
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
	redirect('backstage/toolbar.php', $lang_back['cache_updated']);
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
				message($lang_back['not_numeric'].$key);

			$db->query('UPDATE '.$db->prefix.'toolbar_conf SET conf_value=\''.$db->escape($input).'\' WHERE conf_name=\''.$db->escape($key).'\'') or error('Unable to update general settings', __FILE__, __LINE__, $db->error());
			$done = true;
		}
	}

	// End message
	if ($done)
	{
		re_generate('forms');
		redirect('backstage/toolbar.php', $lang_back['success']);
	}
	else
		redirect('backstage/toolbar.php', $lang_back['no_change']);
}

// Buttons positions modifications and form settings, or tags to edit or delete
else if (isset($_POST['form_button']))
{
	// Update display positions (all buttons) AND enables
	if (isset($_POST['edit_pos']))
	{
		$pos = array_map('intval', array_map('trim', $_POST['pos']));
		$pos['smilies'] = 0;	// Special position for 'smilies'
		$c_form = array_map('intval', array_map('trim', $_POST['c_form']));
		$q_form = array_map('intval', array_map('trim', $_POST['q_form']));

		// Retrieve current config and find changed values
		$result = $db->query('SELECT name, enable_form, enable_quick, position FROM '.$db->prefix.'toolbar_tags WHERE name IN (\''.implode('\', \'', array_keys($c_form)).'\') ORDER by position') or error('Unable to retrieve tags', __FILE__, __LINE__, $db->error());
		$modified = array();
		while ($button = $db->fetch_assoc($result))
		{
			// Check new position
			if ($pos[$button['name']] < 1 && $button['name'] != 'smilies')
				message($lang_back['incorrect_pos'].$pos[$button['name']]);

			// Check modification
			if ($c_form[$button['name']] != $button['enable_form'] || $q_form[$button['name']] != $button['enable_quick'] || $pos[$button['name']] != $button['position'])
				$modified[$button['name']] = array(($c_form[$button['name']] ? 1 : 0), ($q_form[$button['name']] ? 1 : 0), $pos[$button['name']]);
		}

		// Do updates
		if (!empty($modified))
		{
			foreach ($modified as $name => $arr)
				$db->query('UPDATE '.$db->prefix.'toolbar_tags SET enable_form='.$arr[0].', enable_quick='.$arr[1].', position='.$arr[2].' WHERE name=\''.$db->escape($name).'\'') or error('Unable to update button', __FILE__, __LINE__, $db->error());
			re_generate('forms');
			redirect('backstage/toolbar.php', $lang_back['success_updated']);
		}
		else
			redirect('backstage/toolbar.php', $lang_back['no_change']);
	}

	else
	{
		if (empty($_POST['name']))
			message($lang_back['no_tags']);
		$tags = array_map('trim', $_POST['name']);

		// Delete selected tags/buttons
		if (isset($_POST['delete_tag']))
		{
			$desc = $lang_back['deleting'];
			$action = 'delete';
		}

		// Modify selected tags/buttons
		else if (isset($_POST['edit_tag']))
		{
			$desc = $lang_back['editing'];
			$action = 'save';
		}

		else
			message($lang_common['Bad request']);

		// Display the admin navigation menu
		generate_admin_menu($plugin);
?>
<h2>Toolbar settings</h2>
<div class="blockform">
	<h2 class="block2"><span><?php echo $lang_back['tag_conf'] ?></span></h2>
	<div class="box">
		<form action="toolbar.php" method="post">
			<div class="inform">
				<input type="hidden" name="edit_delete" value="1" />
				<fieldset>
					<legend><?php echo ($action == 'delete') ? $lang_back['tags_deleting'] : $lang_back['tags_editing'] ?></legend>
					<div class="infldset">
						<table class="table">
							<thead><tr>
								<th scope="col" style="width: 25%"><?php echo $lang_back['name'] ?></th>
								<th scope="col" colspan="2" style="width: 35%"><?php echo $lang_back['image'] ?></th>
								<th scope="col" style="width: 25%"><?php echo $lang_back['code'] ?></th>
								<th scope="col" style="width: 15%"><?php echo $lang_back['function'] ?></th>
							</tr></thead>
							<tbody>
<?php
		// Retrieve selected buttons
		$result = $db->query('SELECT name, code, image, func FROM '.$db->prefix.'toolbar_tags WHERE name IN (\''.implode('\', \'', array_keys($tags)).'\') ORDER by position') or error('Unable to retrieve selected tags', __FILE__, __LINE__, $db->error());

		// Output each button
		while ($button = $db->fetch_assoc($result))
		{
			echo "\t\t\t\t\t\t\t\t".'<tr>'."\n";
			if ($action == 'delete')
				echo "\t\t\t\t\t\t\t\t\t".'<td><input type="hidden" name="name['.pun_htmlspecialchars($button['name']).']" value="1" />'.pun_htmlspecialchars($button['name']).'</td>'."\n";
			else
				echo "\t\t\t\t\t\t\t\t\t".'<td><input type="text" size="10" maxlength="20" name="name['.pun_htmlspecialchars($button['name']).']" value="'.pun_htmlspecialchars($button['name']).'" /></td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t".'<td><img src="img/toolbar/'.$ftb_conf['img_pack'].'/'.pun_htmlspecialchars($button['image']).'" title="'.pun_htmlspecialchars($lang_common['bt_'.$button['name']]).'" alt="" style="vertical-align: -8px" /></td>'."\n";
			if ($action == 'delete')
			{
				echo "\t\t\t\t\t\t\t\t\t".'<td>'.pun_htmlspecialchars($button['image']).'</td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t".'<td>'.pun_htmlspecialchars($button['code']).'</td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t".'<td>'.$button['func'].'</td>'."\n";
			}
			else
			{
				echo "\t\t\t\t\t\t\t\t\t".'<td><select name="image['.pun_htmlspecialchars($button['name']).']">'."\n";
				foreach ($images as $img)
				{
					echo "\t\t\t\t\t\t\t\t\t\t".'<option';
					if ($img == $button['image'])
						echo ' selected="selected"';
					echo ' value="'.$img.'">'.$img.'</option>'."\n";
				}
				echo "\t\t\t\t\t\t\t\t\t".'</select></td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t".'<td><input type="text" size="10" maxlength="20" name="code['.pun_htmlspecialchars($button['name']).']" value="'.pun_htmlspecialchars($button['code']).'" /></td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t".'<td><select name="func['.pun_htmlspecialchars($button['name']).']">'."\n";
				for ($i = 0; $i <= 3; $i++)
				{
					echo "\t\t\t\t\t\t\t\t\t\t".'<option value="'.$i.'"';
					if ($button['func'] == $i)
						echo ' selected="selected"';
					echo '>'.$i.'</option>'."\n";
				}
				echo "\t\t\t\t\t\t\t\t\t".'</select></td>'."\n";
			}
			echo "\t\t\t\t\t\t\t\t".'</tr>'."\n";
		}
?>
							</tbody>
						</table>
					</div>
					<p><strong><?php echo $lang_back['edit_info'] ?></strong></p>
				</fieldset>
			</div>
			<p class="submitend"><input type="submit" name="<?php echo $action ?>" value="<?php echo $lang_back[$action] ?>" /></p>
		</form>
	</div>
</div>
<?php
	}
}

// Tags edited/deleted
else if (isset($_POST['edit_delete']))
{
	if (empty($_POST['name']))
		message($lang_back['no_tags']);
	$tags = array_map('trim', $_POST['name']);

	// Delete selected tags/buttons
	if (isset($_POST['delete']))
	{
		$db->query('DELETE FROM '.$db->prefix.'toolbar_tags WHERE name IN (\''.implode(array_keys($_POST['name']), "', '").'\')') or error('Unable to delete tags', __FILE__, __LINE__, $db->error());
		re_generate('all');
		redirect('backstage/toolbar.php', $lang_back['success_deleted']);
	}

	// Modify selected tags/buttons
	else if (isset($_POST['save']))
	{
		$edited = false;

		// Perform each update
		foreach (array_keys($_POST['name']) as $name)
		{
			$form = array(
				'name' => trim($_POST['name'][$name]),
				'code' => trim($_POST['code'][$name]),
				'image' => trim($_POST['image'][$name]),
				'func' => intval(trim($_POST['func'][$name]))
			);

			// Checking tag
			if (validate_tag($form, 'edit', $name))
			{
				$edited = true;

				// Delete old, insert new
				if ($form['name'] != $name)
				{
					$db->query('DELETE FROM '.$db->prefix.'toolbar_tags WHERE name=\''.$db->escape($name).'\'') or error('Unable to delete old tag', __FILE__, __LINE__, $db->error());
					$db->query('INSERT INTO '.$db->prefix.'toolbar_tags (name, code, enable_form, enable_quick, image, func, position) VALUES(\''.$db->escape($form['name']).'\', \''.$db->escape($form['code']).'\', 0, 0, \''.$db->escape($form['image']).'\', '.$form['func'].', 1)') or error('Unable to insert new tag', __FILE__, __LINE__, $db->error());
				}

				// Insert new tag
				else
					$db->query('UPDATE '.$db->prefix.'toolbar_tags SET code=\''.$db->escape($form['code']).'\', image=\''.$db->escape($form['image']).'\', func='.$form['func'].' WHERE name=\''.$db->escape($name).'\'') or error('Unable to modify tag', __FILE__, __LINE__, $db->error());
			}
		}

		// End message
		if (!empty($errors))
			message(implode($errors, "<br />\n"));
		else if ($edited)
		{
			re_generate('all');
			redirect('backstage/toolbar.php', $lang_back['success_edited']);
		}
		header('Location: '.toolbar.php);
		exit;
	}

	else
		message($lang_common['Bad request']);
}

// Normal Display
else
{
	// Display the admin navigation menu


$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['Toolbar']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('toolbar');
?>
<h2>Toolbar settings</h2>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['glob_conf'] ?></h3>
    </div>
    <div class="panel-body">
		<form action="toolbar.php" method="post">
            <input type="hidden" name="form_conf" value="1" />
            <fieldset>
                <table class="table">
                    <tr>
                        <th><?php echo $lang_back['enable_form'] ?></th>
                        <td><input type="radio" name="form[enable_form]" value="1"<?php if ($ftb_conf['enable_form'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_back['yes'] ?></strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[enable_form]" value="0"<?php if ($ftb_conf['enable_form'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_back['no'] ?></strong>
                            <span class="help-block"><?php echo $lang_back['enable_form_infos'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['enable_quickform'] ?></th>
                        <td><input type="radio" name="form[enable_quickform]" value="1"<?php if ($ftb_conf['enable_quickform'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_back['yes'] ?></strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[enable_quickform]" value="0"<?php if ($ftb_conf['enable_quickform'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_back['no'] ?></strong>
                            <span class="help-block"><?php echo $lang_back['enable_quickform_infos'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['images_pack'] ?></th>
                        <td><select class="form-control" name="form[img_pack]">
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
                        <br /><span class="help-block"><?php echo $lang_back['images_pack_infos'] ?></span></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['nb_smilies'] ?></th>
                        <td><input type="text" class="form-control" name="form[nb_smilies]" size="3" maxlength="3" value="<?php echo $ftb_conf['nb_smilies'] ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['nb_smilies_info'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
			<input type="submit" class="btn btn-primary" name="save" value="<?php echo $lang_common['Submit'] ?>" />
		</form>
	</div>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['button_conf'] ?></h3>
    </div>
    <div class="panel-body">
		<form action="toolbar.php" method="post">
            <input type="hidden" name="form_button" value="1" />
            <fieldset>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 6em"><?php echo $lang_back['position'] ?></th>
                            <th scope="col" style="width: 6em"><?php echo $lang_back['button'] ?></th>
                            <th scope="col"><?php echo $lang_back['classic_form'] ?></th>
                            <th scope="col"><?php echo $lang_back['quickreply_form'] ?></th>
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
		echo "\t\t\t\t\t\t\t\t\t".'<td><img src="../img/toolbar/'.$ftb_conf['img_pack'].'/'.pun_htmlspecialchars($button['image']).'" title="'.pun_htmlspecialchars($lang_common['bt_'.$button['name']]).'" alt="" style="vertical-align: -8px" /></td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t".'<td><input type="radio" name="c_form['.pun_htmlspecialchars($button['name']).']" value="1"';
		if ($button['enable_form'] == 1)
			echo ' checked="checked"';
		echo ' />&nbsp;<strong>'.$lang_back['yes'].'</strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="c_form['.pun_htmlspecialchars($button['name']).']" value="0"';
		if ($button['enable_form'] == 0)
			echo ' checked="checked"';
		echo ' />&nbsp;<strong>'.$lang_back['no'].'</strong></td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t".'<td><input type="radio" name="q_form['.pun_htmlspecialchars($button['name']).']" value="1"';
		if ($button['enable_quick'] == 1)
			echo ' checked="checked"';
		echo ' />&nbsp;<strong>'.$lang_back['yes'].'</strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="q_form['.pun_htmlspecialchars($button['name']).']" value="0"';
		if ($button['enable_quick'] == 0)
			echo ' checked="checked"';
		echo ' />&nbsp;<strong>'.$lang_back['no'].'</strong></td>'."\n";
		echo "\t\t\t\t\t\t\t\t".'</tr>'."\n";
	}
?>
                    </tbody>
                </table>
            </fieldset>
            <input type="submit" class="btn btn-primary" name="edit_pos" value="<?php echo $lang_back['update_pos'] ?>" />
		</form>
	</div>
</div>
<?php
}

require FORUM_ROOT.'backstage/footer.php';