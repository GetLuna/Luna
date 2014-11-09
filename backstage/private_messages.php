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

	redirect('private_messages.php');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('settings', 'private_messages');

?>

<form class="form-horizontal" method="post" action="private_messages.php">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">zPMMainSettings<span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label">zEnablePMs</label>
                    <div class="col-sm-9">
                        <div class="checkbox">
							<label>
								<input type="checkbox" name="form[pms_enabled]" value="1" <?php if ($luna_config['o_pms_enabled'] == '1') echo ' checked="checked"' ?> />
								Enable Private Messaging
							</label>
						</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">zNotificationPMs</label>
                    <div class="col-sm-9">
                        <div class="checkbox">
							<label>
								<input type="checkbox" name="form[pms_notification]" value="1" <?php if ($luna_config['o_pms_notification'] == '1') echo ' checked="checked"' ?> />
								Allow users to be notified through email about new private messages
							</label>
						</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">zReceivers<span class="help-block">The number of receivers a PMs can have</span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="form[pms_max_receiver]" maxlength="5" value="<?php echo luna_htmlspecialchars($luna_config['o_pms_max_receiver'] - 1) ?>" />
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">zPMPermissions<span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
<?php
	$result = $db->query('SELECT g_id, g_title, g_pm, g_pm_limit FROM '.$db->prefix.'groups WHERE g_id !=1 AND g_id !=3 ORDER BY g_id') or error('Unable to find usergroup list', __FILE__, __LINE__, $db->error());
	while ($cur_group = $db->fetch_assoc($result)) :
		if ($luna_user['is_admmod']) :
?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo luna_htmlspecialchars($cur_group['g_title']) ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
							<label>
								<input type="checkbox" name="allow[<?php echo $cur_group['g_id'] ?>]" value="1" <?php if ($cur_group['g_pm'] == '1') echo ' checked="checked"' ?> />
								Allow users to be notified through email about new private messages
							</label>
						</div>
                        <input type="text" class="form-control" name="limit[<?php echo $cur_group['g_id'] ?>]" maxlength="5" value="<?php echo $cur_group['g_pm_limit'] ?>" />
						<p class="help-block">The maximum amount of messages a user in this group can have in his inbox. 0 is no limit.</p>
                    </div>
                </div>
<?php
		endif;
	endwhile;
?>
            </fieldset>
        </div>
    </div>
</form>

<?php

require 'footer.php';