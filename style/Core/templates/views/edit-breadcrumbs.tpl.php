<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="btn-group btn-breadcrumb">
    <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-home"></span></a>
    <a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_post['fid'] ?>"><?php echo luna_htmlspecialchars($cur_post['forum_name']) ?></a>
    <a class="btn btn-primary" href="viewtopic.php?id=<?php echo $cur_post['tid'] ?>"><?php echo luna_htmlspecialchars($cur_post['subject']) ?></a>
    <a class="btn btn-primary" href="#"><?php echo $lang['Edit post'] ?></a>
</div>