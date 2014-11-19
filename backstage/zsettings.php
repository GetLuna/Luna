<?php

/*
 * Copyright (C) 2013-2014 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod']) {
    header("Location: ../login.php");
}

if (file_exists('../z.txt'))
	$zset = '1';

if (($luna_user['g_id'] != FORUM_ADMIN) || (!isset($zset)))
	message_backstage($lang['No permission'], false, '403 Forbidden');

if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/zsettings.php', $lang['Bad HTTP Referer message']);

	$form = array(
		'notifications'			=> isset($_POST['form']['notifications']) ? '1' : '0',
		'forum_new_style'		=> isset($_POST['form']['forum_new_style']) ? '1' : '0',
		'reading_list'			=> isset($_POST['form']['reading_list']) ? '1' : '0'
	);

	foreach ($form as $key => $input) {
		// Only update values that have changed
		if (array_key_exists('o_'.$key, $luna_config) && $luna_config['o_'.$key] != $input) {
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

	redirect('backstage/zsettings.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('settings', 'zsettings');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>'
?>
<div class="alert alert-danger">
	<h4>zSettings can be activated</h4>
	<p>This board system has been given permission to enable zSettings. It is strongly recommended to do this only when you know what you're doing and you know what these features enable/disable/change. If you do not, please disable zSettings at once.</p>
	<p>zSettings have been provided with a risk label. How higher the label, how higher the risk is for corrupting your database, board or how unstable the feature is. Settings with a "disabled"-label point to settings that can't be changed or settings of which the code isn't available in the core.</p>
</div>
<form class="form-horizontal" method="post" action="zsettings.php">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">zSettings<span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label">zForumNewStyle<span class="help-block"><span class="label label-warning">high</span></span></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
							<label>
								<input type="checkbox" name="form[forum_new_style]" value="1" <?php if ($luna_config['o_forum_new_style'] == '1') echo ' checked="checked"' ?> />
								use the new (experimental) index design
							</label>
						</div>
					</div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">zHooks<span class="help-block"><span class="label label-default">disabled</span> <span class="label label-danger">very high</span></span></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
							<label>
								<input disabled type="checkbox" name="form[hooks]" value="1" <?php if ($luna_config['o_hooks'] == '1') echo ' checked="checked"' ?> />
								enable hooks to be used by plugins
							</label>
						</div>
					</div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">zNotifications<span class="help-block"><span class="label label-warning">high</span></span></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
							<label>
								<input type="checkbox" name="form[notifications]" value="1" <?php if ($luna_config['o_notifications'] == '1') echo ' checked="checked"' ?> />
								enable notifications through Luna
							</label>
						</div>
					</div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">zReadingList<span class="help-block"><span class="label label-success">normal</span></span></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
							<label>
								<input type="checkbox" name="form[reading_list]" value="1" <?php if ($luna_config['o_reading_list'] == '1') echo ' checked="checked"' ?> />
								enable reading list
							</label>
						</div>
					</div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
<?php

require 'footer.php';
