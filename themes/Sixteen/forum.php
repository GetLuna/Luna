<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
</div>
<div class="container">
	<?php if ((is_subforum($id, '1') && $id != '0')): ?>
		<div class="category-box subforum-box">
			<div class="row category-header">
				<div class="col-xs-12">
					<?php _e('Subforums', 'luna') ?>
				</div>
			</div>
			<div class="list-group list-group-forum">
				<?php draw_subforum_list('subforum.php') ?>
			</div>
		</div>
		<hr />
	<?php endif; ?>
	<h2 class="profile-title"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></h2>
	<div class="forum-navigation btn-toolbar">
		<span class="btn-group">
			<a href="index.php" class="btn btn-primary"><span class="fa fa-fw fa-home"></span></a>
			<a href="viewforum.php?id=<?php echo $id ?>" class="btn btn-primary"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></a>
		</span>
		<?php if ($id != '0' && $is_admmod) { ?>
			<span class="btn-group">
				<a class="btn btn-default" href="backstage/moderate.php?fid=<?php echo $forum_id ?>&p=<?php echo $p ?>"><span class="fa fa-fw fa-eye"></span> <?php _e('Moderate', 'luna') ?></a>
			</span>
		<?php } ?>
		<span class="btn-group pull-right">
			<?php echo $post_link ?><?php echo $paging_links ?>
		</span>
	</div>
	<div class="forumview">
		<div class="forum-box">
			<div class="row forum-header">
				<div class="col-xs-12"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></div>
			</div>
			<?php draw_topics_list(); ?>
		</div>
	</div>
	<div class="forum-navigation btn-toolbar">
		<span class="btn-group">
			<a href="index.php" class="btn btn-primary"><span class="fa fa-fw fa-home"></span></a>
			<a href="viewforum.php?id=<?php echo $id ?>" class="btn btn-primary"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></a>
		</span>
		<?php if ($id != '0' && $is_admmod) { ?>
			<span class="btn-group">
				<a class="btn btn-default" href="backstage/moderate.php?fid=<?php echo $forum_id ?>&p=<?php echo $p ?>"><span class="fa fa-fw fa-eye"></span> <?php _e('Moderate', 'luna') ?></a>
			</span>
		<?php } ?>
		<span class="btn-group pull-right">
			<?php echo $post_link ?><?php echo $paging_links ?>
		</span>
	</div>