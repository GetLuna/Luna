<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div id="posterror">
    <h2><?php echo $lang['New password errors'] ?></h2>
    <div class="error-info">
        <p><?php echo $lang['New passworderrors info'] ?></p>
        <ul class="error-list">
<?php

    foreach ($errors as $cur_error)
        echo "\t\t\t\t".'<li><strong>'.$cur_error.'</strong></li>'."\n";
?>
        </ul>
    </div>
</div>