<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div id="idx<?php echo $cat_count ?>">
    <div class="category-box">
        <div class="row category-header">
            <div class="col-xs-12"><?php echo luna_htmlspecialchars($cur_forum['cat_name']) ?></div>
        </div>