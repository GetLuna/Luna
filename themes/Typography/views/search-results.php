<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

include(LUNA_ROOT.'themes/Typography/functions.php');

?>
<div class="main container">
	<div class="jumbotron default" style="background-color: <?php echo $cur_forum['color']; ?>;">
		<h2><?php _e('Search results', 'luna') ?></h2>
	</div>
	<div class="row">
		<div class="col-12">
			<?php typography_paginate($paging_links) ?>
            <div class="list-group list-group-thread">
                <?php draw_search_results(); ?>
            </div>
		</div>
	</div>
</div>