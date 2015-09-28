<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$is_admin)
	header("Location: login.php");
if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/features.php', __('Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database (look for o_base_url in the config table) and then clear the cache by deleting all .php files in the /cache directory.', 'luna'));
	
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

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('Features', 'luna'));
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('settings', 'features');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.__('Your settings have been saved.', 'luna').'</h4></div>'
?>
<form class="form-horizontal" method="post" action="features.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('General', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Threads and comments', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[censoring]" value="1" <?php if ($luna_config['o_censoring'] == '1') echo ' checked' ?> />
								<?php printf(__('Censor words in comments. See %s for more info.', 'luna'), '<a href="censoring.php">'.__('Censoring', 'luna').'</a>') ?>
							</label>
						</div>   
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[topic_views]" value="1" <?php if ($luna_config['o_topic_views'] == '1') echo ' checked' ?> />
								<?php _e('Show the number of views for each thread.', 'luna') ?>
							</label>
						</div>  
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[has_posted]" value="1" <?php if ($luna_config['o_has_posted'] == '1') echo ' checked' ?> />
								<?php _e('Show a label in front of the thread where users have commented.', 'luna') ?>
							</label>
						</div>					
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('User features', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[users_online]" value="1" <?php if ($luna_config['o_users_online'] == '1') echo ' checked' ?> />
								<?php _e('Display info on the index page about users currently browsing the board.', 'luna') ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[signatures]" value="1" <?php if ($luna_config['o_signatures'] == '1') echo ' checked' ?> />
								<?php _e('Allow users to attach a signature to their comments.', 'luna') ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[ranks]" value="1" <?php if ($luna_config['o_ranks'] == '1') echo ' checked' ?> />
								<?php printf(__('Use user ranks. See %s for more info.', 'luna'), '<a href="ranks.php">'.__('Ranks', 'luna').'</a>') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Search', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[enable_advanced_search]" value="1" <?php if ($luna_config['o_enable_advanced_search'] == '1') echo ' checked' ?> />
								<?php _e('Allow users to use the advanced search options.', 'luna') ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[search_all_forums]" value="1" <?php if ($luna_config['o_search_all_forums'] == '1') echo ' checked' ?> />
								<?php _e('Allow search only in 1 forum at a time.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Inbox', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Use Inbox', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[pms_enabled]" value="1" <?php if ($luna_config['o_pms_enabled'] == '1') echo ' checked' ?> />
								<?php _e('Allow users to use Inbox.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Receivers', 'luna') ?><span class="help-block"><?php _e('The number of receivers an Inbox message can have.', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[pms_max_receiver]" maxlength="5" value="<?php echo $luna_config['o_pms_max_receiver'] ?>" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('First run', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('General settings', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[show_first_run]" value="1" <?php if ($luna_config['o_show_first_run'] == '1') echo ' checked' ?> />
								<?php _e('Show the first run panel when an user logs in for the first time.', 'luna') ?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[first_run_guests]" value="1" <?php if ($luna_config['o_first_run_guests'] == '1') echo ' checked' ?> />
								<?php _e('Show the first run panel to guests with login field and registration button.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Welcome text', 'luna') ?><span class="help-block"><?php _e('The introduction to the forum displayed in the middle of the first run panel', 'luna') ?></span>  </label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[first_run_message]" maxlength="255" value="<?php echo luna_htmlspecialchars($luna_config['o_first_run_message']) ?>" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('BBCode', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Threads and comments', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[smilies_sig]" value="1" <?php if ($luna_config['o_smilies_sig'] == '1') echo ' checked' ?> />
								<?php _e('Convert smilies to small graphic icons in user signatures.', 'luna') ?>
							</label>
						</div>   
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[make_links]" value="1" <?php if ($luna_config['o_make_links'] == '1') echo ' checked' ?> />
								<?php _e('Convert URLs automatically to clickable hyperlinks.', 'luna') ?>
							</label>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Indent size', 'luna') ?><span class="help-block"><?php _e('Amount of spaces that represent a tab', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[indent_num_spaces]" maxlength="3" value="<?php echo $luna_config['o_indent_num_spaces'] ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Maximum [quote] depth', 'luna') ?><span class="help-block"><?php _e('Maximum [quote] can be used in [quote]', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[quote_depth]" maxlength="3" value="<?php echo $luna_config['o_quote_depth'] ?>" />
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Video height', 'luna') ?><span class="help-block"><?php _e('Height of an embedded video', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[video_width]" maxlength="4" value="<?php echo $luna_config['o_video_width'] ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Video width', 'luna') ?><span class="help-block"><?php _e('Width of an embedded video', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="form[video_height]" maxlength="4" value="<?php echo $luna_config['o_video_height'] ?>" />
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Advanced', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<input type="hidden" name="form_sent" value="1" />
			<fieldset>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Advanced', 'luna') ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[gzip]" value="1" <?php if ($luna_config['o_gzip'] == '1') echo ' checked' ?> />
								<?php _e('Gzip output sent to the browser. This will reduce bandwidth usage, but use some more CPU. This feature requires that PHP is configured with zlib. If you already have one of the Apache modules (mod_gzip/mod_deflate) set up to compress PHP scripts, disable this feature.', 'luna') ?>
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
