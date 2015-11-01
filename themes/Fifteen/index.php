<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

if ($luna_user['first_run'] == '0') {
?>
<div class="first-run panel panel-default">
	<div class="row first-run-content">
		<div class="col-md-4 col-sm-6 hidden-xs first-run-profile">
			<h3 class="first-run-title"><span class="hidden-xs"><?php echo sprintf(__('Hi there, %s', 'luna'), luna_htmlspecialchars($luna_user['username'])) ?></span><span class="visible-xs-block"><?php echo luna_htmlspecialchars($luna_user['username']) ?></span></h3>
			<span class="first-run-avatar thumbnail">
				<?php echo $user_avatar ?>
			</span>
		</div>
		<?php if (!$luna_user['is_guest']) { ?>
		<div class="col-md-4 hidden-sm hidden-xs">
			<h3 class="first-run-forumtitle"><?php echo sprintf(__('Welcome to %s', 'luna'), $luna_config['o_board_title']) ?></h3>
			<p><?php echo $luna_config['o_first_run_message']; ?></p>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12">
			<div class="list-group first-run-list">
				<a href="settings.php" class="list-group-item"><?php _e('Extend your details', 'luna') ?></a>
				<a href="help.php" class="list-group-item"><?php _e('Get help', 'luna') ?></a>
				<a href="search.php" class="list-group-item"><?php _e('Search the board', 'luna') ?></a>
				<a href="index.php?action=do_not_show&id=<?php echo $luna_user['id'] ?>" class="list-group-item active"><?php _e('Don\'t show again', 'luna') ?></a>
			</div>
		</div>
		<?php } else { ?>
		<?php $redirect_url = check_url(); ?>
		<div class="col-md-4 hidden-sm hidden-xs">
			<h3 class="first-run-forumtitle"><?php echo sprintf(__('Welcome to %s', 'luna'), $luna_config['o_board_title']) ?></h3>
			<div class="list-group first-run-list">
				<a href="register.php" class="list-group-item"><?php _e('Register', 'luna') ?></a>
				<a href="#" data-toggle="modal" data-target="#reqpass" class="list-group-item"><?php _e('Forgotten password', 'luna') ?></a>
			</div>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12">
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
		<div class="list-group list-group-forum">
			<?php draw_forum_list('forum.php', 1, 'category.php', ''); ?>
		</div>
		<hr />
		<div class="list-group list-group-forum">
			<?php echo implode('', $page_threadsearches) ?>
		</div>
		<hr />
		<div class="list-group list-group-forum">
			<?php draw_mark_read('list-group-item', 'index'); ?>
		</div>
	</div>
	<div class="col-sm-9 col-xs-12">
<?php
	// Announcement
	if ($luna_config['o_announcement'] == '1') {
?>
		<div class="alert alert-<?php echo $luna_config['o_announcement_type']; ?> announcement">
			<?php if (!empty($luna_config['o_announcement_title'])) { ?><h4><?php echo $luna_config['o_announcement_title']; ?></h4><?php } ?>
			<?php echo $luna_config['o_announcement_message']; ?>
		</div>
<?php
	}
?>
        <div class="alert alert-info alert-section alert-all">
            <h3><?php _e('Recent activity', 'luna') ?></h3>
        </div>
		<div class="list-group list-group-thread">
<?php
			draw_index_threads_list();
?>
		</div>
	</div>
</div>