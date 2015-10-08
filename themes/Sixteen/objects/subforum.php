<div class="row forum-row <?php echo $item_status ?>">
	<div class="col-md-11 col-sm-10 col-xs-8">
		<strong><a href="viewforum.php?id=<?php echo $cur_forum['fid'] ?>"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></a></strong><br />
		<?php echo $forum_desc ?>
	</div>
	<div class="col-md-1 col-sm-2 col-xs-4 text-center">
		<?php echo '<b>'.$cur_forum['num_topics'].'</b> '.$topics_label; ?><br />
		<?php echo '<b>'.$cur_forum['num_posts'].'</b> '.$posts_label; ?>
	</div>
</div>