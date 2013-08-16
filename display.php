<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$pun_user['is_admmod']) {
    header("Location: login.php");
}

// Load the admin_display.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/admin_display.php';

if (isset($_POST['form_sent']))
{
	confirm_referrer('display.php', $lang_admin_display['Bad HTTP Referer message']);

	$form = array(
		'show_version'			=> $_POST['form']['show_version'] != '1' ? '0' : '1',
		'show_user_info'		=> $_POST['form']['show_user_info'] != '1' ? '0' : '1',
		'show_post_count'		=> $_POST['form']['show_post_count'] != '1' ? '0' : '1',
		'smilies'				=> $_POST['form']['smilies'] != '1' ? '0' : '1',
		'smilies_sig'			=> $_POST['form']['smilies_sig'] != '1' ? '0' : '1',
		'make_links'			=> $_POST['form']['make_links'] != '1' ? '0' : '1',
		'topic_review'			=> (intval($_POST['form']['topic_review']) >= 0) ? intval($_POST['form']['topic_review']) : 0,
		'disp_topics_default'	=> intval($_POST['form']['disp_topics_default']),
		'disp_posts_default'	=> intval($_POST['form']['disp_posts_default']),
		'indent_num_spaces'		=> (intval($_POST['form']['indent_num_spaces']) >= 0) ? intval($_POST['form']['indent_num_spaces']) : 0,
		'quote_depth'			=> (intval($_POST['form']['quote_depth']) > 0) ? intval($_POST['form']['quote_depth']) : 1,
	);

	// Make sure the number of displayed topics and posts is between 3 and 75
	if ($form['disp_topics_default'] < 3)
		$form['disp_topics_default'] = 3;
	else if ($form['disp_topics_default'] > 75)
		$form['disp_topics_default'] = 75;

	if ($form['disp_posts_default'] < 3)
		$form['disp_posts_default'] = 3;
	else if ($form['disp_posts_default'] > 75)
		$form['disp_posts_default'] = 75;

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

	redirect('display.php', $lang_admin_display['Options updated redirect']);
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_admin_common['Admin'], $lang_admin_common['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'admin/header.php';
generate_admin_menu('display');

?>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_admin_display['Display head'] ?></h3>
    </div>
	<div class="panel-body">
        <form method="post" action="display.php">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <table class="table">
                    <tr>
                        <th class="span2"><?php echo $lang_admin_display['Version number label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[show_version]" value="1"<?php if ($pun_config['o_show_version'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_admin_common['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[show_version]" value="0"<?php if ($pun_config['o_show_version'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_admin_common['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_admin_display['Version number help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_admin_display['Info in posts label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[show_user_info]" value="1"<?php if ($pun_config['o_show_user_info'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_admin_common['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[show_user_info]" value="0"<?php if ($pun_config['o_show_user_info'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_admin_common['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_admin_display['Info in posts help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_admin_display['Post count label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[show_post_count]" value="1"<?php if ($pun_config['o_show_post_count'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_admin_common['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[show_post_count]" value="0"<?php if ($pun_config['o_show_post_count'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_admin_common['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_admin_display['Post count help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_admin_display['Smilies label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[smilies]" value="1"<?php if ($pun_config['o_smilies'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_admin_common['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[smilies]" value="0"<?php if ($pun_config['o_smilies'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_admin_common['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_admin_display['Smilies help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_admin_display['Smilies sigs label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="form[smilies_sig]" value="1"<?php if ($pun_config['o_smilies_sig'] == '1') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_admin_common['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="form[smilies_sig]" value="0"<?php if ($pun_config['o_smilies_sig'] == '0') echo ' checked="checked"' ?> />&#160;<strong><?php echo $lang_admin_common['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_admin_display['Smilies sigs help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_admin_display['Clickable links label'] ?></th>
                        <td>
                            <input type="radio" name="form[make_links]" id="form_make_links_1" value="1"<?php if ($pun_config['o_make_links'] == '1') echo ' checked="checked"' ?> />&#160;<label class="conl" for="form_make_links_1"><strong><?php echo $lang_admin_common['Yes'] ?></strong></label>&#160;&#160;&#160;<input type="radio" name="form[make_links]" id="form_make_links_0" value="0"<?php if ($pun_config['o_make_links'] == '0') echo ' checked="checked"' ?> />&#160;<label class="conl" for="form_make_links_0"><strong><?php echo $lang_admin_common['No'] ?></strong></label>
                            <span class="help-block"><?php echo $lang_admin_display['Clickable links help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_admin_display['Topic review label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[topic_review]" size="3" maxlength="2" value="<?php echo $pun_config['o_topic_review'] ?>" />
                            <span class="help-block"><?php echo $lang_admin_display['Topic review help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_admin_display['Topics per page label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[disp_topics_default]" size="3" maxlength="2" value="<?php echo $pun_config['o_disp_topics_default'] ?>" />
                            <span class="help-block"><?php echo $lang_admin_display['Topics per page help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_admin_display['Posts per page label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[disp_posts_default]" size="3" maxlength="3" value="<?php echo $pun_config['o_disp_posts_default'] ?>" />
                            <span class="help-block"><?php echo $lang_admin_display['Posts per page help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_admin_display['Indent label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[indent_num_spaces]" size="3" maxlength="3" value="<?php echo $pun_config['o_indent_num_spaces'] ?>" />
                            <span class="help-block"><?php echo $lang_admin_display['Indent help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_admin_display['Quote depth label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="form[quote_depth]" size="3" maxlength="3" value="<?php echo $pun_config['o_quote_depth'] ?>" />
                            <span class="help-block"><?php echo $lang_admin_display['Quote depth help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <p class="control-group"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang_admin_common['Save changes'] ?>" /></p>
        </form>
    </div>
</div>
<?php

require FORUM_ROOT.'admin/footer.php';
