<a href="viewforum.php?id=<?php echo $cur_forum['fid'] ?>" class="list-group-item <?php echo $item_status ?>" style="<?php echo ( strpos( $item_status, 'active') !== false) ? 'background-color: '.$cur_forum['color'].';' : '' ?>">
	<h5 class="mb-1" style="<?php echo ( strpos( $item_status, 'active') === false) ? 'color: '.$cur_forum['color'].';' : 'color: #fff;' ?>">
		<?php echo $faicon?> <span "forum-title"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></span>
	</h5>
	<p class="text-muted"><?php echo $cur_forum['num_threads'].' '.$threads_label ?> &middot; <?php echo $cur_forum['num_comments'].' '.$comments_label ?></p>
	<?php echo $forum_desc ?>
</a>
