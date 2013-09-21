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

// Load the backstage.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/backstage.php';

if (isset($_POST['form_sent']))
{
	$form = array(
		'default_style'			=> pun_trim($_POST['form']['default_style']),
		'show_version'			=> isset($_POST['form']['show_version']) ? '0' : '1',
		'show_user_info'		=> isset($_POST['form']['show_user_info']) ? '0' : '1',
		'show_post_count'		=> isset($_POST['form']['show_post_count']) ? '0' : '1',
		'smilies'				=> isset($_POST['form']['smilies']) ? '0' : '1',
		'smilies_sig'			=> isset($_POST['form']['smilies_sig']) ? '0' : '1',
		'make_links'			=> isset($_POST['form']['make_links']) ? '0' : '1',
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
		message($lang_common['Bad request']);

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

	redirect('backstage/display.php', $lang_back['Options updated redirect']);
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['Options']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
generate_admin_menu('display');

?>
<h2><?php echo $lang_back['Appearance'] ?></h2>
<form method="post" action="display.php">
    <input type="hidden" name="form_sent" value="1" />
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['General appearance'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <h4><?php echo $lang_back['Default style label'] ?></h4>
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
				<br /><span class="help-block"><?php echo $lang_back['Default style help'] ?></span>
            </fieldset>
		</div>
	</div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Header appearance'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
				<h4><?php echo $lang_back['Menu items label'] ?></h4>
				<textarea class="form-control" name="form[additional_navlinks]" rows="3" cols="55"><?php echo pun_htmlspecialchars($pun_config['o_additional_navlinks']) ?></textarea>
				<p class="help-block"><?php echo $lang_back['Menu items help'] ?></p>
				<h4>Title settings</h4>
                <p class="alert alert-danger">The feature showed below is not available in this version of ModernBB.</p>
                <input type="checkbox" name="form[title_menu]" id="title_menu" value="1" /> Show board title in menu.<br />
				<input type="checkbox" name="form[title_header]" id="title_header" value="1" /> Show board title in header.<br /></p>
            </fieldset>
		</div>
	</div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Footer appearance'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
        	    <input type="checkbox" name="form[show_version]" value="1" <?php if ($pun_config['o_show_version'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Version number help'] ?>
            </fieldset>
		</div>
	</div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang_back['Display head'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
            	<h4>User profile</h4>
				<input type="checkbox" name="form[show_user_info]" value="1" <?php if ($pun_config['o_show_user_info'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Info in posts help'] ?><br />
				<input type="checkbox" name="form[show_post_count]" value="1" <?php if ($pun_config['o_show_post_count'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Post count help'] ?>
            	<h4>Topics and posts</h4>
				<input type="checkbox" name="form[smilies]" value="1" <?php if ($pun_config['o_smilies'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Smilies help'] ?><br />
				<input type="checkbox" name="form[smilies_sig]" value="1" <?php if ($pun_config['o_smilies_sig'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Smilies sigs help'] ?><br />
				<input type="checkbox" name="form[make_links]" value="1" <?php if ($pun_config['o_make_links'] == '1') echo ' checked="checked"' ?> /> <?php echo $lang_back['Clickable links help'] ?><br /><br />

                <b><?php echo $lang_back['Topic review label'] ?></b><br />
                <input type="text" class="form-control" name="form[topic_review]" size="3" maxlength="2" value="<?php echo $pun_config['o_topic_review'] ?>" />
                <br /><span class="help-block"><?php echo $lang_back['Topic review help'] ?></span><br /><br />
                <b><?php echo $lang_back['Topics per page label'] ?></b><br />
                <input type="text" class="form-control" name="form[disp_topics_default]" size="3" maxlength="2" value="<?php echo $pun_config['o_disp_topics_default'] ?>" />
                <br /><span class="help-block"><?php echo $lang_back['Topics per page help'] ?></span><br /><br />
                <b><?php echo $lang_back['Posts per page label'] ?></b><br />
                <input type="text" class="form-control" name="form[disp_posts_default]" size="3" maxlength="3" value="<?php echo $pun_config['o_disp_posts_default'] ?>" />
                <br /><span class="help-block"><?php echo $lang_back['Posts per page help'] ?></span><br /><br />
                <b><?php echo $lang_back['Indent label'] ?></b><br />
                <input type="text" class="form-control" name="form[indent_num_spaces]" size="3" maxlength="3" value="<?php echo $pun_config['o_indent_num_spaces'] ?>" />
                <br /><span class="help-block"><?php echo $lang_back['Indent help'] ?></span><br /><br />
                <b><?php echo $lang_back['Quote depth label'] ?></b><br />
                <input type="text" class="form-control" name="form[quote_depth]" size="3" maxlength="3" value="<?php echo $pun_config['o_quote_depth'] ?>" />
                <br /><span class="help-block"><?php echo $lang_back['Quote depth help'] ?></span><br />
            </fieldset>
        </div>
    </div>
	<div class="alert alert-info"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang_back['Save changes'] ?>" /></div>
</form>
<?php

require FORUM_ROOT.'backstage/footer.php';
