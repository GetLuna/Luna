<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="row row-nav">
    <div class="col-sm-6">
        <div class="btn-group btn-breadcrumb">
            <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-home"></span></a>
            <a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
            <a class="btn btn-primary" href="viewtopic.php?id=<?php echo $id ?>"><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></a>
        </div>
    </div>
    <div class="col-sm-6">
        <?php echo $post_link ?>
        <ul class="pagination">
            <?php echo $paging_links ?>
        </ul>
    </div>
</div>