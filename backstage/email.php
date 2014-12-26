<?php

/*
 * Copyright (C) 2013-2015 Luna
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
	confirm_referrer('backstage/email.php', $lang['Bad HTTP Referer message']);
	
	$form = array(
		'admin_email'			=> strtolower(luna_trim($_POST['form']['admin_email'])),
		'webmaster_email'		=> strtolower(luna_trim($_POST['form']['webmaster_email'])),
		'forum_subscriptions'	=> isset($_POST['form']['forum_subscriptions']) ? '1' : '0',
		'topic_subscriptions'	=> isset($_POST['form']['topic_subscriptions']) ? '1' : '0',
		'smtp_host'				=> luna_trim($_POST['form']['smtp_host']),
		'smtp_user'				=> luna_trim($_POST['form']['smtp_user']),
		'smtp_ssl'				=> isset($_POST['form']['smtp_ssl']) ? '1' : '0',
	);
	
	// Change or enter a SMTP password
	if (isset($_POST['form']['smtp_change_pass'])) {
		$smtp_pass1 = isset($_POST['form']['smtp_pass1']) ? luna_trim($_POST['form']['smtp_pass1']) : '';
		$smtp_pass2 = isset($_POST['form']['smtp_pass2']) ? luna_trim($_POST['form']['smtp_pass2']) : '';

		if ($smtp_pass1 == $smtp_pass2)
			$form['smtp_pass'] = $smtp_pass1;
		else
			message_backstage($lang['SMTP passwords did not match']);
	}

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

	redirect('backstage/email.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('settings', 'email');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>'
?>
<form class="form-horizontal" method="post" action="email.php">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Contact head'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Admin e-mail label'] ?><span class="help-block"><?php echo $lang['Admin e-mail help'] ?></span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="form[admin_email]" maxlength="80" value="<?php echo luna_htmlspecialchars($luna_config['o_admin_email']) ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Webmaster e-mail label'] ?><span class="help-block"><?php echo $lang['Webmaster e-mail help'] ?></span></label>
						<div class="col-sm-9"><input type="text" class="form-control" name="form[webmaster_email]" maxlength="80" value="<?php echo luna_htmlspecialchars($luna_config['o_webmaster_email']) ?>" />
                    </div>
                </div>
            </fieldset>
		</div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Subscriptions head'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Subscriptions head'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
								<input type="checkbox" name="form[forum_subscriptions]" value="1" <?php if ($luna_config['o_forum_subscriptions'] == '1') echo ' checked' ?> />
								<?php echo $lang['Forum subscriptions help'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
								<input type="checkbox" name="form[topic_subscriptions]" value="1" <?php if ($luna_config['o_topic_subscriptions'] == '1') echo ' checked' ?> />
								<?php echo $lang['Topic subscriptions help'] ?>
                            </label>
                        </div>
                    </div>
                </div>
            </fieldset>
		</div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['SMTP head'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['SMTP address label'] ?><span class="help-block"><?php echo $lang['SMTP address help'] ?></span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="form[smtp_host]" maxlength="100" value="<?php echo luna_htmlspecialchars($luna_config['o_smtp_host']) ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['SMTP username label'] ?><span class="help-block"><?php echo $lang['SMTP username help'] ?></span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="form[smtp_user]" maxlength="50" value="<?php echo luna_htmlspecialchars($luna_config['o_smtp_user']) ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['SMTP password label'] ?><span class="help-block"><?php echo $lang['SMTP password help'] ?></span></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
                            	<input type="checkbox" name="form[smtp_change_pass]" id="form_smtp_change_pass" value="1" />
                                <?php echo $lang['SMTP change password help'] ?>
                            </label>
                        </div>
<?php $smtp_pass = !empty($luna_config['o_smtp_pass']) ? random_key(luna_strlen($luna_config['o_smtp_pass']), true) : ''; ?>
						<div class="row">
                        	<div class="col-sm-6">
								<input class="form-control" type="password" name="form[smtp_pass1]" maxlength="50" value="<?php echo $smtp_pass ?>" />
                            </div>
                        	<div class="col-sm-6">
								<input class="form-control" type="password" name="form[smtp_pass2]" maxlength="50" value="<?php echo $smtp_pass ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"></label>
                    <div class="col-sm-9">
                        <input type="checkbox" name="form[smtp_ssl]" value="1" <?php if ($luna_config['o_smtp_ssl'] == '1') echo ' checked' ?> />
						<?php echo $lang['SMTP SSL help'] ?>
                    </div>
                </div>
            </fieldset>
		</div>
    </div>
</form>
<?php

require 'footer.php';
