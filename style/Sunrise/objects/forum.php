<?php
if ($luna_config['o_forum_new_style'] == '0' || (!isset($zset))) {
	$forum_style = 'style="background-color:'.$cur_forum['color'].';border-color:'.$cur_forum['color'].';"';
	$group_style = 'style="border-color:'.$cur_forum['color'].';"';

    // If there is a last_post/last_poster
    if ($cur_forum['last_post'] != '') {
        if (luna_strlen($cur_forum['last_topic']) > 43)
            $cur_forum['last_topic'] = utf8_substr($cur_forum['last_topic'], 0, 40).'...';

			$last_post = '<a href="viewtopic.php?pid='.$cur_forum['last_post_id'].'#p'.$cur_forum['last_post_id'].'" class="list-group-item" '.$group_style.'><span class="forum-newestitem">'.luna_htmlspecialchars($cur_forum['last_topic']).'<span class="help-block">by '.luna_htmlspecialchars($cur_forum['last_poster']).'</span></span></a>';
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

    // If there is a last_post/last_poster
    if ($cur_forum['last_post'] != '') {
        if (luna_strlen($cur_forum['last_topic']) > 43)
            $cur_forum['last_topic'] = utf8_substr($cur_forum['last_topic'], 0, 40).'...';

			$last_post = '<a href="viewtopic.php?pid='.$cur_forum['last_post_id'].'#p'.$cur_forum['last_post_id'].'">'.luna_htmlspecialchars($cur_forum['last_topic']).'</a><span class="help-block">by '.luna_htmlspecialchars($cur_forum['last_poster']).'</span>';
    } else
		$last_post = 'No posts yet';

	$forum_stats = '<b>'.$cur_forum['num_topics'].'</b> topics<br /><b>'.$cur_forum['num_posts'].'</b> posts';

?>
<div class="row forum-entry" <?php echo $group_style ?>>
	<div class="col-md-6 col-sm-6 col-xs-6 forum-subject"<?php echo $forum_style ?>>
		<a href="viewforum.php?id=<?php echo $cur_forum['fid'] ?>"><h3><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></h3></a>
		<?php echo $forum_desc ?>
	</div>
	<div class="col-md-2 hidden-sm hidden-xs forum-stats">
		<?php echo $forum_stats ?>
	</div>
	<div class="col-md-4 col-sm-6 col-xs-6 forum-last">
		<?php echo $last_post ?>
	</div>
</div>
<?php } ?>