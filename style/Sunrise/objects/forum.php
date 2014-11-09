<?php
if ($luna_config['o_forum_new_style'] == '0' || (!isset($zset))) {
	$forum_style = 'style="background-color:'.$cur_forum['color'].';border-color:'.$cur_forum['color'].';"';
	$group_style = 'style="border-color:'.$cur_forum['color'].';"';

    // If there is a last_post/last_poster
    if ($cur_forum['last_post'] != '') {
        if (luna_strlen($cur_forum['subject']) > 43)
            $cur_forum['subject'] = utf8_substr($cur_forum['subject'], 0, 42).'…';

			$last_post = '<a href="viewtopic.php?pid='.$cur_forum['last_post_id'].'#p'.$cur_forum['last_post_id'].'" class="list-group-item" '.$group_style.'><span class="forum-newestitem">'.luna_htmlspecialchars($cur_forum['subject']).'<span class="help-block">by '.luna_htmlspecialchars($cur_forum['username']).'</span></span></a>';
    }

	$forum_stats = '<span class="list-group-item" '.$group_style.'>'.$cur_forum['num_topics'].' topics and '.$cur_forum['num_posts'].' posts</span>';

?>
<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
	<div class="list-group list-group-forum">
		<a href="viewforum.php?id=<?php echo $cur_forum['fid'] ?>" class="list-group-item list-group-item-cat" <?php echo $forum_style ?>>
			<h4><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></h4>
			<?php echo $forum_desc ?>
		</a>
		<?php echo $last_post ?>
		<?php echo $forum_stats ?>
	</div>
</div>
<?php
} else {
	$forum_style = 'style="background-color:'.$cur_forum['color'].';border-color:'.$cur_forum['color'].';"';
	$group_style = 'style="border:'.$cur_forum['color'].' 3px solid;"';
	$forum_background_color = 'style="background-color:'.$cur_forum['color'].';"';

    // If there is a last_post/last_poster
    if ($cur_forum['last_post'] != '') {
        if (luna_strlen($cur_forum['subject']) > 43)
            $cur_forum['subject'] = utf8_substr($cur_forum['subject'], 0, 42).'…';

			$last_post = '
				<a href="viewtopic.php?pid='.$cur_forum['last_post_id'].'#p'.$cur_forum['last_post_id'].'" class="list-group-item">
					'.luna_htmlspecialchars($cur_forum['subject']).'
					<span class="pull-right">
						by '.luna_htmlspecialchars($cur_forum['username']).'
					</span>
				</a>
			';
    } else
		$last_post = '
			<div class="list-group-item no-posts">
				No posts yet
			</div>
		';

	$forum_stats = '<b>'.$cur_forum['num_topics'].'</b> topics<br /><b>'.$cur_forum['num_posts'].'</b> posts';

?>
<div class="row forum-entry">
	<div class="col-sm-4">
		<h4><a href="viewforum.php?id=<?php echo $cur_forum['fid'] ?>" class="label label-lg label-default" <?php echo $forum_background_color ?>><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></a></h4>
		<div class="help-block"><?php echo $forum_desc ?></div>
	</div>
	<div class="col-sm-8">
		<div class="list-group">
			<?php echo $last_post ?>
		</div>
	</div>
</div>
<span class="hr-forum-entry">
	<hr />
</span>
<?php } ?>