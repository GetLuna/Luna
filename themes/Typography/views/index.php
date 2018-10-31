<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;
?>
<div class="container">
	<div class="jumbotron index">
		<div class="row">
			<?php if ($luna_user['first_run'] == '1' && !$luna_user['is_guest']) { ?>
				<div class="col-md-4 col-6">
					<h3 class="text-center">
						<span class="d-none d-md-inline"><?php echo sprintf(__('Hi there, %s', 'luna'), luna_htmlspecialchars($luna_user['username'])) ?></span>
						<span class="d-inline d-md-none"><?php echo luna_htmlspecialchars($luna_user['username']) ?></span>
					</h3>
					<img class="img-fluid avatar" src="<?php echo get_user_avatar($luna_user['id']) ?>" />
				</div>
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
			<?php } ?>
			<?php if ($luna_config['o_header_search'] && $luna_user['g_search'] == '1'): ?>
			<div class="col-12 col-search">
				<form id="search" class="input-group search-form" method="get" action="search.php?section=simple">
					<input type="hidden" name="action" value="search" />
					<input type="hidden" name="sort_dir" value="DESC" />
					<div class="input-group">
						<input class="form-control" type="text" name="keywords" placeholder="<?php _e('Search', 'luna') ?>" maxlength="100" autofocus />
						<div class="input-group-append">
							<button class="btn btn-light" type="submit" name="search" accesskey="s"><i class="fas fa-fw fa-search"></i></button>
						</div>
					</div>
				</form>
			</div>
			<?php endif; ?>
			<?php if ($luna_user['g_search'] == '1' && !$luna_user['is_guest']) { ?>
				<div class="col-12 col-tabs">
					<nav class="nav nav-tabs">
						<a class="nav-item nav-link active" href="index.php">
							<?php _e('Latest', 'luna') ?>
						</a>
						<a class="nav-item nav-link" href="search.php?action=show_new">
							<?php _e('New', 'luna') ?>
						</a>
						<a class="nav-item nav-link" href="search.php?action=show_recent">
							<?php _e('Active', 'luna') ?>
						</a>
						<a class="nav-item nav-link" href="search.php?action=show_unanswered">
							<?php _e('Unanswered', 'luna') ?>
						</a>
					</nav>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<div class="main index container">
	<div class="row">
		<div class="col-md-4 col-12 sidebar">
			<div class="list-group list-group-nav">
				<?php foreach( $board as $category ) { ?>
					<h4><?php echo $category->getName() ?></h4>
					<?php foreach( $category->getForums() as $forum ) { ?>
						<a href="<?php echo $forum->getForumUrl() ?>" class="list-group-item <?php echo $item_status ?>">
							<h5 class="mb-1" style="<?php echo 'color: '.$forum->getColor().';' ?>">
								<?php echo $forum->getIconMarkup() ?> <span class="forum-title"><?php echo $forum->getName() ?></span>
							</h5>
							<p class="text-muted"><?php printf( _n('%s thread', '%s threads', $forum->getNumThreads(), 'luna'), $forum->getNumThreads() ) ?> &middot; <?php printf( _n('%s comment', '%s comments', $forum->getNumThreads(), 'luna'), $forum->getNumComments() ) ?></p>
							<?php echo $forum->getDescription() ?>
						</a>
					<?php } ?>
				<?php } ?>
			</div>
			<?php if (get_read_url('forumview')) { ?>
				<hr />
				<div class="list-group list-group-none">
					<a class="list-group-item" href="<?php echo get_read_url('index') ?>"><i class="fas fa-fw fa-glasses"></i> <?php _e('Mark all as read', 'luna') ?></a>
				</div>
			<?php } ?>
		</div>
		<div class="col-md-8 col-12">
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
			<div class="list-group list-group-thread">
				<?php draw_index_threads_list(); ?>
			</div>
		</div>
	</div>
</div>