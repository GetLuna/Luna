<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="panel panel-danger">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Post errors'] ?></h3>
    </div>
    <div class="panel-body">
        <p>
<?php

    foreach ($errors as $cur_error)
        echo "\t\t\t\t".$cur_error."\n";
?>
        </p>
    </div>
</div>