<div class="list-group-item <?php echo $item_status ?><?php if ($cur_thread['soft'] == true) echo ' soft'; ?>">
	<span class="middot">&middot;</span>
	<span class="hidden-xs hidden-sm hidden-md hidden-lg">
		<?php echo forum_number_format($thread_count + $start_from) ?>
	</span>
	<?php echo $subject_status ?> <?php echo $subject ?><span class="hidden-xs"> <?php echo $by ?></span> <?php echo $subject_multipage ?>
	<?php if ($cur_thread['moved_to'] == 0) { ?>
		<span class="text-muted"> &middot; 
			<?php echo $last_comment_date ?>
			<span class="hidden-xs">
				<?php echo $last_commenter ?>
			</span>
		</span>
		<span class="pull-right label label-default"><?php echo forum_number_format($cur_thread['num_replies']) ?></span>
	<?php } ?>
</div>