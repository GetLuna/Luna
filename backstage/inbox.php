<?php

/*
 * Copyright (C) 2014 Luna
 * Based on work by Adaur (2010), Vincent Garnier, Connorhd and David 'Chacmool' Djurback
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod']) {
    header("Location: ../login.php");
}

if ($luna_user['g_id'] != FORUM_ADMIN)
	message_backstage($lang['No permission'], false, '403 Forbidden');

// Load language file
if (file_exists(FORUM_ROOT.'lang/'.$luna_user['language'].'/pms_plugin.php'))
       require FORUM_ROOT.'lang/'.$luna_user['language'].'/pms_plugin.php';
else
       require FORUM_ROOT.'lang/English/pms_plugin.php';

if (isset($_POST['form_sent']))
{
	$form = array_map('trim', $_POST['form']);
	$allow = array_map('trim', $_POST['allow']);
	$limit = array_map('trim', $_POST['limit']);

	while (list($key, $input) = @each($form))
	{
		// Only update values that have changed
		if ((isset($luna_config['o_'.$key])) || ($luna_config['o_'.$key] == NULL))
		{
			if ($luna_config['o_'.$key] != $input)
			{
				if ($key == 'pms_max_receiver')
					$input = $input+1;
				
				if ($input != '' || is_int($input))
					$value = '\''.$db->escape($input).'\'';
				else
					$value = 'NULL';
	
				$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$value.' WHERE conf_name=\'o_'.$key.'\'') or error('Unable to update the configuration', __FILE__, __LINE__, $db->error());
			}
		}
	}

	while (list($id, $set) = @each($allow))
		$db->query('UPDATE '.$db->prefix.'groups SET g_pm='.intval($set).' WHERE g_id=\''.intval($id).'\'') or error('Unable to change the permissions', __FILE__, __LINE__, $db->error());
	
	while (list($id, $set) = @each($limit))
		$db->query('UPDATE '.$db->prefix.'groups SET g_pm_limit='.intval($set).' WHERE g_id=\''.intval($id).'\'') or error('Unable to change the permissions', __FILE__, __LINE__, $db->error());
	
	// Regenerate the config cache
	require_once FORUM_ROOT.'include/cache.php';
	generate_config_cache();

	redirect('inbox.php');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('settings', 'inbox');

?>

<?php

require 'footer.php';