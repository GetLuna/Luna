<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
</div>
<div class="jumbotron">
	<div class="container">
		<h2 class="forum-title"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></h2><span class="pull-right"><?php echo $post_link ?><?php echo $paging_links ?></span>
		<div class="forum-desc"><?php echo luna_htmlspecialchars($cur_forum['forum_desc']) ?></div>
	</div>
</div>
<div class="container">
	<div class="row forumview">
		<div class="col-sm-3">
			<div class="list-group list-group-forum">
				<a class="list-group-item" href="index.php"><span class="fa fa-fw fa-chevron-left"></span> <?php _e('Back to index', 'luna') ?></a>
			</div>
			<hr />
			<?php if ((is_subforum($id) && $id != '0')): ?>
				<h5 class="list-group-head"><?php _e('Subforums', 'luna') ?></h5>
				<div class="list-group list-group-forum">
					<?php draw_subforum_list('forum.php') ?>
				</div>
				<hr />
			<?php endif; ?>
			<div class="list-group list-group-forum">
				<?php draw_mark_read('list-group-item', 'forumview') ?>
				<?php if ($id != '0' && $is_admmod) { ?>
					<a class="list-group-item" href="backstage/moderate.php?fid=<?php echo $forum_id ?>&p=<?php echo $p ?>"><span class="fa fa-fw fa-eye"></span> <?php _e('Moderate forum', 'luna') ?></a>
				<?php } ?>
			</div>
		</div>
		<div class="col-sm-9">
			<div class="list-group list-group-topic">
				<?php draw_topics_list(); ?>
			</div>
		</div>
	</div>