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

if ($luna_user['g_id'] != FORUM_ADMIN)
	message_backstage($lang['No permission'], false, '403 Forbidden');

if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/zsettings.php', $lang['Bad HTTP Referer message']);

	$form = array(
		'notifications'			=> luna_trim($_POST['form']['notifications'])
	);

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
<form class="form-horizontal" method="post" action="zsettings.php">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">zSettings<span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label">zNotifications<span class="help-block">zNotificationsHelp</span></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
							<label>
								<input type="checkbox" name="form[notifications]" value="1" <?php if ($luna_config['o_notifications'] == '1') echo ' checked="checked"' ?> />
								zNotificationLabel
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
