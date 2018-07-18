<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

include(LUNA_ROOT.'themes/Typography/functions.php');

?>
<div class="container main">
	<div class="jumbotron jumbotron-title" style="background-color: <?php echo $cur_forum['color']; ?>;">
		<h2><?php echo $faicon.' '.luna_htmlspecialchars($cur_forum['forum_name']) ?><span class="float-right"><?php echo $comment_link ?></span></h2>
		<div class="description"><?php echo $cur_forum['forum_desc'] ?></div>
	</div>
	<div class="row forumview">
		<div class="col-12">
			<div class="btn-toolbar btn-toolbar-options">
				<a class="btn btn-light" href="index.php"><i class="fas fa-fw fa-chevron-left"></i> <?php _e('Back', 'luna') ?></a>
				<?php if ($cur_forum['is_subscribed']) { ?>
					<a class="btn btn-light btn-light-active" href="misc.php?action=unsubscribe&amp;fid=<?php echo $id ?><?php echo $token_url ?>"><i class="fas fa-fw fa-star"></i></a>
				<?php } else { ?>
					<a class="btn btn-light" href="misc.php?action=subscribe&amp;fid=<?php echo $id ?><?php echo $token_url ?>"><i class="far fa-fw fa-star"></i></a>
				<?php } ?>
				<?php if (get_read_url('forumview')) { ?>
					<a class="btn btn-light" href="<?php echo get_read_url('forumview') ?>"><i class="fas fa-fw fa-glasses"></i></a>
				<?php } ?>
				<?php if ($id != '0' && $is_admmod) { ?>
					<a class="btn btn-light" href="backstage/moderate.php?fid=<?php echo $forum_id ?>&p=<?php echo $p ?>"><i class="fas fa-fw fa-eye"></i></a>
				<?php } ?>
			</div>
			<?php if ((is_subforum($id) && $id != '0')): ?>
				<div class="list-group list-group-nav">
					<?php draw_subforum_list('forum.php') ?>
				</div>
			<?php endif; ?>
			<div class="list-group list-group-thread">
				<?php draw_threads_list(); ?>
			</div>
			<?php typography_paginate($paging_links) ?>
		</div>
	</div>
</div>