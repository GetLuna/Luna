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

if ($pun_user['g_id'] != FORUM_ADMIN)
	message($lang_common['No permission'], false, '403 Forbidden');

// Load the backstage.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/backstage.php';

if (isset($_POST['form_sent']))
{
	$form = array(
		'quickpost'				=> $_POST['form']['quickpost'] != '1' ? '0' : '1',
		'users_online'			=> $_POST['form']['users_online'] != '1' ? '0' : '1',
		'censoring'				=> $_POST['form']['censoring'] != '1' ? '0' : '1',
		'signatures'			=> $_POST['form']['signatures'] != '1' ? '0' : '1',
		'ranks'					=> $_POST['form']['ranks'] != '1' ? '0' : '1',
		'show_dot'				=> $_POST['form']['show_dot'] != '1' ? '0' : '1',
		'topic_views'			=> $_POST['form']['topic_views'] != '1' ? '0' : '1',
		'quickjump'				=> $_POST['form']['quickjump'] != '1' ? '0' : '1',
		'gzip'					=> $_POST['form']['gzip'] != '1' ? '0' : '1',
		'search_all_forums'		=> $_POST['form']['search_all_forums'] != '1' ? '0' : '1',
		'additional_navlinks'	=> pun_trim($_POST['form']['additional_navlinks']),
	);

	if ($form['additional_navlinks'] != '')
		$form['additional_navlinks'] = pun_trim(pun_linebreaks($form['additional_navlinks']));

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
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Features head'] ?></h3>
    </div>
	<div class="panel-body">
        <form method="post" action="features.php">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <table class="table">
                    <tr>
                        <th class="col-md-2"><?php echo $lang_back['Quick post label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[quickpost]" value="1"<?php if ($pun_config['o_quickpost'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[quickpost]" value="0"<?php if ($pun_config['o_quickpost'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Quick post help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Users online label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[users_online]" value="1"<?php if ($pun_config['o_users_online'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[users_online]" value="0"<?php if ($pun_config['o_users_online'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Users online help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><a name="censoring"></a><?php echo $lang_back['Censor words label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[censoring]" value="1"<?php if ($pun_config['o_censoring'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[censoring]" value="0"<?php if ($pun_config['o_censoring'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php printf($lang_back['Censor words help'], '<a href="admin_censoring.php">'.$lang_back['Censoring'].'</a>') ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><a name="signatures"></a><?php echo $lang_back['Signatures label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[signatures]" value="1"<?php if ($pun_config['o_signatures'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[signatures]" value="0"<?php if ($pun_config['o_signatures'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Signatures help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><a name="ranks"></a><?php echo $lang_back['User ranks label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[ranks]" value="1"<?php if ($pun_config['o_ranks'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[ranks]" value="0"<?php if ($pun_config['o_ranks'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php printf($lang_back['User ranks help'], '<a href="admin_ranks.php">'.$lang_back['Ranks'].'</a>') ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['User has posted label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[show_dot]" value="1"<?php if ($pun_config['o_show_dot'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[show_dot]" value="0"<?php if ($pun_config['o_show_dot'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['User has posted help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Topic views label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[topic_views]" value="1"<?php if ($pun_config['o_topic_views'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[topic_views]" value="0"<?php if ($pun_config['o_topic_views'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Topic views help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Quick jump label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[quickjump]" value="1"<?php if ($pun_config['o_quickjump'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[quickjump]" value="0"<?php if ($pun_config['o_quickjump'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Quick jump help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['GZip label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[gzip]" value="1"<?php if ($pun_config['o_gzip'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[gzip]" value="0"<?php if ($pun_config['o_gzip'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['GZip help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Search all label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[search_all_forums]" value="1"<?php if ($pun_config['o_search_all_forums'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[search_all_forums]" value="0"<?php if ($pun_config['o_search_all_forums'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_back['Search all help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Menu items label'] ?></th>
                        <td>
                            <textarea class="form-control" name="form[additional_navlinks]" rows="3" cols="55"><?php echo pun_htmlspecialchars($pun_config['o_additional_navlinks']) ?></textarea>
                            <span class="help-block"><?php echo $lang_back['Menu items help'] ?></span>
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
