<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<nav class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<a href="search.php" class="navbar-brand"><span class="fa fa-fw fa-search"></span> <?php _e('Search', 'luna') ?></a>
	</div>
</nav>
<?php echo $paging_links ?>
<div class="list-group list-group-topic">
	<?php draw_search_results(); ?>
</div>