<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>
</div>
<div class="jumbotron" style="background:#999;">
	<div class="container">
		<h2><?php echo $lang['Search'] ?></h2>
        <span class="pull-right">
            <ul class="pagination">
                <?php echo $paging_links ?>
            </ul>
        </span>
	</div>
</div>
<div class="container">
<?php draw_search_results(); ?>