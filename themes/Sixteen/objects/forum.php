<div class="row forum-entry <?php echo $item_status ?>">
	<div class="col-sm-6 col-xs-6">
		<strong><a href="viewforum.php?id=<?php echo $cur_forum['fid'] ?>"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></a></strong><br />
		<?php echo $forum_desc ?>
	</div>
	<div class="col-sm-1 hidden-xs text-center">
		<?php echo '<b>'.$cur_forum['num_topics'].'</b> '.$topics_label; ?><br />
		<?php echo '<b>'.$cur_forum['num_posts'].'</b> '.$posts_label; ?>
	</div>
	<div class="col-sm-5 col-xs-6">
		<?php echo $last_post ?> <?php echo $forum_field_new ?>
	</div>
</div>