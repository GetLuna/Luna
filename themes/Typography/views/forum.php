<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

include(LUNA_ROOT.'themes/Typography/functions.php');

?>
<div class="container main">
	<div class="jumbotron default" style="background-color: <?php echo $cur_forum2->getColor() ?>;">
		<h2><?php echo $cur_forum2->getIconMarkup().' '.$cur_forum2->getName() ?><span class="float-right"><?php echo $comment_link ?></span></h2>
		<div class="description"><?php echo $cur_forum2->getDescription() ?></div>
	</div>
	<div class="row forumview">
		<div class="col-md-12">	
		</div>
		<div class="col-md-4">
			<?php if ((is_subforum($cur_forum2->getId()) && $cur_forum2->getId() != '0')): ?>
				<div class="list-group list-group-nav">
					<h4><?php _e('Subforums', 'luna') ?></h4>
					<?php draw_subforum_list('forum.php') ?>
				</div>
				<hr />
			<?php endif; ?>
			<div class="forum-list d-none d-md-block">
				<div class="list-group list-group-nav">
					<?php foreach( $board as $category ) { ?>
						<h4><?php echo $category->getName() ?></h4>
						<?php foreach( $category->getForums() as $forum ) { ?>
							<a href="<?php echo $forum->getForumUrl() ?>" class="list-group-item <?php echo $forum->getForumClasses() ?>" style="<?php echo ( $forum->isActive() ) ? 'background-color: '.$forum->getColor().';' : '' ?>">
								<h5 class="mb-1" style="color: <?php echo ( $forum->isActive() ) ? '#fff' : $forum->getColor() ?>;">
									<?php echo $forum->getIconMarkup() ?> <span class="forum-title"><?php echo $forum->getName() ?></span>
								</h5>
								<p class="text-muted"><?php printf( _n('%s thread', '%s threads', $forum->getNumThreads(), 'luna'), $forum->getNumThreads() ) ?> &middot; <?php printf( _n('%s comment', '%s comments', $forum->getNumThreads(), 'luna'), $forum->getNumComments() ) ?></p>
								<?php echo $forum->getDescription() ?>
							</a>
						<?php } ?>
					<?php } ?>
				</div>
			</div>
			<?php if (get_read_url('forumview')) { ?>
				<hr />
				<div class="list-group list-group-none">
					<a class="list-group-item" href="<?php echo get_read_url('forumview') ?>"><i class="fas fa-fw fa-glasses"></i> <?php _e('Mark as read', 'luna') ?></a>
				</div>
			<?php } ?>
		</div>
		<div class="col-md-8">
			<div class="btn-toolbar btn-toolbar-options">
				<a class="btn btn-light" href="index.php"><i class="fas fa-fw fa-chevron-left"></i> <?php _e('Back', 'luna') ?></a>
				<?php if ($cur_forum['is_subscribed']) { ?>
					<a class="btn btn-light btn-light-active" href="misc.php?action=unsubscribe&amp;fid=<?php echo $cur_forum2->getId() ?><?php echo $token_url ?>"><i class="fas fa-fw fa-star"></i></a>
				<?php } else { ?>
					<a class="btn btn-light" href="misc.php?action=subscribe&amp;fid=<?php echo $cur_forum2->getId() ?><?php echo $token_url ?>"><i class="far fa-fw fa-star"></i></a>
				<?php } ?>
				<?php if ($cur_forum2->getId() != '0' && $is_admmod) { ?>
					<a class="btn btn-light" href="backstage/moderate.php?fid=<?php echo $cur_forum2->getId() ?>&p=<?php echo $p ?>"><i class="fas fa-fw fa-eye"></i></a>
				<?php } ?>
			</div>
			<div class="list-group list-group-thread">
				<?php draw_threads_list(); ?>
			</div>
			<?php typography_paginate($paging_links) ?>
		</div>
	</div>
</div>