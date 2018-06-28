<div class="list-group-item <?php echo $item_status ?><?php if ($cur_thread['soft'] == true) echo ' soft'; ?>">
	<div class="row">
		<div class="col-lg-6 col-md-6 col-10">
			<?php echo $subject_status ?> <a href="<?php echo $url ?>" class="forum-title"><?php echo $subject ?></a><br />
			<span class="thread-desc"><?php echo $by ?> <?php echo $subject_multipage ?><span class="d-inline d-md-none"><?php echo (($cur_thread['moved_to'] == 0)? ' &middot; '.__('Latest comment on', 'luna').' '.$last_comment_date : '') ?></span></span>
		</div>
		<div class="col-lg-1 col-md-2 col-2">
			<?php echo '<h5>'.(($cur_thread['moved_to'] == 0)? $cur_thread['num_replies'] : '-').'</h5> <h6><small>'.$comments_label.'</small></h6>';  ?>
		</div>
		<div class="col-lg-1 d-lg-block d-none">
			<?php echo '<h5>'.(($cur_thread['moved_to'] == 0)? $cur_thread['num_views'] : '-').'</h5> <h6><small>'.$views_label.'</small></h6>'; ?>
		</div>
		<div class="col-lg-4 col-md-4 d-none overflow">
			<span class="thread-date">
				<?php echo (($cur_thread['moved_to'] == 0)? $last_comment_date : '-') ?>
			</span>
		</div>
	</div>
</div>
