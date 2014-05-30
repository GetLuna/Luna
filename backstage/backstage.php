<?php

/**
 * Copyright (C) 2013-2014 ModernBB Group
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License under GPLv3
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$luna_user['is_admmod']) {
    header("Location: ../login.php");
}

if (isset($_POST['form_sent']))
{
	confirm_referrer('backstage/backstage.php');
	
	$form = array(
		'index_update_check'	=> isset($_POST['form']['index_update_check']) ? '1' : '0',
	);

	foreach ($form as $key => $input)
	{
		// Only update values that have changed
		if (array_key_exists('o_'.$key, $luna_config) && $luna_config['o_'.$key] != $input)
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

	redirect('backstage/backstage.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Backstage settings']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
generate_admin_menu('backstage');

?>
<h2><?php echo $lang['Backstage settings'] ?></h2>
<?php
if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>'
?>
<form method="post" action="backstage.php">
    <input type="hidden" name="form_sent" value="1" />
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Update settings head'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="form[index_update_check]" value="1" <?php if ($luna_config['o_index_update_check'] == '1') echo ' checked="checked"' ?> />
						<?php echo $lang['Index update check'] ?>
                    </label>
                </div>
            </fieldset>
		</div>
	</div>
</form>
<?php

require FORUM_ROOT.'backstage/footer.php';
