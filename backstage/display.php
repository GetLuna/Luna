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

// Load the language file
require FORUM_ROOT.'lang/'.$admin_language.'/language.php';

if (isset($_POST['form_sent']))
{
	$form = array(
		'default_style'			=> pun_trim($_POST['form']['default_style']),
		'show_version'			=> isset($_POST['form']['show_version']) ? '1' : '0',
		'show_user_info'		=> isset($_POST['form']['show_user_info']) ? '1' : '0',
		'show_post_count'		=> isset($_POST['form']['show_post_count']) ? '1' : '0',
		'smilies'				=> isset($_POST['form']['smilies']) ? '1' : '0',
		'smilies_sig'			=> isset($_POST['form']['smilies_sig']) ? '1' : '0',
		'make_links'			=> isset($_POST['form']['make_links']) ? '1' : '0',
		'header_title'			=> isset($_POST['form']['header_title']) ? '1' : '0',
		'menu_title'			=> isset($_POST['form']['menu_title']) ? '1' : '0',
		'topic_review'			=> (intval($_POST['form']['topic_review']) >= 0) ? intval($_POST['form']['topic_review']) : 0,
		'disp_topics_default'	=> intval($_POST['form']['disp_topics_default']),
		'disp_posts_default'	=> intval($_POST['form']['disp_posts_default']),
		'indent_num_spaces'		=> (intval($_POST['form']['indent_num_spaces']) >= 0) ? intval($_POST['form']['indent_num_spaces']) : 0,
		'quote_depth'			=> (intval($_POST['form']['quote_depth']) > 0) ? intval($_POST['form']['quote_depth']) : 1,
		'additional_navlinks'	=> pun_trim($_POST['form']['additional_navlinks']),
	);

	if ($form['additional_navlinks'] != '')
		$form['additional_navlinks'] = pun_trim(pun_linebreaks($form['additional_navlinks']));

	// Make sure the number of displayed topics and posts is between 3 and 75
	if ($form['disp_topics_default'] < 3)
		$form['disp_topics_default'] = 3;
	else if ($form['disp_topics_default'] > 75)
		$form['disp_topics_default'] = 75;

	if ($form['disp_posts_default'] < 3)
		$form['disp_posts_default'] = 3;
	else if ($form['disp_posts_default'] > 75)
		$form['disp_posts_default'] = 75;

	$styles = forum_list_styles();
	if (!in_array($form['default_style'], $styles))
		message($lang['Bad request']);

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

	redirect('backstage/display.php', $lang['Options updated redirect']);
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang['Admin'], $lang['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
generate_admin_menu('display');

?>
<h2><?php echo $lang['Appearance'] ?></h2>
<form method="post" action="display.php">
    <input type="hidden" name="form_sent" value="1" />
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['General appearance'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <h4><?php echo $lang['Default style label'] ?></h4>
				<select class="form-control" name="form[default_style]">
<?php

		$styles = forum_list_styles();

		foreach ($styles as $temp)
		{
			if ($pun_config['o_default_style'] == $temp)
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$temp.'" selected="selected">'.str_replace('_', ' ', $temp).'</option>'."\n";
			else
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$temp.'">'.str_replace('_', ' ', $temp).'</option>'."\n";
		}

?>
				</select>
				<br /><span class="help-block"><?php echo $lang['Default style help'] ?></span>
            </fieldset>
		</div>
	</div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Header appearance'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
				<h4><?php echo $lang['Menu items head'] ?></h4>
				<textarea class="form-control" name="form[additional_navlinks]" rows="3" cols="55"><?php echo pun_htmlspecialchars($pun_config['o_additional_navlinks']) ?></textarea>
				<p class="help-block"><?php echo $lang['Menu items help'] ?></p>
				<h4><?php echo $lang['Title settings head'] ?></h4>
                <input type="checkbox" name="form[menu_title]" value="1" <?php if ($pun_config['o_menu_title'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang['Title in menu'] ?><br />
				<input type="checkbox" name="form[header_title]" value="1" <?php if ($pun_config['o_header_title'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang['Title in header'] ?></p>
            </fieldset>
		</div>
	</div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Footer appearance'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
        	    <input type="checkbox" name="form[show_version]" value="1" <?php if ($pun_config['o_show_version'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang['Version number help'] ?>
            </fieldset>
		</div>
	</div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Display head'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save changes'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
            	<h4><?php echo $lang['User profile head'] ?></h4>
				<input type="checkbox" name="form[show_user_info]" value="1" <?php if ($pun_config['o_show_user_info'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang['Info in posts help'] ?><br />
				<input type="checkbox" name="form[show_post_count]" value="1" <?php if ($pun_config['o_show_post_count'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang['Post count help'] ?>
            	<h4><?php echo $lang['Topics posts head'] ?></h4>
				<input type="checkbox" name="form[smilies]" value="1" <?php if ($pun_config['o_smilies'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang['Smilies help'] ?><br />
				<input type="checkbox" name="form[smilies_sig]" value="1" <?php if ($pun_config['o_smilies_sig'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang['Smilies sigs help'] ?><br />
				<input type="checkbox" name="form[make_links]" value="1" <?php if ($pun_config['o_make_links'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang['Clickable links help'] ?><br /><br />
                <b><?php echo $lang['Topic review label'] ?></b><br />
                <input type="text" class="form-control" name="form[topic_review]" size="3" maxlength="2" value="<?php echo $pun_config['o_topic_review'] ?>" />
                <br /><span class="help-block"><?php echo $lang['Topic review help'] ?></span><br />
                <b><?php echo $lang['Topics per page label'] ?></b><br />
                <input type="text" class="form-control" name="form[disp_topics_default]" size="3" maxlength="2" value="<?php echo $pun_config['o_disp_topics_default'] ?>" />
                <br /><span class="help-block"><?php echo $lang['Topics per page help'] ?></span><br />
                <b><?php echo $lang['Posts per page label'] ?></b><br />
                <input type="text" class="form-control" name="form[disp_posts_default]" size="3" maxlength="3" value="<?php echo $pun_config['o_disp_posts_default'] ?>" />
                <br /><span class="help-block"><?php echo $lang['Posts per page help'] ?></span><br />
                <b><?php echo $lang['Indent label'] ?></b><br />
                <input type="text" class="form-control" name="form[indent_num_spaces]" size="3" maxlength="3" value="<?php echo $pun_config['o_indent_num_spaces'] ?>" />
                <br /><span class="help-block"><?php echo $lang['Indent help'] ?></span><br />
                <b><?php echo $lang['Quote depth label'] ?></b><br />
                <input type="text" class="form-control" name="form[quote_depth]" size="3" maxlength="3" value="<?php echo $pun_config['o_quote_depth'] ?>" />
                <br /><span class="help-block"><?php echo $lang['Quote depth help'] ?></span>
            </fieldset>
        </div>
    </div>
</form>
<?php

require FORUM_ROOT.'backstage/footer.php';
