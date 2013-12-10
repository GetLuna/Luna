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

if ($pun_user['g_id'] != FORUM_ADMIN)
	message($lang['No permission'], false, '403 Forbidden');

if (isset($_POST['form_sent']))
{
	$form = array(
		'quickpost'				=> isset($_POST['form']['quickpost']) ? '1' : '0',
		'users_online'			=> isset($_POST['form']['users_online']) ? '1' : '0',
		'censoring'				=> isset($_POST['form']['censoring']) ? '1' : '0',
		'signatures'			=> isset($_POST['form']['signatures']) ? '1' : '0',
		'ranks'					=> isset($_POST['form']['ranks']) ? '1' : '0',
		'topic_views'			=> isset($_POST['form']['topic_views']) ? '1' : '0',
		'gzip'					=> isset($_POST['form']['gzip']) ? '1' : '0',
		'search_all_forums'		=> isset($_POST['form']['search_all_forums']) ? '1' : '0',
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

	redirect('backstage/features.php', $lang['Options updated redirect']);
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang['Admin'], $lang['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
generate_admin_menu('features');

?>
<form class="form-horizontal" method="post" action="features.php">
    <h2><?php echo $lang['Features head'] ?></h2>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['General'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Topics and posts'] ?></label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[quickpost]" value="1" <?php if ($pun_config['o_quickpost'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Quick post help'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[censoring]" value="1" <?php if ($pun_config['o_censoring'] == '1') echo ' checked="checked"' ?> />
								<?php printf($lang['Censor words help'], '<a href="censoring.php">'.$lang['Censoring'].'</a>') ?>
                            </label>
                        </div>   
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[topic_views]" value="1" <?php if ($pun_config['o_topic_views'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Topic views help'] ?>
                            </label>
                        </div>                     
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['User features'] ?></label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[users_online]" value="1" <?php if ($pun_config['o_users_online'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Users online help'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[signatures]" value="1" <?php if ($pun_config['o_signatures'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Signatures help'] ?>
                            </label>
                        </div>   
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[ranks]" value="1" <?php if ($pun_config['o_ranks'] == '1') echo ' checked="checked"' ?> />
								<?php printf($lang['User ranks help'], '<a href="ranks.php">'.$lang['Ranks'].'</a>') ?>
                            </label>
                        </div>                     
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Others'] ?></label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[search_all_forums]" value="1" <?php if ($pun_config['o_search_all_forums'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Search all help'] ?>
                            </label>
                        </div>                  
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Advanced'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="form[gzip]" value="1" <?php if ($pun_config['o_gzip'] == '1') echo ' checked="checked"' ?> />
						<?php echo $lang['GZip help'] ?>
                    </label>
                </div>
            </fieldset>
        </div>
    </div>
</form>
<?php

require FORUM_ROOT.'backstage/footer.php';
