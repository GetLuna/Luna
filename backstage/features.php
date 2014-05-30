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

if ($luna_user['g_id'] != FORUM_ADMIN)
	message_backstage($lang['No permission'], false, '403 Forbidden');

if (isset($_POST['form_sent']))
{
	confirm_referrer('backstage/features.php', $lang['Bad HTTP Referer message']);
	
	$form = array(
		'quickpost'					=> isset($_POST['form']['quickpost']) ? '1' : '0',
		'users_online'				=> isset($_POST['form']['users_online']) ? '1' : '0',
		'censoring'					=> isset($_POST['form']['censoring']) ? '1' : '0',
		'signatures'				=> isset($_POST['form']['signatures']) ? '1' : '0',
		'ranks'						=> isset($_POST['form']['ranks']) ? '1' : '0',
		'topic_views'				=> isset($_POST['form']['topic_views']) ? '1' : '0',
		'has_posted'				=> isset($_POST['form']['has_posted']) ? '1' : '0',
		'show_first_run'			=> isset($_POST['form']['show_first_run']) ? '1' : '0',
		'first_run_guests'			=> isset($_POST['form']['first_run_guests']) ? '1' : '0',
		'first_run_message'			=> luna_trim($_POST['form']['first_run_message']),
		'gzip'						=> isset($_POST['form']['gzip']) ? '1' : '0',
		'search_all_forums'			=> isset($_POST['form']['search_all_forums']) ? '1' : '0',
		'enable_advanced_search'	=> isset($_POST['form']['enable_advanced_search']) ? '1' : '0',
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

	redirect('backstage/features.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
generate_admin_menu('features');

?>
<h2><?php echo $lang['Features head'] ?></h2>
<?php
if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>'
?>
<form class="form-horizontal" method="post" action="features.php">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['General'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Topics and posts'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[quickpost]" value="1" <?php if ($luna_config['o_quickpost'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Quick post help'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[censoring]" value="1" <?php if ($luna_config['o_censoring'] == '1') echo ' checked="checked"' ?> />
								<?php printf($lang['Censor words help'], '<a href="censoring.php">'.$lang['Censoring'].'</a>') ?>
                            </label>
                        </div>   
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[topic_views]" value="1" <?php if ($luna_config['o_topic_views'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Topic views help'] ?>
                            </label>
                        </div>  
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[has_posted]" value="1" <?php if ($luna_config['o_has_posted'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Has posted help'] ?>
                            </label>
                        </div>                    
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['User features'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[users_online]" value="1" <?php if ($luna_config['o_users_online'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Users online help'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[signatures]" value="1" <?php if ($luna_config['o_signatures'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Signatures help'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[ranks]" value="1" <?php if ($luna_config['o_ranks'] == '1') echo ' checked="checked"' ?> />
								<?php printf($lang['User ranks help'], '<a href="ranks.php">'.$lang['Ranks'].'</a>') ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Search'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[enable_advanced_search]" value="1" <?php if ($luna_config['o_enable_advanced_search'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Enable advanced search'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[search_all_forums]" value="1" <?php if ($luna_config['o_search_all_forums'] == '1') echo ' checked="checked"' ?> />
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
            <h3 class="panel-title"><?php echo $lang['First run'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['General settings'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[show_first_run]" value="1" <?php if ($luna_config['o_show_first_run'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Show first run label'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[first_run_guests]" value="1" <?php if ($luna_config['o_first_run_guests'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Show guests label'] ?>
                            </label>
                        </div>
					</div>
				</div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Welcome text'] ?><span class="help-block"><?php echo $lang['First run help message'] ?></span>  </label>
                    <div class="col-sm-9">
						<input type="text" class="form-control" name="form[first_run_message]" maxlength="255" value="<?php echo luna_htmlspecialchars($luna_config['o_first_run_message']) ?>" />
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Advanced'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Advanced'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="form[gzip]" value="1" <?php if ($luna_config['o_gzip'] == '1') echo ' checked="checked"' ?> />
                                <?php echo $lang['GZip help'] ?>
                            </label>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
<?php

require FORUM_ROOT.'backstage/footer.php';
