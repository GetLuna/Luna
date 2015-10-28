<div class="row thread-row <?php echo $item_status ?><?php if ($cur_thread['soft'] == true) echo ' soft'; ?>">
	<div class="col-sm-6 col-xs-6">
		<span class="middot">&middot; </span> <?php echo $subject_status ?> <a href="<?php echo $url ?>"><?php echo $subject."\n" ?></a><?php echo $by ?>
	</div>
	<div class="col-sm-2 hidden-xs"><?php if (is_null($cur_thread['moved_to'])) { ?><b><?php echo forum_number_format($cur_thread['num_replies']) ?></b> <?php echo $replies_label ?><br /><b><?php echo forum_number_format($cur_thread['num_views']) ?></b> <?php echo $views_label ?><?php } ?></div>
	<div class="col-sm-4 col-xs-6"><?php echo $last_comment_date ?></div>
</div>