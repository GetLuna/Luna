<div class="list-group-item <?php echo $item_status ?><?php if ($cur_thread['soft'] == true) echo ' soft'; ?>">
	<span class="hidden-xs hidden-sm hidden-md hidden-lg">
		<?php echo forum_number_format($thread_count + $start_from) ?>
	</span>
	<a href="<?php echo $url ?>"><?php echo $subject ?></a>
    <br />
    <?php _e('In', 'luna' ) ?> <?php echo $forum_name ?> &middot; <span class="label label-default"><?php echo forum_number_format($cur_thread['num_replies']) ?></span>
</div>
