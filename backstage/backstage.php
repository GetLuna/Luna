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

// Load the language file
require FORUM_ROOT.'lang/'.$admin_language.'/language.php';

if (isset($_POST['form_sent']))
{
	$form = array(
		'index_update_check'	=> isset($_POST['form']['index_update_check']) ? '1' : '0',
	);

	foreach ($form as $key => $input)
	{
		// Only update values that have changed
		if (array_key_exists('o_'.$key, $pun_config) && $pun_config['o_'.$key] != $input)
		{
			if ($input != '' || is_int($input))
				$value = '\''.$db->escape($input).'\'';
			else
				$value = 'NULL';

			$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$value.' WHERE conf_name=\'o_'.$db->escape($key).'\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());
		}
	}

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/backstage.php', $lang['Options updated redirect']);
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang['Admin'], $lang['Backstage settings']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
generate_admin_menu('backstage');

?>
<h2><?php echo $lang['Backstage settings'] ?></h2>
<form method="post" action="backstage.php">
    <input type="hidden" name="form_sent" value="1" />
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Update settings head'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
				<input type="checkbox" name="form[index_update_check]" value="1" <?php if ($pun_config['o_index_update_check'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang['Index update check'] ?>
            </fieldset>
		</div>
	</div>
</form>
<?php

require FORUM_ROOT.'backstage/footer.php';
