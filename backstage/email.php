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
    header("Location: login.php");
}

// Load the backstage.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/backstage.php';

if (isset($_POST['form_sent']))
{
	$form = array(
		'admin_email'			=> strtolower(pun_trim($_POST['form']['admin_email'])),
		'webmaster_email'		=> strtolower(pun_trim($_POST['form']['webmaster_email'])),
		'forum_subscriptions'	=> $_POST['form']['forum_subscriptions'] != '1' ? '0' : '1',
		'topic_subscriptions'	=> $_POST['form']['topic_subscriptions'] != '1' ? '0' : '1',
		'smtp_host'				=> pun_trim($_POST['form']['smtp_host']),
		'smtp_user'				=> pun_trim($_POST['form']['smtp_user']),
		'smtp_ssl'				=> $_POST['form']['smtp_ssl'] != '1' ? '0' : '1',
	);
	
	// Change or enter a SMTP password
	if (isset($_POST['form']['smtp_change_pass']))
	{
		$smtp_pass1 = isset($_POST['form']['smtp_pass1']) ? pun_trim($_POST['form']['smtp_pass1']) : '';
		$smtp_pass2 = isset($_POST['form']['smtp_pass2']) ? pun_trim($_POST['form']['smtp_pass2']) : '';

		if ($smtp_pass1 == $smtp_pass2)
			$form['smtp_pass'] = $smtp_pass1;
		else
			message($lang_back['SMTP passwords did not match']);
	}

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

	redirect('backstage/email.php', $lang_back['Options updated redirect']);
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
generate_admin_menu('email');

?>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['E-mail head'] ?></h3>
    </div>
	<div class="panel-body">
        <form method="post" action="email.php">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <table class="table">
                    <tr>
                        <th width="col-md-2"><?php echo $lang_back['Admin e-mail label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[admin_email]" size="50" maxlength="80" value="<?php echo $pun_config['o_admin_email'] ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Admin e-mail help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Webmaster e-mail label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[webmaster_email]" size="50" maxlength="80" value="<?php echo $pun_config['o_webmaster_email'] ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['Webmaster e-mail help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Forum subscriptions label'] ?></th>
                        <td>
                            <label><input type="radio" name="form[forum_subscriptions]" value="1"<?php if ($pun_config['o_forum_subscriptions'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label><input type="radio" name="form[forum_subscriptions]" value="0"<?php if ($pun_config['o_forum_subscriptions'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Forum subscriptions help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Topic subscriptions label'] ?></th>
                        <td>
                            <label><input type="radio" name="form[topic_subscriptions]" value="1"<?php if ($pun_config['o_topic_subscriptions'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label><input type="radio" name="form[topic_subscriptions]" value="0"<?php if ($pun_config['o_topic_subscriptions'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Topic subscriptions help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['SMTP address label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[smtp_host]" size="30" maxlength="100" value="<?php echo pun_htmlspecialchars($pun_config['o_smtp_host']) ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['SMTP address help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['SMTP username label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[smtp_user]" size="25" maxlength="50" value="<?php echo pun_htmlspecialchars($pun_config['o_smtp_user']) ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['SMTP username help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['SMTP password label'] ?></th>
                        <td>
                            <span><input type="checkbox" name="form[smtp_change_pass]" id="form_smtp_change_pass" value="1" />&#160;&#160;<label for="form_smtp_change_pass"><?php echo $lang_back['SMTP change password help'] ?></label></span>
    <?php $smtp_pass = !empty($pun_config['o_smtp_pass']) ? random_key(pun_strlen($pun_config['o_smtp_pass']), true) : ''; ?>
                            <br /><input class="form-control" type="password" name="form[smtp_pass1]" size="25" maxlength="50" value="<?php echo $smtp_pass ?>" />
                            <input class="form-control" type="password" name="form[smtp_pass2]" size="25" maxlength="50" value="<?php echo $smtp_pass ?>" />
                            <br /><span class="help-block"><?php echo $lang_back['SMTP password help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['SMTP SSL label'] ?></th>
                        <td>
                            <label><input type="radio" name="form[smtp_ssl]" value="1"<?php if ($pun_config['o_smtp_ssl'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label><input type="radio" name="form[smtp_ssl]" value="0"<?php if ($pun_config['o_smtp_ssl'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['SMTP SSL help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <p class="control-group"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang_back['Save changes'] ?>" /></p>
        </form>
    </div>
</div>
<?php

require FORUM_ROOT.'backstage/footer.php';
