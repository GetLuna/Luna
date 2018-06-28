<div class="list-group-item <?php echo $item_status ?>"<?php echo $item_style ?>>
	<div class="row">
		<div class="col-md-6 col-6">
			<a href="viewforum.php?id=<?php echo $cur_forum['fid'] ?>" style="color: <?php echo $cur_forum['color'] ?>" class="forum-title"><?php echo $faicon.' '.luna_htmlspecialchars($cur_forum['forum_name']) ?></a><br />
			<?php echo $forum_desc ?>
		</div>
		<div class="col-lg-1 col-md-2 d-none d-md-block">
			<?php echo '<h5>'.$cur_forum['num_threads'].'</h5> <h6><small>'.$threads_label.'</small></h6>'; ?>
		</div>
		<div class="col-lg-1 d-none d-lg-block">
			<?php echo '<h5>'.$cur_forum['num_comments'].'</h5> <h6><small>'.$comments_label.'</small></h6>';  ?>
		</div>
		<div class="col-lg-4 col-md-4 col-6 overflow">
			<?php echo $last_comment ?>
		</div>
	</div>
</div>