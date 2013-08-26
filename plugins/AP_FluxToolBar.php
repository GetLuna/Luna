<?php
/***********************************************************************

  Copyright (C) 2010-2011 Mpok
  based on code Copyright (C) 2005 Vincent Garnier
  License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher

************************************************************************/

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

// Tell admin_loader.php that this is indeed a plugin and that it is loaded
define('FORUM_PLUGIN_LOADED', 1);
define('PLUGIN_VERSION', '2.1.1');
define('PLUGIN_URL', $_SERVER['REQUEST_URI']);

// Load the puntoolbar language files
if (file_exists(FORUM_ROOT.'lang/'.$pun_user['language'].'/fluxtoolbar.php'))
	require FORUM_ROOT.'lang/'.$pun_user['language'].'/fluxtoolbar.php';
else
	require FORUM_ROOT.'lang/English/fluxtoolbar.php';
if (file_exists(FORUM_ROOT.'lang/'.$pun_user['language'].'/fluxtoolbar_admin.php'))
	require FORUM_ROOT.'lang/'.$pun_user['language'].'/fluxtoolbar_admin.php';
else
	require FORUM_ROOT.'lang/English/fluxtoolbar_admin.php';

// Retrieve configuration
$ftb_conf = array();
$result = $db->query('SELECT conf_name, conf_value FROM '.$db->prefix.'toolbar_conf') or error('Unable to retrieve toolbar configuration', __FILE__, __LINE__, $db->error());
while ($conf = $db->fetch_assoc($result))
	$ftb_conf[$conf['conf_name']] = $conf['conf_value'];

// Retrieve image files
$images = array();
$d = dir(FORUM_ROOT.'img/fluxtoolbar/'.$ftb_conf['img_pack']);
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
	require_once FORUM_ROOT.'include/cache_fluxtoolbar.php';
	if ($mode == 'tags' || $mode == 'all')
		generate_ftb_cache('tags');
	if ($mode == 'forms' || $mode == 'all')
	{
		generate_ftb_cache('form');
		generate_ftb_cache('quickform');
	}
}

// Validation function for tag
function validate_tag($t, $mode, $name = '')
{
	global $db, $errors, $ftb_conf, $lang_common, $lang_ftb_admin;

	// Checking mode - not very useful :)
	if ($mode != 'edit' && $mode != 'create')
		message($lang_common['Bad request']);

	// Checking empty param
	if ($t['name'] == '' || $t['code'] == '' || $t['image'] == '')
	{
		$errors[] = $lang_ftb_admin['missing_param'];
		return false;
	}

	// Checking name already used
	if ($mode == 'create' || ($mode == 'edit' && $name != $t['name']))
	{
		$res = $db->query('SELECT 1 FROM '.$db->prefix.'toolbar_tags WHERE name=\''.$db->escape($t['name']).'\'') or error('Unable to retrieve tag name', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($res))
			$errors[] = $lang_ftb_admin['name_used'].$t['name'];
	}

	// Checking incorrect characters for name
	if (preg_match('%[^a-zA-Z0-9\-_]%', $t['name']))
		$errors[] = $lang_ftb_admin['incorrect_name'];

	// Retrieve old tag for edit
	if ($mode == 'edit')
	{
		$res = $db->query('SELECT name, code, image, func FROM '.$db->prefix.'toolbar_tags WHERE name=\''.$db->escape($name).'\'') or error('Unable to retrieve tag', __FILE__, __LINE__, $db->error());
		$old = $db->fetch_assoc($res);
	}
	else
		$old = array('name' => '', 'code' => '', 'image' => '', 'func' => 0);

	// Checking code already used
	$res = $db->query('SELECT 1 FROM '.$db->prefix.'toolbar_tags WHERE name!=\''.$db->escape($old['name']).'\' AND code!=\''.$db->escape($old['code']).'\' AND code=\''.$db->escape($t['code']).'\'') or error('Unable to retrieve tag name', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($res))
		$errors[] = $lang_ftb_admin['code_used'].$t['code'];

	// Checking incorrect characters for code
	if (preg_match('%[^a-zA-Z0-9]%', $t['code']))
		$errors[] = $lang_ftb_admin['incorrect_code'];

	// Checking image - should not be triggered (cause <select>)
	if ($t['image'] != $old['image'] && !file_exists(FORUM_ROOT.'img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.$t['image']))
		$errors[] = $lang_ftb_admin['incorrect_image'].$t['image'];

	// Checking func - should not be triggered (cause <select>)
	if ($t['func'] != $old['func'] && ($t['func'] < 0 || $t['func'] > 3))
		$errors[] = $lang_ftb_admin['incorrect_func'].$t['func'];

	// Checking position
	if (isset($t['position']) && $t['position'] < 1)
		$errors[] = $lang_ftb_admin['incorrect_pos'].$t['position'];

	// Return false if errors or no edit
	if (!empty($errors) || ($old['name'] == $t['name'] && $old['code'] == $t['code'] && $old['image'] == $t['image'] && $old['func'] == $t['func']))
		return false;
	else
		return true;
}

// Regenerate cache
if (isset($_POST['regenerate']))
{
	re_generate('all');
	redirect(PLUGIN_URL, $lang_ftb_admin['cache_updated']);
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
				message($lang_ftb_admin['not_numeric'].$key);

			$db->query('UPDATE '.$db->prefix.'toolbar_conf SET conf_value=\''.$db->escape($input).'\' WHERE conf_name=\''.$db->escape($key).'\'') or error('Unable to update general settings', __FILE__, __LINE__, $db->error());
			$done = true;
		}
	}

	// End message
	if ($done)
	{
		re_generate('forms');
		redirect(PLUGIN_URL, $lang_ftb_admin['success']);
	}
	else
		redirect(PLUGIN_URL, $lang_ftb_admin['no_change']);
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
				message($lang_ftb_admin['incorrect_pos'].$pos[$button['name']]);

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
			redirect(PLUGIN_URL, $lang_ftb_admin['success_updated']);
		}
		else
			redirect(PLUGIN_URL, $lang_ftb_admin['no_change']);
	}

	else
	{
		if (empty($_POST['name']))
			message($lang_ftb_admin['no_tags']);
		$tags = array_map('trim', $_POST['name']);

		// Delete selected tags/buttons
		if (isset($_POST['delete_tag']))
		{
			$desc = $lang_ftb_admin['deleting'];
			$action = 'delete';
		}

		// Modify selected tags/buttons
		else if (isset($_POST['edit_tag']))
		{
			$desc = $lang_ftb_admin['editing'];
			$action = 'save';
		}

		else
			message($lang_common['Bad request']);

		// Display the admin navigation menu
		generate_admin_menu($plugin);
?>
<div class="block">
	<h2><span>FluxToolBar v.<?php echo PLUGIN_VERSION ?></span></h2>
	<div class="box">
		<div class="inbox">
			<p><?php echo $desc ?></p>
		</div>
	</div>
</div>
<div class="blockform">
	<h2 class="block2"><span><?php echo $lang_ftb_admin['tag_conf'] ?></span></h2>
	<div class="box">
		<form action="<?php echo PLUGIN_URL ?>" method="post">
			<div class="inform">
				<input type="hidden" name="edit_delete" value="1" />
				<fieldset>
					<legend><?php echo ($action == 'delete') ? $lang_ftb_admin['tags_deleting'] : $lang_ftb_admin['tags_editing'] ?></legend>
					<div class="infldset">
						<table class="aligntop" cellspacing="0">
							<thead><tr>
								<th scope="col" style="width: 25%"><?php echo $lang_ftb_admin['name'] ?></th>
								<th scope="col" colspan="2" style="width: 35%"><?php echo $lang_ftb_admin['image'] ?></th>
								<th scope="col" style="width: 25%"><?php echo $lang_ftb_admin['code'] ?></th>
								<th scope="col" style="width: 15%"><?php echo $lang_ftb_admin['function'] ?></th>
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
			echo "\t\t\t\t\t\t\t\t\t".'<td><img src="img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.pun_htmlspecialchars($button['image']).'" title="'.pun_htmlspecialchars($lang_ftb['bt_'.$button['name']]).'" alt="" style="vertical-align: -8px" /></td>'."\n";
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
					<p><strong><?php echo $lang_ftb_admin['edit_info'] ?></strong></p>
				</fieldset>
			</div>
			<p class="submitend"><input type="submit" name="<?php echo $action ?>" value="<?php echo $lang_ftb_admin[$action] ?>" /></p>
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
		message($lang_ftb_admin['no_tags']);
	$tags = array_map('trim', $_POST['name']);

	// Delete selected tags/buttons
	if (isset($_POST['delete']))
	{
		$db->query('DELETE FROM '.$db->prefix.'toolbar_tags WHERE name IN (\''.implode(array_keys($_POST['name']), "', '").'\')') or error('Unable to delete tags', __FILE__, __LINE__, $db->error());
		re_generate('all');
		redirect(PLUGIN_URL, $lang_ftb_admin['success_deleted']);
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
			redirect(PLUGIN_URL, $lang_ftb_admin['success_edited']);
		}
		header('Location: '.PLUGIN_URL);
		exit;
	}

	else
		message($lang_common['Bad request']);
}

// Tag created
else if (isset($_POST['create']))
{
	$form = array_map('trim', $_POST['form']);
	$form['func'] = intval($form['func']);
	$form['position'] = intval($form['position']);

	// Validate and insert tag
	if (validate_tag($form, 'create'))
	{
		$db->query('INSERT INTO '.$db->prefix.'toolbar_tags (name, code, enable_form, enable_quick, image, func, position) VALUES(\''.$db->escape($form['name']).'\', \''.$db->escape($form['code']).'\', 0, 0, \''.$db->escape($form['image']).'\', '.$form['func'].', '.$form['position'].')') or error('Unable to insert new tag', __FILE__, __LINE__, $db->error());
		re_generate('tags');
		redirect(PLUGIN_URL, $lang_ftb_admin['success_created']);
	}
	else
		message(implode($errors, "<br />\n"));
}

// Delete images
else if (isset($_POST['delete_img']))
{
	if (empty($_POST['del_images']))
		message($lang_ftb_admin['no_images']);
	$del_images = array_map('trim', $_POST['del_images']);

	$to_delete = array();
	$images_affected = array();
	$not_deleted = array();

	// Checking if images to delete are used by some buttons
	$result = $db->query('SELECT image FROM '.$db->prefix.'toolbar_tags') or error('Unable to retrieve images', __FILE__, __LINE__, $db->error());
	$res = array();
	while ($img = $db->fetch_assoc($result))
		$res[] = $img['image'];
	$button_img = array_unique($res);
	foreach (array_keys($del_images) as $img)
	{
		if (!in_array($img, $button_img))
			$to_delete[] = $img;
		else
			$images_affected[] = $img;
	}

	if (!empty($images_affected))
		message(sprintf($lang_ftb_admin['images_affected'], implode(', ', $images_affected)));
	else
	{
		// Delete each image
		foreach ($to_delete as $img)
		{
			if (!@unlink(FORUM_ROOT.'img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.$img))
				$not_deleted[] = $img;
		}
	}

	if (!empty($not_deleted))
		$message = sprintf($lang_ftb_admin['images_not_deleted'], implode(', ', $not_deleted));
	else
		$message = $lang_ftb_admin['images_deleted'];
	redirect(PLUGIN_URL, $message);
}

// Add image
else if (isset($_POST['add_image']))
{
	if (!isset($_FILES['req_file']))
		message($lang_ftb_admin['no_file']);

	$uploaded_file = $_FILES['req_file'];

	// Make sure the upload went smooth
	if (isset($uploaded_file['error']))
	{
		switch ($uploaded_file['error'])
		{
			case 1:	// UPLOAD_ERR_INI_SIZE
			case 2:	// UPLOAD_ERR_FORM_SIZE
				message($lang_ftb_admin['too_large_ini']);
				break;

			case 3:	// UPLOAD_ERR_PARTIAL
				message($lang_ftb_admin['partial_upload']);
				break;

			case 4:	// UPLOAD_ERR_NO_FILE
				message($lang_ftb_admin['no_file']);
				break;

			case 6:	// UPLOAD_ERR_NO_TMP_DIR
				message($lang_ftb_admin['no_tmp_directory']);
				break;

			default:
				// No error occured, but was something actually uploaded?
				if ($uploaded_file['size'] == 0)
					message($lang_ftb_admin['no_file']);
				break;
		}
	}

	if (is_uploaded_file($uploaded_file['tmp_name']))
	{
		$filename = substr($uploaded_file['name'], 0, strpos($uploaded_file['name'], '.'));

		// Check types
		$allowed_types = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
		if (!in_array($uploaded_file['type'], $allowed_types))
			message($lang_ftb_admin['bad_type']);

		// Make sure the file isn't too big
		if ($uploaded_file['size'] > $ftb_conf['button_size'])
			message($lang_ftb_admin['too_large'].' '.$ftb_conf['button_size'].' '.$lang_ftb_admin['bytes'].'.');

		// Determine type
		$extensions = null;
		if ($uploaded_file['type'] == 'image/gif')
			$extensions = array('.gif', '.jpg', '.png');
		else if ($uploaded_file['type'] == 'image/jpeg' || $uploaded_file['type'] == 'image/pjpeg')
			$extensions = array('.jpg', '.gif', '.png');
		else
			$extensions = array('.png', '.gif', '.jpg');

		// Move the file to the avatar directory. We do this before checking the width/height to circumvent open_basedir restrictions.
		if (!@move_uploaded_file($uploaded_file['tmp_name'], FORUM_ROOT.'img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.$filename.'.tmp'))
			message($lang_ftb_admin['move_failed']);

		// Now check the width/height
		list($width, $height, $type,) = getimagesize(FORUM_ROOT.'img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.$filename.'.tmp');
		if (empty($width) || empty($height) || $width > $ftb_conf['button_width'] || $height > $ftb_conf['button_height'])
		{
			@unlink(FORUM_ROOT.'img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.$filename.'.tmp');
			message($lang_ftb_admin['too_wide_or_high'].' '.$ftb_conf['button_width'].'x'.$ftb_conf['button_height'].' '.$lang_ftb_admin['pixels'].'.');
		}
		else if ($type == 1 && $uploaded_file['type'] != 'image/gif')			// Prevent dodgy uploads
		{
			@unlink(FORUM_ROOT.'img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.$filename.'.tmp');
			message($lang_ftb_admin['bad_type']);
		}

		// Delete any old images and put the new one in place
		@unlink(FORUM_ROOT.'img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.$filename.$extensions[0]);
		@unlink(FORUM_ROOT.'img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.$filename.$extensions[1]);
		@unlink(FORUM_ROOT.'img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.$filename.$extensions[2]);
		@rename(FORUM_ROOT.'img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.$filename.'.tmp', FORUM_ROOT.'img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.$filename.$extensions[0]);
		@chmod(FORUM_ROOT.'img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.$filename.$extensions[0], 0644);
	}
	else
		message($lang_ftb_admin['unknown_failure']);

	redirect(PLUGIN_URL, $lang_ftb_admin['successful_upload']);
}

// Tag creation form
else if (isset($_GET['tag_create']))
{
	// Retrieve max position
	$result = $db->query('SELECT MAX(position) FROM '.$db->prefix.'toolbar_tags') or error('Unable to retrieve position', __FILE__, __LINE__, $db->error());
	$def_pos = intval($db->result($result, 0)) + 1;

	// Display the admin navigation menu
	generate_admin_menu($plugin);
?>
<div class="block">
	<h2><span>FluxToolBar v.<?php echo PLUGIN_VERSION ?></span></h2>
	<div class="box">
		<div class="inbox">
			<p><?php echo $lang_ftb_admin['creating'] ?></p>
		</div>
	</div>
</div>
<div class="blockform">
	<h2 class="block2"><span><?php echo $lang_ftb_admin['create_conf'] ?></span></h2>
	<div class="box">
		<form action="<?php echo str_replace('&', '&amp;', PLUGIN_URL) ?>" method="post">
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_ftb_admin['new_tag'] ?></legend>
					<div class="infldset">
						<table class="aligntop" cellspacing="0">
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['name'] ?></th>
								<td><input type="text" size="10" maxlength="20" name="form[name]" />
									<span><?php echo $lang_ftb_admin['name_info'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['image'] ?></th>
								<td><select name="form[image]">
<?php
	foreach ($images as $img)
		echo "\t\t\t\t\t\t\t\t\t".'<option value="'.$img.'">'.$img.'</option>'."\n";
?>
								</select>
								<span><?php echo $lang_ftb_admin['image_info'] ?></span></td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['code'] ?></th>
								<td><input type="text" size="10" maxlength="20" name="form[code]" />
									<span><?php echo $lang_ftb_admin['tag_info'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['function'] ?></th>
								<td><select name="form[func]">
									<option value="0" selected="selected">0</option>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
								</select>
								<span><?php echo $lang_ftb_admin['func_info'] ?></span></td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['position'] ?></th>
								<td><input type="text" size="2" maxlength="3" name="form[position]" value="<?php echo $def_pos ?>" />
									<span><?php echo $lang_ftb_admin['pos_info'] ?></span>
								</td>
							</tr>
						</table>
					</div>
					<p><strong><?php echo $lang_ftb_admin['create_info'] ?></strong></p>
				</fieldset>
			</div>
			<p class="submitend"><input type="submit" name="create" value="<?php echo $lang_ftb_admin['save'] ?>" /></p>
		</form>
	</div>
</div>
<div class="blockform">
	<h2 class="block2"><span><?php echo $lang_ftb_admin['current_images'] ?></span></h2>
	<div class="box">
		<form action="<?php echo str_replace('&', '&amp;', PLUGIN_URL) ?>" method="post">
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_ftb_admin['list_images'] ?></legend>
					<div class="infldset">
						<table>
							<thead><tr>
								<th scope="col"><?php echo $lang_ftb_admin['image_filename']; ?></th>
								<th scope="col"><?php echo $lang_ftb_admin['image']; ?></th>
								<th scope="col"><?php echo $lang_ftb_admin['delete']; ?></th>
							</tr></thead>
							<tbody>
<?php
	foreach ($images as $img)
	{
?>
								<tr>
									<th scope="row"><?php echo $img ?></th>
									<td><img src="img/fluxtoolbar/<?php echo $ftb_conf['img_pack'].'/'.$img ?>" alt="" /></td>
									<td><input name="del_images[<?php echo $img ?>]" type="checkbox" value="1" /></td>
								</tr>
<?php
	}
?>
							</tbody>
						</table>
					</div>
				</fieldset>
			</div>
			<p class="submitend"><input name="delete_img" type="submit" value="<?php echo $lang_ftb_admin['delete_img'] ?>" /></p>
		</form>
		<form method="post" enctype="multipart/form-data"  action="<?php echo str_replace('&', '&amp;', PLUGIN_URL) ?>">
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_ftb_admin['add_image'] ?></legend>
					<div class="infldset">
						<label><?php echo $lang_ftb_admin['image_file'] ?><br /><input name="req_file" type="file" size="40" /><br /></label>
					</div>
				</fieldset>
			</div>
			<p class="submitend"><input name="add_image" type="submit" value="<?php echo $lang_ftb_admin['upload'] ?>" /></p>
		</form>
	</div>
</div>
<div class="blockform">
	<h2><a href="<?php echo str_replace('&tag_create', '', PLUGIN_URL) ?>"><?php echo $lang_ftb_admin['back_conf'] ?></a></h2>
</div>
<?php
}

// Normal Display
else
{
	// Display the admin navigation menu
	generate_admin_menu($plugin);
?>
<div class="block">
	<h2><span>FluxToolBar v.<?php echo PLUGIN_VERSION ?></span></h2>
	<div style="float: right; margin-right: 5em">
		<form action="<?php echo PLUGIN_URL ?>" method="post">
			<p class="submitend"><input type="submit" name="regenerate" value="<?php echo $lang_ftb_admin['regenerate'] ?>" /></p>
		</form>
	</div>
	<div class="box">
		<div class="inbox">
			<p><?php echo $lang_ftb_admin['plugin_desc'] ?></p>
		</div>
	</div>
</div>
<div class="blockform">
	<h2 class="block2"><span><?php echo $lang_ftb_admin['glob_conf'] ?></span></h2>
	<div class="box">
		<form action="<?php echo PLUGIN_URL ?>" method="post">
			<div class="inform">
				<input type="hidden" name="form_conf" value="1" />
				<fieldset>
					<legend><?php echo $lang_ftb_admin['settings'] ?></legend>
					<div class="infldset">
						<table class="aligntop" cellspacing="0">
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['enable_form'] ?></th>
								<td><input type="radio" name="form[enable_form]" value="1"<?php if ($ftb_conf['enable_form'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ftb_admin['yes'] ?></strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[enable_form]" value="0"<?php if ($ftb_conf['enable_form'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ftb_admin['no'] ?></strong>
									<span><?php echo $lang_ftb_admin['enable_form_infos'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['enable_quickform'] ?></th>
								<td><input type="radio" name="form[enable_quickform]" value="1"<?php if ($ftb_conf['enable_quickform'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ftb_admin['yes'] ?></strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[enable_quickform]" value="0"<?php if ($ftb_conf['enable_quickform'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_ftb_admin['no'] ?></strong>
									<span><?php echo $lang_ftb_admin['enable_quickform_infos'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['images_pack'] ?></th>
								<td><select name="form[img_pack]">
<?php
	$packs = array();
	$d = dir(FORUM_ROOT.'img/fluxtoolbar');
	while (($entry = $d->read()) !== false)
	{
		 if ($entry != '.' && $entry != '..' && is_dir(FORUM_ROOT.'img/fluxtoolbar/'.$entry))
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
								<span><?php echo $lang_ftb_admin['images_pack_infos'] ?></span></td>
							</tr>
						</table>
					</div>
				</fieldset>
			</div>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_ftb_admin['smilies_settings'] ?></legend>
					<div class="infldset">
						<table class="aligntop" cellspacing="0">
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['nb_smilies'] ?></th>
								<td><input type="text" name="form[nb_smilies]" size="3" maxlength="3" value="<?php echo $ftb_conf['nb_smilies'] ?>" />
									<span><?php echo $lang_ftb_admin['nb_smilies_info'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['smilies_width'] ?></th>
								<td><input type="text" name="form[pop_up_width]" size="5" maxlength="5" value="<?php echo $ftb_conf['pop_up_width'] ?>" />
									<span><?php echo $lang_ftb_admin['smilies_width_info'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['smilies_height'] ?></th>
								<td><input type="text" name="form[pop_up_height]" size="5" maxlength="5" value="<?php echo $ftb_conf['pop_up_height'] ?>" />
									<span><?php echo $lang_ftb_admin['smilies_height_info'] ?></span>
								</td>
							</tr>
						</table>
					</div>
				</fieldset>
			</div>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_ftb_admin['buttons_settings'] ?></legend>
					<div class="infldset">
						<table class="aligntop" cellspacing="0">
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['button_size'] ?></th>
								<td><input type="text" name="form[button_size]" size="3" maxlength="5" value="<?php echo $ftb_conf['button_size'] ?>" />
									<span><?php echo $lang_ftb_admin['button_size_info'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['button_width'] ?></th>
								<td><input type="text" name="form[button_width]" size="2" maxlength="3" value="<?php echo $ftb_conf['button_width'] ?>" />
									<span><?php echo $lang_ftb_admin['button_width_info'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_ftb_admin['button_height'] ?></th>
								<td><input type="text" name="form[button_height]" size="2" maxlength="3" value="<?php echo $ftb_conf['button_height'] ?>" />
									<span><?php echo $lang_ftb_admin['button_height_info'] ?></span>
								</td>
							</tr>
						</table>
					</div>
				</fieldset>
			</div>
			<p class="submitend"><input type="submit" name="save" value="<?php echo $lang_ftb_admin['save'] ?>" /></p>
		</form>
	</div>
</div>
<div class="blockform">
	<h2 class="block2"><span><?php echo $lang_ftb_admin['button_conf'] ?></span></h2>
	<div class="box">
		<form action="<?php echo PLUGIN_URL ?>" method="post">
			<div class="inform">
				<input type="hidden" name="form_button" value="1" />
				<fieldset>
					<legend><?php echo $lang_ftb_admin['buttons_settings'] ?></legend>
					<div class="infldset">
						<table class="aligntop" cellspacing="0">
							<thead><tr>
								<th scope="col" style="width: 6em"><?php echo $lang_ftb_admin['position'] ?></th>
								<th scope="col" style="width: 6em"><?php echo $lang_ftb_admin['button'] ?></th>
								<th scope="col"><?php echo $lang_ftb_admin['classic_form'] ?></th>
								<th scope="col"><?php echo $lang_ftb_admin['quickreply_form'] ?></th>
								<th scope="col" style="width: 4em"><?php echo $lang_ftb_admin['select'] ?></th>
							</tr></thead>
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
			echo '<input type="text" name="pos['.pun_htmlspecialchars($button['name']).']" value="'.$button['position'].'" size="3" maxlength="3" /></td>'."\n";
		else
			echo '&nbsp;</td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t".'<td><img src="img/fluxtoolbar/'.$ftb_conf['img_pack'].'/'.pun_htmlspecialchars($button['image']).'" title="'.pun_htmlspecialchars($lang_ftb['bt_'.$button['name']]).'" alt="" style="vertical-align: -8px" /></td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t".'<td><input type="radio" name="c_form['.pun_htmlspecialchars($button['name']).']" value="1"';
		if ($button['enable_form'] == 1)
			echo ' checked="checked"';
		echo ' />&nbsp;<strong>'.$lang_ftb_admin['yes'].'</strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="c_form['.pun_htmlspecialchars($button['name']).']" value="0"';
		if ($button['enable_form'] == 0)
			echo ' checked="checked"';
		echo ' />&nbsp;<strong>'.$lang_ftb_admin['no'].'</strong></td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t".'<td><input type="radio" name="q_form['.pun_htmlspecialchars($button['name']).']" value="1"';
		if ($button['enable_quick'] == 1)
			echo ' checked="checked"';
		echo ' />&nbsp;<strong>'.$lang_ftb_admin['yes'].'</strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="q_form['.pun_htmlspecialchars($button['name']).']" value="0"';
		if ($button['enable_quick'] == 0)
			echo ' checked="checked"';
		echo ' />&nbsp;<strong>'.$lang_ftb_admin['no'].'</strong></td>'."\n";
		if (in_array($button['name'], $def_tags))
			echo "\t\t\t\t\t\t\t\t\t".'<td>&nbsp;</td>'."\n";
		else
			echo "\t\t\t\t\t\t\t\t\t".'<td><input name="name['.pun_htmlspecialchars($button['name']).']" type="checkbox" value="1" /></td>'."\n";
		echo "\t\t\t\t\t\t\t\t".'</tr>'."\n";
	}
?>
							</tbody>
						</table>
					</div>
				</fieldset>
			</div>
			<p class="submitend">
				<input type="submit" name="edit_pos" value="<?php echo $lang_ftb_admin['update_pos'] ?>" /><br /><br />
				<input type="submit" name="edit_tag" value="<?php echo $lang_ftb_admin['edit_tag'] ?>" />&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="submit" name="delete_tag" value="<?php echo $lang_ftb_admin['delete_tag'] ?>" />
			</p>
		</form>
	</div>
</div>
<div class="blockform">
	<h2><a href="<?php echo PLUGIN_URL.'&amp;tag_create' ?>"><?php echo $lang_ftb_admin['tag_create'] ?></a></h2>
</div>
<?php
}
