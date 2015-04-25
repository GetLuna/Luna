<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

if ($luna_user['first_run'] == '0') {
?>
<div class="first-run panel panel-default">
	<div class="row first-run-content">
		<div class="col-md-4 col-sm-6 col-xs-5 first-run-profile">
			<h3 class="first-run-title"><?php echo sprintf(__('Hi there, %s', 'luna'), luna_htmlspecialchars($luna_user['username'])) ?></h3>
			<span class="first-run-avatar thumbnail">
				<?php echo $user_avatar ?>
			</span>
		</div>
		<?php if (!$luna_user['is_guest']) { ?>
		<div class="col-md-4 hidden-sm hidden-xs">
			<h3 class="first-run-forumtitle"><?php echo sprintf(__('Welcome to %s', 'luna'), $luna_config['o_board_title']) ?></h3>
			<p><?php echo $luna_config['o_first_run_message']; ?></p>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-7">
			<div class="list-group first-run-list">
				<a href="settings.php" class="list-group-item"><?php _e('Extend your details', 'luna') ?></a>
				<a href="help.php" class="list-group-item"><?php _e('Get help', 'luna') ?></a>
				<a href="search.php" class="list-group-item"><?php _e('Search the board', 'luna') ?></a>
				<a href="index.php?action=do_not_show&id=<?php echo $luna_user['id'] ?>" class="list-group-item active"><?php _e('Don\'t show again', 'luna') ?></a>
			</div>
		</div>
		<?php } else { ?>
		<?php $redirect_url = check_url(); ?>
		<div class="col-md-4 hidden-sm">
			<h3 class="first-run-forumtitle"><?php echo sprintf(__('Welcome to %s', 'luna'), $luna_config['o_board_title']) ?></h3>
			<div class="list-group first-run-list">
				<a href="register.php" class="list-group-item"><?php _e('Register', 'luna') ?></a>
				<a href="#" data-toggle="modal" data-target="#reqpass" class="list-group-item"><?php _e('Forgotten password', 'luna') ?></a>
			</div>
		</div>
		<div class="col-md-4 col-sm-6">
			<form class="form form-first-run" id="login" method="post" action="login.php?action=in" onsubmit="return process_form(this)">
				<fieldset>
					<h3><?php _e('Login', 'luna') ?></h3>
					<input type="hidden" name="form_sent" value="1" />
					<input type="hidden" name="redirect_url" value="<?php echo luna_htmlspecialchars($redirect_url) ?>" />
					<div class="first-run-login">
						<input class="form-control top-form" type="text" name="req_username" maxlength="25" tabindex="1" placeholder="<?php _e('Username', 'luna') ?>" />
						<input class="form-control bottom-form" type="password" name="req_password" tabindex="2" placeholder="<?php _e('Password', 'luna') ?>" />
					</div>
					<label><input type="checkbox" name="save_pass" value="1" tabindex="3" checked /> <?php _e('Remember me', 'luna') ?></label>
					<span class="pull-right">
						<input class="btn btn-primary btn-login" type="submit" name="login" value="<?php _e('Login', 'luna') ?>" tabindex="4" />
					</span>
				</fieldset>
			</form>
		</div>
		<?php } ?>
	</div>
</div>
<?php } ?>
<div class="row index">
	<div class="col-sm-3 col-xs-12">
		<?php if ((is_subforum($id) && $id != '0')): ?>
			<h5 class="list-group-head"><?php _e('Subforums', 'luna') ?></h5>
			<div class="list-group list-group-forum">
				<?php draw_subforum_list('index.php') ?>
			</div>
			<hr />
		<?php endif; ?>
		<div class="list-group list-group-forum hidden-xs">
			<?php draw_forum_list('index.php') ?>
		</div>
		<div class="list-group list-group-forum visible-xs-block">
			<?php draw_forum_list('viewforum.php') ?>
		</div>
		<hr />
		<div class="list-group list-group-forum">
			<?php draw_mark_read('list-group-item', 'index') ?>
			<?php if ($id != '0' && $is_admmod) { ?>
				<a class="list-group-item" href="backstage/moderate.php?fid=<?php echo $forum_id ?>&p=<?php echo $p ?>"><span class="fa fa-fw fa-eye"></span> <?php _e('Moderate forum', 'luna') ?></a>
			<?php } ?>
		</div>
	</div>
	<div class="col-sm-9 col-xs-12">
<?php
	// Announcement
	if ($luna_config['o_announcement'] == '1')
		echo '<div class="alert alert-info announcement"><div>'.$luna_config['o_announcement_message'].'</div></div>';

	draw_section_info($id);

	if ($id != '0')
		echo $paging_links;
?>
		<div class="list-group list-group-topic">
<?php
			draw_index_topics_list($id);
?>
		</div>
<?php
	if ($id != '0')
		echo $paging_links;
?>
	</div>
</div>