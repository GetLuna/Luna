<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;


?>
<div class="index profile-header container-fluid">
	<div class="jumbotron profile">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h2 class="username"><?php _e( 'Welcome back', 'luna' ) ?></h2>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="main index profile container">
	<div class="row">
		<div class="col-sm-3 col-xs-12 sidebar">
			<div class="container-avatar hidden-xs">
				<img src="<?php echo get_avatar( $luna_user['id'] ) ?>" alt="Avatar" class="img-avatar img-center">
			</div>
			<?php if ($luna_config['o_header_search'] && $luna_user['g_search'] == '1'): ?>
			<form id="search" class="input-group search-form" method="get" action="search.php?section=simple">
				<input type="hidden" name="action" value="search" />
				<input class="form-control" type="text" name="keywords" placeholder="<?php _e('Search in comments', 'luna') ?>" maxlength="100" />
				<span class="input-group-btn">
					<button class="btn btn-default btn-search" type="submit" name="search" accesskey="s"><i class="fa fa-fw fa-search"></i></button>
				</span>
			</form>
			<hr />
			<?php endif; ?>
			<div class="list-group list-group-luna">
				<?php draw_forum_list('forum.php', 1, 'category.php', ''); ?>
			</div>
			<?php if ($luna_user['g_search'] == '1') { ?>
			<hr />
			<div class="list-group list-group-luna">
				<?php echo implode('', $page_threadsearches) ?>
			</div>
			<?php } ?>
			<hr />
			<div class="list-group list-group-luna">
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
			<div class="title-block title-block-primary">
				<h3><?php _e('Recent activity', 'luna') ?></h3>
			</div>
			<div class="list-group list-group-thread">
				<?php draw_index_threads_list(); ?>
			</div>
		</div>
	</div>
</div>