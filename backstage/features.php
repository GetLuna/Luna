<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
	header("Location: ../login.php");
if ($luna_user['g_id'] != FORUM_ADMIN)
	message_backstage($lang['No permission'], false, '403 Forbidden');

if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/features.php', $lang['Bad HTTP Referer message']);
	
	$form = array(
		'users_online'					=> isset($_POST['form']['users_online']) ? '1' : '0',
		'censoring'						=> isset($_POST['form']['censoring']) ? '1' : '0',
		'signatures'					=> isset($_POST['form']['signatures']) ? '1' : '0',
		'ranks'							=> isset($_POST['form']['ranks']) ? '1' : '0',
		'topic_views'					=> isset($_POST['form']['topic_views']) ? '1' : '0',
		'has_posted'					=> isset($_POST['form']['has_posted']) ? '1' : '0',
		'show_first_run'				=> isset($_POST['form']['show_first_run']) ? '1' : '0',
		'first_run_guests'				=> isset($_POST['form']['first_run_guests']) ? '1' : '0',
		'first_run_message'				=> luna_trim($_POST['form']['first_run_message']),
		'smilies'						=> isset($_POST['form']['smilies']) ? '1' : '0',
		'smilies_sig'					=> isset($_POST['form']['smilies_sig']) ? '1' : '0',
		'make_links'					=> isset($_POST['form']['make_links']) ? '1' : '0',
		'indent_num_spaces'				=> (intval($_POST['form']['indent_num_spaces']) >= 0) ? intval($_POST['form']['indent_num_spaces']) : 0,
		'quote_depth'					=> (intval($_POST['form']['quote_depth']) > 0) ? intval($_POST['form']['quote_depth']) : 1,
		'video_width'					=> (intval($_POST['form']['video_width']) > 0) ? intval($_POST['form']['video_width']) : 640,
		'video_height'					=> (intval($_POST['form']['video_height']) > 0) ? intval($_POST['form']['video_height']) : 360,
		'gzip'							=> isset($_POST['form']['gzip']) ? '1' : '0',
		'search_all_forums'				=> isset($_POST['form']['search_all_forums']) ? '1' : '0',
		'enable_advanced_search'		=> isset($_POST['form']['enable_advanced_search']) ? '1' : '0',
		'pms_enabled'					=> isset($_POST['form']['pms_enabled']) ? '1' : '0',
		'pms_notification'				=> isset($_POST['form']['pms_notification']) ? '1' : '0',
		'pms_max_receiver'				=> (intval($_POST['form']['pms_max_receiver']) > 0) ? intval($_POST['form']['pms_max_receiver']) : 5
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

	redirect('backstage/features.php?saved=true');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Features']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('settings', 'features');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>'
?>
<form class="form-horizontal" method="post" action="features.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['General'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Topics and posts'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[censoring]" value="1" <?php if ($luna_config['o_censoring'] == '1') echo ' checked' ?> />
								<?php printf($lang['Censor words help'], '<a href="censoring.php">'.$lang['Censoring'].'</a>') ?>
							</label>
						</div>   
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[topic_views]" value="1" <?php if ($luna_config['o_topic_views'] == '1') echo ' checked' ?> />
								<?php echo $lang['Topic views help'] ?>
							</label>
						</div>  
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[has_posted]" value="1" <?php if ($luna_config['o_has_posted'] == '1') echo ' checked' ?> />
								<?php echo $lang['Has posted help'] ?>
							</label>
						</div>					
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['User features'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[users_online]" value="1" <?php if ($luna_config['o_users_online'] == '1') echo ' checked' ?> />
								<?php echo $lang['Users online help'] ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[signatures]" value="1" <?php if ($luna_config['o_signatures'] == '1') echo ' checked' ?> />
								<?php echo $lang['Signatures help'] ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[ranks]" value="1" <?php if ($luna_config['o_ranks'] == '1') echo ' checked' ?> />
								<?php printf($lang['User ranks help'], '<a href="ranks.php">'.$lang['Ranks'].'</a>') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Search'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input disabled type="checkbox" name="form[enable_advanced_search]" value="1" <?php if ($luna_config['o_enable_advanced_search'] == '1') echo ' checked' ?> />
								<?php echo $lang['Enable advanced search'] ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input disabled type="checkbox" name="form[search_all_forums]" value="1" <?php if ($luna_config['o_search_all_forums'] == '1') echo ' checked' ?> />
								<?php echo $lang['Search all help'] ?>
							</label>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Inbox<span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label">Use Inbox</label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[pms_enabled]" value="1" <?php if ($luna_config['o_pms_enabled'] == '1') echo ' checked' ?> />
								Allow users to use Inbox.
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">Inbox Notifications</label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[pms_notification]" value="1" <?php if ($luna_config['o_pms_notification'] == '1') echo ' checked' ?> />
								Allow users to be notified through email about new Inbox messages.
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">Receivers<span class="help-block">The number of receivers an Inbox message can have</span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[pms_max_receiver]" maxlength="5" value="<?php echo $luna_config['o_pms_max_receiver'] ?>" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['First run'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['General settings'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[show_first_run]" value="1" <?php if ($luna_config['o_show_first_run'] == '1') echo ' checked' ?> />
								<?php echo $lang['Show first run label'] ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[first_run_guests]" value="1" <?php if ($luna_config['o_first_run_guests'] == '1') echo ' checked' ?> />
								<?php echo $lang['Show guests label'] ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Welcome text'] ?><span class="help-block"><?php echo $lang['First run help message'] ?></span>  </label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[first_run_message]" maxlength="255" value="<?php echo luna_htmlspecialchars($luna_config['o_first_run_message']) ?>" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['BBCode'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Topics and posts'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input disabled type="checkbox" name="form[smilies]" value="1" <?php if ($luna_config['o_smilies'] == '1') echo ' checked' ?> />
								<?php echo $lang['Smilies help'] ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input disabled type="checkbox" name="form[smilies_sig]" value="1" <?php if ($luna_config['o_smilies_sig'] == '1') echo ' checked' ?> />
								<?php echo $lang['Smilies sigs help'] ?>
							</label>
						</div>   
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[make_links]" value="1" <?php if ($luna_config['o_make_links'] == '1') echo ' checked' ?> />
								<?php echo $lang['Clickable links help'] ?>
							</label>
						</div>
					</div>
				</div>
				<hr />
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
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Video height'] ?><span class="help-block"><?php echo $lang['Video height help'] ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[video_width]" maxlength="4" value="<?php echo $luna_config['o_video_width'] ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Video width'] ?><span class="help-block"><?php echo $lang['Video width help'] ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[video_height]" maxlength="4" value="<?php echo $luna_config['o_video_height'] ?>" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Advanced'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Advanced'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[gzip]" value="1" <?php if ($luna_config['o_gzip'] == '1') echo ' checked' ?> />
								<?php echo $lang['GZip help'] ?>
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
