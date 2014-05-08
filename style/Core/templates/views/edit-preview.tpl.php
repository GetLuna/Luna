<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

require_once FORUM_ROOT.'include/parser.php';
$preview_message = parse_message($message, $hide_smilies);

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Post preview'] ?></h3>
    </div>
    <div class="panel-body">
        <?php echo $preview_message ?>
    </div>
</div>