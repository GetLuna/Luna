<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></h2>

<?php

    require get_view_path('viewtopic-breadcrumbs.tpl.php');