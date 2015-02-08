<div class="topic-entry <?php echo $item_status ?>">
	<h3><div class="hidden-xs hidden-sm- hidden-md hidden-lg"><?php echo forum_number_format($topic_count + $start_from) ?></div><?php echo $subject ?> <small><?php echo $by ?> in <?php echo $forum ?></small></h3>
	<span><?php echo forum_number_format($cur_search['num_replies']) ?> replies, last post on <?php echo $last_poster ?></span>
</div>