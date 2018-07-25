<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

if ($luna_user['first_run'] == '0') {

	if ( $luna_user['id'] == -1 ) {
?>
<style>
@media (min-width:768px) {
	.navbar-inverse {
		background-color: rgba(0,0,0,.1);
	}
}
</style>
	<?php } ?>
<div class="heading">
    <div class="jumbotron first-run">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-6">
                	<h3 class="text-center">
						<span class="d-none d-md-inline"><?php echo sprintf(__('Hi there, %s', 'luna'), luna_htmlspecialchars($luna_user['username'])) ?></span>
						<span class="d-inline d-md-none"><?php echo luna_htmlspecialchars($luna_user['username']) ?></span>
					</h3>
                    <img class="img-fluid avatar" src="<?php echo get_user_avatar($luna_user['id']) ?>" />
                </div>
				<?php if (!$luna_user['is_guest']) { ?>
					<div class="col-md-4 d-none d-md-block">
						<h3><?php echo sprintf(__('Welcome to %s', 'luna'), $luna_config['o_board_title']) ?></h3>
						<p><?php echo $luna_config['o_first_run_message']; ?></p>
					</div>
					<div class="col-md-4 col-6">
						<div class="list-group list-group-transparent">
							<a href="settings.php" class="list-group-item"><?php _e('Extend your details', 'luna') ?></a>
							<a href="help.php" class="list-group-item"><?php _e('Get help', 'luna') ?></a>
							<a href="search.php" class="list-group-item"><?php _e('Search the board', 'luna') ?></a>
							<a href="index.php?action=do_not_show&id=<?php echo $luna_user['id'] ?>" class="list-group-item active"><?php _e('Don\'t show again', 'luna') ?></a>
						</div>
					</div>
				<?php } else { ?>
					<?php $redirect_url = check_url(); ?>
					<div class="col-md-4 d-none d-md-block">
						<h3><?php echo sprintf(__('Welcome to %s', 'luna'), $luna_config['o_board_title']) ?></h3>
						<div class="list-group list-group-transparent">
							<a href="register.php" class="list-group-item"><?php _e('Register', 'luna') ?></a>
							<a href="#" data-toggle="modal" data-target="#reqpass" class="list-group-item"><?php _e('Forgotten password', 'luna') ?></a>
						</div>
					</div>
					<div class="col-md-4 col-6">
						<form class="form form-first-run" id="login" method="post" action="login.php?action=in" onsubmit="return process_form(this)">
							<fieldset>
								<h3><?php _e('Login', 'luna') ?></h3>
								<input type="hidden" name="form_sent" value="1" />
								<input type="hidden" name="redirect_url" value="<?php echo luna_htmlspecialchars($redirect_url) ?>" />
								<div class="first-run-login">
									<input class="form-control top-form" type="text" name="req_username" maxlength="25" tabindex="1" placeholder="<?php _e('Username', 'luna') ?>" />
									<input class="form-control bottom-form" type="password" name="req_password" tabindex="2" placeholder="<?php _e('Password', 'luna') ?>" />
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="save_pass" name="save_pass" value="1" checked>
									<label class="custom-control-label" for="save_pass">
										<?php _e('Remember me', 'luna')?>
									</label>
								</div>
								<span class="float-right">
									<input class="btn btn-primary btn-login" type="submit" name="login" value="<?php _e('Login', 'luna') ?>" tabindex="4" />
								</span>
							</fieldset>
						</form>
					</div>
				<?php } ?>
            </div>
        </div>
    </div>
</div>
<?php } else { ?>
<div class="jumbotron profile">
	<div class="container">
		<div class="row">
			<div class="col">
				<h4><?php _e( 'Welcome back', 'luna' ) ?></h4>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<div class="main index profile container">
	<div class="row">
		<div class="col-md-3 col-12 sidebar">
			<?php if (!$luna_user['is_guest'] && $luna_user['first_run'] == '1') { ?>
			<div class="container-avatar d-none d-md-block">
				<img src="<?php echo get_avatar( $luna_user['id'] ) ?>" alt="Avatar" class="avatar">
			</div>
			<?php } if ($luna_config['o_header_search'] && $luna_user['g_search'] == '1'): ?>
			<form id="search" class="input-group search-form" method="get" action="search.php?section=simple">
				<input type="hidden" name="action" value="search" />
                <input type="hidden" name="sort_dir" value="DESC" />
				<div class="input-group">
					<input class="form-control" type="text" name="keywords" placeholder="<?php _e('Search in comments', 'luna') ?>" maxlength="100" />
					<div class="input-group-append">
						<button class="btn btn-light" type="submit" name="search" accesskey="s"><i class="fas fa-fw fa-search"></i></button>
					</div>
				</div>
			</form>
			<hr />
			<?php endif; ?>
            <div class="title-block title-block-primary">
                <h5><?php _e('Recent activity', 'luna') ?></h5>
            </div>
            <div class="list-group list-group-thread">
                <?php draw_index_threads_list(7, 'thread2.php', true); ?>
            </div>
			<?php if ($luna_user['g_search'] == '1') { ?>
			<hr />
			<div class="list-group list-group-none">
				<?php echo implode('', $page_threadsearches) ?>
			</div>
			<?php } ?>
			<hr />
			<div class="list-group list-group-none">
				<a class="list-group-item" href="<?php echo get_read_url('index') ?>"><i class="fas fa-fw fa-glasses"></i> <?php _e('Mark all as read', 'luna') ?></a>
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
            <div class="list-group list-group-thread list-group-advanced">
                <?php draw_forum_list('forum.php', 1, 'category.php', ''); ?>
            </div>
		</div>
	</div>
</div>