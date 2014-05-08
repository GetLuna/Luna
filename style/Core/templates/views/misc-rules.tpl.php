<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="panel panel-default">
    <div id="rules-block" class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Forum rules'] ?></h3>
    </div>
    <div class="panel-body">
        <div class="usercontent"><?php echo $luna_config['o_rules_message'] ?></div>
    </div>
</div>

<?php

    require FORUM_ROOT.'footer.php';