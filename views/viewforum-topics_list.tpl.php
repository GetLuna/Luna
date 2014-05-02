<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

    <div class="row topic-row <?php echo $item_status ?>">
        <div class="col-sm-6 col-xs-6">
            <div class="<?php echo $icon_type ?>"><?php echo forum_number_format($topic_count + $start_from) ?></div>
            <div class="tclcon">
                <div>
                    <?php echo $subject."\n" ?>
                </div>
            </div>
        </div>
        <div class="col-sm-2 hidden-xs"><?php if (is_null($cur_topic['moved_to'])) { ?><b><?php echo forum_number_format($cur_topic['num_replies']) ?></b> <?php echo $replies_label ?><br /><b><?php echo forum_number_format($cur_topic['num_views']) ?></b> <?php echo $views_label ?><?php } ?></div>
        <div class="col-sm-4 col-xs-6"><?php echo $last_post ?></div>
    </div>