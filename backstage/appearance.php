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
	confirm_referrer('backstage/appearance.php', $lang['Bad HTTP Referer message']);
	
	$form = array(
		'default_style'			=> luna_trim($_POST['form']['default_style']),
		'show_version'			=> isset($_POST['form']['show_version']) ? '1' : '0',
		'show_user_info'		=> isset($_POST['form']['show_user_info']) ? '1' : '0',
		'show_post_count'		=> isset($_POST['form']['show_post_count']) ? '1' : '0',
		'smilies'				=> isset($_POST['form']['smilies']) ? '1' : '0',
		'smilies_sig'			=> isset($_POST['form']['smilies_sig']) ? '1' : '0',
		'make_links'			=> isset($_POST['form']['make_links']) ? '1' : '0',
		'show_index'			=> isset($_POST['form']['show_index']) ? '1' : '0',
		'show_userlist'			=> isset($_POST['form']['show_userlist']) ? '1' : '0',
		'show_search'			=> isset($_POST['form']['show_search']) ? '1' : '0',
		'show_rules'			=> isset($_POST['form']['show_rules']) ? '1' : '0',
		'header_title'			=> isset($_POST['form']['header_title']) ? '1' : '0',
		'header_desc'			=> isset($_POST['form']['header_desc']) ? '1' : '0',
		'menu_title'			=> isset($_POST['form']['menu_title']) ? '1' : '0',
		'show_index_stats'		=> isset($_POST['form']['show_index_stats']) ? '1' : '0',
		'topic_review'			=> (intval($_POST['form']['topic_review']) >= 0) ? intval($_POST['form']['topic_review']) : 0,
		'disp_topics_default'	=> intval($_POST['form']['disp_topics_default']),
		'disp_posts_default'	=> intval($_POST['form']['disp_posts_default']),
		'indent_num_spaces'		=> (intval($_POST['form']['indent_num_spaces']) >= 0) ? intval($_POST['form']['indent_num_spaces']) : 0,
		'quote_depth'			=> (intval($_POST['form']['quote_depth']) > 0) ? intval($_POST['form']['quote_depth']) : 1,
		'additional_navlinks'	=> luna_trim($_POST['form']['additional_navlinks']),
	);

	if ($form['additional_navlinks'] != '')
		$form['additional_navlinks'] = luna_trim(luna_linebreaks($form['additional_navlinks']));

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

	redirect('backstage/appearance.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Appearance']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
generate_admin_menu('display');

?>
<h2><?php echo $lang['Appearance'] ?></h2>
<?php
if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>'
?>
<form class="form-horizontal" method="post" action="appearance.php">
    <input type="hidden" name="form_sent" value="1" />
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Header appearance'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Menu items head'] ?><span class="help-block"><?php echo $lang['Menu items help'] ?></span></label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="form[additional_navlinks]" rows="8"><?php echo luna_htmlspecialchars($luna_config['o_additional_navlinks']) ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Default menu'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
								<input type="checkbox" name="form[show_index]" value="1" <?php if ($luna_config['o_show_index'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Menu show index'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
								<input type="checkbox" name="form[show_userlist]" value="1" <?php if ($luna_config['o_show_userlist'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Menu show user list'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
								<input type="checkbox" name="form[show_search]" value="1" <?php if ($luna_config['o_show_search'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Menu show search'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
								<input type="checkbox" name="form[show_rules]" value="1" <?php if ($luna_config['o_show_rules'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Menu show rules'] ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Title settings head'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
								<input type="checkbox" name="form[header_title]" value="1" <?php if ($luna_config['o_header_title'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Title in header'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
								<input type="checkbox" name="form[menu_title]" value="1" <?php if ($luna_config['o_menu_title'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Title in menu'] ?>
                            </label>
                        </div>                        
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Description settings head'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
								<input type="checkbox" name="form[header_desc]" value="1" <?php if ($luna_config['o_header_desc'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Description in header'] ?>
                            </label>
                        </div>                       
                    </div>
                </div>
            </fieldset>
		</div>
	</div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Footer appearance'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Footer'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="form[show_version]" value="1" <?php if ($luna_config['o_show_version'] == '1') echo ' checked="checked"' ?> />
                                <?php echo $lang['Version number help'] ?>
                            </label>
                        </div>
                    </div>
               	</div>
            </fieldset>
		</div>
	</div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Display head'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="save" value="<?php echo $lang['Save'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['User profile head'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
								<input type="checkbox" name="form[show_user_info]" value="1" <?php if ($luna_config['o_show_user_info'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Info in posts help'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
								<input type="checkbox" name="form[show_post_count]" value="1" <?php if ($luna_config['o_show_post_count'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Post count help'] ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Topics and posts'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="form[smilies]" value="1" <?php if ($luna_config['o_smilies'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Smilies help'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="form[smilies_sig]" value="1" <?php if ($luna_config['o_smilies_sig'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Smilies sigs help'] ?>
                            </label>
                        </div>   
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="form[make_links]" value="1" <?php if ($luna_config['o_make_links'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Clickable links help'] ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Index panels head'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="form[show_index_stats]" value="1" <?php if ($luna_config['o_show_index_stats'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Index statistics help'] ?>
                            </label>
                        </div>           
                    </div>
                </div>
                <hr />
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Topic review label'] ?><span class="help-block"><?php echo $lang['Topic review help'] ?></span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="form[topic_review]" maxlength="2" value="<?php echo $luna_config['o_topic_review'] ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Topics'] ?><span class="help-block"><?php echo $lang['Topics per page help'] ?></span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="form[disp_topics_default]" maxlength="2" value="<?php echo $luna_config['o_disp_topics_default'] ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Posts per page label'] ?><span class="help-block"><?php echo $lang['Posts per page help'] ?></span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="form[disp_posts_default]" maxlength="3" value="<?php echo $luna_config['o_disp_posts_default'] ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Indent label'] ?><span class="help-block"><?php echo $lang['Indent help'] ?></span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="form[indent_num_spaces]" maxlength="3" value="<?php echo $luna_config['o_indent_num_spaces'] ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Quote depth label'] ?><span class="help-block"><?php echo $lang['Quote depth help'] ?></span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="form[quote_depth]" maxlength="3" value="<?php echo $luna_config['o_quote_depth'] ?>" />
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
<?php

require FORUM_ROOT.'backstage/footer.php';
