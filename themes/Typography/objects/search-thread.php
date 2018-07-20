<div class="thread-item <?php echo $item_status.(($cur_thread['soft'] == true) ? ' soft' : '') ?>">
	<div>
		<span class="middot h4">&middot;</span> 
		<a href="<?php echo $url ?>" class="h4">
			<?php echo $subject ?>
		</a>
	</div>
	<span class="d-none">
		<?php echo forum_number_format($thread_count + $start_from) ?>
	</span>
	<?php echo $subject_status ?> <?php echo $by ?> <?php echo $subject_multipage ?>
	<?php if ($cur_thread['moved_to'] == 0) { ?>
		<span class="text-muted"> &middot;
			<?php echo $last_commenter ?>
			&middot; <?php if (isset($forum_name)) { echo $forum_name.' &middot; '; } ?><?php if ($cur_thread['moved_to'] == 0) { ?><span class="badge badge-light"><?php echo forum_number_format($cur_search['num_replies']) ?></span><?php } ?>
		</span>
	<?php } ?>
</div>
