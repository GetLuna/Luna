<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></h2>

<?php

    require FORUM_ROOT.'views/viewtopic-breadcrumbs.tpl.php';