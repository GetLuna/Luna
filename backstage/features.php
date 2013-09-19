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
	$form = array(
		'quickpost'				=> $_POST['form']['quickpost'] ? '1' : '0',
		'users_online'			=> $_POST['form']['users_online'] ? '1' : '0',
		'censoring'				=> $_POST['form']['censoring'] ? '1' : '0',
		'signatures'			=> $_POST['form']['signatures'] ? '1' : '0',
		'ranks'					=> $_POST['form']['ranks'] ? '1' : '0',
		'topic_views'			=> $_POST['form']['topic_views'] ? '1' : '0',
		'gzip'					=> $_POST['form']['gzip'] ? '1' : '0',
		'search_all_forums'		=> $_POST['form']['search_all_forums'] ? '1' : '0',
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

	redirect('backstage/features.php', $lang_back['Options updated redirect']);
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
generate_admin_menu('features');

?>
<form method="post" action="features.php">
    <h2><?php echo $lang_back['Features head'] ?></h2>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">General</h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
            	<h4>Topics and posts</h4>
				<input type="checkbox" name="form[quickpost]" value="1" <?php if ($pun_config['o_quickpost'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Quick post help'] ?><br />
				<input type="checkbox" name="form[censoring]" value="1" <?php if ($pun_config['o_censoring'] == '1') echo ' checked="checked"' ?> /> <?php  printf($lang_back['Censor words help'], '<a href="admin_censoring.php">'.$lang_back['Censoring'].'</a>') ?><br />
				<input type="checkbox" name="form[topic_views]" value="1" <?php if ($pun_config['o_topic_views'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Topic views help'] ?><br /><br />
				<h4>User features</h4>
				<input type="checkbox" name="form[users_online]" value="1" <?php if ($pun_config['o_users_online'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Users online help'] ?><br />
				<input type="checkbox" name="form[signatures]" value="1" <?php if ($pun_config['o_signatures'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Signatures help'] ?><br />
				<input type="checkbox" name="form[ranks]" value="1" <?php if ($pun_config['o_ranks'] == '1') echo ' checked="checked"' ?> /> <?php printf($lang_back['User ranks help'], '<a href="admin_ranks.php">'.$lang_back['Ranks'].'</a>') ?><br /><br />
                <h4>Others</h4>
				<input type="checkbox" name="form[search_all_forums]" value="1" <?php if ($pun_config['o_search_all_forums'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Search all help'] ?>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Advanced</h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
				<input type="checkbox" name="form[gzip]" value="1" <?php if ($pun_config['o_gzip'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['GZip help'] ?><br />
            </fieldset>
        </div>
    </div>
	<div class="alert alert-info"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang_back['Save changes'] ?>" /></div>
</form>
<?php

require FORUM_ROOT.'backstage/footer.php';
