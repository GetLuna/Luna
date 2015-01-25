<?php
$style = '';

if ($current_id == $cur_forum['fid'])
	$style = ' style="background-color: '.$cur_forum['color'].';border-left-color: '.$cur_forum['color'].';"';
?>
<a href="<?php echo $page ?>?id=<?php echo $cur_forum['fid'] ?>" class="list-group-item <?php echo $item_status ?>"<?php echo $style ?>>
	<h3><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?> <small><?php echo '<b>'.$cur_forum['num_topics'].'</b> '.$topics_label.' and <b>'.$cur_forum['num_posts'].'</b> '.$posts_label; ?></small></h3>
	<?php echo $forum_desc ?>
</a>