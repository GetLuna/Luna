<div class="topic-entry <?php echo $item_status ?>">
    <h3><input type="checkbox" name="topics[<?php echo $cur_topic['id'] ?>]" value="1" /> <div class="hidden-xs hidden-sm- hidden-md hidden-lg"><?php echo forum_number_format($topic_count + $start_from) ?></div><?php echo $subject ?> <small><?php echo $by ?></small></h3>
    <span><b><?php echo forum_number_format($cur_topic['num_replies']) ?></b> posts, last post by <b><?php echo $last_post ?></b></span>
</div>