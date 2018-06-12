<div class="list-group-item clearfix <?php echo $item_status ?>"<?php echo $item_style ?>>
	<div class="col-md-6 col-sm-6 col-xs-6">
		<a href="viewforum.php?id=<?php echo $cur_forum['fid'] ?>" style="color: <?php echo $cur_forum['color'] ?>" class="forum-title"><?php echo $faicon.' '.luna_htmlspecialchars($cur_forum['forum_name']) ?></a><br />
		<?php echo $forum_desc ?>
	</div>
	<div class="col-md-1 col-sm-2 hidden-xs">
		<?php echo '<h5>'.$cur_forum['num_threads'].'</h5> <h6><small>'.$threads_label.'</small></h6>'; ?>
	</div>
	<div class="col-md-1 hidden-sm hidden-xs">
		<?php echo '<h5>'.$cur_forum['num_comments'].'</h5> <h6><small>'.$comments_label.'</small></h6>';  ?>
	</div>
	<div class="col-md-4 col-sm-4 col-xs-6 overflow">
		<?php echo $last_comment ?>
	</div>
</div>
