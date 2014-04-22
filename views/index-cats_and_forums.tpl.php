<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

            <div class="<?php echo $item_status ?> row forum-row">
                <div class="col-sm-6 col-xs-6">
                    <div class="<?php echo $icon_type ?>"><div class="nosize"><?php echo forum_number_format($forum_count) ?></div></div>
                    <div class="tclcon">
                        <div>
                            <?php echo $forum_field."\n".$moderators ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2 hidden-xs"><b><?php echo forum_number_format($num_topics) ?></b> <?php echo $topics_label ?><br /><b><?php echo forum_number_format($num_posts) ?></b> <?php echo $posts_label ?></div>
                <div class="col-sm-4 col-xs-6"><?php echo $last_post ?></div>
            </div>