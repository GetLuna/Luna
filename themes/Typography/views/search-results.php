<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

include(LUNA_ROOT.'themes/Typography/functions.php');

?>
<div class="main container">
	<div class="row">
		<div class="col-12">
			<div class="title-block title-block-primary">
				<h2><i class="fas fa-fw fa-search"></i> <?php _e('Search results', 'luna') ?></h2>
			</div>
			<div class="btn-toolbar btn-toolbar-options">
            	<?php echo typography_paginate($paging_links) ?>
			</div>
            <div class="list-group list-group-thread">
                <?php draw_search_results(); ?>
            </div>
		</div>
	</div>
</div>