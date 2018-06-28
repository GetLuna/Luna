<div class="list-group-item <?php echo $item_status ?><?php if ($cur_thread['soft'] == true) echo ' soft'; ?>">
	<span class="d-none">
		<?php echo forum_number_format($thread_count + $start_from) ?>
	</span>
	<a href="<?php echo $url ?>"><?php echo $subject ?></a><span class="d-none d-md-inline"><br /><?php _e('In', 'luna' ) ?></span><span class="d-inline d-md-none"> &middot; </span>
    <?php echo $forum_name ?> &middot; <span class="badge badge-light"><?php echo forum_number_format($cur_thread['num_replies']) ?></span>
</div>
