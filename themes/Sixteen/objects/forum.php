<div class="row forum-row <?php echo $item_status ?>">
	<div class="col-md-5 col-sm-4 col-xs-5">
		<strong><a href="viewforum.php?id=<?php echo $cur_forum['fid'] ?>"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></a></strong><br />
		<?php echo $forum_desc ?>
	</div>
	<div class="col-md-1 col-sm-2 hidden-xs text-center">
		<?php echo '<b>'.$cur_forum['num_topics'].'</b> '.$topics_label; ?><br />
		<?php echo '<b>'.$cur_forum['num_posts'].'</b> '.$posts_label; ?>
	</div>
	<div class="col-md-6 col-sm-6 col-xs-7">
		<?php echo $last_post ?> <?php echo $forum_field_new ?>
	</div>
</div>