<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<h2 class="profile-title"><?php _e('Search', 'luna') ?></h2>
<?php echo $paging_links ?>
<div class="list-group list-group-thread">
	<?php draw_search_results(); ?>
</div>