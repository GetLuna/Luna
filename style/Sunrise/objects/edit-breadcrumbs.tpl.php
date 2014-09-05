<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<a class="btn btn-primary" href="viewtopic.php?id=<?php echo $cur_post['tid'] ?>"><span class="fa fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_post['subject']) ?></a>