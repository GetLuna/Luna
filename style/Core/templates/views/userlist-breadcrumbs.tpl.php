<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="row">
    <div class="col-sm-12">
        <ul class="pagination pagination-user">
            <?php echo $paging_links ?>
        </ul>
    </div>
</div>