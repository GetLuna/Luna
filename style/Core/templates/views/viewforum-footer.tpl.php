<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

</div>
<div class="row row-nav">
    <div class="col-sm-6">
        <div class="btn-group btn-breadcrumb">
            <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-home"></span></a>
            <a class="btn btn-primary" href="viewforum.php?id=<?php echo $id ?>"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></a>
        </div>
    </div>
    <div class="col-sm-6">
        <?php echo $post_link ?>
        <ul class="pagination">
            <?php echo $paging_links ?>
        </ul>
    </div>
</div>

<?php

    require FORUM_ROOT.'footer.php';