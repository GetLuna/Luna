<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="btn-group btn-breadcrumb">
    <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-home"></span></a>
    <a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_posting['id'] ?>"><?php echo luna_htmlspecialchars($cur_posting['forum_name']) ?></a>
    <?php if (isset($_POST['req_subject'])): ?>
        <a class="btn btn-primary" href="viewtopic.php?id=<?php echo $cur_post['tid'] ?>"><?php echo luna_htmlspecialchars($_POST['req_subject']) ?></a>
    <?php endif; ?>
    <?php if (isset($cur_posting['subject'])): ?>
        <a class="btn btn-primary" href="viewtopic.php?id=<?php echo $tid ?>"><?php echo luna_htmlspecialchars($cur_posting['subject']) ?></a>
    <?php endif; ?>
    <?php if (!isset($_POST['req_subject'])): ?>
        <a class="btn btn-primary" href="#"><?php echo $action ?></a>
    <?php endif; ?>
</div>