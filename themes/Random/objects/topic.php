<div class="row topic-row <?php echo $item_status ?><?php if ($cur_topic['soft'] == true) echo ' soft'; ?>">
	<div class="col-sm-6 col-xs-6">
		<div class="<?php echo $icon_type ?>"><?php echo forum_number_format($topic_count + $start_from) ?></div>
		<div class="tclcon">
			<?php echo $subject_status ?> <a href="<?php echo $url ?>"><?php echo $subject ?></a> <?php echo $subject_new_posts ?> <?php echo $by ?> <?php echo $subject_multipage ?>
		</div>
	</div>
	<?php if ($cur_topic['moved_to'] == 0) { ?>
	<div class="col-sm-2 hidden-xs text-center">
		<span><b><?php echo forum_number_format($cur_topic['num_replies']) ?></b> <?php echo $replies_label ?><br /><b><?php echo forum_number_format($cur_topic['num_views']) ?></b> <?php echo $views_label ?></span>
	</div>
	<div class="col-sm-4 col-xs-6">
		<span class="text-muted">
			<?php echo $last_post_date ?>
			<span class="hidden-xs">
				<?php echo $last_poster ?>
			</span>
		</span>
	</div>
	<?php } ?>
</div>