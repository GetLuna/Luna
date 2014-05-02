<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="btn-group btn-breadcrumb">
    <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-home"></span></a>
    <a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_post['fid'] ?>"><?php echo luna_htmlspecialchars($cur_post['forum_name']) ?></a>
    <a class="btn btn-primary" href="viewtopic.php?pid=<?php echo $id ?>#p<?php echo $id ?>"><?php echo luna_htmlspecialchars($cur_post['subject']) ?></a>
    <a class="btn btn-primary" href="#"><?php echo $lang['Delete post'] ?></a>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php printf($is_topic_post ? $lang['Topic by'] : $lang['Reply by'], '<strong>'.luna_htmlspecialchars($cur_post['poster']).'</strong>', format_time($cur_post['posted'])) ?></h3>
    </div>
    <div class="panel-body">
        <form method="post" action="delete.php?id=<?php echo $id ?>">
            <p><?php echo ($is_topic_post) ? '<strong>'.$lang['Topic warning'].'</strong>' : '<strong>'.$lang['Warning'].'</strong>' ?><br /><?php echo $lang['Delete info'] ?></p>
            <div><input type="submit" class="btn btn-danger" name="delete" value="<?php echo $lang['Delete'] ?>" /> <a href="javascript:history.go(-1)" class="btn btn-link"><?php echo $lang['Go back'] ?></a></div>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo luna_htmlspecialchars($cur_post['poster']) ?></h3>
    </div>
    <div class="panel-body">
        <?php echo $cur_post['message'] ?>
    </div>
</div>

<?php

    require FORUM_ROOT.'footer.php';