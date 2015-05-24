<?php
if ($section_head == '1') {
?>
<div class="alert alert-info alert-section alert-all">
	<h3 class="inline"><?php echo $cur_section['forum_name']; ?></h3>
	<?php if (($cur_forum['post_topics'] == '' && $luna_user['g_post_topics'] == '1') || $cur_forum['post_topics'] == '1' || $luna_user['is_admmod']) { ?>
	<span class="pull-right"><a class="btn btn-default" href="post.php?fid=<?php echo $cur_section['id'] ?>"><?php _e('Post', 'luna') ?></a></span>
	<?php } ?>
	<p><?php echo $cur_section['forum_desc']; ?></p>
</div>
<?php } else { ?>
<div class="alert alert-info alert-section alert-all">
	<h3><?php _e('Recent activity', 'luna') ?></h3>
</div>
<?php } ?>