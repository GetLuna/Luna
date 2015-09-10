<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<div class="topicview">
	<h2 class="profile-title"><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></h2>
	<div class="forum-navigation forum-navigation-top btn-toolbar">
		<span class="btn-group">
			<a href="index.php" class="btn btn-primary"><span class="fa fa-fw fa-home"></span></a>
			<a href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>" class="btn btn-primary"><?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
			<a href="viewtopic.php?id=<?php echo $id ?>" class="btn btn-primary"><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></a>
		</span>
		<span class="btn-group pull-right">
			<?php echo $post_link ?><?php echo $paging_links ?>
		</span>
	</div>
	<?php draw_comment_list(); ?>
	<div class="forum-navigation forum-navigation-bottom btn-toolbar">
		<span class="btn-group">
			<a href="index.php" class="btn btn-primary"><span class="fa fa-fw fa-home"></span></a>
			<a href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>" class="btn btn-primary"><?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
			<a href="viewtopic.php?id=<?php echo $id ?>" class="btn btn-primary"><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></a>
		</span>
		<span class="btn-group pull-right">
			<?php echo $post_link ?><?php echo $paging_links ?>
		</span>
	</div>
	<form method="post" action="post.php?tid=<?php echo $id ?>" onsubmit="window.onbeforeunload=null;this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
		<?php draw_editor('7'); ?>
	</form>
	<div class="btn-toolbar">
		<?php if ($is_admmod): ?>
			<a class="btn btn-primary" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&tid=<?php echo $id ?>&p=<?php echo $p ?>"><span class="fa fa-fw fa-eye"></span> <?php _e('Moderate', 'luna') ?></a>
			<?php if($num_pages > 1) { ?>
				<a class="btn btn-primary" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&tid=<?php echo $id ?>&action=all"><span class="fa fa-fw fa-list"></span> <?php _e('Show all posts', 'luna') ?></a>
			<?php } ?>
			<div class="btn-group">
				<a class="btn btn-primary" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&move_topics=<?php echo $id ?>"><span class="fa fa-fw fa-arrows-alt"></span> <?php _e('Move', 'luna') ?></a>
				<?php if ($cur_topic['closed'] == '1') { ?>
					<a class="btn btn-success" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&open=<?php echo $id ?>"><span class="fa fa-fw fa-unlock"></span> <?php _e('Open', 'luna') ?></a>
				<?php } else { ?>
					<a class="btn btn-danger" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&close=<?php echo $id ?>"><span class="fa fa-fw fa-lock"></span> <?php _e('Close', 'luna') ?></a>
				<?php } ?>
				
				<?php if ($cur_topic['sticky'] == '1') { ?>
					<a class="btn btn-danger" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&unstick=<?php echo $id ?>"><span class="fa fa-fw fa-thumb-tack"></span> <?php _e('Unpin', 'luna') ?></a>
				<?php } else { ?>
					<a class="btn btn-primary" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&stick=<?php echo $id ?>"><span class="fa fa-fw fa-thumb-tack"></span> <?php _e('Pin', 'luna') ?></a>
				<?php } ?>
			</div>
		<?php endif; ?>
	</div>
</div>