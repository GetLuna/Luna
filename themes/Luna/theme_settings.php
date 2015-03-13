<?php
if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/theme.php', $lang['Bad HTTP Referer message']);

	$form = array(
		'luna_default_color'	=> intval($_POST['form']['color_scheme']),
	);

	foreach ($form as $key => $input) {
		// Only update values that have changed
		if (array_key_exists('t_'.$key, $luna_config) && $luna_config['t_'.$key] != $input) {
			if ($input != '' || is_int($input))
				$value = '\''.$db->escape($input).'\'';
			else
				$value = 'NULL';

			$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$value.' WHERE conf_name=\'t_'.$db->escape($key).'\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());
		}
	}

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/theme.php?saved=true');
}
if (isset($_POST['uninstall_theme'])) {
	confirm_referrer('backstage/theme.php', $lang['Bad HTTP Referer message']);

	// Remove obsolete t_luna_default_color permission from config table
	if (array_key_exists('t_luna_revision', $luna_config))
		$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'t_luna_revision\'') or error('Unable to remove config value \'t_luna_revision\'', __FILE__, __LINE__, $db->error());

	// Remove obsolete t_luna_default_color permission from config table
	if (array_key_exists('t_luna_default_color', $luna_config))
		$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'t_luna_default_color\'') or error('Unable to remove config value \'t_luna_default_color\'', __FILE__, __LINE__, $db->error());

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/theme.php?saved=true');
}
if (isset($_POST['install_theme'])) {
	confirm_referrer('backstage/theme.php', $lang['Bad HTTP Referer message']);

	// Add t_luna_revision feature
	if (!array_key_exists('t_luna_revision', $luna_config))
		$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'t_luna_revision\', \''.$theme_info->version.'\')') or error('Unable to insert config value \'t_luna_revision\'', __FILE__, __LINE__, $db->error());

	// Add t_luna_default_color feature
	if (!array_key_exists('t_luna_default_color', $luna_config))
		$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'t_luna_default_color\', \'3\')') or error('Unable to insert config value \'t_luna_default_color\'', __FILE__, __LINE__, $db->error());
		
	$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.$theme_info->version.'\' WHERE conf_name = \'t_luna_revision\'') or error('Unable to update version', __FILE__, __LINE__, $db->error());

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/theme.php?saved=true');
}
?>
<?php if (!isset($luna_config['t_luna_revision'])) { ?>
	<div class="alert alert-info">If you install this theme, it will add additional configuration options which can be managed here. This theme works without installing it, but of course, you'll get a better experience if you do this.</div>
	<form class="form-horizontal" method="post" action="theme.php">
		<div class="form-group">
			<label class="col-sm-3 control-label">Theme</label>
			<div class="col-sm-9">
				<input type="hidden" name="install_theme" value="1" />
				<button class="btn btn-success" type="submit" name="install"><span class="fa fa-fw fa-check"></span> Install</button>
			</div>
		</div>
	</form>
<?php } elseif ((isset($luna_config['t_luna_revision'])) && ($luna_config['t_luna_revision'] < $theme_info->version)) { ?>
	<div class="alert alert-info">The theme version doesn't match the database version, please update now.</div>
	<form class="form-horizontal" method="post" action="theme.php">
		<div class="form-group">
			<label class="col-sm-3 control-label">Theme</label>
			<div class="col-sm-9">
				<input type="hidden" name="install_theme" value="1" />
				<button class="btn btn-success" type="submit" name="uninstall"><span class="fa fa-fw fa-trash"></span> Update</button>
				<button class="btn btn-danger" type="submit" name="uninstall"><span class="fa fa-fw fa-trash"></span> Uninstall</button>
			</div>
		</div>
	</form>
<?php } else { ?>
	<form class="form-horizontal" method="post" action="theme.php">
		<div class="form-group">
			<label class="col-sm-3 control-label">Theme</label>
			<div class="col-sm-9">
				<input type="hidden" name="uninstall_theme" value="1" />
				<button class="btn btn-danger" type="submit" name="uninstall"><span class="fa fa-fw fa-trash"></span> Uninstall</button>
			</div>
		</div>
	</form>
<?php } ?>
<hr />
<form class="form-horizontal" method="post" action="theme.php">
	<input type="hidden" name="form_sent" value="1" />
	<div class="form-group">
		<label class="col-sm-3 control-label">Save settings</label>
		<div class="col-sm-9">
			<button class="btn btn-primary" type="submit" name="install"><span class="fa fa-fw fa-check"></span> Save</button>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Default color</label>
		<div class="col-sm-9">
			<div class="btn-group accent-group" data-toggle="buttons">
				<label class="btn btn-primary color-accent accent-blue<?php if ($luna_config['t_luna_default_color'] == '1') echo ' active' ?>">
					<input type="radio" name="form[color_scheme]" id="blue" value="1"<?php if ($luna_config['t_luna_default_color'] == '1') echo ' checked' ?>>
				</label>
				<label class="btn btn-primary color-accent accent-denim<?php if ($luna_config['t_luna_default_color'] == '2') echo ' active' ?>">
					<input type="radio" name="form[color_scheme]" id="denim" value="2"<?php if ($luna_config['t_luna_default_color'] == '2') echo ' checked' ?>>
				</label>
				<label class="btn btn-primary color-accent accent-luna<?php if ($luna_config['t_luna_default_color'] == '3') echo ' active' ?>">
					<input type="radio" name="form[color_scheme]" id="luna" value="3"<?php if ($luna_config['t_luna_default_color'] == '3') echo ' checked' ?>>
				</label>
				<label class="btn btn-primary color-accent accent-purple<?php if ($luna_config['t_luna_default_color'] == '4') echo ' active' ?>">
					<input type="radio" name="form[color_scheme]" id="purple" value="4"<?php if ($luna_config['t_luna_default_color'] == '4') echo ' checked' ?>>
				</label>
				<label class="btn btn-primary color-accent accent-green<?php if ($luna_config['t_luna_default_color'] == '5') echo ' active' ?>">
					<input type="radio" name="form[color_scheme]" id="green" value="5"<?php if ($luna_config['t_luna_default_color'] == '5') echo ' checked' ?>>
				</label>
				<label class="btn btn-primary color-accent accent-ao<?php if ($luna_config['t_luna_default_color'] == '6') echo ' active' ?>">
					<input type="radio" name="form[color_scheme]" id="ao" value="6"<?php if ($luna_config['t_luna_default_color'] == '6') echo ' checked' ?>>
				</label>
				<label class="btn btn-primary color-accent accent-yellow<?php if ($luna_config['t_luna_default_color'] == '7') echo ' active' ?>">
					<input type="radio" name="form[color_scheme]" id="yellow" value="7"<?php if ($luna_config['t_luna_default_color'] == '7') echo ' checked' ?>>
				</label>
				<label class="btn btn-primary color-accent accent-orange<?php if ($luna_config['t_luna_default_color'] == '8') echo ' active' ?>">
					<input type="radio" name="form[color_scheme]" id="orange" value="8"<?php if ($luna_config['t_luna_default_color'] == '8') echo ' checked' ?>>
				</label>
				<label class="btn btn-primary color-accent accent-red<?php if ($luna_config['t_luna_default_color'] == '9') echo ' active' ?>">
					<input type="radio" name="form[color_scheme]" id="red" value="9"<?php if ($luna_config['t_luna_default_color'] == '9') echo ' checked' ?>>
				</label>
				<label class="btn btn-primary color-accent accent-white<?php if ($luna_config['t_luna_default_color'] == '10') echo ' active' ?>">
					<input type="radio" name="form[color_scheme]" id="white" value="10"<?php if ($luna_config['t_luna_default_color'] == '10') echo ' checked' ?>>
				</label>
				<label class="btn btn-primary color-accent accent-grey<?php if ($luna_config['t_luna_default_color'] == '11') echo ' active' ?>">
					<input type="radio" name="form[color_scheme]" id="grey" value="11"<?php if ($luna_config['t_luna_default_color'] == '11') echo ' checked' ?>>
				</label>
				<label class="btn btn-primary color-accent accent-black<?php if ($luna_config['t_luna_default_color'] == '12') echo ' active' ?>">
					<input type="radio" name="form[color_scheme]" id="black" value="12"<?php if ($luna_config['t_luna_default_color'] == '12') echo ' checked' ?>>
				</label>
			</div>
		</div>
	</div>
</form>