<div class="row forum-entry">
	<div class="col-md-6">
		<a href="<?php echo $page ?>?id=<?php echo $cur_forum['fid'] ?>"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></a><br />
		<?php echo $forum_desc ?>
	</div>
	<div class="col-md-1 text-center">
		<?php echo '<b>'.$cur_forum['num_topics'].'</b> '.$topics_label; ?>
	</div>
	<div class="col-md-1 text-center">
		<?php echo '<b>'.$cur_forum['num_posts'].'</b> '.$posts_label; ?>
	</div>
	<div class="col-md-4">
		<?php echo $last_post ?>
	</div>
</div>